<?php
/**
 * LinkedIn export.
 */
class SocialExportLinkedIn extends AExport {
	
	/**
	 * OAuth object
	 */
	private $oauth;
	
	/**
	 * Constructor. Initializes variables
	 * @param Provider $provider Export Provider
	 */
	public function __construct($provider) {
		parent::__construct($provider);
		
		$this->exported_used_machines = ExportedUsedMachine::getAll($this->provider);
		
		$this->oauth = new OAuth($this->provider->social_app_id, $this->provider->social_app_secret);
	}	

	/*
	 * Holt die in der Datenbank gespeicherte LinkedIn ID.
	 * @param ExportConfig $config Exportkonfiguration
	 * @param String $table_prefix Tabellen Praefix.
	 * @return String LinkedIn ID
	 */
	function getLinkedInID($config, $table_prefix = "rex_") {
		$get_id_qry = 'SELECT linkedin_id FROM '. $table_prefix .'d2u_baumaschinen_plugin_export_config '
				.'WHERE export_config_id = '. $config->id;

		$get_id_result = new rex_sql();
		$get_id_result->setQuery($get_id_qry);

		$linkedin_id = $get_id_result->getValue("linkedin_id");
		return $linkedin_id;
	}

	/*
	 * Speichert die LinkedIn ID in der Datenbank.
	 * @param ExportConfig $config Exportkonfiguration
	 * @param String $table_prefix Tabellen Praefix.
	 * @return boolean True wenn erfolgreich gespeichert, sonst false
	 */
	function setLinkedInID($config, $table_prefix = "rex_") {
		$set_id_qry = "UPDATE ". $table_prefix ."d2u_baumaschinen_plugin_export_config "
				."SET linkedin_id = '". trim($config->linkedin_id) ."' "
				.'WHERE export_config_id = '. $config->id;
		$set_id_sql = new rex_sql();
		$set_id_sql->setQuery($set_id_qry);

		if(getLinkedInID($config, $table_prefix) == $config->linkedin_id) {
			return true;
		}
		else {
			return false;
		}
	}

