<?php

\rex_sql_table::get(\rex::getTable('d2u_machinery_industry_sectors'))
    ->ensureColumn(new rex_sql_column('industry_sector_id', 'INT(11) unsigned', false, null, 'auto_increment'))
    ->setPrimaryKey('industry_sector_id')
    ->ensureColumn(new \rex_sql_column('online_status', 'VARCHAR(7)', true))
    ->ensureColumn(new \rex_sql_column('pic', 'VARCHAR(255)', true))
    ->ensureColumn(new \rex_sql_column('icon', 'VARCHAR(255)', true))
    ->ensure();
\rex_sql_table::get(\rex::getTable('d2u_machinery_industry_sectors_lang'))
    ->ensureColumn(new rex_sql_column('industry_sector_id', 'INT(11)', false))
    ->ensureColumn(new \rex_sql_column('clang_id', 'INT(11)', false))
    ->setPrimaryKey(['industry_sector_id', 'clang_id'])
    ->ensureColumn(new \rex_sql_column('name', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('teaser', 'VARCHAR(255)', true))
    ->ensureColumn(new \rex_sql_column('description', 'TEXT', true))
    ->ensureColumn(new \rex_sql_column('translation_needs_update', 'VARCHAR(7)', true))
    ->ensureColumn(new \rex_sql_column('updatedate', 'DATETIME', true))
    ->ensureColumn(new \rex_sql_column('updateuser', 'VARCHAR(255)', true))
    ->ensure();

// Alter machine table
\rex_sql_table::get(
    \rex::getTable('d2u_machinery_machines'))
    ->ensureColumn(new \rex_sql_column('industry_sector_ids', 'TEXT'))
    ->alter();

$sql = \rex_sql::factory();
// Create views for url addon
$sql->setQuery('CREATE OR REPLACE VIEW '. \rex::getTablePrefix() .'d2u_machinery_url_industry_sectors AS
	SELECT lang.industry_sector_id, lang.clang_id, lang.name, lang.name AS seo_title, lang.teaser AS seo_description, industries.pic AS picture, lang.updatedate
	FROM '. \rex::getTablePrefix() .'d2u_machinery_industry_sectors_lang AS lang
	LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_industry_sectors AS industries ON lang.industry_sector_id = industries.industry_sector_id
	LEFT JOIN '. \rex::getTablePrefix() .'clang AS clang ON lang.clang_id = clang.id
	WHERE clang.`status` = 1');

if (\rex_addon::get('url')->isAvailable()) {
    $clang_id = 1 === count(rex_clang::getAllIds()) ? rex_clang::getStartId() : 0;
    $article_id = rex_config::get('d2u_machinery', 'industry_sectors_article_id', rex_article::getSiteStartArticleId());

    // Insert url schemes Version 2.x
    $sql->setQuery('DELETE FROM '. \rex::getTablePrefix() ."url_generator_profile WHERE `namespace` = 'industry_sector_id';");
    $sql->setQuery('INSERT INTO '. \rex::getTablePrefix() ."url_generator_profile (`namespace`, `article_id`, `clang_id`, `table_name`, `table_parameters`, `relation_1_table_name`, `relation_1_table_parameters`, `relation_2_table_name`, `relation_2_table_parameters`, `relation_3_table_name`, `relation_3_table_parameters`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
		('industry_sector_id', "
        . $article_id .', '
        . $clang_id .', '
        . "'1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_industry_sectors', "
        . "'{\"column_id\":\"industry_sector_id\",\"column_clang_id\":\"clang_id\",\"restriction_1_column\":\"\",\"restriction_1_comparison_operator\":\"=\",\"restriction_1_value\":\"\",\"restriction_2_logical_operator\":\"\",\"restriction_2_column\":\"\",\"restriction_2_comparison_operator\":\"=\",\"restriction_2_value\":\"\",\"restriction_3_logical_operator\":\"\",\"restriction_3_column\":\"\",\"restriction_3_comparison_operator\":\"=\",\"restriction_3_value\":\"\",\"column_segment_part_1\":\"name\",\"column_segment_part_2_separator\":\"\\/\",\"column_segment_part_2\":\"\",\"column_segment_part_3_separator\":\"\\/\",\"column_segment_part_3\":\"\",\"relation_1_column\":\"\",\"relation_1_position\":\"BEFORE\",\"relation_2_column\":\"\",\"relation_2_position\":\"BEFORE\",\"relation_3_column\":\"\",\"relation_3_position\":\"BEFORE\",\"append_user_paths\":\"\",\"append_structure_categories\":\"0\",\"column_seo_title\":\"seo_title\",\"column_seo_description\":\"seo_description\",\"column_seo_image\":\"picture\",\"sitemap_add\":\"1\",\"sitemap_frequency\":\"monthly\",\"sitemap_priority\":\"0.5\",\"column_sitemap_lastmod\":\"updatedate\"}', "
        . "'', '[]', '', '[]', '', '[]', CURRENT_TIMESTAMP, '". (rex::getUser() instanceof rex_user ? rex::getUser()->getValue('login') : '') ."', CURRENT_TIMESTAMP, '". (rex::getUser() instanceof rex_user ? rex::getUser()->getValue('login') : '') ."');");

    \TobiasKrais\D2UHelper\BackendHelper::generateUrlCache('industry_sector_id');
}

// Insert / update frontend translations
if (!class_exists(d2u_machinery_industry_sectors_lang_helper::class)) {
    // Load class in case addon is deactivated
    require_once 'lib/d2u_machinery_industry_sectors_lang_helper.php';
}
d2u_machinery_industry_sectors_lang_helper::factory()->install();

// Default Config
if (!rex_config::has('d2u_machinery', 'industry_sectors_article_id')) {
    rex_config::set('d2u_machinery', 'industry_sectors_article_id', rex_config::get('d2u_machinery', 'article_id'));
}
