<?php

$sql = \rex_sql::factory();

// Delete views
$sql->setQuery('DROP VIEW IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_url_industry_sectors');
// Delete url scheme
if (\rex_addon::get('url')->isAvailable()) {
    $sql->setQuery('DELETE FROM '. \rex::getTablePrefix() ."url_generator_profile WHERE `namespace` = 'industry_sector_id';");
}

// Delete tables
$sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_industry_sectors');
$sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_industry_sectors_lang');

\rex_sql_table::get(
    \rex::getTable('d2u_machinery_machines'))
    ->removeColumn('industry_sector_ids')
    ->ensure();

// Delete language replacements
if (!class_exists(d2u_machinery_industry_sectors_lang_helper::class)) {
    // Load class in case addon is deactivated
    require_once 'lib/d2u_machinery_industry_sectors_lang_helper.php';
}
d2u_machinery_industry_sectors_lang_helper::factory()->uninstall();