	/*
	 * Gibt die Objekt IDs der zu exportierenden Maschinen zurueck. Im LinkedIn Export
	 * sind das nur die neuen Objekte.
	 * @param Baumaschinen[] $maschinen Array mit Maschinenn
	 * @param String $umfang Exportumfang: NEW, CHANGE oder DELETE
	 * @return int[] Array mit den IDs der exportierten Maschinen.
	 */
	function getLinkeInObjektIDs($maschinen, $umfang = "") {
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

	/**
	 * Export used machines.
	 * @return string Error message
	 */
	public function export() {
		foreach($this->exported_used_machines as $exported_used_machine) {
			$used_machine = new UsedMachine($exported_used_machine->used_machine_id, $this->provider->clang_id);
			// Delete from wall
			if($exported_used_machine->export_action == "delete" || $exported_used_machine->export_action == "update") {
				if($exported_used_machine->provider_import_id != "") {
					// Delete stream update			
					try {
						$this->oauth->fetch($exported_used_machine->provider_import_id, false, OAUTH_HTTP_METHOD_DELETE);
					} catch (OAuthException $e) {
						// Status Sept. 2012: deleting is not supported
					}
				}

				// delete in database
				if($used_machine->export_action == "delete") {
					$exported_used_machine->delete();
				}
				else {
					$exported_used_machine->export_action = "add";
					$exported_used_machine->provider_import_id = "";
				}
			}

			// Post on wall
			if($exported_used_machine->export_action == "add") {
				// Create XML for LinkedIn Social Stream
				// Documentation: https://developer.linkedin.com/documents/share-api
				// <?xml version="1.0" encoding="UTF-8">
				$xml = new DOMDocument("1.0", "UTF-8");
				$xml->formatOutput = true;

				// Post on Social Stream: prepare XML
				if($this->provider->linkedin_groupid == "") {
					// <share>
					$share = $xml->createElement("share");

					// <comment>Bester Kran auf dem Markt</comment>
					$comment = $xml->createElement("comment");
					$comment->appendChild($xml->createTextNode(Wildcard::get('d2u_machinery_used_machines_year_built', $this->provider->clang_id)));
					$share->appendChild($comment);

					// <content>
					$content = $xml->createElement("content");

					// <title>Potain IGO 32</title>
					$title = $xml->createElement("title");
					$title->appendChild($xml->createTextNode($used_machine->manufacturer ." ". $used_machine->name));
					$content->appendChild($title);

					// <description>Price: on request; ... </description>
					$description = $xml->createElement("description");
					$description->appendChild($xml->createTextNode($used_machine->getExtendedTeaser()));
					$content->appendChild($description);

					// <submitted-url>http://www.meier-krantechnik.de/de/produkte/gebrauchte-krane?action=detail&item=13</submitted-url>
					$submitted_url = $xml->createElement("submitted-url");
					$submitted_url->appendChild($xml->createTextNode($used_machine->getURL(TRUE)));
					$content->appendChild($submitted_url);

					// <submitted-image-url>http://www.meier-krantechnik.de/index.php?rex_img_type=d2u_baumaschinen_list&amp;rex_img_file=sjjdc_826.jpg</submitted-image-url>
					if(count($used_machine->pics) > 0) {
						$submitted_image_url = $xml->createElement("submitted-image-url");
						$submitted_image_url->appendChild($xml->createTextNode(rex::getServer() .'?rex_media_type='. $this->provider->media_manager_type .'&rex_media_file='. $used_machine->pics[0]));
						$content->appendChild($submitted_image_url);
					}

					// </content>
					$share->appendChild($content);

					// <visibility>
					$visibility = $xml->createElement("visibility");

					// <code>anyone</code>
					$code = $xml->createElement("code");
					$code->appendChild($xml->createTextNode("anyone"));
					$visibility->appendChild($code);

					// </visibility>
					$share->appendChild($visibility);

					// </share>
					$xml->appendChild($share);
				}
				// Post on group stream: prepare XML
				else {
					$title_text = $this->provider->company_name ." ". Wildcard::get('d2u_machinery_export_linkedin_offers', $this->provider->clang_id) .": "
						. $used_machine->machine ." ". $used_machine->name;
					$summary_text = Wildcard::get('d2u_machinery_export_linkedin_details', $this->provider->clang_id) ." ". $used_machine->getURL(TRUE) ;

					// <post>
					$post = $xml->createElement("post");

					// <title>Potain IGO 32</title>
					$title = $xml->createElement("title");
					$title->appendChild($xml->createTextNode($title_text));
					$post->appendChild($title);

					// <summary>Price: on request; ... </summary>
					$summary = $xml->createElement("summary");
					$summary->appendChild($xml->createTextNode($summary_text));
					$post->appendChild($summary);

					// </share>
					$xml->appendChild($post);
				}

				$stream_id = "";
				try {
					// Let's post it
					$api_url = "http://api.linkedin.com/v1/people/~/shares";
					if($this->provider->linkedin_groupid != "") {
						$api_url = "http://api.linkedin.com/v1/groups/". $this->provider->linkedin_groupid ."/posts";
					}

					// $_SESSION['linkedin']['verifier'] wurde schon beim Login geholt
	//				$access_token_info = $oauth->getAccessToken("https://api.linkedin.com/uas/oauth/accessToken", "", $_SESSION['linkedin']['verifier']);

					$this->oauth->fetch($api_url, $xml->saveXML(), OAUTH_HTTP_METHOD_POST, array("Content-Type" => "text/xml"));

					$response_headers = http_parse_headers($this->oauth->getLastResponseHeaders());

					// Stream ID
					if($response_headers !== false) {
						if(isset($response_headers["Location"])) {
							$stream_id = $response_headers["Location"];
						}
						else {
							print "<p>". $I18N_EXPORT->msg('linkedin_keine_streamid') ."<br />";
							print "response_headers: <pre>". $response_headers ."</pre>";
							print "</p>";
						}
					}
					else {
						print "<p>". $I18N_EXPORT->msg('linkedin_keine_streamid') ."<br />";
						print "oauth->getLastResponseHeaders(): <pre>". $this->oauth->getLastResponseHeaders() ."</pre>";
						print "</p>";
					}
				} catch (OAuthException $e) {
					print "<p>". $I18N_EXPORT->msg('linkedin_fehler') ."<br />";
					print "<pre>". $e ."</pre></p>";
					return false;
				}
				// Backup Objekt erstellen
				set_export_timestamps($config, array($used_machine->maschinen_id), $table_prefix);

				// Social Stream Post ID zum Backup Objekt schreiben
				$set_id_qry = 'UPDATE '. $table_prefix .'d2u_baumaschinen_plugin_export_maschinen_backup '
					.'SET portal_export_id = "'. $stream_id .'" '
					.'WHERE maschinen_id = '. $used_machine->maschinen_id .' '
						.'AND export_config_id = '. $config->id;
				$set_id_sql = new rex_sql();
				$set_id_data = $set_id_sql->getArray($set_id_qry);
			}
		}
		return true;
	}

	/*
	 * Liest ID, Vor- und Nachname aus einer LinkedIn Antwort aus.
	 * @param OAuth $oauth OAuth objekt von dem Personendaten geholt werden.
	 * @return Array String LinkedIn id, first-name, lastname
	 */
	function getLinkedInPersonInfos($oauth) {
		$return_array = array();

		// eingeloggtes Profil holen
		$api_url = "http://api.linkedin.com/v1/people/~:(id,first-name,last-name)";
		$this->oauth->fetch($api_url, null, OAUTH_HTTP_METHOD_GET);
		$response = $this->oauth->getLastResponse(); 

		$xml = new DOMDocument(); 
		$xml->loadXML($response);
		$persons = $xml->getElementsByTagName("person"); 
		foreach($persons as $person) { 
			$id = $person->getElementsByTagName("id"); 
			$return_array["id"] = $id->item(0)->nodeValue; 

			$first_name = $person->getElementsByTagName("first-name"); 
			$return_array["first_name"] = $first_name->item(0)->nodeValue;

			$last_name = $person->getElementsByTagName("last-name"); 
			$return_array["last_name"] = $last_name->item(0)->nodeValue; 
		}
		return $return_array;
	}
}