<?php
// Update language replacements
d2u_machinery_lang_helper::factory()->install();

// Update modules
if(class_exists(D2UModuleManager)) {
	$modules = [];
	$modules[] = new D2UModule("90-1",
		"D2U Machinery Addon - Hauptausgabe",
		6);
	if(rex_plugin::get('d2u_machinery', 'industry_sectors')->isAvailable()) {
		$modules[] = new D2UModule("90-2",
			"D2U Machinery Addon - Branchen",
			1);
	}
	$modules[] = new D2UModule("90-3",
		"D2U Machinery Addon - Kategorien",
		3);
	if(rex_plugin::get('d2u_machinery', 'used_machines')->isAvailable()) {
		$modules[] = new D2UModule("90-4",
			"D2U Machinery Addon - Gebrauchtmaschinen",
			7);
	}
	$modules[] = new D2UModule("90-5",
		"D2U Machinery Addon - Box Beratungshinweis",
		1);
	$d2u_module_manager = new D2UModuleManager($modules, "", "d2u_machinery");
	$d2u_module_manager->autoupdate();
}

// 1.0.1 Update database
$sql = \rex_sql::factory();
$sql->setQuery("SHOW COLUMNS FROM ". \rex::getTablePrefix() ."d2u_machinery_machines LIKE 'priority';");
if($sql->getRows() == 0) {
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_machinery_machines "
		. "ADD priority INT(10) NULL DEFAULT NULL AFTER machine_id;");
}
// 1.0.2 Update database
$sql->setQuery("SHOW COLUMNS FROM ". \rex::getTablePrefix() ."d2u_machinery_machines_lang LIKE 'lang_name';");
if($sql->getRows() == 0) {
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_machinery_machines_lang "
		. "ADD lang_name varchar(255) collate utf8_general_ci default NULL AFTER clang_id;");
}
$sql->setQuery("SHOW COLUMNS FROM ". \rex::getTablePrefix() ."d2u_machinery_categories LIKE 'video_ids';");
if($sql->getRows() == 0) {
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_machinery_categories "
		. "ADD video_ids varchar(255) collate utf8_general_ci default NULL AFTER pic_usage;");
}
$sql->setQuery("SHOW COLUMNS FROM ". \rex::getTablePrefix() ."d2u_machinery_machines LIKE 'video_ids';");
if($sql->getRows() == 0) {
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_machinery_machines "
		. "ADD video_ids varchar(255) collate utf8_general_ci default NULL AFTER operating_voltage_a;");
}
$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_machinery_machines_lang "
		. "CHANGE `pdfs` `pdfs` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `description`;");
// 1.1.3 Update database
$sql->setQuery("SHOW COLUMNS FROM ". \rex::getTablePrefix() ."d2u_machinery_categories LIKE 'videomanager_ids';");
if($sql->getRows() > 0) {
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_machinery_categories "
		. "CHANGE videomanager_ids video_ids varchar(255) collate utf8_general_ci default NULL;");
}
$sql->setQuery("SHOW COLUMNS FROM ". \rex::getTablePrefix() ."d2u_machinery_machines LIKE 'videomanager_ids';");
if($sql->getRows() > 0) {
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_machinery_machines "
		. "CHANGE videomanager_ids video_ids varchar(255) collate utf8_general_ci default NULL;");
}
// 1.2 Update database
$sql->setQuery("SHOW COLUMNS FROM ". \rex::getTablePrefix() ."d2u_machinery_machines LIKE 'internal_name';");
if($sql->getRows() > 0) {
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_machinery_machines "
		. "DROP internal_name;");
}
// 1.3 Update database
$sql->setQuery("SHOW COLUMNS FROM ". \rex::getTablePrefix() ."d2u_machinery_machines LIKE 'engine_power_frequency_controlled';");
if($sql->getRows() == 0) {
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_machinery_machines "
		. "ADD engine_power_frequency_controlled VARCHAR(10) collate utf8_general_ci default NULL AFTER engine_power;");
}

