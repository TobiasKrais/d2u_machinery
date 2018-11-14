<?php
$sql = \rex_sql::factory();

// Delete views
$sql->setQuery('DROP VIEW IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_url_used_machines_rent');
$sql->setQuery('DROP VIEW IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_url_used_machine_categories_rent');
$sql->setQuery('DROP VIEW IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_url_used_machines_sale');
$sql->setQuery('DROP VIEW IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_url_used_machine_categories_sale');
// Delete url schemes
if(\rex_addon::get('url')->isAvailable()) {
	$sql->setQuery("DELETE FROM `". \rex::getTablePrefix() ."url_generate` WHERE `table` LIKE '%d2u_machinery_url_used_machine%'");
}

// Delete tables
$sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_used_machines');
$sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_used_machines_lang');

// Delete language replacements
if(!class_exists('d2u_machinery_used_machines_lang_helper')) {
	// Load class in case addon is deactivated
	require_once 'lib/d2u_machinery_used_machines_lang_helper.php';
}
d2u_machinery_used_machines_lang_helper::factory()->uninstall();