<?php
$sql = \rex_sql::factory();

// Delete tables
// Delete tables
$sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_export_machines');
$sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_export_provider');

// Delete category extensions
$sql->setQuery('ALTER TABLE ' . \rex::getTablePrefix() . 'd2u_machinery_categories
	DROP export_machinerypark_category_id,
	DROP export_europemachinery_category_id,
	DROP export_europemachinery_category_name,
	DROP export_mascus_category_name;');

// Delete Autoexport if activated
if(!class_exists('export_backend_helper')) {
	// Load class in case addon is deactivated
	require_once 'lib/export_backend_helper.php';
}
$export_cronjob = d2u_machinery_export_cronjob::factory();
if($export_cronjob->isInstalled()) {
	$export_cronjob->delete();
}

// Delete language replacements
if(!class_exists('d2u_machinery_export_lang_helper')) {
	// Load class in case addon is deactivated
	require_once 'lib/d2u_machinery_export_lang_helper.php';
}
d2u_machinery_export_lang_helper::factory()->uninstall();