// 1.0.2 Update machine URL view and regenerate path file
$sql->setQuery('CREATE OR REPLACE VIEW '. \rex::getTablePrefix() .'d2u_machinery_url_machines AS
	SELECT lang.machine_id, lang.clang_id, IF(lang.lang_name IS NULL or lang.lang_name = "", machines.name, lang.lang_name) as name, CONCAT(IF(lang.lang_name IS NULL or lang.lang_name = "", machines.name, lang.lang_name), " - ", categories.name) AS seo_title, lang.teaser AS seo_description, machines.category_id, lang.updatedate
	FROM '. \rex::getTablePrefix() .'d2u_machinery_machines_lang AS lang
	LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_machines AS machines ON lang.machine_id = machines.machine_id
	LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_categories_lang AS categories ON machines.category_id = categories.category_id AND lang.clang_id = categories.clang_id
	LEFT JOIN '. \rex::getTablePrefix() .'clang AS clang ON lang.clang_id = clang.id
	WHERE clang.status = 1 AND machines.online_status = "online"');
$sql->setQuery('CREATE OR REPLACE VIEW '. \rex::getTablePrefix() .'d2u_machinery_url_machine_categories AS
	SELECT machines.category_id, categories_lang.clang_id, CONCAT_WS(" - ", parent_categories.name, categories_lang.name) AS name, CONCAT_WS(" - ", categories_lang.name, parent_categories.name) AS seo_title, categories_lang.teaser AS seo_description, categories_lang.updatedate
	FROM '. \rex::getTablePrefix() .'d2u_machinery_machines_lang AS lang
	LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_machines AS machines ON lang.machine_id = machines.machine_id
	LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_categories_lang AS categories_lang ON machines.category_id = categories_lang.category_id AND lang.clang_id = categories_lang.clang_id
	LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_categories AS categories ON categories_lang.category_id = categories.category_id
	LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_categories_lang AS parent_categories ON categories.parent_category_id = parent_categories.category_id AND lang.clang_id = parent_categories.clang_id
	LEFT JOIN '. \rex::getTablePrefix() .'clang AS clang ON lang.clang_id = clang.id
	WHERE clang.status = 1 AND machines.online_status = "online" 
	GROUP BY category_id, clang_id, name, seo_title, seo_description, updatedate');
if(\rex_addon::get("url")->isAvailable()) {
	$clang_id = count(rex_clang::getAllIds()) == 1 ? rex_clang::getStartId() : 0;
	$sql_replace = \rex_sql::factory();
	$sql->setQuery("SELECT id FROM `". \rex::getTablePrefix() ."url_generate` WHERE `table_parameters` LIKE '%d2u_machinery_url_machines_url_param_key\":\"machine_id\"%';");
	if($sql->getRows() == 0) {
		$sql_replace->setQuery("DELETE FROM `". \rex::getTablePrefix() ."url_generate` WHERE `table_parameters` LIKE '%d2u_machinery_url_machines_url_param_key\":\"machine_id\"%';");
		$sql_replace->setQuery("INSERT INTO `". \rex::getTablePrefix() ."url_generate` (`article_id`, `clang_id`, `url`, `table`, `table_parameters`, `relation_table`, `relation_table_parameters`, `relation_insert`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
			(". rex_article::getSiteStartArticleId() .", ". $clang_id .", '', '1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machines', '{\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machines_field_1\":\"name\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machines_field_2\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machines_field_3\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machines_id\":\"machine_id\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machines_clang_id\":\"". (count(rex_clang::getAllIds()) > 1 ? "clang_id" : "") ."\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machines_restriction_field\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machines_restriction_operator\":\"=\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machines_restriction_value\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machines_url_param_key\":\"machine_id\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machines_seo_title\":\"seo_title\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machines_seo_description\":\"seo_description\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machines_seo_image\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machines_sitemap_add\":\"1\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machines_sitemap_frequency\":\"monthly\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machines_sitemap_priority\":\"1.0\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machines_sitemap_lastmod\":\"updatedate\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machines_path_names\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machines_path_categories\":\"0\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machines_relation_field\":\"category_id\"}', '1_xxx_relation_". \rex::getTablePrefix() ."d2u_machinery_url_machine_categories', '{\"1_xxx_relation_". \rex::getTablePrefix() ."d2u_machinery_url_machine_categories_field_1\":\"name\",\"1_xxx_relation_". \rex::getTablePrefix() ."d2u_machinery_url_machine_categories_field_2\":\"\",\"1_xxx_relation_". \rex::getTablePrefix() ."d2u_machinery_url_machine_categories_field_3\":\"\",\"1_xxx_relation_". \rex::getTablePrefix() ."d2u_machinery_url_machine_categories_id\":\"category_id\",\"1_xxx_relation_". \rex::getTablePrefix() ."d2u_machinery_url_machine_categories_clang_id\":\"clang_id\"}', 'before', UNIX_TIMESTAMP(), 'd2u_machinery_addon_installer', UNIX_TIMESTAMP(), 'd2u_machinery_addon_installer');");
	}
	$sql->setQuery("SELECT id FROM `". \rex::getTablePrefix() ."url_generate` WHERE `table_parameters` LIKE '%d2u_machinery_url_machine_categories_url_param_key\":\"category_id\"%';");
	if($sql->getRows() == 0) {
		$sql_replace->setQuery("DELETE FROM `". \rex::getTablePrefix() ."url_generate` WHERE `table_parameters` LIKE '%d2u_machinery_url_machine_categories_url_param_key\":\"category_id\"%';");
		$sql->setQuery("INSERT INTO `". \rex::getTablePrefix() ."url_generate` (`article_id`, `clang_id`, `url`, `table`, `table_parameters`, `relation_table`, `relation_table_parameters`, `relation_insert`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
			(". rex_article::getSiteStartArticleId() .", ". $clang_id .", '', '1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machine_categories', '{\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machine_categories_field_1\":\"name\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machine_categories_field_2\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machine_categories_field_3\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machine_categories_id\":\"category_id\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machine_categories_clang_id\":\"". (count(rex_clang::getAllIds()) > 1 ? "clang_id" : "") ."\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machine_categories_restriction_field\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machine_categories_restriction_operator\":\"=\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machine_categories_restriction_value\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machine_categories_url_param_key\":\"category_id\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machine_categories_seo_title\":\"seo_title\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machine_categories_seo_description\":\"seo_description\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machine_categories_seo_image\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machine_categories_sitemap_add\":\"1\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machine_categories_sitemap_frequency\":\"always\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machine_categories_sitemap_priority\":\"0.7\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machine_categories_sitemap_lastmod\":\"updatedate\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machine_categories_path_names\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machine_categories_path_categories\":\"0\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machine_categories_relation_field\":\"\"}', '', '[]', 'before', UNIX_TIMESTAMP(), 'd2u_machinery_addon_installer', UNIX_TIMESTAMP(), 'd2u_machinery_addon_installer');");
	}
	
	UrlGenerator::generatePathFile([]);
}

// 1.0.2 YForm e-mail template
$sql->setQuery("SELECT * FROM ". \rex::getTablePrefix() ."yform_email_template WHERE name = 'd2u_machinery_machine_request'");
if($sql->getRows() == 0) {
	$sql->setQuery("REPLACE INTO ". \rex::getTablePrefix() ."yform_email_template (`name`, `mail_from`, `mail_from_name`, `subject`, `body`, `body_html`, `attachments`) VALUES
		('d2u_machinery_machine_request', 'REX_YFORM_DATA[field=\"email\"]', 'REX_YFORM_DATA[field=\"vorname\"] REX_YFORM_DATA[field=\"name\"]', 'Maschinenanfrage', 'Maschinenanfrage von Internetseite:\r\nMaschine: REX_YFORM_DATA[field=\"machine_name\"]\r\n\r\nEs fragt an:\r\nVorname: REX_YFORM_DATA[field=\"vorname\"]\r\nName: REX_YFORM_DATA[field=\"name\"]\r\nFunktion: REX_YFORM_DATA[field=\"position\"]\r\nFirma: REX_YFORM_DATA[field=\"company\"]\r\nAnschrift: REX_YFORM_DATA[field=\"address\"]\r\nPLZ/Ort: REX_YFORM_DATA[field=\"zip\"] REX_YFORM_DATA[field=\"city\"]\r\nLand: REX_YFORM_DATA[field=\"country\"]\r\nFax: REX_YFORM_DATA[field=\"fax\"]\r\nTelefon: REX_YFORM_DATA[field=\"phone\"]\r\nEmail: REX_YFORM_DATA[field=\"email\"]\r\nBitte um Rückruf: REX_YFORM_DATA[field=\"please_call\"]\r\n\r\nNachricht: REX_YFORM_DATA[field=\"message\"]\r\n', '', '')");
}

// 1.2.0 Persmission upgrade
$sql->setQuery("SELECT * FROM ". \rex::getTablePrefix() ."user_role WHERE INSTR(perms, 'd2u_machinery[edit_tech_data]') > 0");
if($sql->getRows() == 0) {
	$sql->setQuery("UPDATE ". \rex::getTablePrefix() ."user_role SET perms = REPLACE(perms, 'd2u_machinery[edit_tech_data]', 'd2u_machinery[edit_data]') WHERE INSTR(perms, 'd2u_machinery[edit_tech_data]') > 0;");
}

// 1.2.2 Media type added
$sql->setQuery("SELECT * FROM ". \rex::getTablePrefix() ."media_manager_type WHERE name = 'd2u_machinery_features'");
if($sql->getRows() == 0) {
	$sql->setQuery("INSERT INTO ". \rex::getTablePrefix() ."media_manager_type (`status`, `name`, `description`) VALUES
		(0, 'd2u_machinery_features', 'D2U Machinery Addon Feature, Zubehör, ... Bilder');");
	$last_id_d2u_machinery_list_tile = $sql->getLastId();
	$sql->setQuery("INSERT INTO ". \rex::getTablePrefix() ."media_manager_type_effect (`type_id`, `effect`, `parameters`, `priority`, `createdate`, `createuser`) VALUES
		(". $last_id_d2u_machinery_list_tile .", 'resize', '{\"rex_effect_convert2img\":{\"rex_effect_convert2img_convert_to\":\"jpg\",\"rex_effect_convert2img_density\":\"100\"},\"rex_effect_crop\":{\"rex_effect_crop_width\":\"\",\"rex_effect_crop_height\":\"\",\"rex_effect_crop_offset_width\":\"\",\"rex_effect_crop_offset_height\":\"\",\"rex_effect_crop_hpos\":\"left\",\"rex_effect_crop_vpos\":\"top\"},\"rex_effect_filter_blur\":{\"rex_effect_filter_blur_repeats\":\"\",\"rex_effect_filter_blur_type\":\"\",\"rex_effect_filter_blur_smoothit\":\"\"},\"rex_effect_filter_colorize\":{\"rex_effect_filter_colorize_filter_r\":\"\",\"rex_effect_filter_colorize_filter_g\":\"\",\"rex_effect_filter_colorize_filter_b\":\"\"},\"rex_effect_filter_sharpen\":{\"rex_effect_filter_sharpen_amount\":\"\",\"rex_effect_filter_sharpen_radius\":\"\",\"rex_effect_filter_sharpen_threshold\":\"\"},\"rex_effect_flip\":{\"rex_effect_flip_flip\":\"X\"},\"rex_effect_header\":{\"rex_effect_header_download\":\"open_media\",\"rex_effect_header_cache\":\"no_cache\"},\"rex_effect_insert_image\":{\"rex_effect_insert_image_brandimage\":\"\",\"rex_effect_insert_image_hpos\":\"left\",\"rex_effect_insert_image_vpos\":\"top\",\"rex_effect_insert_image_padding_x\":\"\",\"rex_effect_insert_image_padding_y\":\"\"},\"rex_effect_mediapath\":{\"rex_effect_mediapath_mediapath\":\"\"},\"rex_effect_mirror\":{\"rex_effect_mirror_height\":\"\",\"rex_effect_mirror_set_transparent\":\"colored\",\"rex_effect_mirror_bg_r\":\"\",\"rex_effect_mirror_bg_g\":\"\",\"rex_effect_mirror_bg_b\":\"\"},\"rex_effect_resize\":{\"rex_effect_resize_width\":\"768\",\"rex_effect_resize_height\":\"768\",\"rex_effect_resize_style\":\"minimum\",\"rex_effect_resize_allow_enlarge\":\"enlarge\"},\"rex_effect_rounded_corners\":{\"rex_effect_rounded_corners_topleft\":\"\",\"rex_effect_rounded_corners_topright\":\"\",\"rex_effect_rounded_corners_bottomleft\":\"\",\"rex_effect_rounded_corners_bottomright\":\"\"},\"rex_effect_workspace\":{\"rex_effect_workspace_width\":\"\",\"rex_effect_workspace_height\":\"\",\"rex_effect_workspace_hpos\":\"left\",\"rex_effect_workspace_vpos\":\"top\",\"rex_effect_workspace_set_transparent\":\"colored\",\"rex_effect_workspace_bg_r\":\"\",\"rex_effect_workspace_bg_g\":\"\",\"rex_effect_workspace_bg_b\":\"\"}}', 1, '". date("Y-m-d H:i:s") ."', 'd2u_machinery');");
}

// Insert frontend translations
d2u_machinery_lang_helper::factory()->install();

// Remove default lang setting
if(!$this->hasConfig()) {
	$this->removeConfig('default_lang');
}
// Remove unused config: show usage areas in navi (never implemented)
if($this->hasConfig('show_categories_usage_area')) {
	$this->removeConfig('show_categories_usage_area');
}

// Rename config (config was splittet in 1.2.2)
if($this->getConfig('show_usage_areas', 'hide') == 'show') {
	$this->setConfig('show_categories_usage_areas', 'show');
	$this->removeConfig('show_usage_areas');
}