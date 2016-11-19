<?php
require_once "tmhOAuth/tmhOAuth.php";

/*
 * Loescht das Twitter OAuth Token mit Secret aus der Datenbank.
 * @param ExportConfig $config Exportkonfiguration
 * @param String $table_prefix Tabellen Praefix.
 * @return ExportConfig Exportkonfiguration aus der die Token geloescht wurden.
 */
function deleteTwitterOAuthToken($config, $table_prefix = "rex_") {
	$set_id_qry = "UPDATE ". $table_prefix ."d2u_baumaschinen_plugin_export_config "
			."SET twitter_oauth_token = '', "
				."twitter_oauth_token_secret = '' "
			.'WHERE export_config_id = '. $config->id;
	$set_id_sql = new rex_sql();
	$set_id_sql->setQuery($set_id_qry);
	
	$config->twitter_oauth_token = "";
	$config->twitter_oauth_token_secret = "";

	return $config;
}

/*
 * Holt die in der Datenbank gespeicherte Twitter ID.
 * @param ExportConfig $config Exportkonfiguration
 * @param String $table_prefix Tabellen Praefix.
 * @return String Twitter ID
 */
function getTwitterID($config, $table_prefix = "rex_") {
	$get_id_qry = 'SELECT twitter_id FROM '. $table_prefix .'d2u_baumaschinen_plugin_export_config '
			.'WHERE export_config_id = '. $config->id;

	$get_id_result = new rex_sql();
	$get_id_result->setQuery($get_id_qry);

	$twitter_id = $get_id_result->getValue("twitter_id");
	return $twitter_id;
}

/*
 * Gibt die Objekt IDs der zu exportierenden Maschinen zurueck. Im Twitter Export
 * sind das nur die neuen Objekte.
 * @param Baumaschinen[] $maschinen Array mit Maschinenn
 * @param String $umfang Exportumfang: NEW, CHANGE oder DELETE
 * @return int[] Array mit den IDs der exportierten Maschinen.
 */
function getTwitterObjektIDs($maschinen, $umfang = "") {
	// Array mit Rueckgabewerten
	$exported_ids = array();

	if(is_array($maschinen) && count($maschinen) > 0) {
		foreach($maschinen as $maschine) {
			if($maschine->aktion == $umfang || $umfang == "") {
				$exported_ids[] = $maschine->maschinen_id;
			}
		}
	}
	return $exported_ids;
}

/*
 * Postet die neuen Maschinen auf dem Twitter Stream und entfernt Alte.
 * @param ExportConfig $config Exportkonfigurationen
 * @param maschinen[] $maschinen Array mit Maschinen
 * @param tmhOAuth $tmhOAuth Twitter authentication object
 * @param i18n $I18N_EXPORT Uebersetzungsdatei
 * @param String $inculde_path Redaxo Pfad fuer includes.
 * @param String $table_prefix Tabellen Praefix.
 * @return boolean True wenn Uebertragung erfolgreich. Sonst false.
 */
