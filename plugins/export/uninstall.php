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
if(export_backend_helper::autoexportIsInstalled()) {
	export_backend_helper::autoexportDelete();
}

// Delete language replacements
d2u_machinery_export_lang_helper::factory()->uninstall();