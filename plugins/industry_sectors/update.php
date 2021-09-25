<?php
$sql = \rex_sql::factory();
$sql->setQuery('CREATE OR REPLACE VIEW '. \rex::getTablePrefix() .'d2u_machinery_url_industry_sectors AS
	SELECT lang.industry_sector_id, lang.clang_id, lang.name, lang.name AS seo_title, lang.teaser AS seo_description, industries.pic AS picture, lang.updatedate
	FROM '. \rex::getTablePrefix() .'d2u_machinery_industry_sectors_lang AS lang
	LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_industry_sectors AS industries ON lang.industry_sector_id = industries.industry_sector_id
	LEFT JOIN '. \rex::getTablePrefix() .'clang AS clang ON lang.clang_id = clang.id
	WHERE clang.status = 1');

// 1.3.5
if(!rex_config::has('d2u_machinery', 'industry_sectors_article_id')) {
	rex_config::set('d2u_machinery', 'industry_sectors_article_id', rex_config::get('d2u_machinery', 'article_id'));
}

if(\rex_addon::get('url')->isAvailable()) {
	$clang_id = count(rex_clang::getAllIds()) == 1 ? rex_clang::getStartId() : 0;
	$article_id = rex_config::get('d2u_machinery', 'industry_sectors_article_id', rex_article::getSiteStartArticleId()); 
	if(rex_version::compare(\rex_addon::get('url')->getVersion(), '1.5', '>=')) {
		// Insert url schemes Version 2.x
		$sql->setQuery("DELETE FROM ". \rex::getTablePrefix() ."url_generator_profile WHERE `namespace` = 'industry_sector_id'");
		$sql->setQuery("INSERT INTO ". \rex::getTablePrefix() ."url_generator_profile (`namespace`, `article_id`, `clang_id`, `table_name`, `table_parameters`, `relation_1_table_name`, `relation_1_table_parameters`, `relation_2_table_name`, `relation_2_table_parameters`, `relation_3_table_name`, `relation_3_table_parameters`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
			('industry_sector_id', "
			. $article_id .", "
			. $clang_id .", "
			. "'1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_industry_sectors', "
			. "'{\"column_id\":\"industry_sector_id\",\"column_clang_id\":\"clang_id\",\"restriction_1_column\":\"\",\"restriction_1_comparison_operator\":\"=\",\"restriction_1_value\":\"\",\"restriction_2_logical_operator\":\"\",\"restriction_2_column\":\"\",\"restriction_2_comparison_operator\":\"=\",\"restriction_2_value\":\"\",\"restriction_3_logical_operator\":\"\",\"restriction_3_column\":\"\",\"restriction_3_comparison_operator\":\"=\",\"restriction_3_value\":\"\",\"column_segment_part_1\":\"industry_sector_id\",\"column_segment_part_2_separator\":\"\\/\",\"column_segment_part_2\":\"\",\"column_segment_part_3_separator\":\"\\/\",\"column_segment_part_3\":\"\",\"relation_1_column\":\"\",\"relation_1_position\":\"BEFORE\",\"relation_2_column\":\"\",\"relation_2_position\":\"BEFORE\",\"relation_3_column\":\"\",\"relation_3_position\":\"BEFORE\",\"append_user_paths\":\"\",\"append_structure_categories\":\"0\",\"column_seo_title\":\"seo_title\",\"column_seo_description\":\"seo_description\",\"column_seo_image\":\"picture\",\"sitemap_add\":\"1\",\"sitemap_frequency\":\"monthly\",\"sitemap_priority\":\"0.5\",\"column_sitemap_lastmod\":\"updatedate\"}', "
			. "'', '[]', '', '[]', '', '[]', CURRENT_TIMESTAMP, '". rex::getUser()->getValue('login') ."', CURRENT_TIMESTAMP, '". rex::getUser()->getValue('login') ."');");
		\d2u_addon_backend_helper::generateUrlCache('industry_sector_id');
	}
	else {
		// Insert url schemes Version 1.x
		$sql->setQuery("DELETE FROM ". \rex::getTablePrefix() ."url_generate WHERE `table` = '1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors'");
		$sql->setQuery("INSERT INTO `". \rex::getTablePrefix() ."url_generate` (`article_id`, `clang_id`, `url`, `table`, `table_parameters`, `relation_table`, `relation_table_parameters`, `relation_insert`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
			(". $article_id .", "
			. $clang_id .", "
			. "'', "
			. "'1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors', "
			. "'{\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors_field_1\":\"name\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors_field_2\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors_field_3\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors_id\":\"industry_sector_id\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors_clang_id\":\"". (count(rex_clang::getAllIds()) > 1 ? "clang_id" : "") ."\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors_restriction_field\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors_restriction_operator\":\"=\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors_restriction_value\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors_url_param_key\":\"industry_sector_id\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors_seo_title\":\"seo_title\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors_seo_description\":\"seo_description\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors_seo_image\":\"picture\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors_sitemap_add\":\"1\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors_sitemap_frequency\":\"always\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors_sitemap_priority\":\"0.5\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors_sitemap_lastmod\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors_path_names\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors_path_categories\":\"0\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_industry_sectors_relation_field\":\"\"}', "
			. "'', '[]', 'before', UNIX_TIMESTAMP(), 'd2u_machinery_addon_installer', UNIX_TIMESTAMP(), 'd2u_machinery_addon_installer');");
		\d2u_addon_backend_helper::generateUrlCache();
	}
}

// Update database
\rex_sql_table::get(
    \rex::getTable('d2u_machinery_industry_sectors'))
    ->ensureColumn(new \rex_sql_column('icon', 'VARCHAR(100)', TRUE))
    ->alter();

\rex_sql_table::get(
    \rex::getTable('d2u_machinery_industry_sectors_lang'))
    ->ensureColumn(new \rex_sql_column('description', 'TEXT', TRUE))
    ->ensureColumn(new \rex_sql_column('updatedate', 'INT(11)', TRUE))
    ->ensureColumn(new \rex_sql_column('updateuser', 'VARCHAR(255)', TRUE))
    ->alter();


// Update language replacements
if(!class_exists('d2u_machinery_industry_sectors_lang_helper')) {
	// Load class in case addon is deactivated
	require_once 'lib/d2u_machinery_industry_sectors_lang_helper.php';
}
d2u_machinery_industry_sectors_lang_helper::factory()->install();

// 1.2.6
$sql->setQuery("ALTER TABLE `". rex::getTablePrefix() ."d2u_machinery_industry_sectors` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
$sql->setQuery("ALTER TABLE `". rex::getTablePrefix() ."d2u_machinery_industry_sectors_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");

if (rex_version::compare($this->getVersion(), '1.2.6', '<')) {
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_machinery_industry_sectors_lang ADD COLUMN `updatedate_new` DATETIME NOT NULL AFTER `updatedate`;");
	$sql->setQuery("UPDATE ". \rex::getTablePrefix() ."d2u_machinery_industry_sectors_lang SET `updatedate_new` = FROM_UNIXTIME(`updatedate`);");
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_machinery_industry_sectors_lang DROP updatedate;");
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_machinery_industry_sectors_lang CHANGE `updatedate_new` `updatedate` DATETIME NOT NULL;");
}