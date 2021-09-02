<?php
\rex_sql_table::get(\rex::getTable('d2u_machinery_production_lines'))
	->ensureColumn(new rex_sql_column('production_line_id', 'int(10) unsigned', false, null, 'auto_increment'))
	->setPrimaryKey('production_line_id')
	->ensureColumn(new \rex_sql_column('complementary_machine_ids', 'VARCHAR(255)', true))
    ->ensureColumn(new \rex_sql_column('industry_sector_ids', 'VARCHAR(255)', true))
    ->ensureColumn(new \rex_sql_column('line_code', 'VARCHAR(50)', true))
    ->ensureColumn(new \rex_sql_column('machine_ids', 'VARCHAR(255)', true))
    ->ensureColumn(new \rex_sql_column('pictures', 'TEXT', true))
    ->ensureColumn(new \rex_sql_column('usp_ids', 'VARCHAR(255)', true))
    ->ensureColumn(new \rex_sql_column('video_ids', 'VARCHAR(255)', true))
    ->ensureColumn(new \rex_sql_column('online_status', 'VARCHAR(10)'))
    ->ensure();
\rex_sql_table::get(\rex::getTable('d2u_machinery_production_lines_lang'))
	->ensureColumn(new rex_sql_column('production_line_id', 'int(10) unsigned', false, null, 'auto_increment'))
    ->ensureColumn(new \rex_sql_column('clang_id', 'INT(11)', false, 1))
	->setPrimaryKey(['production_line_id', 'clang_id'])
    ->ensureColumn(new \rex_sql_column('name', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('teaser', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('description_long', 'TEXT', true))
    ->ensureColumn(new \rex_sql_column('description_short', 'TEXT', true))
    ->ensureColumn(new \rex_sql_column('translation_needs_update', 'VARCHAR(7)'))
    ->ensureColumn(new \rex_sql_column('updatedate', 'DATETIME'))
    ->ensureColumn(new \rex_sql_column('updateuser', 'VARCHAR(255)'))
    ->ensure();

\rex_sql_table::get(\rex::getTable('d2u_machinery_production_lines_usps'))
	->ensureColumn(new rex_sql_column('usp_id', 'int(10) unsigned', false, null, 'auto_increment'))
	->setPrimaryKey('usp_id')
    ->ensureColumn(new \rex_sql_column('picture', 'VARCHAR(100)', true))
    ->ensure();
\rex_sql_table::get(\rex::getTable('d2u_machinery_production_lines_usps_lang'))
	->ensureColumn(new rex_sql_column('usp_id', 'int(10) unsigned', false, null, 'auto_increment'))
    ->ensureColumn(new \rex_sql_column('clang_id', 'INT(11)', false, 1))
	->setPrimaryKey(['usp_id', 'clang_id'])
    ->ensureColumn(new \rex_sql_column('name', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('teaser', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('translation_needs_update', 'VARCHAR(7)'))
    ->ensureColumn(new \rex_sql_column('updatedate', 'DATETIME'))
    ->ensureColumn(new \rex_sql_column('updateuser', 'VARCHAR(255)'))
    ->ensure();

$sql = rex_sql::factory();
$sql->setQuery('CREATE OR REPLACE VIEW '. \rex::getTablePrefix() .'d2u_machinery_url_production_lines AS
	SELECT lang.production_line_id, lang.clang_id, lang.name, lang.name AS seo_title, lang.teaser AS seo_description, SUBSTRING_INDEX(production_lines.pictures, ",", 1) as picture, lang.updatedate
	FROM '. \rex::getTablePrefix() .'d2u_machinery_production_lines_lang AS lang
	LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_production_lines AS production_lines ON lang.production_line_id = production_lines.production_line_id
	LEFT JOIN '. \rex::getTablePrefix() .'clang AS clang ON lang.clang_id = clang.id
	WHERE clang.status = 1 AND production_lines.online_status = "online"');

// add url addon stuff
if(\rex_addon::get('url')->isAvailable()) {
	$clang_id = count(rex_clang::getAllIds()) == 1 ? rex_clang::getStartId() : 0;
	$article_id = rex_config::get('d2u_machinery', 'article_id', 0) > 0 ? rex_config::get('d2u_machinery', 'article_id') : rex_article::getSiteStartArticleId(); 
	if(rex_version::compare(\rex_addon::get('url')->getVersion(), '1.5', '>=')) {
		// Insert url schemes Version 2.x
		$sql->setQuery("DELETE FROM ". \rex::getTablePrefix() ."url_generator_profile WHERE `namespace` = 'production_line_id';");
		$sql->setQuery("INSERT INTO ". \rex::getTablePrefix() ."url_generator_profile (`namespace`, `article_id`, `clang_id`, `table_name`, `table_parameters`, `relation_1_table_name`, `relation_1_table_parameters`, `relation_2_table_name`, `relation_2_table_parameters`, `relation_3_table_name`, `relation_3_table_parameters`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
			('production_line_id', "
			. $article_id .", "
			. $clang_id .", "
			. "'1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_production_lines', "
			. "'{\"column_id\":\"production_line_id\",\"column_clang_id\":\"clang_id\",\"restriction_1_column\":\"\",\"restriction_1_comparison_operator\":\"=\",\"restriction_1_value\":\"\",\"restriction_2_logical_operator\":\"\",\"restriction_2_column\":\"\",\"restriction_2_comparison_operator\":\"=\",\"restriction_2_value\":\"\",\"restriction_3_logical_operator\":\"\",\"restriction_3_column\":\"\",\"restriction_3_comparison_operator\":\"=\",\"restriction_3_value\":\"\",\"column_segment_part_1\":\"name\",\"column_segment_part_2_separator\":\"\\/\",\"column_segment_part_2\":\"\",\"column_segment_part_3_separator\":\"\\/\",\"column_segment_part_3\":\"\",\"relation_1_column\":\"\",\"relation_1_position\":\"BEFORE\",\"relation_2_column\":\"\",\"relation_2_position\":\"BEFORE\",\"relation_3_column\":\"\",\"relation_3_position\":\"BEFORE\",\"append_user_paths\":\"\",\"append_structure_categories\":\"0\",\"column_seo_title\":\"seo_title\",\"column_seo_description\":\"seo_description\",\"column_seo_image\":\"picture\",\"sitemap_add\":\"1\",\"sitemap_frequency\":\"monthly\",\"sitemap_priority\":\"0.7\",\"column_sitemap_lastmod\":\"updatedate\"}', "
			. "'', '[]', '', '[]', '', '[]', CURRENT_TIMESTAMP, '". rex::getUser()->getValue('login') ."', CURRENT_TIMESTAMP, '". rex::getUser()->getValue('login') ."');");
	}
	else {
		// Insert url schemes Version 1.x
		$sql->setQuery("DELETE FROM ". \rex::getTablePrefix() ."url_generate WHERE `table` = '1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_production_lines';");
		$sql->setQuery("INSERT INTO `". \rex::getTablePrefix() ."url_generate` (`article_id`, `clang_id`, `url`, `table`, `table_parameters`, `relation_table`, `relation_table_parameters`, `relation_insert`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
			(". $article_id .", "
			. $clang_id .", "
			. "'', "
			. "'1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_production_lines', "
			. "'{\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_production_lines_field_1\":\"name\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_production_lines_field_2\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_production_lines_field_3\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_production_lines_id\":\"production_line_id\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_production_lines_clang_id\":\"". (count(rex_clang::getAllIds()) > 1 ? "clang_id" : "") ."\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_production_lines_restriction_field\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_production_lines_restriction_operator\":\"=\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_production_lines_restriction_value\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_production_lines_url_param_key\":\"production_line_id\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_production_lines_seo_title\":\"seo_title\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_production_lines_seo_description\":\"seo_description\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_production_lines_seo_image\":\"picture\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_production_lines_sitemap_add\":\"1\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_production_lines_sitemap_frequency\":\"always\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_production_lines_sitemap_priority\":\"0.5\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_production_lines_sitemap_lastmod\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_production_lines_path_names\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_production_lines_path_categories\":\"0\",\"1_xxx_". \rex::getTablePrefix() ."d2u_machinery_url_production_lines_relation_field\":\"\"}', "
			. "'', '[]', 'before', UNIX_TIMESTAMP(), 'd2u_machinery_addon_installer', UNIX_TIMESTAMP(), 'd2u_machinery_addon_installer');");
	}

	\d2u_addon_backend_helper::generateUrlCache('production_line_id');
}