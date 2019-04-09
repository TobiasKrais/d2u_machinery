<?php
// Create standard configuration
$d2u_machinery = rex_addon::get('d2u_machinery');
if(!$d2u_machinery->hasConfig('used_machine_article_id_rent')) {
	$d2u_machinery->setConfig('used_machine_article_id_rent', rex_article::getSiteStartArticleId());
}
if(!$d2u_machinery->hasConfig('used_machine_article_id_sale')) {
	$d2u_machinery->setConfig('used_machine_article_id_sale', rex_article::getSiteStartArticleId());
}

$sql = \rex_sql::factory();

// Create database
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". \rex::getTablePrefix() ."d2u_machinery_used_machines (
	used_machine_id int(10) unsigned NOT NULL auto_increment,
	manufacturer varchar(255) collate utf8mb4_unicode_ci default NULL,
	name varchar(255) collate utf8mb4_unicode_ci default NULL,
	category_id int(10) UNSIGNED NOT NULL,
	availability varchar(10) collate utf8mb4_unicode_ci default NULL,
	product_number varchar(255) collate utf8mb4_unicode_ci default NULL,
	offer_type varchar(10) collate utf8mb4_unicode_ci default NULL,
	year_built int(4) default NULL,
	price decimal(10,2) default NULL,
	currency_code varchar(3) collate utf8mb4_unicode_ci default NULL,
	vat int(2) default NULL,
	online_status varchar(10) collate utf8mb4_unicode_ci default 'online',
	pics text collate utf8mb4_unicode_ci default NULL,
	machine_id int(10) default NULL,
	location varchar(255) collate utf8mb4_unicode_ci default NULL,
	external_url varchar(255) collate utf8mb4_unicode_ci default NULL,
	PRIMARY KEY (used_machine_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;");
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". \rex::getTablePrefix() ."d2u_machinery_used_machines_lang (
	used_machine_id int(10) NOT NULL,
	clang_id int(10) NOT NULL,
	teaser varchar(255) collate utf8mb4_unicode_ci default NULL,
	description text collate utf8mb4_unicode_ci default NULL,
	downloads text collate utf8mb4_unicode_ci default NULL,
	translation_needs_update varchar(7) collate utf8mb4_unicode_ci default NULL,
	updatedate DATETIME default NULL,
	updateuser varchar(255) collate utf8mb4_unicode_ci default NULL,
	PRIMARY KEY (used_machine_id, clang_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;");

// Create views for url addon
$sql->setQuery('CREATE OR REPLACE VIEW '. \rex::getTablePrefix() .'d2u_machinery_url_used_machines_rent AS
	SELECT lang.used_machine_id, categories.clang_id, CONCAT(machines.manufacturer, " ", machines.name) AS name, CONCAT(machines.manufacturer, " ", machines.name, " - ", categories.name) AS seo_title, lang.teaser AS seo_description, SUBSTRING_INDEX(machines.pics, ",", 1) as picture, machines.category_id, lang.updatedate
	FROM '. \rex::getTablePrefix() .'d2u_machinery_used_machines_lang AS lang
	LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_used_machines AS machines ON lang.used_machine_id = machines.used_machine_id
	LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_categories_lang AS categories ON machines.category_id = categories.category_id AND lang.clang_id = categories.clang_id
	LEFT JOIN '. \rex::getTablePrefix() .'clang AS clang ON lang.clang_id = clang.id
	WHERE clang.status = 1 AND machines.online_status = "online" AND machines.offer_type = "rent"
	GROUP BY used_machine_id, clang_id, name, seo_title, seo_description, picture, category_id, updatedate;');
$sql->setQuery('CREATE OR REPLACE VIEW '. \rex::getTablePrefix() .'d2u_machinery_url_used_machine_categories_rent AS
	SELECT machines.category_id, categories_lang.clang_id, CONCAT_WS(" - ", parent_categories.name, categories_lang.name) AS name, CONCAT_WS(" - ", categories_lang.name, parent_categories.name) AS seo_title, categories_lang.teaser AS seo_description,IF(categories_lang.pic_lang IS NULL or categories_lang.pic_lang = "", categories.pic, categories_lang.pic_lang) as picture, categories_lang.updatedate
	FROM '. \rex::getTablePrefix() .'d2u_machinery_used_machines_lang AS lang
	LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_used_machines AS machines ON lang.used_machine_id = machines.used_machine_id
	LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_categories_lang AS categories_lang ON machines.category_id = categories_lang.category_id AND lang.clang_id = categories_lang.clang_id
	LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_categories AS categories ON categories_lang.category_id = categories.category_id
	LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_categories_lang AS parent_categories ON categories.parent_category_id = parent_categories.category_id AND lang.clang_id = parent_categories.clang_id
	LEFT JOIN '. \rex::getTablePrefix() .'clang AS clang ON lang.clang_id = clang.id
	WHERE clang.status = 1 AND machines.online_status = "online" AND machines.offer_type = "rent"
	GROUP BY category_id, clang_id, name, seo_title, seo_description, picture, updatedate;');
$sql->setQuery('CREATE OR REPLACE VIEW '. \rex::getTablePrefix() .'d2u_machinery_url_used_machines_sale AS
	SELECT lang.used_machine_id, categories.clang_id, CONCAT(machines.manufacturer, " ", machines.name) AS name, CONCAT(machines.manufacturer, " ", machines.name, " - ", categories.name) AS seo_title, lang.teaser AS seo_description, SUBSTRING_INDEX(machines.pics, ",", 1) as picture, machines.category_id, lang.updatedate
	FROM '. \rex::getTablePrefix() .'d2u_machinery_used_machines_lang AS lang
	LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_used_machines AS machines ON lang.used_machine_id = machines.used_machine_id
	LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_categories_lang AS categories ON machines.category_id = categories.category_id AND lang.clang_id = categories.clang_id
	LEFT JOIN '. \rex::getTablePrefix() .'clang AS clang ON lang.clang_id = clang.id
	WHERE clang.status = 1 AND machines.online_status = "online" AND machines.offer_type = "sale"
	GROUP BY used_machine_id, clang_id, name, seo_title, seo_description, picture, category_id, updatedate;');
$sql->setQuery('CREATE OR REPLACE VIEW '. \rex::getTablePrefix() .'d2u_machinery_url_used_machine_categories_sale AS
	SELECT machines.category_id, categories_lang.clang_id, CONCAT_WS(" - ", parent_categories.name, categories_lang.name) AS name, CONCAT_WS(" - ", categories_lang.name, parent_categories.name) AS seo_title, categories_lang.teaser AS seo_description,IF(categories_lang.pic_lang IS NULL or categories_lang.pic_lang = "", categories.pic, categories_lang.pic_lang) as picture, categories_lang.updatedate
	FROM '. \rex::getTablePrefix() .'d2u_machinery_used_machines_lang AS lang
	LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_used_machines AS machines ON lang.used_machine_id = machines.used_machine_id
	LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_categories_lang AS categories_lang ON machines.category_id = categories_lang.category_id AND lang.clang_id = categories_lang.clang_id
	LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_categories AS categories ON categories_lang.category_id = categories.category_id
	LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_categories_lang AS parent_categories ON categories.parent_category_id = parent_categories.category_id AND lang.clang_id = parent_categories.clang_id
	LEFT JOIN '. \rex::getTablePrefix() .'clang AS clang ON lang.clang_id = clang.id
	WHERE clang.status = 1 AND machines.online_status = "online" AND machines.offer_type = "sale"
	GROUP BY category_id, clang_id, name, seo_title, seo_description, picture, updatedate;');

// Insert url schemes
if(\rex_addon::get('url')->isAvailable()) {
	$clang_id = count(rex_clang::getAllIds()) == 1 ? rex_clang::getStartId() : 0;
	$article_id_rent = rex_config::get('d2u_machinery', 'used_machine_article_id_rent', 0) > 0 ? rex_config::get('d2u_machinery', 'used_machine_article_id_rent') : rex_article::getSiteStartArticleId(); 
	$article_id_sale = rex_config::get('d2u_machinery', 'used_machine_article_id_sale', 0) > 0 ? rex_config::get('d2u_machinery', 'used_machine_article_id_sale') : rex_article::getSiteStartArticleId(); 
	if(rex_string::versionCompare(\rex_addon::get('url')->getVersion(), '1.5', '>=')) {
		$sql->setQuery("DELETE FROM ". \rex::getTablePrefix() ."rex_url_generator_profile WHERE `namespace` = 'used_rent_machine_id';");
		$sql->setQuery("INSERT INTO `rex_url_generator_profile` (`namespace`, `article_id`, `clang_id`, `table_name`, `table_parameters`, `relation_1_table_name`, `relation_1_table_parameters`, `relation_2_table_name`, `relation_2_table_parameters`, `relation_3_table_name`, `relation_3_table_parameters`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
			('used_rent_machine_id', "
			. $article_id_rent .", "
			. $clang_id .", "
			. "'1_xxx_rex_d2u_machinery_url_used_machines_rent', "
			. "'{\"column_id\":\"used_machine_id\",\"column_clang_id\":\"clang_id\",\"restriction_1_column\":\"\",\"restriction_1_comparison_operator\":\"=\",\"restriction_1_value\":\"\",\"restriction_2_logical_operator\":\"\",\"restriction_2_column\":\"\",\"restriction_2_comparison_operator\":\"=\",\"restriction_2_value\":\"\",\"restriction_3_logical_operator\":\"\",\"restriction_3_column\":\"\",\"restriction_3_comparison_operator\":\"=\",\"restriction_3_value\":\"\",\"column_segment_part_1\":\"name\",\"column_segment_part_2_separator\":\"\-\",\"column_segment_part_2\":\"used_machine_id\",\"column_segment_part_3_separator\":\"\\/\",\"column_segment_part_3\":\"\",\"relation_1_column\":\"category_id\",\"relation_1_position\":\"BEFORE\",\"relation_2_column\":\"\",\"relation_2_position\":\"BEFORE\",\"relation_3_column\":\"\",\"relation_3_position\":\"BEFORE\",\"append_user_paths\":\"\",\"append_structure_categories\":\"0\",\"column_seo_title\":\"seo_title\",\"column_seo_description\":\"seo_description\",\"column_seo_image\":\"picture\",\"sitemap_add\":\"1\",\"sitemap_frequency\":\"weekly\",\"sitemap_priority\":\"1.0\",\"column_sitemap_lastmod\":\"updatedate\"}', "
			. "'relation_1_xxx_1_xxx_rex_d2u_machinery_categories_lang', "
			. "'{\"column_id\":\"category_id\",\"column_clang_id\":\"clang_id\",\"column_segment_part_1\":\"name\",\"column_segment_part_2_separator\":\"\\/\",\"column_segment_part_2\":\"\",\"column_segment_part_3_separator\":\"\\/\",\"column_segment_part_3\":\"\"}', "
			. "'', '[]', '', '[]', CURRENT_TIMESTAMP, '". rex::getUser()->getValue('login') ."', CURRENT_TIMESTAMP, '". rex::getUser()->getValue('login') ."');");
		$sql->setQuery("DELETE FROM ". \rex::getTablePrefix() ."rex_url_generator_profile WHERE `namespace` = 'used_rent_category_id';");
		$sql->setQuery("INSERT INTO `rex_url_generator_profile` (`namespace`, `article_id`, `clang_id`, `table_name`, `table_parameters`, `relation_1_table_name`, `relation_1_table_parameters`, `relation_2_table_name`, `relation_2_table_parameters`, `relation_3_table_name`, `relation_3_table_parameters`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
			('used_rent_category_id', "
			. $article_id_rent .", "
			. $clang_id .", "
			. "'1_xxx_rex_d2u_machinery_url_used_machine_categories_rent', "
			. "'{\"column_id\":\"category_id\",\"column_clang_id\":\"clang_id\",\"restriction_1_column\":\"\",\"restriction_1_comparison_operator\":\"=\",\"restriction_1_value\":\"\",\"restriction_2_logical_operator\":\"\",\"restriction_2_column\":\"\",\"restriction_2_comparison_operator\":\"=\",\"restriction_2_value\":\"\",\"restriction_3_logical_operator\":\"\",\"restriction_3_column\":\"\",\"restriction_3_comparison_operator\":\"=\",\"restriction_3_value\":\"\",\"column_segment_part_1\":\"name\",\"column_segment_part_2_separator\":\"\\/\",\"column_segment_part_2\":\"\",\"column_segment_part_3_separator\":\"\\/\",\"column_segment_part_3\":\"\",\"relation_1_column\":\"\",\"relation_1_position\":\"BEFORE\",\"relation_2_column\":\"\",\"relation_2_position\":\"BEFORE\",\"relation_3_column\":\"\",\"relation_3_position\":\"BEFORE\",\"append_user_paths\":\"\",\"append_structure_categories\":\"0\",\"column_seo_title\":\"seo_title\",\"column_seo_description\":\"seo_description\",\"column_seo_image\":\"picture\",\"sitemap_add\":\"1\",\"sitemap_frequency\":\"weekly\",\"sitemap_priority\":\"0.7\",\"column_sitemap_lastmod\":\"updatedate\"}', "
			. "'', '[]', '', '[]', '', '[]', CURRENT_TIMESTAMP, '". rex::getUser()->getValue('login') ."', CURRENT_TIMESTAMP, '". rex::getUser()->getValue('login') ."');");
		$sql->setQuery("DELETE FROM ". \rex::getTablePrefix() ."rex_url_generator_profile WHERE `namespace` = 'used_sale_machine_id';");
		$sql->setQuery("INSERT INTO `rex_url_generator_profile` (`namespace`, `article_id`, `clang_id`, `table_name`, `table_parameters`, `relation_1_table_name`, `relation_1_table_parameters`, `relation_2_table_name`, `relation_2_table_parameters`, `relation_3_table_name`, `relation_3_table_parameters`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
			('used_sale_machine_id', "
			. $article_id_sale .", "
			. $clang_id .", "
			. "'1_xxx_rex_d2u_machinery_url_used_machines_sale', "
			. "'{\"column_id\":\"used_machine_id\",\"column_clang_id\":\"clang_id\",\"restriction_1_column\":\"\",\"restriction_1_comparison_operator\":\"=\",\"restriction_1_value\":\"\",\"restriction_2_logical_operator\":\"\",\"restriction_2_column\":\"\",\"restriction_2_comparison_operator\":\"=\",\"restriction_2_value\":\"\",\"restriction_3_logical_operator\":\"\",\"restriction_3_column\":\"\",\"restriction_3_comparison_operator\":\"=\",\"restriction_3_value\":\"\",\"column_segment_part_1\":\"name\",\"column_segment_part_2_separator\":\"\-\",\"column_segment_part_2\":\"used_machine_id\",\"column_segment_part_3_separator\":\"\\/\",\"column_segment_part_3\":\"\",\"relation_1_column\":\"category_id\",\"relation_1_position\":\"BEFORE\",\"relation_2_column\":\"\",\"relation_2_position\":\"BEFORE\",\"relation_3_column\":\"\",\"relation_3_position\":\"BEFORE\",\"append_user_paths\":\"\",\"append_structure_categories\":\"0\",\"column_seo_title\":\"seo_title\",\"column_seo_description\":\"seo_description\",\"column_seo_image\":\"picture\",\"sitemap_add\":\"1\",\"sitemap_frequency\":\"weekly\",\"sitemap_priority\":\"1.0\",\"column_sitemap_lastmod\":\"updatedate\"}', "
			. "'relation_1_xxx_1_xxx_rex_d2u_machinery_categories_lang', "
			. "'{\"column_id\":\"category_id\",\"column_clang_id\":\"clang_id\",\"column_segment_part_1\":\"name\",\"column_segment_part_2_separator\":\"\\/\",\"column_segment_part_2\":\"\",\"column_segment_part_3_separator\":\"\\/\",\"column_segment_part_3\":\"\"}', "
			. "'', '[]', '', '[]', CURRENT_TIMESTAMP, '". rex::getUser()->getValue('login') ."', CURRENT_TIMESTAMP, '". rex::getUser()->getValue('login') ."');");
		$sql->setQuery("DELETE FROM ". \rex::getTablePrefix() ."rex_url_generator_profile WHERE `namespace` = 'used_sale_category_id';");
		$sql->setQuery("INSERT INTO `rex_url_generator_profile` (`namespace`, `article_id`, `clang_id`, `table_name`, `table_parameters`, `relation_1_table_name`, `relation_1_table_parameters`, `relation_2_table_name`, `relation_2_table_parameters`, `relation_3_table_name`, `relation_3_table_parameters`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
			('used_sale_category_id', "
			. $article_id_sale .", "
			. $clang_id .", "
			. "'1_xxx_rex_d2u_machinery_url_used_machine_categories_sale', "
			. "'{\"column_id\":\"category_id\",\"column_clang_id\":\"clang_id\",\"restriction_1_column\":\"\",\"restriction_1_comparison_operator\":\"=\",\"restriction_1_value\":\"\",\"restriction_2_logical_operator\":\"\",\"restriction_2_column\":\"\",\"restriction_2_comparison_operator\":\"=\",\"restriction_2_value\":\"\",\"restriction_3_logical_operator\":\"\",\"restriction_3_column\":\"\",\"restriction_3_comparison_operator\":\"=\",\"restriction_3_value\":\"\",\"column_segment_part_1\":\"name\",\"column_segment_part_2_separator\":\"\\/\",\"column_segment_part_2\":\"\",\"column_segment_part_3_separator\":\"\\/\",\"column_segment_part_3\":\"\",\"relation_1_column\":\"\",\"relation_1_position\":\"BEFORE\",\"relation_2_column\":\"\",\"relation_2_position\":\"BEFORE\",\"relation_3_column\":\"\",\"relation_3_position\":\"BEFORE\",\"append_user_paths\":\"\",\"append_structure_categories\":\"0\",\"column_seo_title\":\"seo_title\",\"column_seo_description\":\"seo_description\",\"column_seo_image\":\"picture\",\"sitemap_add\":\"1\",\"sitemap_frequency\":\"always\",\"sitemap_priority\":\"0.7\",\"column_sitemap_lastmod\":\"updatedate\"}', "
			. "'', '[]', '', '[]', '', '[]', CURRENT_TIMESTAMP, '". rex::getUser()->getValue('login') ."', CURRENT_TIMESTAMP, '". rex::getUser()->getValue('login') ."');");
	}
	else {
		// Insert url schemes Version 1.x
		$sql->setQuery("DELETE FROM ". \rex::getTablePrefix() ."url_generate WHERE `table` = '1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machines_rent';");
		$sql->setQuery("INSERT INTO `". \rex::getTablePrefix() ."url_generate` (`article_id`, `clang_id`, `url`, `table`, `table_parameters`, `relation_table`, `relation_table_parameters`, `relation_insert`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
			(". $article_id_rent .", "
			. $clang_id .", "
			. "'', "
			. "'1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machines_rent', "
			. "'{\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machines_rent_field_1\":\"name\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machines_rent_field_2\":\"used_machine_id\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machines_rent_field_3\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machines_rent_id\":\"used_machine_id\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machines_rent_clang_id\":\"". (count(rex_clang::getAllIds()) > 1 ? "clang_id" : "") ."\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machines_rent_restriction_field\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machines_rent_restriction_operator\":\"=\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machines_rent_restriction_value\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machines_rent_url_param_key\":\"used_rent_machine_id\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machines_rent_seo_title\":\"seo_title\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machines_rent_seo_description\":\"seo_description\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machines_rent_seo_image\":\"picture\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machines_rent_sitemap_add\":\"1\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machines_rent_sitemap_frequency\":\"always\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machines_rent_sitemap_priority\":\"1.0\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machines_rent_sitemap_lastmod\":\"updatedate\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machines_rent_path_names\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machines_rent_path_categories\":\"0\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machines_rent_relation_field\":\"category_id\"}', "
			. "'1_xxx_relation_". \rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_rent', "
			. "'{\"1_xxx_relation_". \rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_rent_field_1\":\"name\",\"1_xxx_relation_". \rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_rent_field_2\":\"\",\"1_xxx_relation_". \rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_rent_field_3\":\"\",\"1_xxx_relation_". \rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_rent_id\":\"category_id\",\"1_xxx_relation_". \rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_rent_clang_id\":\"clang_id\"}', "
			. "'before', UNIX_TIMESTAMP(), '". rex::getUser()->getValue('login') ."', UNIX_TIMESTAMP(), '". rex::getUser()->getValue('login') ."');");
		$sql->setQuery("DELETE FROM ". \rex::getTablePrefix() ."url_generate WHERE `table` = '1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machines_sale';");
		$sql->setQuery("INSERT INTO `". \rex::getTablePrefix() ."url_generate` (`article_id`, `clang_id`, `url`, `table`, `table_parameters`, `relation_table`, `relation_table_parameters`, `relation_insert`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
			(". $article_id_sale .", "
			. $clang_id .", "
			. "'', "
			. "'1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machines_sale', "
			. "'{\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machines_sale_field_1\":\"name\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machines_sale_field_2\":\"used_machine_id\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machines_sale_field_3\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machines_sale_id\":\"used_machine_id\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machines_sale_clang_id\":\"". (count(rex_clang::getAllIds()) > 1 ? "clang_id" : "") ."\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machines_sale_restriction_field\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machines_sale_restriction_operator\":\"=\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machines_sale_restriction_value\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machines_sale_url_param_key\":\"used_sale_machine_id\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machines_sale_seo_title\":\"seo_title\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machines_sale_seo_description\":\"seo_description\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machines_sale_seo_image\":\"picture\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machines_sale_sitemap_add\":\"1\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machines_sale_sitemap_frequency\":\"always\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machines_sale_sitemap_priority\":\"1.0\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machines_sale_sitemap_lastmod\":\"updatedate\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machines_sale_path_names\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machines_sale_path_categories\":\"0\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machines_sale_relation_field\":\"category_id\"}', "
			. "'1_xxx_relation_". \rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sale', "
			. "'{\"1_xxx_relation_". \rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sale_field_1\":\"name\",\"1_xxx_relation_". \rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sale_field_2\":\"\",\"1_xxx_relation_". \rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sale_field_3\":\"\",\"1_xxx_relation_". \rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sale_id\":\"category_id\",\"1_xxx_relation_". \rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sale_clang_id\":\"clang_id\"}', "
			. "'before', UNIX_TIMESTAMP(), '". rex::getUser()->getValue('login') ."', UNIX_TIMESTAMP(), '". rex::getUser()->getValue('login') ."');");
		$sql->setQuery("DELETE FROM ". \rex::getTablePrefix() ."url_generate WHERE `table` = '1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_rent';");
		$sql->setQuery("INSERT INTO `". \rex::getTablePrefix() ."url_generate` (`article_id`, `clang_id`, `url`, `table`, `table_parameters`, `relation_table`, `relation_table_parameters`, `relation_insert`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
			(". $article_id_rent .", "
			. $clang_id .", "
			. "'', "
			. "'1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_rent', "
			. "'{\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_rent_field_1\":\"name\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_rent_field_2\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_rent_field_3\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_rent_id\":\"category_id\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_rent_clang_id\":\"". (count(rex_clang::getAllIds()) > 1 ? "clang_id" : "") ."\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_rent_restriction_field\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_rent_restriction_operator\":\"=\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_rent_restriction_value\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_rent_url_param_key\":\"used_rent_category_id\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_rent_seo_title\":\"seo_title\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_rent_seo_description\":\"seo_description\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_rent_seo_image\":\"picture\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_rent_sitemap_add\":\"1\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_rent_sitemap_frequency\":\"monthly\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_rent_sitemap_priority\":\"0.7\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_rent_sitemap_lastmod\":\"updatedate\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_rent_path_names\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_rent_path_categories\":\"0\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_rent_relation_field\":\"\"}',"
			. "'', '[]', 'before', UNIX_TIMESTAMP(), '". rex::getUser()->getValue('login') ."', UNIX_TIMESTAMP(), '". rex::getUser()->getValue('login') ."');");
		$sql->setQuery("DELETE FROM ". \rex::getTablePrefix() ."url_generate WHERE `table` = '1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sale';");
		$sql->setQuery("INSERT INTO `". \rex::getTablePrefix() ."url_generate` (`article_id`, `clang_id`, `url`, `table`, `table_parameters`, `relation_table`, `relation_table_parameters`, `relation_insert`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
			(". $article_id_sale .", "
			. $clang_id .", "
			. "'', "
			. "'1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sale', "
			. "'{\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sale_field_1\":\"name\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sale_field_2\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sale_field_3\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sale_id\":\"category_id\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sale_clang_id\":\"". (count(rex_clang::getAllIds()) > 1 ? "clang_id" : "") ."\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sale_restriction_field\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sale_restriction_operator\":\"=\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sale_restriction_value\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sale_url_param_key\":\"used_sale_category_id\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sale_seo_title\":\"seo_title\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sale_seo_description\":\"seo_description\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sale_seo_image\":\"picture\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sale_sitemap_add\":\"1\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sale_sitemap_frequency\":\"always\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sale_sitemap_priority\":\"0.7\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sale_sitemap_lastmod\":\"updatedate\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sale_path_names\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sale_path_categories\":\"0\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sale_relation_field\":\"\"}', "
			. "'', '[]', 'before', UNIX_TIMESTAMP(), '". rex::getUser()->getValue('login') ."', UNIX_TIMESTAMP(), '". rex::getUser()->getValue('login') ."');");
	}

	d2u_addon_backend_helper::generateUrlCache();
}

// Insert frontend translations
if(class_exists(d2u_machinery_used_machines_lang_helper)) {
	d2u_machinery_used_machines_lang_helper::factory()->install();
}