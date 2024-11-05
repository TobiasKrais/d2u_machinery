<?php

// Create standard configuration
$d2u_machinery = rex_addon::get('d2u_machinery');
if (!$d2u_machinery->hasConfig('used_machine_article_id_rent')) {
    $d2u_machinery->setConfig('used_machine_article_id_rent', rex_article::getSiteStartArticleId());
}
if (!$d2u_machinery->hasConfig('used_machine_article_id_sale')) {
    $d2u_machinery->setConfig('used_machine_article_id_sale', rex_article::getSiteStartArticleId());
}

// Create database
\rex_sql_table::get(\rex::getTable('d2u_machinery_used_machines'))
    ->ensureColumn(new rex_sql_column('used_machine_id', 'INT(10) unsigned', false, null, 'auto_increment'))
    ->setPrimaryKey('used_machine_id')
    ->ensureColumn(new \rex_sql_column('manufacturer', 'VARCHAR(191)', true))
    ->ensureColumn(new \rex_sql_column('name', 'VARCHAR(191)', true))
    ->ensureColumn(new \rex_sql_column('category_id', 'INT(10)', true))
    ->ensureColumn(new \rex_sql_column('contact_id', 'INT(10)', true))
    ->ensureColumn(new \rex_sql_column('availability', 'VARCHAR(10)', true))
    ->ensureColumn(new \rex_sql_column('product_number', 'VARCHAR(191)', true))
    ->ensureColumn(new \rex_sql_column('offer_type', 'VARCHAR(10)', true))
    ->ensureColumn(new \rex_sql_column('year_built', 'INT(4)', true))
    ->ensureColumn(new \rex_sql_column('price', 'DECIMAL(10,2)', true))
    ->ensureColumn(new \rex_sql_column('currency_code', 'VARCHAR(3)', true))
    ->ensureColumn(new \rex_sql_column('vat', 'INT(2)', true))
    ->ensureColumn(new \rex_sql_column('online_status', 'VARCHAR(10)', true))
    ->ensureColumn(new \rex_sql_column('pics', 'TEXT', true))
    ->ensureColumn(new \rex_sql_column('machine_id', 'INT(10)', true))
    ->ensureColumn(new \rex_sql_column('location', 'VARCHAR(191)', true))
    ->ensureColumn(new \rex_sql_column('external_url', 'VARCHAR(191)', true))
    ->ensureColumn(new \rex_sql_column('video_ids', 'VARCHAR(191)', true))
    ->ensure();

\rex_sql_table::get(\rex::getTable('d2u_machinery_used_machines_lang'))
    ->ensureColumn(new rex_sql_column('used_machine_id', 'INT(10) unsigned', false, null, 'auto_increment'))
    ->ensureColumn(new \rex_sql_column('clang_id', 'INT(11)', false))
    ->setPrimaryKey(['used_machine_id', 'clang_id'])
    ->ensureColumn(new \rex_sql_column('teaser', 'TEXT', true))
    ->ensureColumn(new \rex_sql_column('description', 'TEXT', true))
    ->ensureColumn(new \rex_sql_column('downloads', 'TEXT', true))
    ->ensureColumn(new \rex_sql_column('translation_needs_update', 'VARCHAR(7)', true))
    ->ensureColumn(new \rex_sql_column('updatedate', 'DATETIME', true))
    ->ensureColumn(new \rex_sql_column('updateuser', 'VARCHAR(50)', true))
    ->ensure();

