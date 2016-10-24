<?php
$sql = rex_sql::factory();

// Delete views
$sql->setQuery('DROP VIEW IF EXISTS ' . rex::getTablePrefix() . 'd2u_machinery_url_machines');
$sql->setQuery('DROP VIEW IF EXISTS ' . rex::getTablePrefix() . 'd2u_machinery_url_machine_categories');
// Delete url schemes
if(rex_addon::get('url')->isAvailable()) {
	$sql->setQuery("DELETE FROM `". rex::getTablePrefix() ."url_generate` WHERE `table` LIKE '%d2u_machinery_url_machine%'");
}

// Delete language replacements
d2u_machinery_lang_helper::factory()->uninstall();

// Delete tables
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_machinery_machines');
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_machinery_machines_lang');
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_machinery_categories');
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_machinery_categories_lang');

// Delete Media Manager media types
$sql->setQuery("DELETE FROM ". rex::getTablePrefix() ."media_manager_type WHERE name LIKE 'd2u_machinery%'");
$sql->setQuery("DELETE FROM ". rex::getTablePrefix() ."media_manager_type_effect WHERE createuser = 'd2u_machinery'");

// Delete e-mail template
$sql->setQuery("DELETE FROM ". rex::getTablePrefix() ."yform_email_template WHERE name LIKE 'd2u_machinery%'");