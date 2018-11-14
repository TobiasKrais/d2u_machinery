<?php
$sql = \rex_sql::factory();

// Delete tables
$sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_service_options');
$sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_service_options_lang');

$sql->setQuery('ALTER TABLE ' . \rex::getTablePrefix() . 'd2u_machinery_machines DROP service_option_ids;');

// Delete language replacements
if(!class_exists('d2u_machinery_service_options_lang_helper')) {
	// Load class in case addon is deactivated
	require_once 'lib/d2u_machinery_service_options_lang_helper.php';
}
d2u_machinery_service_options_lang_helper::factory()->uninstall();