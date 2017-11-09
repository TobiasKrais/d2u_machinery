<?php
$sql = rex_sql::factory();
$sql->setQuery('CREATE OR REPLACE VIEW '. rex::getTablePrefix() .'d2u_machinery_url_used_machines_rent AS
	SELECT lang.used_machine_id, categories.clang_id, CONCAT(machines.manufacturer, " ", machines.name) AS name, CONCAT(machines.manufacturer, " ", machines.name, " - ", categories.name) AS seo_title, lang.teaser AS seo_description, machines.category_id, lang.updatedate
	FROM '. rex::getTablePrefix() .'d2u_machinery_used_machines_lang AS lang
	LEFT JOIN '. rex::getTablePrefix() .'d2u_machinery_used_machines AS machines ON lang.used_machine_id = machines.used_machine_id
	LEFT JOIN '. rex::getTablePrefix() .'d2u_machinery_categories_lang AS categories ON machines.category_id = categories.category_id AND lang.clang_id = categories.clang_id
	LEFT JOIN '. rex::getTablePrefix() .'clang AS clang ON lang.clang_id = clang.id
	WHERE clang.status = 1 AND machines.online_status = "online" AND machines.offer_type = "rent"
	GROUP BY used_machine_id, clang_id, name, seo_title, seo_description, category_id, updatedate');
$sql->setQuery('CREATE OR REPLACE VIEW '. rex::getTablePrefix() .'d2u_machinery_url_used_machine_categories_rent AS
	SELECT machines.category_id, categories_lang.clang_id, CONCAT_WS(" - ", parent_categories.name, categories_lang.name) AS name, CONCAT_WS(" - ", categories_lang.name, parent_categories.name) AS seo_title, categories_lang.teaser AS seo_description, categories_lang.updatedate
	FROM '. rex::getTablePrefix() .'d2u_machinery_used_machines_lang AS lang
	LEFT JOIN '. rex::getTablePrefix() .'d2u_machinery_used_machines AS machines ON lang.used_machine_id = machines.used_machine_id
	LEFT JOIN '. rex::getTablePrefix() .'d2u_machinery_categories_lang AS categories_lang ON machines.category_id = categories_lang.category_id AND lang.clang_id = categories_lang.clang_id
	LEFT JOIN '. rex::getTablePrefix() .'d2u_machinery_categories AS categories ON categories_lang.category_id = categories.category_id
	LEFT JOIN '. rex::getTablePrefix() .'d2u_machinery_categories_lang AS parent_categories ON categories.parent_category_id = parent_categories.category_id AND lang.clang_id = parent_categories.clang_id
	LEFT JOIN '. rex::getTablePrefix() .'clang AS clang ON lang.clang_id = clang.id
	WHERE clang.status = 1 AND machines.online_status = "online" AND machines.offer_type = "rent"
	GROUP BY category_id, clang_id, name, seo_title, seo_description, updatedate');
$sql->setQuery('CREATE OR REPLACE VIEW '. rex::getTablePrefix() .'d2u_machinery_url_used_machines_sale AS
	SELECT lang.used_machine_id, categories.clang_id, CONCAT(machines.manufacturer, " ", machines.name) AS name, CONCAT(machines.manufacturer, " ", machines.name, " - ", categories.name) AS seo_title, lang.teaser AS seo_description, machines.category_id, lang.updatedate
	FROM '. rex::getTablePrefix() .'d2u_machinery_used_machines_lang AS lang
	LEFT JOIN '. rex::getTablePrefix() .'d2u_machinery_used_machines AS machines ON lang.used_machine_id = machines.used_machine_id
	LEFT JOIN '. rex::getTablePrefix() .'d2u_machinery_categories_lang AS categories ON machines.category_id = categories.category_id AND lang.clang_id = categories.clang_id
	LEFT JOIN '. rex::getTablePrefix() .'clang AS clang ON lang.clang_id = clang.id
	WHERE clang.status = 1 AND machines.online_status = "online" AND machines.offer_type = "sale"
	GROUP BY used_machine_id, clang_id, name, seo_title, seo_description, category_id, updatedate');
$sql->setQuery('CREATE OR REPLACE VIEW '. rex::getTablePrefix() .'d2u_machinery_url_used_machine_categories_sale AS
	SELECT machines.category_id, categories_lang.clang_id, CONCAT_WS(" - ", parent_categories.name, categories_lang.name) AS name, CONCAT_WS(" - ", categories_lang.name, parent_categories.name) AS seo_title, categories_lang.teaser AS seo_description, categories_lang.updatedate
	FROM '. rex::getTablePrefix() .'d2u_machinery_used_machines_lang AS lang
	LEFT JOIN '. rex::getTablePrefix() .'d2u_machinery_used_machines AS machines ON lang.used_machine_id = machines.used_machine_id
	LEFT JOIN '. rex::getTablePrefix() .'d2u_machinery_categories_lang AS categories_lang ON machines.category_id = categories_lang.category_id AND lang.clang_id = categories_lang.clang_id
	LEFT JOIN '. rex::getTablePrefix() .'d2u_machinery_categories AS categories ON categories_lang.category_id = categories.category_id
	LEFT JOIN '. rex::getTablePrefix() .'d2u_machinery_categories_lang AS parent_categories ON categories.parent_category_id = parent_categories.category_id AND lang.clang_id = parent_categories.clang_id
	LEFT JOIN '. rex::getTablePrefix() .'clang AS clang ON lang.clang_id = clang.id
	WHERE clang.status = 1 AND machines.online_status = "online" AND machines.offer_type = "sale"
	GROUP BY category_id, clang_id, name, seo_title, seo_description, updatedate');
// Insert url schemes
if(rex_addon::get('url')->isAvailable()) {
	$clang_id = count(rex_clang::getAllIds()) == 1 ? rex_clang::getStartId() : 0;
	$sql_replace = rex_sql::factory();
	$sql->setQuery("SELECT id FROM `". rex::getTablePrefix() ."url_generate` WHERE `table_parameters` LIKE '%d2u_machinery_url_used_machines_rent_url_param_key\":\"used_machine_id\"%'");
	if($sql->getRows() == 0) {
		$sql_replace->setQuery("DELETE FROM `". rex::getTablePrefix() ."url_generate` WHERE `table_parameters` LIKE '%d2u_machinery_url_used_machines_rent_url_param_key\":\"used_machine_id\"%';");
		$sql_replace->setQuery("INSERT INTO `". rex::getTablePrefix() ."url_generate` (`article_id`, `clang_id`, `url`, `table`, `table_parameters`, `relation_table`, `relation_table_parameters`, `relation_insert`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
			(". $d2u_machinery->getConfig('used_machine_article_id_rent') .", ". $clang_id .", '', '1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines_rent', '{\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines_rent_field_1\":\"name\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines_rent_field_2\":\"used_machine_id\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines_rent_field_3\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines_rent_id\":\"used_machine_id\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines_rent_clang_id\":\"". (count(rex_clang::getAllIds()) > 1 ? "clang_id" : "") ."\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines_rent_restriction_field\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines_rent_restriction_operator\":\"=\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines_rent_restriction_value\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines_rent_url_param_key\":\"used_rent_machine_id\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines_rent_seo_title\":\"seo_title\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines_rent_seo_description\":\"seo_description\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines_rent_seo_image\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines_rent_sitemap_add\":\"1\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines_rent_sitemap_frequency\":\"always\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines_rent_sitemap_priority\":\"1.0\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines_rent_sitemap_lastmod\":\"updatedate\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines_rent_path_names\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines_rent_path_categories\":\"0\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines_rent_relation_field\":\"category_id\"}', '1_xxx_relation_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_rent', '{\"1_xxx_relation_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_rent_field_1\":\"name\",\"1_xxx_relation_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_rent_field_2\":\"\",\"1_xxx_relation_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_rent_field_3\":\"\",\"1_xxx_relation_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_rent_id\":\"category_id\",\"1_xxx_relation_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_rent_clang_id\":\"clang_id\"}', 'before', UNIX_TIMESTAMP(), 'd2u_machinery_addon_installer', UNIX_TIMESTAMP(), 'd2u_machinery_addon_installer');");
	}
	$sql->setQuery("SELECT id FROM `". rex::getTablePrefix() ."url_generate` WHERE `table_parameters` LIKE '%d2u_machinery_url_used_machines_sale_url_param_key\":\"used_machine_id\"%'");
	if($sql->getRows() == 0) {
		$sql_replace->setQuery("DELETE FROM `". rex::getTablePrefix() ."url_generate` WHERE `table_parameters` LIKE '%d2u_machinery_url_used_machines_sale_url_param_key\":\"used_machine_id\"%';");
		$sql_replace->setQuery("INSERT INTO `". rex::getTablePrefix() ."url_generate` (`article_id`, `clang_id`, `url`, `table`, `table_parameters`, `relation_table`, `relation_table_parameters`, `relation_insert`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
			(". $d2u_machinery->getConfig('used_machine_article_id_sale') .", ". $clang_id .", '', '1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines_sale', '{\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines_sale_field_1\":\"name\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines_sale_field_2\":\"used_machine_id\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines_sale_field_3\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines_sale_id\":\"used_machine_id\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines_sale_clang_id\":\"". (count(rex_clang::getAllIds()) > 1 ? "clang_id" : "") ."\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines_sale_restriction_field\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines_sale_restriction_operator\":\"=\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines_sale_restriction_value\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines_sale_url_param_key\":\"used_sale_machine_id\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines_sale_seo_title\":\"seo_title\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines_sale_seo_description\":\"seo_description\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines_sale_seo_image\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines_sale_sitemap_add\":\"1\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines_sale_sitemap_frequency\":\"always\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines_sale_sitemap_priority\":\"1.0\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines_sale_sitemap_lastmod\":\"updatedate\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines_sale_path_names\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines_sale_path_categories\":\"0\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines_sale_relation_field\":\"category_id\"}', '1_xxx_relation_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sale', '{\"1_xxx_relation_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sale_field_1\":\"name\",\"1_xxx_relation_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sale_field_2\":\"\",\"1_xxx_relation_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sale_field_3\":\"\",\"1_xxx_relation_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sale_id\":\"category_id\",\"1_xxx_relation_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sale_clang_id\":\"clang_id\"}', 'before', UNIX_TIMESTAMP(), 'd2u_machinery_addon_installer', UNIX_TIMESTAMP(), 'd2u_machinery_addon_installer');");
	}
	$sql->setQuery("SELECT id FROM ". rex::getTablePrefix() ."url_generate WHERE `table` = '1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_rent'");
	if($sql->getRows() == 0) {
		$sql_replace->setQuery("DELETE FROM ". rex::getTablePrefix() ."url_generate WHERE `table` = '1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_rent';");
		$sql_replace->setQuery("INSERT INTO `". rex::getTablePrefix() ."url_generate` (`article_id`, `clang_id`, `url`, `table`, `table_parameters`, `relation_table`, `relation_table_parameters`, `relation_insert`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
			(". $d2u_machinery->getConfig('used_machine_article_id_rent') .", ". $clang_id .", '', '1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_rent', '{\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_rent_field_1\":\"name\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_rent_field_2\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_rent_field_3\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_rent_id\":\"category_id\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_rent_clang_id\":\"". (count(rex_clang::getAllIds()) > 1 ? "clang_id" : "") ."\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_rent_restriction_field\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_rent_restriction_operator\":\"=\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_rent_restriction_value\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_rent_url_param_key\":\"used_rent_category_id\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_rent_seo_title\":\"seo_title\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_rent_seo_description\":\"seo_description\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_rent_seo_image\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_rent_sitemap_add\":\"1\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_rent_sitemap_frequency\":\"monthly\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_rent_sitemap_priority\":\"0.7\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_rent_sitemap_lastmod\":\"updatedate\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_rent_path_names\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_rent_path_categories\":\"0\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_rent_relation_field\":\"\"}', '', '[]', 'before', UNIX_TIMESTAMP(), 'd2u_machinery_addon_installer', UNIX_TIMESTAMP(), 'd2u_machinery_addon_installer');");
	}
	$sql->setQuery("SELECT id FROM ". rex::getTablePrefix() ."url_generate WHERE `table` = '1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sale'");
	if($sql->getRows() == 0) {
		$sql_replace->setQuery("DELETE FROM ". rex::getTablePrefix() ."url_generate WHERE `table` = '1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sale';");
		$sql_replace->setQuery("INSERT INTO `". rex::getTablePrefix() ."url_generate` (`article_id`, `clang_id`, `url`, `table`, `table_parameters`, `relation_table`, `relation_table_parameters`, `relation_insert`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
			(". $d2u_machinery->getConfig('used_machine_article_id_sale') .", ". $clang_id .", '', '1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sale', '{\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sale_field_1\":\"name\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sale_field_2\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sale_field_3\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sale_id\":\"category_id\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sale_clang_id\":\"". (count(rex_clang::getAllIds()) > 1 ? "clang_id" : "") ."\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sale_restriction_field\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sale_restriction_operator\":\"=\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sale_restriction_value\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sale_url_param_key\":\"used_sale_category_id\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sale_seo_title\":\"seo_title\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sale_seo_description\":\"seo_description\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sale_seo_image\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sale_sitemap_add\":\"1\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sale_sitemap_frequency\":\"always\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sale_sitemap_priority\":\"0.7\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sale_sitemap_lastmod\":\"updatedate\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sale_path_names\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sale_path_categories\":\"0\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sale_relation_field\":\"\"}', '', '[]', 'before', UNIX_TIMESTAMP(), 'd2u_machinery_addon_installer', UNIX_TIMESTAMP(), 'd2u_machinery_addon_installer');");
	}
	
	UrlGenerator::generatePathFile([]);
}

// Update language replacements
d2u_machinery_used_machines_lang_helper::factory()->install();