$sql = \rex_sql::factory();
// Create views for url addon
$sql->setQuery('CREATE OR REPLACE VIEW '. \rex::getTablePrefix() .'d2u_machinery_url_used_machines_rent AS
	SELECT lang.used_machine_id, lang.clang_id, CONCAT(machines.manufacturer, " ", machines.name) AS name, CONCAT(machines.manufacturer, " ", machines.name, " - ", categories.name) AS seo_title, lang.teaser AS seo_description, SUBSTRING_INDEX(machines.pics, ",", 1) as picture, machines.category_id, lang.updatedate
	FROM '. \rex::getTablePrefix() .'d2u_machinery_used_machines_lang AS lang
	LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_used_machines AS machines ON lang.used_machine_id = machines.used_machine_id
	LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_categories_lang AS categories ON machines.category_id = categories.category_id AND lang.clang_id = categories.clang_id
	LEFT JOIN '. \rex::getTablePrefix() .'clang AS clang ON lang.clang_id = clang.id
	WHERE clang.`status` = 1 AND machines.online_status = "online" AND machines.offer_type = "rent"
	GROUP BY used_machine_id, clang_id, name, seo_title, seo_description, picture, category_id, updatedate;');
$sql->setQuery('CREATE OR REPLACE VIEW '. \rex::getTablePrefix() .'d2u_machinery_url_used_machine_categories_rent AS
	SELECT machines.category_id, categories_lang.clang_id, CONCAT_WS(" - ", parent_categories.name, categories_lang.name) AS name, CONCAT_WS(" - ", categories_lang.name, parent_categories.name) AS seo_title, categories_lang.teaser AS seo_description,IF(categories_lang.pic_lang IS NULL or categories_lang.pic_lang = "", categories.pic, categories_lang.pic_lang) as picture, categories_lang.updatedate
	FROM '. \rex::getTablePrefix() .'d2u_machinery_used_machines_lang AS lang
	LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_used_machines AS machines ON lang.used_machine_id = machines.used_machine_id
	LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_categories_lang AS categories_lang ON machines.category_id = categories_lang.category_id AND lang.clang_id = categories_lang.clang_id
	LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_categories AS categories ON categories_lang.category_id = categories.category_id
	LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_categories_lang AS parent_categories ON categories.parent_category_id = parent_categories.category_id AND lang.clang_id = parent_categories.clang_id
	LEFT JOIN '. \rex::getTablePrefix() .'clang AS clang ON lang.clang_id = clang.id
	WHERE clang.`status` = 1 AND machines.online_status = "online" AND machines.offer_type = "rent"
	GROUP BY category_id, clang_id, name, seo_title, seo_description, picture, updatedate;');
$sql->setQuery('CREATE OR REPLACE VIEW '. \rex::getTablePrefix() .'d2u_machinery_url_used_machines_sale AS
	SELECT lang.used_machine_id, lang.clang_id, CONCAT(machines.manufacturer, " ", machines.name) AS name, CONCAT(machines.manufacturer, " ", machines.name, " - ", categories.name) AS seo_title, lang.teaser AS seo_description, SUBSTRING_INDEX(machines.pics, ",", 1) as picture, machines.category_id, lang.updatedate
	FROM '. \rex::getTablePrefix() .'d2u_machinery_used_machines_lang AS lang
	LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_used_machines AS machines ON lang.used_machine_id = machines.used_machine_id
	LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_categories_lang AS categories ON machines.category_id = categories.category_id AND lang.clang_id = categories.clang_id
	LEFT JOIN '. \rex::getTablePrefix() .'clang AS clang ON lang.clang_id = clang.id
	WHERE clang.`status` = 1 AND machines.online_status = "online" AND machines.offer_type = "sale"
	GROUP BY used_machine_id, clang_id, name, seo_title, seo_description, picture, category_id, updatedate;');
$sql->setQuery('CREATE OR REPLACE VIEW '. \rex::getTablePrefix() .'d2u_machinery_url_used_machine_categories_sale AS
	SELECT machines.category_id, categories_lang.clang_id, CONCAT_WS(" - ", parent_categories.name, categories_lang.name) AS name, CONCAT_WS(" - ", categories_lang.name, parent_categories.name) AS seo_title, categories_lang.teaser AS seo_description,IF(categories_lang.pic_lang IS NULL or categories_lang.pic_lang = "", categories.pic, categories_lang.pic_lang) as picture, categories_lang.updatedate
	FROM '. \rex::getTablePrefix() .'d2u_machinery_used_machines_lang AS lang
	LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_used_machines AS machines ON lang.used_machine_id = machines.used_machine_id
	LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_categories_lang AS categories_lang ON machines.category_id = categories_lang.category_id AND lang.clang_id = categories_lang.clang_id
	LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_categories AS categories ON categories_lang.category_id = categories.category_id
	LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_categories_lang AS parent_categories ON categories.parent_category_id = parent_categories.category_id AND lang.clang_id = parent_categories.clang_id
	LEFT JOIN '. \rex::getTablePrefix() .'clang AS clang ON lang.clang_id = clang.id
	WHERE clang.`status` = 1 AND machines.online_status = "online" AND machines.offer_type = "sale"
	GROUP BY category_id, clang_id, name, seo_title, seo_description, picture, updatedate;');

// Insert url schemes
if (\rex_addon::get('url')->isAvailable()) {
    $clang_id = 1 === count(rex_clang::getAllIds()) ? rex_clang::getStartId() : 0;
    $article_id_rent = rex_config::get('d2u_machinery', 'used_machine_article_id_rent', rex_article::getSiteStartArticleId());
    $article_id_sale = rex_config::get('d2u_machinery', 'used_machine_article_id_sale', rex_article::getSiteStartArticleId());

    $sql->setQuery('DELETE FROM '. \rex::getTablePrefix() ."url_generator_profile WHERE `namespace` = 'used_rent_machine_id';");
    $sql->setQuery('INSERT INTO '. \rex::getTablePrefix() ."url_generator_profile (`namespace`, `article_id`, `clang_id`, `table_name`, `table_parameters`, `relation_1_table_name`, `relation_1_table_parameters`, `relation_2_table_name`, `relation_2_table_parameters`, `relation_3_table_name`, `relation_3_table_parameters`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
		('used_rent_machine_id', "
        . $article_id_rent .', '
        . $clang_id .', '
        . "'1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines_rent', "
        . "'{\"column_id\":\"used_machine_id\",\"column_clang_id\":\"clang_id\",\"restriction_1_column\":\"\",\"restriction_1_comparison_operator\":\"=\",\"restriction_1_value\":\"\",\"restriction_2_logical_operator\":\"\",\"restriction_2_column\":\"\",\"restriction_2_comparison_operator\":\"=\",\"restriction_2_value\":\"\",\"restriction_3_logical_operator\":\"\",\"restriction_3_column\":\"\",\"restriction_3_comparison_operator\":\"=\",\"restriction_3_value\":\"\",\"column_segment_part_1\":\"name\",\"column_segment_part_2_separator\":\"\\-\",\"column_segment_part_2\":\"used_machine_id\",\"column_segment_part_3_separator\":\"\\/\",\"column_segment_part_3\":\"\",\"relation_1_column\":\"category_id\",\"relation_1_position\":\"BEFORE\",\"relation_2_column\":\"\",\"relation_2_position\":\"BEFORE\",\"relation_3_column\":\"\",\"relation_3_position\":\"BEFORE\",\"append_user_paths\":\"\",\"append_structure_categories\":\"0\",\"column_seo_title\":\"seo_title\",\"column_seo_description\":\"seo_description\",\"column_seo_image\":\"picture\",\"sitemap_add\":\"1\",\"sitemap_frequency\":\"weekly\",\"sitemap_priority\":\"1.0\",\"column_sitemap_lastmod\":\"updatedate\"}', "
        . "'relation_1_xxx_1_xxx_". rex::getTablePrefix() ."d2u_machinery_categories_lang', "
        . "'{\"column_id\":\"category_id\",\"column_clang_id\":\"clang_id\",\"column_segment_part_1\":\"name\",\"column_segment_part_2_separator\":\"\\/\",\"column_segment_part_2\":\"\",\"column_segment_part_3_separator\":\"\\/\",\"column_segment_part_3\":\"\"}', "
        . "'', '[]', '', '[]', CURRENT_TIMESTAMP, '". (rex::getUser() instanceof rex_user ? rex::getUser()->getValue('login') : '') ."', CURRENT_TIMESTAMP, '". (rex::getUser() instanceof rex_user ? rex::getUser()->getValue('login') : '') ."');");
    $sql->setQuery('DELETE FROM '. \rex::getTablePrefix() ."url_generator_profile WHERE `namespace` = 'used_rent_category_id';");
    $sql->setQuery('INSERT INTO '. \rex::getTablePrefix() ."url_generator_profile (`namespace`, `article_id`, `clang_id`, `table_name`, `table_parameters`, `relation_1_table_name`, `relation_1_table_parameters`, `relation_2_table_name`, `relation_2_table_parameters`, `relation_3_table_name`, `relation_3_table_parameters`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
		('used_rent_category_id', "
        . $article_id_rent .', '
        . $clang_id .', '
        . "'1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_rent', "
        . "'{\"column_id\":\"category_id\",\"column_clang_id\":\"clang_id\",\"restriction_1_column\":\"\",\"restriction_1_comparison_operator\":\"=\",\"restriction_1_value\":\"\",\"restriction_2_logical_operator\":\"\",\"restriction_2_column\":\"\",\"restriction_2_comparison_operator\":\"=\",\"restriction_2_value\":\"\",\"restriction_3_logical_operator\":\"\",\"restriction_3_column\":\"\",\"restriction_3_comparison_operator\":\"=\",\"restriction_3_value\":\"\",\"column_segment_part_1\":\"name\",\"column_segment_part_2_separator\":\"\\/\",\"column_segment_part_2\":\"\",\"column_segment_part_3_separator\":\"\\/\",\"column_segment_part_3\":\"\",\"relation_1_column\":\"\",\"relation_1_position\":\"BEFORE\",\"relation_2_column\":\"\",\"relation_2_position\":\"BEFORE\",\"relation_3_column\":\"\",\"relation_3_position\":\"BEFORE\",\"append_user_paths\":\"\",\"append_structure_categories\":\"0\",\"column_seo_title\":\"seo_title\",\"column_seo_description\":\"seo_description\",\"column_seo_image\":\"picture\",\"sitemap_add\":\"1\",\"sitemap_frequency\":\"weekly\",\"sitemap_priority\":\"0.7\",\"column_sitemap_lastmod\":\"updatedate\"}', "
        . "'', '[]', '', '[]', '', '[]', CURRENT_TIMESTAMP, '". (rex::getUser() instanceof rex_user ? rex::getUser()->getValue('login') : '') ."', CURRENT_TIMESTAMP, '". (rex::getUser() instanceof rex_user ? rex::getUser()->getValue('login') : '') ."');");
    $sql->setQuery('DELETE FROM '. \rex::getTablePrefix() ."url_generator_profile WHERE `namespace` = 'used_sale_machine_id';");
    $sql->setQuery('INSERT INTO '. \rex::getTablePrefix() ."url_generator_profile (`namespace`, `article_id`, `clang_id`, `table_name`, `table_parameters`, `relation_1_table_name`, `relation_1_table_parameters`, `relation_2_table_name`, `relation_2_table_parameters`, `relation_3_table_name`, `relation_3_table_parameters`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
		('used_sale_machine_id', "
        . $article_id_sale .', '
        . $clang_id .', '
        . "'1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines_sale', "
        . "'{\"column_id\":\"used_machine_id\",\"column_clang_id\":\"clang_id\",\"restriction_1_column\":\"\",\"restriction_1_comparison_operator\":\"=\",\"restriction_1_value\":\"\",\"restriction_2_logical_operator\":\"\",\"restriction_2_column\":\"\",\"restriction_2_comparison_operator\":\"=\",\"restriction_2_value\":\"\",\"restriction_3_logical_operator\":\"\",\"restriction_3_column\":\"\",\"restriction_3_comparison_operator\":\"=\",\"restriction_3_value\":\"\",\"column_segment_part_1\":\"name\",\"column_segment_part_2_separator\":\"\\-\",\"column_segment_part_2\":\"used_machine_id\",\"column_segment_part_3_separator\":\"\\/\",\"column_segment_part_3\":\"\",\"relation_1_column\":\"category_id\",\"relation_1_position\":\"BEFORE\",\"relation_2_column\":\"\",\"relation_2_position\":\"BEFORE\",\"relation_3_column\":\"\",\"relation_3_position\":\"BEFORE\",\"append_user_paths\":\"\",\"append_structure_categories\":\"0\",\"column_seo_title\":\"seo_title\",\"column_seo_description\":\"seo_description\",\"column_seo_image\":\"picture\",\"sitemap_add\":\"1\",\"sitemap_frequency\":\"weekly\",\"sitemap_priority\":\"1.0\",\"column_sitemap_lastmod\":\"updatedate\"}', "
        . "'relation_1_xxx_1_xxx_". rex::getTablePrefix() ."d2u_machinery_categories_lang', "
        . "'{\"column_id\":\"category_id\",\"column_clang_id\":\"clang_id\",\"column_segment_part_1\":\"name\",\"column_segment_part_2_separator\":\"\\/\",\"column_segment_part_2\":\"\",\"column_segment_part_3_separator\":\"\\/\",\"column_segment_part_3\":\"\"}', "
        . "'', '[]', '', '[]', CURRENT_TIMESTAMP, '". (rex::getUser() instanceof rex_user ? rex::getUser()->getValue('login') : '') ."', CURRENT_TIMESTAMP, '". (rex::getUser() instanceof rex_user ? rex::getUser()->getValue('login') : '') ."');");
    $sql->setQuery('DELETE FROM '. \rex::getTablePrefix() ."url_generator_profile WHERE `namespace` = 'used_sale_category_id';");
    $sql->setQuery('INSERT INTO '. \rex::getTablePrefix() ."url_generator_profile (`namespace`, `article_id`, `clang_id`, `table_name`, `table_parameters`, `relation_1_table_name`, `relation_1_table_parameters`, `relation_2_table_name`, `relation_2_table_parameters`, `relation_3_table_name`, `relation_3_table_parameters`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
		('used_sale_category_id', "
        . $article_id_sale .', '
        . $clang_id .', '
        . "'1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sale', "
        . "'{\"column_id\":\"category_id\",\"column_clang_id\":\"clang_id\",\"restriction_1_column\":\"\",\"restriction_1_comparison_operator\":\"=\",\"restriction_1_value\":\"\",\"restriction_2_logical_operator\":\"\",\"restriction_2_column\":\"\",\"restriction_2_comparison_operator\":\"=\",\"restriction_2_value\":\"\",\"restriction_3_logical_operator\":\"\",\"restriction_3_column\":\"\",\"restriction_3_comparison_operator\":\"=\",\"restriction_3_value\":\"\",\"column_segment_part_1\":\"name\",\"column_segment_part_2_separator\":\"\\/\",\"column_segment_part_2\":\"\",\"column_segment_part_3_separator\":\"\\/\",\"column_segment_part_3\":\"\",\"relation_1_column\":\"\",\"relation_1_position\":\"BEFORE\",\"relation_2_column\":\"\",\"relation_2_position\":\"BEFORE\",\"relation_3_column\":\"\",\"relation_3_position\":\"BEFORE\",\"append_user_paths\":\"\",\"append_structure_categories\":\"0\",\"column_seo_title\":\"seo_title\",\"column_seo_description\":\"seo_description\",\"column_seo_image\":\"picture\",\"sitemap_add\":\"1\",\"sitemap_frequency\":\"always\",\"sitemap_priority\":\"0.7\",\"column_sitemap_lastmod\":\"updatedate\"}', "
        . "'', '[]', '', '[]', '', '[]', CURRENT_TIMESTAMP, '". (rex::getUser() instanceof rex_user ? rex::getUser()->getValue('login') : '') ."', CURRENT_TIMESTAMP, '". (rex::getUser() instanceof rex_user ? rex::getUser()->getValue('login') : '') ."');");

    \TobiasKrais\D2UHelper\BackendHelper::generateUrlCache();
}

// Insert frontend translations
if (!class_exists(d2u_machinery_used_machines_lang_helper::class)) {
    // Load class in case addon is deactivated
    require_once 'lib/d2u_machinery_used_machines_lang_helper.php';
}
if (class_exists(d2u_machinery_used_machines_lang_helper::class)) {
    d2u_machinery_used_machines_lang_helper::factory()->install();
}