function manageTwitterStream($config, $maschinen, $tmhOAuth, $I18N_EXPORT, $include_path, $table_prefix = "rex_") {
	// Return value
	$ret_val = true;

	foreach($maschinen as $maschine) {

		// Objekt vom Stream loeschen
		if($maschine->aktion == "DELETE" || $maschine->aktion == "CHANGE") {
			// Tweet ID aus DB holen
			$get_id_qry = 'SELECT portal_export_id FROM '. $table_prefix .'d2u_baumaschinen_plugin_export_maschinen_backup '
				.'WHERE maschinen_id = '. $maschine->maschinen_id .' '
					.'AND export_config_id = '. $config->id;
			$get_id_result = new sql;
			$get_id_result->setQuery($get_id_qry);

			// Example: $tweet_id_str = "253460353529311232";
			$tweet_id_str = $get_id_result->getValue("portal_export_id");

			$code = $tmhOAuth->request(
				'POST',
				'https://api.twitter.com/1.1/statuses/destroy/'. $tweet_id_str .'.json',
				array(
					'id' => $tweet_id_str
			  	)
			);

			if ($code == 200) {
				// alles paletti
			}
			else {
				print "<p>". $I18N_EXPORT->msg('twitter_delete_failed') ." <b>".
						trim($maschine->hersteller) ." ". trim($maschine->modell) ."</b>. "
						. $I18N_EXPORT->msg('tweet_manuell_loeschen') ."</p><br />";

				$ret_val = false;
				// Wenn es fehlschlaegt soll die Maschine nicht nochmals auf
				// den Tweet geschrieben werden->weiter zur naechsten
				if($maschine->aktion == "CHANGE") {
					continue;
				}
			}

			if($maschine->aktion == "DELETE") {
				// Backup Objekt loeschen, aber nur wenn Aktion auf DELETE ist
				set_export_timestamps($config, array($maschine->maschinen_id), $table_prefix);
			}
		}
		
		// Objekt auf den Stream tweeten
		if($maschine->aktion == "NEW" || $maschine->aktion == "CHANGE") {
			// News schreiben: Bilder vorbereiten
			$bilder = explode(",", $maschine->bilder);
			$image_folder = $include_path .	DIRECTORY_SEPARATOR
					."generated". DIRECTORY_SEPARATOR ."files".	DIRECTORY_SEPARATOR;
			$config->resize_objekt = 100;
			$filename = getPictureNameAndResize($include_path, $config, $bilder[0]);
			$image_path = $image_folder . $filename;
			$image_type = get_filetype_from_db($bilder[0], $table_prefix);
			$handle = fopen($image_path, "rb");
			$image_data = fread($handle, filesize($image_path));
			fclose($handle);

			$image = "{$image_data};type=$image_type;filename={$filename}";

			// News schreiben: Link erstellen
			$url = getMaschinenlisteURL($config->machine_list_articleid, $maschine, $config->clang_id);

			// News schreiben: Beschreibung erstellen
			$dreizeiler = $I18N_EXPORT->msg('twitter_comment_text') ." ";
			$dreizeiler .= trim($maschine->hersteller) ." ". trim($maschine->modell) .". ";

			// Nur 140 Zeichen erlaubt, inkl. Link
			if (strlen($dreizeiler) > (140 - strlen($url))) {
				$dreizeiler = substr($dreizeiler, 0, (140 - strlen($url) - 4)) ."... ";
			}
			$dreizeiler .= $url;

			// News schreiben: XML fuer Twitter Stream erstellen
			$tweet_id_str = "";

			$code = $tmhOAuth->request(
				'POST',
				'https://api.twitter.com/1.1/statuses/update_with_media.json',
				array(
					'media[]'  => $image,
					'status'   => $dreizeiler
			  	),
				true, // use auth
				true // multipart
			);

			if ($code == 200) {
				$resp = json_decode($tmhOAuth->response['response'], true);
				$tweet_id_str = $resp['id_str'];
			}
			else {
				print "<p>". $I18N_EXPORT->msg('twitter_transfer_failed') ." ".
						$maschine->hersteller ." ". $maschine->modell .". "
						. $I18N_EXPORT->msg('twitter_answer'). "</p>";
				print $code;
				print "<br />";
				$ret_val = false;
				continue;
			}

			// Backup Objekt erstellen
			set_export_timestamps($config, array($maschine->maschinen_id), $table_prefix);
			
			// Tweet ID zum Backup Objekt schreiben
			$set_id_qry = 'UPDATE '. $table_prefix .'d2u_baumaschinen_plugin_export_maschinen_backup '
				.'SET portal_export_id = "'. $tweet_id_str .'" '
				.'WHERE maschinen_id = '. $maschine->maschinen_id .' '
					.'AND export_config_id = '. $config->id;
			$set_id_sql = new rex_sql();
			$set_id_data = $set_id_sql->getArray($set_id_qry);
		}
	}
	return $ret_val;
}

/*
 * Speichert das Twitter OAuth Token mit Secret in der Datenbank.
 * @param ExportConfig $config Exportkonfiguration
 * @param String $table_prefix Tabellen Praefix.
 * @return boolean True wenn erfolgreich gespeichert, sonst false
 */
function setTwitterOAuthToken($config, $table_prefix = "rex_") {
	$set_id_qry = "UPDATE ". $table_prefix ."d2u_baumaschinen_plugin_export_config "
			."SET twitter_oauth_token = '". trim($config->twitter_oauth_token) ."', "
				."twitter_oauth_token_secret = '". trim($config->twitter_oauth_token_secret) ."' "
			.'WHERE export_config_id = '. $config->id;
	$set_id_sql = new rex_sql();
	$set_id_sql->setQuery($set_id_qry);
	
	return true;
}
?>
