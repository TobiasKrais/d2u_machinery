<?php
$sql = \rex_sql::factory();

// Delete tables
$sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_agitator_types');
$sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_agitator_types_lang');
$sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_agitators');
$sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_agitators_lang');

\rex_sql_table::get(
    \rex::getTable('d2u_machinery_machines'))
    ->removeColumn('agitator_type_id')
    ->removeColumn('viscosity')
    ->ensure();
\rex_sql_table::get(
    \rex::getTable('d2u_machinery_categories'))
    ->removeColumn('show_agitators')
    ->ensure();

// Delete language replacements
if(!class_exists('d2u_machinery_machine_agitator_extension_lang_helper')) {
	// Load class in case addon is deactivated
	require_once 'lib/d2u_machinery_machine_agitator_extension_lang_helper.php';
}
d2u_machinery_machine_agitator_extension_lang_helper::factory()->uninstall();