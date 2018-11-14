<?php
$sql = \rex_sql::factory();

// Delete tables
$sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_features');
$sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_features_lang');

$sql->setQuery('ALTER TABLE ' . \rex::getTablePrefix() . 'd2u_machinery_machines DROP feature_ids;');

// Delete language replacements
if(!class_exists('d2u_machinery_machine_features_extension_lang_helper')) {
	// Load class in case addon is deactivated
	require_once 'lib/d2u_machinery_machine_features_extension_lang_helper.php';
}
d2u_machinery_machine_features_extension_lang_helper::factory()->uninstall();