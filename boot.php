<?php

if(rex::isBackend() && is_object(rex::getUser())) {
	rex_perm::register('d2u_machinery[]', rex_i18n::msg('d2u_machinery_rights_all'));
	rex_perm::register('d2u_machinery[edit_lang]', rex_i18n::msg('d2u_machinery_rights_edit_lang'), rex_perm::OPTIONS);
	rex_perm::register('d2u_machinery[edit_tech_data]', rex_i18n::msg('d2u_machinery_rights_edit_tech_data'), rex_perm::OPTIONS);
	rex_perm::register('d2u_machinery[settings]', rex_i18n::msg('d2u_machinery_rights_settings'));
	
	rex_extension::register('MEDIA_IS_IN_USE', 'rex_d2u_machinery_media_is_in_use');
}

/**
 * Checks if media is used by this addon
 * @param String[] $params Extension point parameter
 * @return string Warning message as array
 */
function rex_d2u_machinery_media_is_in_use($params) {
	$warning = $params['subject'];
	$filename = addslashes($params['filename']);

	// Maschinen
	$sql_machine_lang = rex_sql::factory();
	$sql_machine_lang->setQuery('SELECT * FROM `' . $REX['TABLE_PREFIX'] . 'd2u_machinery_machines_lang` AS lang '
		.'LEFT JOIN `' . $REX['TABLE_PREFIX'] . 'd2u_machinery_machines` AS machines ON lang.machine_id = machines.machine_id '
		.'WHERE pdfs = "%'. $filename .'%" OR pics LIKE "%'. $filename .'%"');
    $rows_machine_lang = $sql_machine_lang->getRows();
	
	// Kategorien
	$sql_kategorien = rex_sql::factory();
	$sql_kategorien->setQuery('SELECT * FROM `' . $REX['TABLE_PREFIX'] . 'd2u_machinery_categories` AS categories '
		.'LEFT JOIN `' . $REX['TABLE_PREFIX'] . 'd2u_ktb_maschinen_kategorien_lang` AS lang ON categories.kategorie_id = lang.kategorie_id '
		.'WHERE clang_id = 1 AND (icon = "'. $filename .'" OR anwendungsbild = "'. $filename .'")');
    $rows_kategorien = $sql_kategorien->getRows();

	$sql_kategorien_lang = rex_sql::factory();
	$sql_kategorien_lang->setQuery('SELECT * FROM `' . $REX['TABLE_PREFIX'] . 'd2u_ktb_maschinen_kategorien_lang` '
		.'WHERE produktinformation_pdf = "'. $filename .'" OR  werkzeugkatalog = "'. $filename .'" OR  schnittbereichskonfigurator = "'. $filename .'"');
    $rows_kategorien_lang = $sql_kategorien_lang->getRows();
	
	// Branchen
	$sql_branchen = rex_sql::factory();
	$sql_branchen->setQuery('SELECT * FROM `' . $REX['TABLE_PREFIX'] . 'd2u_ktb_maschinen_branchen` WHERE bild = "'. $filename .'"');
    $rows_branchen = $sql_branchen->getRows();

	// Features
	$sql_features = rex_sql::factory();
	$sql_features->setQuery('SELECT * FROM `' . $REX['TABLE_PREFIX'] . 'd2u_ktb_maschinen_features` AS features '
		.'LEFT JOIN `' . $REX['TABLE_PREFIX'] . 'd2u_ktb_maschinen_features_lang` AS lang ON features.feature_id = lang.feature_id '
		.'WHERE clang_id = 1 AND bild = "'. $filename .'"');
    $rows_features = $sql_features->getRows();

	// Zu-/Anfuhrseite
	$sql_zuabfuhr = rex_sql::factory();
	$sql_zuabfuhr->setQuery('SELECT * FROM `' . $REX['TABLE_PREFIX'] . 'd2u_ktb_maschinen_zuabfuhr` AS zuabfuhr '
		.'LEFT JOIN ' . $REX['TABLE_PREFIX'] . 'd2u_ktb_maschinen_zuabfuhr_lang AS lang ON zuabfuhr.zuabfuhr_id = lang.zuabfuhr_id '
		.'WHERE clang_id = 1 AND bild = "'. $filename .'"');
    $rows_zuabfuhr = $sql_zuabfuhr->getRows();

	// Gebrauchtmaschinen
	$sql_gebrauchtmaschinen = rex_sql::factory();
	$sql_gebrauchtmaschinen->setQuery('SELECT * FROM `' . $REX['TABLE_PREFIX'] . 'd2u_ktb_maschinen_gebrauchtmaschinen` WHERE bilder LIKE "%'. $filename .'%"');
    $rows_gebrauchtmaschinen = $sql_gebrauchtmaschinen->getRows();

	// Warnung vorbereiten
	$message = "";
	if($rows_maschinen > 0 || $rows_machine_lang > 0 || $rows_kategorien > 0 || $rows_kategorien_lang > 0 || $rows_branchen > 0 || $rows_features > 0 || $rows_zuabfuhr > 0 || $rows_gebrauchtmaschinen > 0) {
		$message = 'KTB Maschinen Addon:<br /><ul>';
	}
	
	// Maschinen
	for($i = 0; $i < $rows_maschinen; $i++) {
		$message .= '<li><a href="javascript:openPage(\'index.php?page=d2u_ktb_maschinen&subpage=maschinen&func=edit&entry_id='.
			$sql_maschinen->getValue('maschinen_id') .'\')">'. $I18N_MASCHINEN -> msg('maschinen') .': '. $sql_maschinen->getValue('name') .'</a></li>';
    }

	for($i = 0; $i < $rows_machine_lang; $i++) {
		$message .= '<li><a href="javascript:openPage(\'index.php?page=d2u_ktb_maschinen&subpage=maschinen&subform='.
			($sql_machine_lang->getValue('clang_id') + 1) .'&func=edit&entry_id='. $sql_machine_lang->getValue('lang.maschinen_id') .'\')">'.
			$I18N_MASCHINEN -> msg('maschinen') .': '. $sql_machine_lang->getValue('name') . '</a></li>';
    }

	// Kategorien
	for($i = 0; $i < $rows_kategorien; $i++) {
		$message .= '<li><a href="javascript:openPage(\'index.php?page=d2u_ktb_maschinen&subpage=kategorien&func=edit&entry_id='.
			$sql_kategorien->getValue('kategorie_id') .'\')">'. $I18N_MASCHINEN -> msg('kategorien') .': '. $sql_kategorien->getValue('name') .'</a></li>';
    }

	for($i = 0; $i < $rows_kategorien_lang; $i++) {
		$message .= '<li><a href="javascript:openPage(\'index.php?page=d2u_ktb_maschinen&subpage=kategorien&subform='.
			($sql_kategorien_lang->getValue('clang_id') + 1) .'&func=edit&entry_id='. $sql_kategorien_lang->getValue('kategorie_id') .'\')">'.
			$I18N_MASCHINEN -> msg('kategorien') .': '. $sql_kategorien_lang->getValue('name') . '</a></li>';
    }

	// Branchen
	for($i = 0; $i < $rows_branchen; $i++) {
		$message .= '<li><a href="javascript:openPage(\'index.php?page=d2u_ktb_maschinen&subpage=branchen&func=edit&entry_id='.
			$sql_branchen->getValue('branchen_id') .'\')">'. $I18N_MASCHINEN -> msg('branchen') .': '. $sql_branchen->getValue('bezeichnung') .'</a></li>';
    }

	// Features
	for($i = 0; $i < $rows_features; $i++) {
		$message .= '<li><a href="javascript:openPage(\'index.php?page=d2u_ktb_maschinen&subpage=features&func=edit&entry_id='.
			$sql_features->getValue('features.feature_id') .'\')">'. $I18N_MASCHINEN -> msg('features') .': '. $sql_features->getValue('ueberschrift') .'</a></li>';
    }

	// Zu-/Abfuhrseite
	for($i = 0; $i < $rows_zuabfuhr; $i++) {
		$message .= '<li><a href="javascript:openPage(\'index.php?page=d2u_ktb_maschinen&subpage=zuabfuhr&func=edit&entry_id='.
			$sql_zuabfuhr->getValue('zuabfuhr.zuabfuhr_id') .'\')">'. $I18N_MASCHINEN -> msg('zuabfuhr') .': '. $sql_zuabfuhr->getValue('ueberschrift') .'</a></li>';
    }

	// Gebrauchtmaschinen
	for($i = 0; $i < $rows_gebrauchtmaschinen; $i++) {
		$message .= '<li><a href="javascript:openPage(\'index.php?page=d2u_ktb_maschinen&subpage=gebrauchtmaschinen&func=edit&entry_id='.
			$sql_gebrauchtmaschinen->getValue('gebrauchtmaschinen_id') .'\')">'. $I18N_MASCHINEN -> msg('gebrauchtmaschinen') .': '.
			$sql_gebrauchtmaschinen->getValue('name') .'</a></li>';
    }

	if(strlen($message) > 0) {
		$message .= '</ul>';
		$warning[] = $message;
	}

	return $warning;
}