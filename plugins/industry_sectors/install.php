<?php
$sql = \rex_sql::factory();

// Create tables
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". \rex::getTablePrefix() ."d2u_machinery_industry_sectors (
	industry_sector_id int(10) unsigned NOT NULL auto_increment,
	online_status varchar(7) collate utf8mb4_unicode_ci default NULL,
	pic varchar(100) collate utf8mb4_unicode_ci default NULL,
	PRIMARY KEY (industry_sector_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;");
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". \rex::getTablePrefix() ."d2u_machinery_industry_sectors_lang (
	industry_sector_id int(10) NOT NULL,
	clang_id int(10) NOT NULL,
	name varchar(255) collate utf8mb4_unicode_ci default NULL,
	teaser varchar(255) collate utf8mb4_unicode_ci default NULL,
	translation_needs_update varchar(7) collate utf8mb4_unicode_ci default NULL,
	updatedate int(11) default NULL,
	updateuser varchar(255) collate utf8mb4_unicode_ci default NULL,
	PRIMARY KEY (industry_sector_id, clang_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;");

// Alter machine table
$sql->setQuery("SHOW COLUMNS FROM ". \rex::getTablePrefix() ."d2u_machinery_machines LIKE 'industry_sector_ids';");
if($sql->getRows() == 0) {
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_machinery_machines "
		. "ADD industry_sector_ids VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;");
}

// Create views for url addon
$sql->setQuery('CREATE OR REPLACE VIEW '. \rex::getTablePrefix() .'d2u_machinery_url_industry_sectors AS
	SELECT industries.industry_sector_id, industries.clang_id, industries.name, industries.name AS seo_title, industries.teaser AS seo_description
	FROM '. \rex::getTablePrefix() .'d2u_machinery_industry_sectors_lang AS industries
	LEFT JOIN '. \rex::getTablePrefix() .'clang AS clang ON industries.clang_id = clang.id
	WHERE clang.status = 1'
);

// Insert url scheme
if(\rex_addon::get('url')->isAvailable()) {
	$sql->setQuery("SELECT * FROM ". \rex::getTablePrefix() ."url_generate WHERE `table` = '1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors'");
	$clang_id = count(rex_clang::getAllIds()) == 1 ? rex_clang::getStartId() : 0;
	if($sql->getRows() == 0) {
		$sql->setQuery("INSERT INTO `". \rex::getTablePrefix() ."url_generate` (`article_id`, `clang_id`, `url`, `table`, `table_parameters`, `relation_table`, `relation_table_parameters`, `relation_insert`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
			(". rex_article::getSiteStartArticleId() .", ". $clang_id .", '', '1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors', '{\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors_field_1\":\"name\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors_field_2\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors_field_3\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors_id\":\"industry_sector_id\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors_clang_id\":\"". (count(rex_clang::getAllIds()) > 1 ? "clang_id" : "") ."\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors_restriction_field\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors_restriction_operator\":\"=\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors_restriction_value\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors_url_param_key\":\"industry_sector_id\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors_seo_title\":\"seo_title\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors_seo_description\":\"seo_description\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors_seo_image\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors_sitemap_add\":\"1\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors_sitemap_frequency\":\"always\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors_sitemap_priority\":\"0.5\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors_sitemap_lastmod\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors_path_names\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors_path_categories\":\"0\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors_relation_field\":\"\"}', '', '[]', 'before', UNIX_TIMESTAMP(), 'd2u_machinery_addon_installer', UNIX_TIMESTAMP(), 'd2u_machinery_addon_installer');");
	}

	UrlGenerator::generatePathFile([]);
}

// Insert frontend translations
if(class_exists(d2u_machinery_industry_sectors_lang_helper)) {
	d2u_machinery_industry_sectors_lang_helper::factory()->install();
}