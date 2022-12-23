<?php
\rex_sql_table::get(\rex::getTable('d2u_machinery_machines'))
	->ensureColumn(new rex_sql_column('machine_id', 'INT(11) unsigned', false, null, 'auto_increment'))
	->setPrimaryKey('machine_id')
	->ensureColumn(new \rex_sql_column('priority', 'INT(11)', true))
    ->ensureColumn(new \rex_sql_column('name', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('pics', 'TEXT', true))
    ->ensureColumn(new \rex_sql_column('category_id', 'INT(11)', true))
    ->ensureColumn(new \rex_sql_column('alternative_machine_ids', 'TEXT', true))
    ->ensureColumn(new \rex_sql_column('product_number', 'VARCHAR(50)', true))
    ->ensureColumn(new \rex_sql_column('article_id_software', 'INT(11)', true))
    ->ensureColumn(new \rex_sql_column('article_id_service', 'INT(11)', true))
    ->ensureColumn(new \rex_sql_column('article_ids_references', 'VARCHAR(255)', true))
    ->ensureColumn(new \rex_sql_column('online_status', 'VARCHAR(10)', true))
    ->ensureColumn(new \rex_sql_column('engine_power', 'VARCHAR(50)', true))
    ->ensureColumn(new \rex_sql_column('engine_power_frequency_controlled', 'VARCHAR(10)', true))
    ->ensureColumn(new \rex_sql_column('length', 'INT(10)', true))
    ->ensureColumn(new \rex_sql_column('width', 'INT(10)', true))
    ->ensureColumn(new \rex_sql_column('height', 'INT(10)', true))
    ->ensureColumn(new \rex_sql_column('depth', 'INT(10)', true))
    ->ensureColumn(new \rex_sql_column('weight', 'VARCHAR(50)', true))
    ->ensureColumn(new \rex_sql_column('operating_voltage_v', 'VARCHAR(10)', true))
    ->ensureColumn(new \rex_sql_column('operating_voltage_hz', 'VARCHAR(10)', true))
    ->ensureColumn(new \rex_sql_column('operating_voltage_a', 'VARCHAR(10)', true))
    ->ensureColumn(new \rex_sql_column('video_ids', 'VARCHAR(255)', true))
    ->ensure();
\rex_sql_table::get(
    \rex::getTable('d2u_machinery_machines'))
    ->removeColumn('internal_name')
    ->ensure();
\rex_sql_table::get(\rex::getTable('d2u_machinery_machines_lang'))
	->ensureColumn(new rex_sql_column('machine_id', 'INT(11)', false))
    ->ensureColumn(new \rex_sql_column('clang_id', 'INT(11)', false))
	->setPrimaryKey(['machine_id', 'clang_id'])
    ->ensureColumn(new \rex_sql_column('lang_name', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('teaser', 'VARCHAR(255)', true))
    ->ensureColumn(new \rex_sql_column('description', 'TEXT', true))
    ->ensureColumn(new \rex_sql_column('benefits_short', 'TEXT', true))
    ->ensureColumn(new \rex_sql_column('benefits_long', 'TEXT', true))
    ->ensureColumn(new \rex_sql_column('leaflet', 'VARCHAR(255)', true))
    ->ensureColumn(new \rex_sql_column('pdfs', 'TEXT', true))
    ->ensureColumn(new \rex_sql_column('translation_needs_update', 'VARCHAR(7)', true))
    ->ensureColumn(new \rex_sql_column('updatedate', 'DATETIME', true))
    ->ensureColumn(new \rex_sql_column('updateuser', 'VARCHAR(255)', true))
    ->ensure();

\rex_sql_table::get(\rex::getTable('d2u_machinery_categories'))
	->ensureColumn(new rex_sql_column('category_id', 'INT(11) unsigned', false, null, 'auto_increment'))
	->setPrimaryKey('category_id')
	->ensureColumn(new \rex_sql_column('priority', 'INT(11)', true))
    ->ensureColumn(new \rex_sql_column('parent_category_id', 'INT(11)', true))
    ->ensureColumn(new \rex_sql_column('name', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('pic', 'VARCHAR(255)', true))
    ->ensureColumn(new \rex_sql_column('pic_usage', 'VARCHAR(255)', true))
    ->ensureColumn(new \rex_sql_column('video_ids', 'TEXT', true))
    ->ensure();
\rex_sql_table::get(\rex::getTable('d2u_machinery_categories_lang'))
	->ensureColumn(new rex_sql_column('category_id', 'INT(11)', false))
    ->ensureColumn(new \rex_sql_column('clang_id', 'INT(11)', false))
	->setPrimaryKey(['category_id', 'clang_id'])
    ->ensureColumn(new \rex_sql_column('name', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('teaser', 'VARCHAR(255)', true))
    ->ensureColumn(new \rex_sql_column('description', 'TEXT', true))
    ->ensureColumn(new \rex_sql_column('usage_area', 'VARCHAR(255)', true))
    ->ensureColumn(new \rex_sql_column('pic_lang', 'VARCHAR(255)', true))
    ->ensureColumn(new \rex_sql_column('pdfs', 'TEXT', true))
    ->ensureColumn(new \rex_sql_column('translation_needs_update', 'VARCHAR(7)', true))
    ->ensureColumn(new \rex_sql_column('updatedate', 'DATETIME', true))
    ->ensureColumn(new \rex_sql_column('updateuser', 'VARCHAR(255)', true))
    ->ensure();


// Create views for url addon
$sql = \rex_sql::factory();
$sql->setQuery('CREATE OR REPLACE VIEW '. \rex::getTablePrefix() .'d2u_machinery_url_machines AS
	SELECT lang.machine_id, lang.clang_id, IF(lang.lang_name IS NULL or lang.lang_name = "", machines.name, lang.lang_name) as name, CONCAT(IF(lang.lang_name IS NULL or lang.lang_name = "", machines.name, lang.lang_name), " - ", categories.name) AS seo_title, lang.teaser AS seo_description, SUBSTRING_INDEX(machines.pics, ",", 1) as picture, machines.category_id, lang.updatedate
	FROM '. \rex::getTablePrefix() .'d2u_machinery_machines_lang AS lang
	LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_machines AS machines ON lang.machine_id = machines.machine_id
	LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_categories_lang AS categories ON machines.category_id = categories.category_id AND lang.clang_id = categories.clang_id
	LEFT JOIN '. \rex::getTablePrefix() .'clang AS clang ON lang.clang_id = clang.id
	WHERE clang.status = 1 AND machines.online_status = "online"');
$sql->setQuery('CREATE OR REPLACE VIEW '. \rex::getTablePrefix() .'d2u_machinery_url_machine_categories AS
	SELECT machines.category_id, categories_lang.clang_id, CONCAT_WS(" - ", parent_categories.name, categories_lang.name) AS name, CONCAT_WS(" - ", categories_lang.name, parent_categories.name) AS seo_title, categories_lang.teaser AS seo_description, IF(categories_lang.pic_lang IS NULL or categories_lang.pic_lang = "", categories.pic, categories_lang.pic_lang) as picture, categories_lang.updatedate
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
	$clang_id = count(rex_clang::getAllIds()) === 1 ? rex_clang::getStartId() : 0;
	$article_id = rex_config::get('d2u_machinery', 'article_id', rex_article::getSiteStartArticleId()); 

	// Insert url schemes Version 2.x
	$sql->setQuery("DELETE FROM ". \rex::getTablePrefix() ."url_generator_profile WHERE `namespace` = 'machine_id';");
	$sql->setQuery("INSERT INTO ". \rex::getTablePrefix() ."url_generator_profile (`namespace`, `article_id`, `clang_id`, `table_name`, `table_parameters`, `relation_1_table_name`, `relation_1_table_parameters`, `relation_2_table_name`, `relation_2_table_parameters`, `relation_3_table_name`, `relation_3_table_parameters`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
		('machine_id', "
		. $article_id .", "
		. $clang_id .", "
		. "'1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_machines', "
		. "'{\"column_id\":\"machine_id\",\"column_clang_id\":\"clang_id\",\"restriction_1_column\":\"\",\"restriction_1_comparison_operator\":\"=\",\"restriction_1_value\":\"\",\"restriction_2_logical_operator\":\"\",\"restriction_2_column\":\"\",\"restriction_2_comparison_operator\":\"=\",\"restriction_2_value\":\"\",\"restriction_3_logical_operator\":\"\",\"restriction_3_column\":\"\",\"restriction_3_comparison_operator\":\"=\",\"restriction_3_value\":\"\",\"column_segment_part_1\":\"name\",\"column_segment_part_2_separator\":\"\\/\",\"column_segment_part_2\":\"\",\"column_segment_part_3_separator\":\"\\/\",\"column_segment_part_3\":\"\",\"relation_1_column\":\"category_id\",\"relation_1_position\":\"BEFORE\",\"relation_2_column\":\"\",\"relation_2_position\":\"BEFORE\",\"relation_3_column\":\"\",\"relation_3_position\":\"BEFORE\",\"append_user_paths\":\"\",\"append_structure_categories\":\"0\",\"column_seo_title\":\"seo_title\",\"column_seo_description\":\"seo_description\",\"column_seo_image\":\"picture\",\"sitemap_add\":\"1\",\"sitemap_frequency\":\"weekly\",\"sitemap_priority\":\"1.0\",\"column_sitemap_lastmod\":\"updatedate\"}', "
		. "'relation_1_xxx_1_xxx_". rex::getTablePrefix() ."d2u_machinery_categories_lang', "
		. "'{\"column_id\":\"category_id\",\"column_clang_id\":\"clang_id\",\"column_segment_part_1\":\"name\",\"column_segment_part_2_separator\":\"\\/\",\"column_segment_part_2\":\"\",\"column_segment_part_3_separator\":\"\\/\",\"column_segment_part_3\":\"\"}', "
		. "'', '[]', '', '[]', CURRENT_TIMESTAMP, '". (rex::getUser() instanceof rex_user ? rex::getUser()->getValue('login') : '') ."', CURRENT_TIMESTAMP, '". (rex::getUser() instanceof rex_user ? rex::getUser()->getValue('login') : '') ."');");
	$sql->setQuery("DELETE FROM ". \rex::getTablePrefix() ."url_generator_profile WHERE `namespace` = 'category_id';");
	$sql->setQuery("INSERT INTO ". \rex::getTablePrefix() ."url_generator_profile (`namespace`, `article_id`, `clang_id`, `table_name`, `table_parameters`, `relation_1_table_name`, `relation_1_table_parameters`, `relation_2_table_name`, `relation_2_table_parameters`, `relation_3_table_name`, `relation_3_table_parameters`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
		('category_id', "
		. $article_id .", "
		. $clang_id .", "
		. "'1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_machine_categories', "
		. "'{\"column_id\":\"category_id\",\"column_clang_id\":\"clang_id\",\"restriction_1_column\":\"\",\"restriction_1_comparison_operator\":\"=\",\"restriction_1_value\":\"\",\"restriction_2_logical_operator\":\"\",\"restriction_2_column\":\"\",\"restriction_2_comparison_operator\":\"=\",\"restriction_2_value\":\"\",\"restriction_3_logical_operator\":\"\",\"restriction_3_column\":\"\",\"restriction_3_comparison_operator\":\"=\",\"restriction_3_value\":\"\",\"column_segment_part_1\":\"name\",\"column_segment_part_2_separator\":\"\\/\",\"column_segment_part_2\":\"\",\"column_segment_part_3_separator\":\"\\/\",\"column_segment_part_3\":\"\",\"relation_1_column\":\"\",\"relation_1_position\":\"BEFORE\",\"relation_2_column\":\"\",\"relation_2_position\":\"BEFORE\",\"relation_3_column\":\"\",\"relation_3_position\":\"BEFORE\",\"append_user_paths\":\"\",\"append_structure_categories\":\"0\",\"column_seo_title\":\"seo_title\",\"column_seo_description\":\"seo_description\",\"column_seo_image\":\"picture\",\"sitemap_add\":\"1\",\"sitemap_frequency\":\"monthly\",\"sitemap_priority\":\"0.7\",\"column_sitemap_lastmod\":\"updatedate\"}', "
		. "'', '[]', '', '[]', '', '[]', CURRENT_TIMESTAMP, '". (rex::getUser() instanceof rex_user ? rex::getUser()->getValue('login') : '') ."', CURRENT_TIMESTAMP, '". (rex::getUser() instanceof rex_user ? rex::getUser()->getValue('login') : '') ."');");

	\d2u_addon_backend_helper::generateUrlCache();
}


// Insert frontend translations
if(!class_exists('d2u_machinery_lang_helper')) {
	// Load class in case addon is deactivated
	require_once 'lib/d2u_machinery_lang_helper.php';
}
d2u_machinery_lang_helper::factory()->install();


// Media Manager media types
$sql->setQuery("SELECT * FROM ". \rex::getTablePrefix() ."media_manager_type WHERE name = 'd2u_machinery_list_tile'");
if(intval($sql->getRows()) === 0) {
	$sql->setQuery("INSERT INTO ". \rex::getTablePrefix() ."media_manager_type (`status`, `name`, `description`) VALUES
		(0, 'd2u_machinery_list_tile', 'D2U Machinery Addon list tile pictures');");
	$last_id_d2u_machinery_list_tile = $sql->getLastId();
	$sql->setQuery("INSERT INTO ". \rex::getTablePrefix() ."media_manager_type_effect (`type_id`, `effect`, `parameters`, `priority`, `createdate`, `createuser`) VALUES
		(". $last_id_d2u_machinery_list_tile .", 'resize', '{\"rex_effect_convert2img\":{\"rex_effect_convert2img_convert_to\":\"jpg\",\"rex_effect_convert2img_density\":\"100\"},\"rex_effect_crop\":{\"rex_effect_crop_width\":\"\",\"rex_effect_crop_height\":\"\",\"rex_effect_crop_offset_width\":\"\",\"rex_effect_crop_offset_height\":\"\",\"rex_effect_crop_hpos\":\"left\",\"rex_effect_crop_vpos\":\"top\"},\"rex_effect_filter_blur\":{\"rex_effect_filter_blur_repeats\":\"\",\"rex_effect_filter_blur_type\":\"\",\"rex_effect_filter_blur_smoothit\":\"\"},\"rex_effect_filter_colorize\":{\"rex_effect_filter_colorize_filter_r\":\"\",\"rex_effect_filter_colorize_filter_g\":\"\",\"rex_effect_filter_colorize_filter_b\":\"\"},\"rex_effect_filter_sharpen\":{\"rex_effect_filter_sharpen_amount\":\"\",\"rex_effect_filter_sharpen_radius\":\"\",\"rex_effect_filter_sharpen_threshold\":\"\"},\"rex_effect_flip\":{\"rex_effect_flip_flip\":\"X\"},\"rex_effect_header\":{\"rex_effect_header_download\":\"open_media\",\"rex_effect_header_cache\":\"no_cache\"},\"rex_effect_insert_image\":{\"rex_effect_insert_image_brandimage\":\"\",\"rex_effect_insert_image_hpos\":\"left\",\"rex_effect_insert_image_vpos\":\"top\",\"rex_effect_insert_image_padding_x\":\"\",\"rex_effect_insert_image_padding_y\":\"\"},\"rex_effect_mediapath\":{\"rex_effect_mediapath_mediapath\":\"\"},\"rex_effect_mirror\":{\"rex_effect_mirror_height\":\"\",\"rex_effect_mirror_set_transparent\":\"colored\",\"rex_effect_mirror_bg_r\":\"\",\"rex_effect_mirror_bg_g\":\"\",\"rex_effect_mirror_bg_b\":\"\"},\"rex_effect_resize\":{\"rex_effect_resize_width\":\"768\",\"rex_effect_resize_height\":\"768\",\"rex_effect_resize_style\":\"minimum\",\"rex_effect_resize_allow_enlarge\":\"enlarge\"},\"rex_effect_rounded_corners\":{\"rex_effect_rounded_corners_topleft\":\"\",\"rex_effect_rounded_corners_topright\":\"\",\"rex_effect_rounded_corners_bottomleft\":\"\",\"rex_effect_rounded_corners_bottomright\":\"\"},\"rex_effect_workspace\":{\"rex_effect_workspace_width\":\"\",\"rex_effect_workspace_height\":\"\",\"rex_effect_workspace_hpos\":\"left\",\"rex_effect_workspace_vpos\":\"top\",\"rex_effect_workspace_set_transparent\":\"colored\",\"rex_effect_workspace_bg_r\":\"\",\"rex_effect_workspace_bg_g\":\"\",\"rex_effect_workspace_bg_b\":\"\"}}', 1, CURRENT_TIMESTAMP, 'd2u_machinery'),
		(". $last_id_d2u_machinery_list_tile .", 'workspace', '{\"rex_effect_convert2img\":{\"rex_effect_convert2img_convert_to\":\"jpg\",\"rex_effect_convert2img_density\":\"100\"},\"rex_effect_crop\":{\"rex_effect_crop_width\":\"\",\"rex_effect_crop_height\":\"\",\"rex_effect_crop_offset_width\":\"\",\"rex_effect_crop_offset_height\":\"\",\"rex_effect_crop_hpos\":\"left\",\"rex_effect_crop_vpos\":\"top\"},\"rex_effect_filter_blur\":{\"rex_effect_filter_blur_repeats\":\"\",\"rex_effect_filter_blur_type\":\"\",\"rex_effect_filter_blur_smoothit\":\"\"},\"rex_effect_filter_colorize\":{\"rex_effect_filter_colorize_filter_r\":\"\",\"rex_effect_filter_colorize_filter_g\":\"\",\"rex_effect_filter_colorize_filter_b\":\"\"},\"rex_effect_filter_sharpen\":{\"rex_effect_filter_sharpen_amount\":\"\",\"rex_effect_filter_sharpen_radius\":\"\",\"rex_effect_filter_sharpen_threshold\":\"\"},\"rex_effect_flip\":{\"rex_effect_flip_flip\":\"X\"},\"rex_effect_header\":{\"rex_effect_header_download\":\"open_media\",\"rex_effect_header_cache\":\"no_cache\"},\"rex_effect_insert_image\":{\"rex_effect_insert_image_brandimage\":\"\",\"rex_effect_insert_image_hpos\":\"left\",\"rex_effect_insert_image_vpos\":\"top\",\"rex_effect_insert_image_padding_x\":\"\",\"rex_effect_insert_image_padding_y\":\"\"},\"rex_effect_mediapath\":{\"rex_effect_mediapath_mediapath\":\"\"},\"rex_effect_mirror\":{\"rex_effect_mirror_height\":\"\",\"rex_effect_mirror_set_transparent\":\"colored\",\"rex_effect_mirror_bg_r\":\"\",\"rex_effect_mirror_bg_g\":\"\",\"rex_effect_mirror_bg_b\":\"\"},\"rex_effect_resize\":{\"rex_effect_resize_width\":\"\",\"rex_effect_resize_height\":\"\",\"rex_effect_resize_style\":\"maximum\",\"rex_effect_resize_allow_enlarge\":\"enlarge\"},\"rex_effect_rounded_corners\":{\"rex_effect_rounded_corners_topleft\":\"\",\"rex_effect_rounded_corners_topright\":\"\",\"rex_effect_rounded_corners_bottomleft\":\"\",\"rex_effect_rounded_corners_bottomright\":\"\"},\"rex_effect_workspace\":{\"rex_effect_workspace_width\":\"768\",\"rex_effect_workspace_height\":\"768\",\"rex_effect_workspace_hpos\":\"center\",\"rex_effect_workspace_vpos\":\"middle\",\"rex_effect_workspace_set_transparent\":\"colored\",\"rex_effect_workspace_bg_r\":\"255\",\"rex_effect_workspace_bg_g\":\"255\",\"rex_effect_workspace_bg_b\":\"255\"}}', 2, CURRENT_TIMESTAMP, 'd2u_machinery');");
}
$sql->setQuery("SELECT * FROM ". \rex::getTablePrefix() ."media_manager_type WHERE name = 'd2u_machinery_features'");
if(intval($sql->getRows()) === 0) {
	$sql->setQuery("INSERT INTO ". \rex::getTablePrefix() ."media_manager_type (`status`, `name`, `description`) VALUES
		(0, 'd2u_machinery_features', 'D2U Machinery Addon Feature, Zubehör, ... Bilder');");
	$last_id_d2u_machinery_list_tile = $sql->getLastId();
	$sql->setQuery("INSERT INTO ". \rex::getTablePrefix() ."media_manager_type_effect (`type_id`, `effect`, `parameters`, `priority`, `createdate`, `createuser`) VALUES
		(". $last_id_d2u_machinery_list_tile .", 'resize', '{\"rex_effect_convert2img\":{\"rex_effect_convert2img_convert_to\":\"jpg\",\"rex_effect_convert2img_density\":\"100\"},\"rex_effect_crop\":{\"rex_effect_crop_width\":\"\",\"rex_effect_crop_height\":\"\",\"rex_effect_crop_offset_width\":\"\",\"rex_effect_crop_offset_height\":\"\",\"rex_effect_crop_hpos\":\"left\",\"rex_effect_crop_vpos\":\"top\"},\"rex_effect_filter_blur\":{\"rex_effect_filter_blur_repeats\":\"\",\"rex_effect_filter_blur_type\":\"\",\"rex_effect_filter_blur_smoothit\":\"\"},\"rex_effect_filter_colorize\":{\"rex_effect_filter_colorize_filter_r\":\"\",\"rex_effect_filter_colorize_filter_g\":\"\",\"rex_effect_filter_colorize_filter_b\":\"\"},\"rex_effect_filter_sharpen\":{\"rex_effect_filter_sharpen_amount\":\"\",\"rex_effect_filter_sharpen_radius\":\"\",\"rex_effect_filter_sharpen_threshold\":\"\"},\"rex_effect_flip\":{\"rex_effect_flip_flip\":\"X\"},\"rex_effect_header\":{\"rex_effect_header_download\":\"open_media\",\"rex_effect_header_cache\":\"no_cache\"},\"rex_effect_insert_image\":{\"rex_effect_insert_image_brandimage\":\"\",\"rex_effect_insert_image_hpos\":\"left\",\"rex_effect_insert_image_vpos\":\"top\",\"rex_effect_insert_image_padding_x\":\"\",\"rex_effect_insert_image_padding_y\":\"\"},\"rex_effect_mediapath\":{\"rex_effect_mediapath_mediapath\":\"\"},\"rex_effect_mirror\":{\"rex_effect_mirror_height\":\"\",\"rex_effect_mirror_set_transparent\":\"colored\",\"rex_effect_mirror_bg_r\":\"\",\"rex_effect_mirror_bg_g\":\"\",\"rex_effect_mirror_bg_b\":\"\"},\"rex_effect_resize\":{\"rex_effect_resize_width\":\"768\",\"rex_effect_resize_height\":\"768\",\"rex_effect_resize_style\":\"minimum\",\"rex_effect_resize_allow_enlarge\":\"enlarge\"},\"rex_effect_rounded_corners\":{\"rex_effect_rounded_corners_topleft\":\"\",\"rex_effect_rounded_corners_topright\":\"\",\"rex_effect_rounded_corners_bottomleft\":\"\",\"rex_effect_rounded_corners_bottomright\":\"\"},\"rex_effect_workspace\":{\"rex_effect_workspace_width\":\"\",\"rex_effect_workspace_height\":\"\",\"rex_effect_workspace_hpos\":\"left\",\"rex_effect_workspace_vpos\":\"top\",\"rex_effect_workspace_set_transparent\":\"colored\",\"rex_effect_workspace_bg_r\":\"\",\"rex_effect_workspace_bg_g\":\"\",\"rex_effect_workspace_bg_b\":\"\"}}', 1, CURRENT_TIMESTAMP, 'd2u_machinery');");
}


// YForm e-mail-template
$sql->setQuery("SELECT * FROM ". \rex::getTablePrefix() ."yform_email_template WHERE name = 'd2u_machinery_machine_request'");
if(intval($sql->getRows()) === 0) {
	$sql->setQuery("INSERT INTO ". \rex::getTablePrefix() ."yform_email_template (`name`, `mail_from`, `mail_from_name`, `mail_reply_to`, `mail_reply_to_name`, `subject`, `body`, `body_html`, `attachments`) VALUES
		('d2u_machinery_machine_request', '', '', 'REX_YFORM_DATA[field=\"email\"]', 'REX_YFORM_DATA[field=\"vorname\"] REX_YFORM_DATA[field=\"name\"]', 'Maschinenanfrage', 'Maschinenanfrage von Internetseite:\r\nMaschine: REX_YFORM_DATA[field=\"machine_name\"]\r\n\r\nEs fragt an:\r\nVorname: REX_YFORM_DATA[field=\"vorname\"]\r\nName: REX_YFORM_DATA[field=\"name\"]\r\nFirma: REX_YFORM_DATA[field=\"company\"]\r\nAnschrift: REX_YFORM_DATA[field=\"address\"]\r\nPLZ/Ort: REX_YFORM_DATA[field=\"zip\"] REX_YFORM_DATA[field=\"city\"]\r\nLand: REX_YFORM_DATA[field=\"country\"]\r\nTelefon: REX_YFORM_DATA[field=\"phone\"]\r\nEmail: REX_YFORM_DATA[field=\"email\"]\r\nBitte um Rückruf: <?php print REX_YFORM_DATA[field=\"please_call\"] == 1 ? \"Ja\" : \"Nein\"; ?>\r\n\r\nNachricht: REX_YFORM_DATA[field=\"message\"]\r\n', '', '')");
}
else if (rex_version::compare($this->getVersion(), '1.3.1', '<')) { /** @phpstan-ignore-line */
	$sql->setQuery("UPDATE ". \rex::getTablePrefix() ."yform_email_template SET `mail_from` = '', `mail_from_name` = '', `mail_reply_to` = 'REX_YFORM_DATA[field=\"email\"]', `mail_reply_to_name` = 'REX_YFORM_DATA[field=\"vorname\"] REX_YFORM_DATA[field=\"name\"]' WHERE name = 'd2u_machinery_machine_request';");
}


// Permission rename
$sql->setQuery("SELECT * FROM ". \rex::getTablePrefix() ."user_role WHERE INSTR(perms, 'd2u_machinery[edit_tech_data]') > 0");
if(intval($sql->getRows()) === 0) {
	$sql->setQuery("UPDATE ". \rex::getTablePrefix() ."user_role SET perms = REPLACE(perms, 'd2u_machinery[edit_tech_data]', 'd2u_machinery[edit_data]') WHERE INSTR(perms, 'd2u_machinery[edit_tech_data]') > 0;");
}


// Standard settings
if (!$this->hasConfig()) { /** @phpstan-ignore-line */
    $this->setConfig('article_id', rex_article::getSiteStartArticleId()); /** @phpstan-ignore-line */
}
// Remove default lang setting
if(!$this->hasConfig()) { /** @phpstan-ignore-line */
	$this->removeConfig('default_lang'); /** @phpstan-ignore-line */
}
// Remove unused config: show usage areas in navi (never implemented)
if($this->hasConfig('show_categories_usage_area')) { /** @phpstan-ignore-line */
	$this->removeConfig('show_categories_usage_area'); /** @phpstan-ignore-line */
}

// Rename config (config was splittet in 1.2.2)
if($this->getConfig('show_usage_areas', 'hide') == 'show') { /** @phpstan-ignore-line */
	$this->setConfig('show_categories_usage_areas', 'show'); /** @phpstan-ignore-line */
	$this->removeConfig('show_usage_areas'); /** @phpstan-ignore-line */
}


// Update modules
if(class_exists("D2UModuleManager")) {
	$modules = [];
	$modules[] = new D2UModule("90-1",
		"D2U Machinery Addon - Hauptausgabe",
		16);
	if(rex_plugin::get('d2u_machinery', 'industry_sectors')->isAvailable()) {
		$modules[] = new D2UModule("90-2",
			"D2U Machinery Addon - Branchen",
			1);
	}
	$modules[] = new D2UModule("90-3",
		"D2U Machinery Addon - Kategorien",
		6);
	if(rex_plugin::get('d2u_machinery', 'used_machines')->isAvailable()) {
		$modules[] = new D2UModule("90-4",
			"D2U Machinery Addon - Gebrauchtmaschinen",
			18);
	}
	$modules[] = new D2UModule("90-5",
		"D2U Machinery Addon - Box Beratungshinweis",
		1);
	if(rex_plugin::get('d2u_machinery', 'used_machines')->isAvailable()) {
		$modules[] = new D2UModule("90-6",
			"D2U Machinery Addon - Gebrauchtmaschinen Topangebote",
			11);
	}
	$d2u_module_manager = new D2UModuleManager($modules, "", "d2u_machinery");
	$d2u_module_manager->autoupdate();
}