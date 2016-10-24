<?php
$sql = rex_sql::factory();

// Create database
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". rex::getTablePrefix() ."d2u_machinery_used_machines (
	used_machine_id int(10) unsigned NOT NULL auto_increment,
	manufacturer varchar(255) collate utf8_general_ci default NULL,
	name varchar(255) collate utf8_general_ci default NULL,
	category_id int(10) UNSIGNED NOT NULL,
	availability varchar(10) collate utf8_general_ci default NULL,
	product_number varchar(255) collate utf8_general_ci default NULL,
	year_built int(4) default NULL,
	price decimal(10,2) default NULL,
	currency_code varchar(3) collate utf8_general_ci default NULL,
	vat int(2) default NULL,
	online_status varchar(10) collate utf8_general_ci default 'online',
	pics text collate utf8_general_ci default NULL,
	machine_id int(10) default NULL,
	location varchar(255) collate utf8_general_ci default NULL,
	external_url varchar(255) collate utf8_general_ci default NULL,
	PRIMARY KEY (used_machine_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1;");
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". rex::getTablePrefix() ."d2u_machinery_used_machines_lang (
	used_machine_id int(10) NOT NULL,
	clang_id int(10) NOT NULL,
	teaser varchar(255) collate utf8_general_ci default NULL,
	description text collate utf8_general_ci default NULL,
	translation_needs_update varchar(7) collate utf8_general_ci default NULL,
	updatedate int(11) default NULL,
	updateuser varchar(255) collate utf8_general_ci default NULL,
	PRIMARY KEY (used_machine_id, clang_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1;");

// Create views for url addon
$sql->setQuery('CREATE OR REPLACE VIEW '. rex::getTablePrefix() .'d2u_machinery_url_used_machines AS
	SELECT lang.used_machine_id, categories.clang_id, CONCAT(machines.manufacturer, " ", machines.name) AS name, CONCAT(machines.manufacturer, " ", machines.name, " - ", categories.name) AS seo_title, lang.teaser AS seo_description, machines.category_id, lang.updatedate
	FROM '. rex::getTablePrefix() .'d2u_machinery_used_machines_lang AS lang
	LEFT JOIN '. rex::getTablePrefix() .'d2u_machinery_used_machines AS machines ON lang.used_machine_id = machines.used_machine_id
	LEFT JOIN '. rex::getTablePrefix() .'d2u_machinery_categories_lang AS categories ON machines.category_id = categories.category_id
	WHERE machines.online_status = "online"');
$sql->setQuery('CREATE OR REPLACE VIEW '. rex::getTablePrefix() .'d2u_machinery_url_used_machine_categories AS
	SELECT machines.category_id, categories_lang.clang_id, categories_lang.name, parent_categories.name AS parent_name, CONCAT_WS(" - ", categories_lang.name, parent_categories.name) AS seo_title, categories_lang.teaser AS seo_description, categories_lang.updatedate
	FROM '. rex::getTablePrefix() .'d2u_machinery_used_machines_lang AS lang
	LEFT JOIN '. rex::getTablePrefix() .'d2u_machinery_used_machines AS machines ON lang.used_machine_id = machines.used_machine_id
	LEFT JOIN '. rex::getTablePrefix() .'d2u_machinery_categories_lang AS categories_lang ON machines.category_id = categories_lang.category_id
	LEFT JOIN '. rex::getTablePrefix() .'d2u_machinery_categories AS categories ON categories_lang.category_id = categories.category_id
	LEFT JOIN '. rex::getTablePrefix() .'d2u_machinery_categories_lang AS parent_categories ON categories.parent_category_id = parent_categories.category_id
	WHERE machines.online_status = "online" 
	GROUP BY category_id, clang_id, name, parent_name, seo_title, seo_description, updatedate');
// Insert url schemes
if(rex_addon::get('url')->isAvailable()) {
	$sql->setQuery("INSERT INTO `". rex::getTablePrefix() ."url_generate` (`article_id`, `clang_id`, `url`, `table`, `table_parameters`, `relation_table`, `relation_table_parameters`, `relation_insert`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
		(". rex_article::getSiteStartArticleId() .", 0, '', '1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines', '{\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines_field_1\":\"name\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines_field_2\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines_field_3\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines_id\":\"used_machine_id\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines_clang_id\":\"clang_id\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines_restriction_field\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines_restriction_operator\":\"=\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines_restriction_value\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines_url_param_key\":\"used_machine_id\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines_seo_title\":\"seo_title\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines_seo_description\":\"seo_description\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines_sitemap_add\":\"1\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines_sitemap_frequency\":\"weekly\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines_sitemap_priority\":\"1.0\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines_sitemap_lastmod\":\"updatedate\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines_path_names\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines_path_categories\":\"0\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines_relation_field\":\"category_id\"}', '1_xxx_relation_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories', '{\"1_xxx_relation_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_field_1\":\"parent_name\",\"1_xxx_relation_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_field_2\":\"name\",\"1_xxx_relation_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_field_3\":\"\",\"1_xxx_relation_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_id\":\"category_id\",\"1_xxx_relation_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_clang_id\":\"clang_id\"}', 'before', UNIX_TIMESTAMP(), 'd2u_machinery_addon_installer', UNIX_TIMESTAMP(), 'd2u_machinery_addon_installer'),
		(". rex_article::getSiteStartArticleId() .", 0, '', '1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories', '{\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_field_1\":\"parent_name\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_field_2\":\"name\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_field_3\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_id\":\"category_id\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_clang_id\":\"clang_id\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_restriction_field\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_restriction_operator\":\"=\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_restriction_value\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_url_param_key\":\"category_id\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_seo_title\":\"seo_title\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_seo_description\":\"seo_description\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sitemap_add\":\"1\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sitemap_frequency\":\"weekly\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sitemap_priority\":\"0.7\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sitemap_lastmod\":\"updatedate\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_path_names\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_path_categories\":\"0\",\"1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_relation_field\":\"\"}', '', '[]', 'before', UNIX_TIMESTAMP(), 'd2u_machinery_addon_installer', UNIX_TIMESTAMP(), 'd2u_machinery_addon_installer');");
}

// Insert frontend translations
used_machines_lang_helper::factory()->install();

// Create standard configuration
$d2u_machinery = rex_addon::get('d2u_machinery');
if(!$d2u_machinery->hasConfig('used_machine_article_id')) {
	$d2u_machinery->setConfig('used_machine_article_id', rex_article::getSiteStartArticleId());
}