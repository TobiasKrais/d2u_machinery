<?php

$sql = \rex_sql::factory();

// Delete tables
$sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_equipments');
$sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_equipments_lang');
$sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_equipment_groups');
$sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_equipment_groups_lang');

\rex_sql_table::get(
    \rex::getTable('d2u_machinery_machines'))
    ->removeColumn('equipment_ids')
    ->ensure();

// Delete language replacements
if (!class_exists(d2u_machinery_equipment_lang_helper::class)) {
    // Load class in case addon is deactivated
    require_once 'lib/d2u_machinery_equipment_lang_helper.php';
}
d2u_machinery_equipment_lang_helper::factory()->uninstall();
