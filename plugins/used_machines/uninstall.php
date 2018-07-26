<?php
$sql = \rex_sql::factory();

// Delete views
$sql->setQuery('DROP VIEW IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_url_used_machines');
$sql->setQuery('DROP VIEW IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_url_used_machine_categories');
// Delete url schemes
if(\rex_addon::get('url')->isAvailable()) {
	$sql->setQuery("DELETE FROM `". \rex::getTablePrefix() ."url_generate` WHERE `table` LIKE '%d2u_machinery_url_used_machine%'");
}

// Delete language replacements
d2u_machinery_used_machines_lang_helper::factory()->uninstall();

// Delete tables
$sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_used_machines');
$sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_used_machines_lang');