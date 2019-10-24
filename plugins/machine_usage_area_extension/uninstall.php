<?php
$sql = \rex_sql::factory();

// Delete tables
$sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_usage_areas');
$sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_usage_areas_lang');

\rex_sql_table::get(
    \rex::getTable('d2u_machinery_machines'))
    ->removeColumn('usage_area_ids')
    ->ensure();

// Delete language replacements
if(!class_exists('d2u_machinery_machine_usage_area_extension_lang_helper')) {
	// Load class in case addon is deactivated
	require_once 'lib/d2u_machinery_machine_usage_area_extension_lang_helper.php';
}
d2u_machinery_machine_usage_area_extension_lang_helper::factory()->uninstall();