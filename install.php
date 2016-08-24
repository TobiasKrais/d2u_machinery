<?php
$sql = rex_sql::factory();
// Install database
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". rex::getTablePrefix() ."d2u_machinery_machines (
	machine_id int(10) unsigned NOT NULL auto_increment,
	internal_name varchar(255) collate utf8_general_ci default NULL,
	name varchar(255) collate utf8_general_ci default NULL,
	pics text collate utf8_general_ci default NULL,
	category_id int(10) default NULL,
	alternative_machine_ids varchar(255) collate utf8_general_ci default NULL,
	product_number varchar(255) collate utf8_general_ci default NULL,
	article_id_software int(10) default NULL,
	article_id_service int(10) default NULL,
	article_ids_references varchar(255) collate utf8_general_ci default NULL,
	online_status varchar(10) collate utf8_general_ci default 'online',
	engine_power varchar(255) collate utf8_general_ci default NULL,
	length int(10) default NULL,
	width int(10) default NULL,
	height int(10) default NULL,
	depth int(10) default NULL,
	weight varchar(255) collate utf8_general_ci default NULL,
	operating_voltage_v varchar(255) collate utf8_general_ci default NULL,
	operating_voltage_hz varchar(255) collate utf8_general_ci default NULL,
	operating_voltage_a varchar(255) collate utf8_general_ci default NULL,
	PRIMARY KEY (machine_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1;");
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". rex::getTablePrefix() ."d2u_machinery_machines_lang (
	machine_id int(10) NOT NULL,
	clang_id int(10) NOT NULL,
	teaser varchar(255) collate utf8_general_ci default NULL,
	description text collate utf8_general_ci default NULL,
	pdfs varchar(255) collate utf8_general_ci default NULL,
	translation_needs_update varchar(7) collate utf8_general_ci default NULL,
	updatedate int(11) default NULL,
	updateuser varchar(255) collate utf8_general_ci default NULL,
	PRIMARY KEY (machine_id, clang_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1;");

$sql->setQuery("CREATE TABLE IF NOT EXISTS ". rex::getTablePrefix() ."d2u_machinery_categories (
	category_id int(10) unsigned NOT NULL auto_increment,
	parent_category_id varchar(255) collate utf8_general_ci default NULL,
	pic varchar(255) collate utf8_general_ci default NULL,
	pic_usage varchar(255) collate utf8_general_ci default NULL,
	videomanager_ids varchar(255) collate utf8_general_ci default NULL,
	PRIMARY KEY (category_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1;");
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". rex::getTablePrefix() ."d2u_machinery_categories_lang (
	category_id int(10) NOT NULL,
	clang_id int(10) NOT NULL,
	name varchar(255) collate utf8_general_ci default NULL,
	usage_area varchar(255) collate utf8_general_ci default NULL,
	pic_lang varchar(255) collate utf8_general_ci default NULL,
	pdfs varchar(255) collate utf8_general_ci default NULL,
	translation_needs_update varchar(7) collate utf8_general_ci default NULL,
	updatedate int(11) default NULL,
	updateuser varchar(255) collate utf8_general_ci default NULL,
	PRIMARY KEY (category_id, clang_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1;");

// Media Manager media types
$sql->setQuery("SELECT * FROM ". rex::getTablePrefix() ."media_manager_type WHERE name = 'd2u_machinery_list_tile'");
if($sql->getRows() == 0) {
	$sql->setQuery("INSERT INTO ". rex::getTablePrefix() ."media_manager_type (`status`, `name`, `description`) VALUES
		(0, 'd2u_machinery_list_tile', 'D2U Machinery Addon list tile pictures');");
	$last_id_d2u_machinery_list_tile = $sql->getLastId();
	$sql->setQuery("INSERT INTO ". rex::getTablePrefix() ."media_manager_type_effect (`type_id`, `effect`, `parameters`, `priority`, `createdate`, `createuser`) VALUES
		(". $last_id_d2u_machinery_list_tile .", 'resize', '{\"rex_effect_convert2img\":{\"rex_effect_convert2img_convert_to\":\"jpg\",\"rex_effect_convert2img_density\":\"100\"},\"rex_effect_crop\":{\"rex_effect_crop_width\":\"\",\"rex_effect_crop_height\":\"\",\"rex_effect_crop_offset_width\":\"\",\"rex_effect_crop_offset_height\":\"\",\"rex_effect_crop_hpos\":\"left\",\"rex_effect_crop_vpos\":\"top\"},\"rex_effect_filter_blur\":{\"rex_effect_filter_blur_repeats\":\"\",\"rex_effect_filter_blur_type\":\"\",\"rex_effect_filter_blur_smoothit\":\"\"},\"rex_effect_filter_colorize\":{\"rex_effect_filter_colorize_filter_r\":\"\",\"rex_effect_filter_colorize_filter_g\":\"\",\"rex_effect_filter_colorize_filter_b\":\"\"},\"rex_effect_filter_sharpen\":{\"rex_effect_filter_sharpen_amount\":\"\",\"rex_effect_filter_sharpen_radius\":\"\",\"rex_effect_filter_sharpen_threshold\":\"\"},\"rex_effect_flip\":{\"rex_effect_flip_flip\":\"X\"},\"rex_effect_header\":{\"rex_effect_header_download\":\"open_media\",\"rex_effect_header_cache\":\"no_cache\"},\"rex_effect_insert_image\":{\"rex_effect_insert_image_brandimage\":\"\",\"rex_effect_insert_image_hpos\":\"left\",\"rex_effect_insert_image_vpos\":\"top\",\"rex_effect_insert_image_padding_x\":\"\",\"rex_effect_insert_image_padding_y\":\"\"},\"rex_effect_mediapath\":{\"rex_effect_mediapath_mediapath\":\"\"},\"rex_effect_mirror\":{\"rex_effect_mirror_height\":\"\",\"rex_effect_mirror_set_transparent\":\"colored\",\"rex_effect_mirror_bg_r\":\"\",\"rex_effect_mirror_bg_g\":\"\",\"rex_effect_mirror_bg_b\":\"\"},\"rex_effect_resize\":{\"rex_effect_resize_width\":\"768\",\"rex_effect_resize_height\":\"768\",\"rex_effect_resize_style\":\"minimum\",\"rex_effect_resize_allow_enlarge\":\"enlarge\"},\"rex_effect_rounded_corners\":{\"rex_effect_rounded_corners_topleft\":\"\",\"rex_effect_rounded_corners_topright\":\"\",\"rex_effect_rounded_corners_bottomleft\":\"\",\"rex_effect_rounded_corners_bottomright\":\"\"},\"rex_effect_workspace\":{\"rex_effect_workspace_width\":\"\",\"rex_effect_workspace_height\":\"\",\"rex_effect_workspace_hpos\":\"left\",\"rex_effect_workspace_vpos\":\"top\",\"rex_effect_workspace_set_transparent\":\"colored\",\"rex_effect_workspace_bg_r\":\"\",\"rex_effect_workspace_bg_g\":\"\",\"rex_effect_workspace_bg_b\":\"\"}}', 1, ". time() .", 'd2u_machinery'),
		(". $last_id_d2u_machinery_list_tile .", 'workspace', '{\"rex_effect_convert2img\":{\"rex_effect_convert2img_convert_to\":\"jpg\",\"rex_effect_convert2img_density\":\"100\"},\"rex_effect_crop\":{\"rex_effect_crop_width\":\"\",\"rex_effect_crop_height\":\"\",\"rex_effect_crop_offset_width\":\"\",\"rex_effect_crop_offset_height\":\"\",\"rex_effect_crop_hpos\":\"left\",\"rex_effect_crop_vpos\":\"top\"},\"rex_effect_filter_blur\":{\"rex_effect_filter_blur_repeats\":\"\",\"rex_effect_filter_blur_type\":\"\",\"rex_effect_filter_blur_smoothit\":\"\"},\"rex_effect_filter_colorize\":{\"rex_effect_filter_colorize_filter_r\":\"\",\"rex_effect_filter_colorize_filter_g\":\"\",\"rex_effect_filter_colorize_filter_b\":\"\"},\"rex_effect_filter_sharpen\":{\"rex_effect_filter_sharpen_amount\":\"\",\"rex_effect_filter_sharpen_radius\":\"\",\"rex_effect_filter_sharpen_threshold\":\"\"},\"rex_effect_flip\":{\"rex_effect_flip_flip\":\"X\"},\"rex_effect_header\":{\"rex_effect_header_download\":\"open_media\",\"rex_effect_header_cache\":\"no_cache\"},\"rex_effect_insert_image\":{\"rex_effect_insert_image_brandimage\":\"\",\"rex_effect_insert_image_hpos\":\"left\",\"rex_effect_insert_image_vpos\":\"top\",\"rex_effect_insert_image_padding_x\":\"\",\"rex_effect_insert_image_padding_y\":\"\"},\"rex_effect_mediapath\":{\"rex_effect_mediapath_mediapath\":\"\"},\"rex_effect_mirror\":{\"rex_effect_mirror_height\":\"\",\"rex_effect_mirror_set_transparent\":\"colored\",\"rex_effect_mirror_bg_r\":\"\",\"rex_effect_mirror_bg_g\":\"\",\"rex_effect_mirror_bg_b\":\"\"},\"rex_effect_resize\":{\"rex_effect_resize_width\":\"\",\"rex_effect_resize_height\":\"\",\"rex_effect_resize_style\":\"maximum\",\"rex_effect_resize_allow_enlarge\":\"enlarge\"},\"rex_effect_rounded_corners\":{\"rex_effect_rounded_corners_topleft\":\"\",\"rex_effect_rounded_corners_topright\":\"\",\"rex_effect_rounded_corners_bottomleft\":\"\",\"rex_effect_rounded_corners_bottomright\":\"\"},\"rex_effect_workspace\":{\"rex_effect_workspace_width\":\"768\",\"rex_effect_workspace_height\":\"768\",\"rex_effect_workspace_hpos\":\"center\",\"rex_effect_workspace_vpos\":\"middle\",\"rex_effect_workspace_set_transparent\":\"colored\",\"rex_effect_workspace_bg_r\":\"255\",\"rex_effect_workspace_bg_g\":\"255\",\"rex_effect_workspace_bg_b\":\"255\"}}', 2, ". time() .", 'd2u_machinery');");
}

// YForm e-mail template
$sql->setQuery("SELECT * FROM ". rex::getTablePrefix() ."yform_email_template WHERE name = 'machine_request'");
if($sql->getRows() == 0) {
	$sql->setQuery("INSERT INTO ". rex::getTablePrefix() ."yform_email_template (`name`, `mail_from`, `mail_from_name`, `subject`, `body`) VALUES
		('d2u_machinery_machine_request', '###email###', '###name###', 'Maschinenanfrage', 'Maschinenanfrage von Internetseite:\r\nMaschine: ###maschinenname###\r\n\r\nEs fragt an:\r\nName: ###name###\r\nFirma: ###company###\r\nAnschrift: ###anschrift###\r\nPLZ/Ort: ###zip### ###city###\r\nLand: ###country###\r\nTelefon: ###phone###\r\nEmail: ###email###\r\n\r\nNachricht: ###message###\r\n')");
}

// Standard settings
if (!$this->hasConfig()) {
	// Find first Redaxo lang an set later as default lang
	$langs = rex_clang::getAll();
	$default_clang_id = 1;
	foreach ($langs as $lang) {
		$default_clang_id = $lang->getId();
		break;
	}
	
    $this->setConfig('article_id', rex_article::getSiteStartArticleId());
	$this->setConfig('default_lang', $default_clang_id);
	$this->setConfig('show_categories_usage_area', 'hide');
}