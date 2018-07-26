<?php
$sql = \rex_sql::factory();
// Install database
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". \rex::getTablePrefix() ."d2u_machinery_machines (
	machine_id int(10) unsigned NOT NULL auto_increment,
	priority int(10) default NULL,
	name varchar(255) collate utf8_general_ci default NULL,
	pics text collate utf8_general_ci default NULL,
	category_id int(10) default NULL,
	alternative_machine_ids varchar(1000) collate utf8_general_ci default NULL,
	product_number varchar(255) collate utf8_general_ci default NULL,
	article_id_software int(10) default NULL,
	article_id_service int(10) default NULL,
	article_ids_references varchar(255) collate utf8_general_ci default NULL,
	online_status varchar(10) collate utf8_general_ci default 'online',
	engine_power varchar(255) collate utf8_general_ci default NULL,
	engine_power_frequency_controlled varchar(10) collate utf8_general_ci default NULL,
	length int(10) default NULL,
	width int(10) default NULL,
	height int(10) default NULL,
	depth int(10) default NULL,
	weight varchar(255) collate utf8_general_ci default NULL,
	operating_voltage_v varchar(255) collate utf8_general_ci default NULL,
	operating_voltage_hz varchar(255) collate utf8_general_ci default NULL,
	operating_voltage_a varchar(255) collate utf8_general_ci default NULL,
	video_ids varchar(255) collate utf8_general_ci default NULL,
	PRIMARY KEY (machine_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1;");
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". \rex::getTablePrefix() ."d2u_machinery_machines_lang (
	machine_id int(10) NOT NULL,
	clang_id int(10) NOT NULL,
	lang_name varchar(255) collate utf8_general_ci default NULL,
	teaser varchar(255) collate utf8_general_ci default NULL,
	description text collate utf8_general_ci default NULL,
	pdfs varchar(255) collate utf8_general_ci default NULL,
	translation_needs_update varchar(7) collate utf8_general_ci default NULL,
	updatedate int(11) default NULL,
	updateuser varchar(255) collate utf8_general_ci default NULL,
	PRIMARY KEY (machine_id, clang_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1;");

$sql->setQuery("CREATE TABLE IF NOT EXISTS ". \rex::getTablePrefix() ."d2u_machinery_categories (
	category_id int(10) unsigned NOT NULL auto_increment,
	priority int(10) default NULL,
	parent_category_id int(10) default NULL,
	pic varchar(255) collate utf8_general_ci default NULL,
	pic_usage varchar(255) collate utf8_general_ci default NULL,
	video_ids varchar(255) collate utf8_general_ci default NULL,
	PRIMARY KEY (category_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1;");
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". \rex::getTablePrefix() ."d2u_machinery_categories_lang (
	category_id int(10) NOT NULL,
	clang_id int(10) NOT NULL,
	name varchar(255) collate utf8_general_ci default NULL,
	teaser varchar(255) collate utf8_general_ci default NULL,
	usage_area varchar(255) collate utf8_general_ci default NULL,
	pic_lang varchar(255) collate utf8_general_ci default NULL,
	pdfs TEXT collate utf8_general_ci default NULL,
	translation_needs_update varchar(7) collate utf8_general_ci default NULL,
	updatedate int(11) default NULL,
	updateuser varchar(255) collate utf8_general_ci default NULL,
	PRIMARY KEY (category_id, clang_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1;");

// Create views for url addon
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
// Insert url schemes
if(\rex_addon::get('url')->isAvailable()) {
	$sql->setQuery("SELECT * FROM ". \rex::getTablePrefix() ."url_generate WHERE `table` = '1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machines'");
	$clang_id = count(rex_clang::getAllIds()) == 1 ? rex_clang::getStartId() : 0;
	if($sql->getRows() == 0) {
		$sql->setQuery("INSERT INTO `". \rex::getTablePrefix() ."url_generate` (`article_id`, `clang_id`, `url`, `table`, `table_parameters`, `relation_table`, `relation_table_parameters`, `relation_insert`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
			(". rex_article::getSiteStartArticleId() .", ". $clang_id .", '', '1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machines', '{\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machines_field_1\":\"name\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machines_field_2\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machines_field_3\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machines_id\":\"machine_id\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machines_clang_id\":\"". (count(rex_clang::getAllIds()) > 1 ? "clang_id" : "") ."\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machines_restriction_field\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machines_restriction_operator\":\"=\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machines_restriction_value\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machines_url_param_key\":\"machine_id\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machines_seo_title\":\"seo_title\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machines_seo_description\":\"seo_description\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machines_seo_image\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machines_sitemap_add\":\"1\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machines_sitemap_frequency\":\"monthly\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machines_sitemap_priority\":\"1.0\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machines_sitemap_lastmod\":\"updatedate\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machines_path_names\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machines_path_categories\":\"0\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machines_relation_field\":\"category_id\"}', '1_xxx_relation_". \rex::getTablePrefix() ."d2u_machinery_url_machine_categories', '{\"1_xxx_relation_". \rex::getTablePrefix() ."d2u_machinery_url_machine_categories_field_1\":\"name\",\"1_xxx_relation_". \rex::getTablePrefix() ."d2u_machinery_url_machine_categories_field_2\":\"\",\"1_xxx_relation_". \rex::getTablePrefix() ."d2u_machinery_url_machine_categories_field_3\":\"\",\"1_xxx_relation_". \rex::getTablePrefix() ."d2u_machinery_url_machine_categories_id\":\"category_id\",\"1_xxx_relation_". \rex::getTablePrefix() ."d2u_machinery_url_machine_categories_clang_id\":\"clang_id\"}', 'before', UNIX_TIMESTAMP(), 'd2u_machinery_addon_installer', UNIX_TIMESTAMP(), 'd2u_machinery_addon_installer');");
	}
	$sql->setQuery("SELECT * FROM ". \rex::getTablePrefix() ."url_generate WHERE `table` = '1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machine_categories'");
	if($sql->getRows() == 0) {
		$sql->setQuery("INSERT INTO `". \rex::getTablePrefix() ."url_generate` (`article_id`, `clang_id`, `url`, `table`, `table_parameters`, `relation_table`, `relation_table_parameters`, `relation_insert`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
			(". rex_article::getSiteStartArticleId() .", ". $clang_id .", '', '1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machine_categories', '{\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machine_categories_field_1\":\"name\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machine_categories_field_2\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machine_categories_field_3\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machine_categories_id\":\"category_id\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machine_categories_clang_id\":\"". (count(rex_clang::getAllIds()) > 1 ? "clang_id" : "") ."\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machine_categories_restriction_field\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machine_categories_restriction_operator\":\"=\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machine_categories_restriction_value\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machine_categories_url_param_key\":\"category_id\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machine_categories_seo_title\":\"seo_title\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machine_categories_seo_description\":\"seo_description\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machine_categories_seo_image\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machine_categories_sitemap_add\":\"1\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machine_categories_sitemap_frequency\":\"always\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machine_categories_sitemap_priority\":\"0.7\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machine_categories_sitemap_lastmod\":\"updatedate\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machine_categories_path_names\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machine_categories_path_categories\":\"0\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_machine_categories_relation_field\":\"\"}', '', '[]', 'before', UNIX_TIMESTAMP(), 'd2u_machinery_addon_installer', UNIX_TIMESTAMP(), 'd2u_machinery_addon_installer');");
	}
	
	UrlGenerator::generatePathFile([]);
}

// Insert frontend translations
if(class_exists(d2u_machinery_lang_helper)) {
	d2u_machinery_lang_helper::factory()->install();
}

// Media Manager media types
$sql->setQuery("SELECT * FROM ". \rex::getTablePrefix() ."media_manager_type WHERE name = 'd2u_machinery_list_tile'");
if($sql->getRows() == 0) {
	$sql->setQuery("INSERT INTO ". \rex::getTablePrefix() ."media_manager_type (`status`, `name`, `description`) VALUES
		(0, 'd2u_machinery_list_tile', 'D2U Machinery Addon list tile pictures');");
	$last_id_d2u_machinery_list_tile = $sql->getLastId();
	$sql->setQuery("INSERT INTO ". \rex::getTablePrefix() ."media_manager_type_effect (`type_id`, `effect`, `parameters`, `priority`, `createdate`, `createuser`) VALUES
		(". $last_id_d2u_machinery_list_tile .", 'resize', '{\"rex_effect_convert2img\":{\"rex_effect_convert2img_convert_to\":\"jpg\",\"rex_effect_convert2img_density\":\"100\"},\"rex_effect_crop\":{\"rex_effect_crop_width\":\"\",\"rex_effect_crop_height\":\"\",\"rex_effect_crop_offset_width\":\"\",\"rex_effect_crop_offset_height\":\"\",\"rex_effect_crop_hpos\":\"left\",\"rex_effect_crop_vpos\":\"top\"},\"rex_effect_filter_blur\":{\"rex_effect_filter_blur_repeats\":\"\",\"rex_effect_filter_blur_type\":\"\",\"rex_effect_filter_blur_smoothit\":\"\"},\"rex_effect_filter_colorize\":{\"rex_effect_filter_colorize_filter_r\":\"\",\"rex_effect_filter_colorize_filter_g\":\"\",\"rex_effect_filter_colorize_filter_b\":\"\"},\"rex_effect_filter_sharpen\":{\"rex_effect_filter_sharpen_amount\":\"\",\"rex_effect_filter_sharpen_radius\":\"\",\"rex_effect_filter_sharpen_threshold\":\"\"},\"rex_effect_flip\":{\"rex_effect_flip_flip\":\"X\"},\"rex_effect_header\":{\"rex_effect_header_download\":\"open_media\",\"rex_effect_header_cache\":\"no_cache\"},\"rex_effect_insert_image\":{\"rex_effect_insert_image_brandimage\":\"\",\"rex_effect_insert_image_hpos\":\"left\",\"rex_effect_insert_image_vpos\":\"top\",\"rex_effect_insert_image_padding_x\":\"\",\"rex_effect_insert_image_padding_y\":\"\"},\"rex_effect_mediapath\":{\"rex_effect_mediapath_mediapath\":\"\"},\"rex_effect_mirror\":{\"rex_effect_mirror_height\":\"\",\"rex_effect_mirror_set_transparent\":\"colored\",\"rex_effect_mirror_bg_r\":\"\",\"rex_effect_mirror_bg_g\":\"\",\"rex_effect_mirror_bg_b\":\"\"},\"rex_effect_resize\":{\"rex_effect_resize_width\":\"768\",\"rex_effect_resize_height\":\"768\",\"rex_effect_resize_style\":\"minimum\",\"rex_effect_resize_allow_enlarge\":\"enlarge\"},\"rex_effect_rounded_corners\":{\"rex_effect_rounded_corners_topleft\":\"\",\"rex_effect_rounded_corners_topright\":\"\",\"rex_effect_rounded_corners_bottomleft\":\"\",\"rex_effect_rounded_corners_bottomright\":\"\"},\"rex_effect_workspace\":{\"rex_effect_workspace_width\":\"\",\"rex_effect_workspace_height\":\"\",\"rex_effect_workspace_hpos\":\"left\",\"rex_effect_workspace_vpos\":\"top\",\"rex_effect_workspace_set_transparent\":\"colored\",\"rex_effect_workspace_bg_r\":\"\",\"rex_effect_workspace_bg_g\":\"\",\"rex_effect_workspace_bg_b\":\"\"}}', 1, '". date("Y-m-d H:i:s") ."', 'd2u_machinery'),
		(". $last_id_d2u_machinery_list_tile .", 'workspace', '{\"rex_effect_convert2img\":{\"rex_effect_convert2img_convert_to\":\"jpg\",\"rex_effect_convert2img_density\":\"100\"},\"rex_effect_crop\":{\"rex_effect_crop_width\":\"\",\"rex_effect_crop_height\":\"\",\"rex_effect_crop_offset_width\":\"\",\"rex_effect_crop_offset_height\":\"\",\"rex_effect_crop_hpos\":\"left\",\"rex_effect_crop_vpos\":\"top\"},\"rex_effect_filter_blur\":{\"rex_effect_filter_blur_repeats\":\"\",\"rex_effect_filter_blur_type\":\"\",\"rex_effect_filter_blur_smoothit\":\"\"},\"rex_effect_filter_colorize\":{\"rex_effect_filter_colorize_filter_r\":\"\",\"rex_effect_filter_colorize_filter_g\":\"\",\"rex_effect_filter_colorize_filter_b\":\"\"},\"rex_effect_filter_sharpen\":{\"rex_effect_filter_sharpen_amount\":\"\",\"rex_effect_filter_sharpen_radius\":\"\",\"rex_effect_filter_sharpen_threshold\":\"\"},\"rex_effect_flip\":{\"rex_effect_flip_flip\":\"X\"},\"rex_effect_header\":{\"rex_effect_header_download\":\"open_media\",\"rex_effect_header_cache\":\"no_cache\"},\"rex_effect_insert_image\":{\"rex_effect_insert_image_brandimage\":\"\",\"rex_effect_insert_image_hpos\":\"left\",\"rex_effect_insert_image_vpos\":\"top\",\"rex_effect_insert_image_padding_x\":\"\",\"rex_effect_insert_image_padding_y\":\"\"},\"rex_effect_mediapath\":{\"rex_effect_mediapath_mediapath\":\"\"},\"rex_effect_mirror\":{\"rex_effect_mirror_height\":\"\",\"rex_effect_mirror_set_transparent\":\"colored\",\"rex_effect_mirror_bg_r\":\"\",\"rex_effect_mirror_bg_g\":\"\",\"rex_effect_mirror_bg_b\":\"\"},\"rex_effect_resize\":{\"rex_effect_resize_width\":\"\",\"rex_effect_resize_height\":\"\",\"rex_effect_resize_style\":\"maximum\",\"rex_effect_resize_allow_enlarge\":\"enlarge\"},\"rex_effect_rounded_corners\":{\"rex_effect_rounded_corners_topleft\":\"\",\"rex_effect_rounded_corners_topright\":\"\",\"rex_effect_rounded_corners_bottomleft\":\"\",\"rex_effect_rounded_corners_bottomright\":\"\"},\"rex_effect_workspace\":{\"rex_effect_workspace_width\":\"768\",\"rex_effect_workspace_height\":\"768\",\"rex_effect_workspace_hpos\":\"center\",\"rex_effect_workspace_vpos\":\"middle\",\"rex_effect_workspace_set_transparent\":\"colored\",\"rex_effect_workspace_bg_r\":\"255\",\"rex_effect_workspace_bg_g\":\"255\",\"rex_effect_workspace_bg_b\":\"255\"}}', 2, '". date("Y-m-d H:i:s") ."', 'd2u_machinery');");
}
$sql->setQuery("SELECT * FROM ". \rex::getTablePrefix() ."media_manager_type WHERE name = 'd2u_machinery_features'");
if($sql->getRows() == 0) {
	$sql->setQuery("INSERT INTO ". \rex::getTablePrefix() ."media_manager_type (`status`, `name`, `description`) VALUES
		(0, 'd2u_machinery_features', 'D2U Machinery Addon Feature, Zubehör, ... Bilder');");
	$last_id_d2u_machinery_list_tile = $sql->getLastId();
	$sql->setQuery("INSERT INTO ". \rex::getTablePrefix() ."media_manager_type_effect (`type_id`, `effect`, `parameters`, `priority`, `createdate`, `createuser`) VALUES
		(". $last_id_d2u_machinery_list_tile .", 'resize', '{\"rex_effect_convert2img\":{\"rex_effect_convert2img_convert_to\":\"jpg\",\"rex_effect_convert2img_density\":\"100\"},\"rex_effect_crop\":{\"rex_effect_crop_width\":\"\",\"rex_effect_crop_height\":\"\",\"rex_effect_crop_offset_width\":\"\",\"rex_effect_crop_offset_height\":\"\",\"rex_effect_crop_hpos\":\"left\",\"rex_effect_crop_vpos\":\"top\"},\"rex_effect_filter_blur\":{\"rex_effect_filter_blur_repeats\":\"\",\"rex_effect_filter_blur_type\":\"\",\"rex_effect_filter_blur_smoothit\":\"\"},\"rex_effect_filter_colorize\":{\"rex_effect_filter_colorize_filter_r\":\"\",\"rex_effect_filter_colorize_filter_g\":\"\",\"rex_effect_filter_colorize_filter_b\":\"\"},\"rex_effect_filter_sharpen\":{\"rex_effect_filter_sharpen_amount\":\"\",\"rex_effect_filter_sharpen_radius\":\"\",\"rex_effect_filter_sharpen_threshold\":\"\"},\"rex_effect_flip\":{\"rex_effect_flip_flip\":\"X\"},\"rex_effect_header\":{\"rex_effect_header_download\":\"open_media\",\"rex_effect_header_cache\":\"no_cache\"},\"rex_effect_insert_image\":{\"rex_effect_insert_image_brandimage\":\"\",\"rex_effect_insert_image_hpos\":\"left\",\"rex_effect_insert_image_vpos\":\"top\",\"rex_effect_insert_image_padding_x\":\"\",\"rex_effect_insert_image_padding_y\":\"\"},\"rex_effect_mediapath\":{\"rex_effect_mediapath_mediapath\":\"\"},\"rex_effect_mirror\":{\"rex_effect_mirror_height\":\"\",\"rex_effect_mirror_set_transparent\":\"colored\",\"rex_effect_mirror_bg_r\":\"\",\"rex_effect_mirror_bg_g\":\"\",\"rex_effect_mirror_bg_b\":\"\"},\"rex_effect_resize\":{\"rex_effect_resize_width\":\"768\",\"rex_effect_resize_height\":\"768\",\"rex_effect_resize_style\":\"minimum\",\"rex_effect_resize_allow_enlarge\":\"enlarge\"},\"rex_effect_rounded_corners\":{\"rex_effect_rounded_corners_topleft\":\"\",\"rex_effect_rounded_corners_topright\":\"\",\"rex_effect_rounded_corners_bottomleft\":\"\",\"rex_effect_rounded_corners_bottomright\":\"\"},\"rex_effect_workspace\":{\"rex_effect_workspace_width\":\"\",\"rex_effect_workspace_height\":\"\",\"rex_effect_workspace_hpos\":\"left\",\"rex_effect_workspace_vpos\":\"top\",\"rex_effect_workspace_set_transparent\":\"colored\",\"rex_effect_workspace_bg_r\":\"\",\"rex_effect_workspace_bg_g\":\"\",\"rex_effect_workspace_bg_b\":\"\"}}', 1, '". date("Y-m-d H:i:s") ."', 'd2u_machinery');");
}

// YForm e-mail template
$sql->setQuery("SELECT * FROM ". \rex::getTablePrefix() ."yform_email_template WHERE name = 'd2u_machinery_machine_request'");
if($sql->getRows() == 0) {
	$sql->setQuery("INSERT INTO ". \rex::getTablePrefix() ."yform_email_template (`name`, `mail_from`, `mail_from_name`, `subject`, `body`, `body_html`, `attachments`) VALUES
		('d2u_machinery_machine_request', 'REX_YFORM_DATA[field=\"email\"]', 'REX_YFORM_DATA[field=\"vorname\"] REX_YFORM_DATA[field=\"name\"]', 'Maschinenanfrage', 'Maschinenanfrage von Internetseite:\r\nMaschine: REX_YFORM_DATA[field=\"machine_name\"]\r\n\r\nEs fragt an:\r\nVorname: REX_YFORM_DATA[field=\"vorname\"]\r\nName: REX_YFORM_DATA[field=\"name\"]\r\nFunktion: REX_YFORM_DATA[field=\"position\"]\r\nFirma: REX_YFORM_DATA[field=\"company\"]\r\nAnschrift: REX_YFORM_DATA[field=\"address\"]\r\nPLZ/Ort: REX_YFORM_DATA[field=\"zip\"] REX_YFORM_DATA[field=\"city\"]\r\nLand: REX_YFORM_DATA[field=\"country\"]\r\nTelefon: REX_YFORM_DATA[field=\"phone\"]\r\nFax: REX_YFORM_DATA[field=\"fax\"]\r\nEmail: REX_YFORM_DATA[field=\"email\"]\r\nBitte um Rückruf: REX_YFORM_DATA[field=\"please_call\"]\r\n\r\nNachricht: REX_YFORM_DATA[field=\"message\"]\r\n', '', '')");
}

// Standard settings
if (!$this->hasConfig()) {
    $this->setConfig('article_id', rex_article::getSiteStartArticleId());
	$this->setConfig('default_category_sort', 'name');
	$this->setConfig('default_machine_sort', 'name');
	$this->setConfig('show_categories_usage_area', 'hide');
	$this->setConfig('show_teaser', 'hide');
	$this->setConfig('show_techdata', 'show');
}