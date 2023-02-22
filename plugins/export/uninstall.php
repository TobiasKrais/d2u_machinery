<?php

$sql = \rex_sql::factory();

// Delete tables
// Delete tables
$sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_export_machines');
$sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_export_provider');

// Delete category extensions
\rex_sql_table::get(
    \rex::getTable('d2u_machinery_categories'))
    ->removeColumn('export_machinerypark_category_id')
    ->removeColumn('export_europemachinery_category_id')
    ->removeColumn('export_europemachinery_category_name')
    ->removeColumn('export_mascus_category_name')
    ->ensure();

// Delete Autoexport if activated
if (!class_exists('d2u_machinery_export_cronjob')) {
    // Load class in case addon is deactivated
    require_once 'lib/d2u_machinery_export_cronjob.php';
}
$export_cronjob = d2u_machinery_export_cronjob::factory();
if ($export_cronjob->isInstalled()) {
    $export_cronjob->delete();
}

// Delete language replacements
if (!class_exists('d2u_machinery_export_lang_helper')) {
    // Load class in case addon is deactivated
    require_once 'lib/d2u_machinery_export_lang_helper.php';
}
d2u_machinery_export_lang_helper::factory()->uninstall();
