<?php
$sql = \rex_sql::factory();
$sql->setQuery('CREATE OR REPLACE VIEW '. \rex::getTablePrefix() .'d2u_machinery_url_industry_sectors AS
	SELECT industries.industry_sector_id, industries.clang_id, industries.name, industries.name AS seo_title, industries.teaser AS seo_description, industries.updatedate
	FROM '. \rex::getTablePrefix() .'d2u_machinery_industry_sectors_lang AS industries
	LEFT JOIN '. \rex::getTablePrefix() .'clang AS clang ON industries.clang_id = clang.id
	WHERE clang.status = 1'
);
if(\rex_addon::get("url")->isAvailable()) {
	$clang_id = count(rex_clang::getAllIds()) == 1 ? rex_clang::getStartId() : 0;
	$sql_replace = \rex_sql::factory();
	$sql->setQuery("SELECT * FROM ". \rex::getTablePrefix() ."url_generate WHERE `table` = '1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors'");
	if($sql->getRows() == 0) {
		$sql_replace->setQuery("DELETE FROM `". \rex::getTablePrefix() ."url_generate` WHERE `table` LIKE '%d2u_machinery_url_industry_sectors';");
		$sql->setQuery("INSERT INTO `". \rex::getTablePrefix() ."url_generate` (`article_id`, `clang_id`, `url`, `table`, `table_parameters`, `relation_table`, `relation_table_parameters`, `relation_insert`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
			(". rex_article::getSiteStartArticleId() .", ". $clang_id .", '', '1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors', '{\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors_field_1\":\"name\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors_field_2\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors_field_3\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors_id\":\"industry_sector_id\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors_clang_id\":\"". (count(rex_clang::getAllIds()) > 1 ? "clang_id" : "") ."\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors_restriction_field\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors_restriction_operator\":\"=\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors_restriction_value\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors_url_param_key\":\"industry_sector_id\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors_seo_title\":\"seo_title\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors_seo_description\":\"seo_description\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors_seo_image\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors_sitemap_add\":\"1\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors_sitemap_frequency\":\"always\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors_sitemap_priority\":\"0.5\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors_sitemap_lastmod\":\"updatedate\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors_path_names\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors_path_categories\":\"0\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors_relation_field\":\"\"}', '', '[]', 'before', UNIX_TIMESTAMP(), 'd2u_machinery_addon_installer', UNIX_TIMESTAMP(), 'd2u_machinery_addon_installer');");
	}
	
	UrlGenerator::generatePathFile([]);
}
// 1.0.1 Update database
$sql->setQuery("SHOW COLUMNS FROM ". \rex::getTablePrefix() ."d2u_machinery_industry_sectors_lang LIKE 'updatedate';");
if($sql->getRows() == 0) {
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_machinery_industry_sectors_lang "
		. "ADD updatedate int(11) default NULL AFTER translation_needs_update;");
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_machinery_industry_sectors_lang "
		. "ADD updateuser varchar(255) collate utf8_general_ci default NULL AFTER updatedate;");
}

// Update language replacements
d2u_machinery_industry_sectors_lang_helper::factory()->install();