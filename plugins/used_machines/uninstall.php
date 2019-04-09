<?php
$sql = \rex_sql::factory();

// Delete views
$sql->setQuery('DROP VIEW IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_url_used_machines_rent');
$sql->setQuery('DROP VIEW IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_url_used_machine_categories_rent');
$sql->setQuery('DROP VIEW IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_url_used_machines_sale');
$sql->setQuery('DROP VIEW IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_url_used_machine_categories_sale');
// Delete url schemes
if(\rex_addon::get('url')->isAvailable()) {
	if(rex_string::versionCompare(\rex_addon::get('url')->getVersion(), '1.5', '>=')) {
		$sql->setQuery("DELETE FROM ". \rex::getTablePrefix() ."rex_url_generator_profile WHERE `namespace` = 'used_rent_machine_id';");
		$sql->setQuery("DELETE FROM ". \rex::getTablePrefix() ."rex_url_generator_profile WHERE `namespace` = 'used_rent_category_id';");		
		$sql->setQuery("DELETE FROM ". \rex::getTablePrefix() ."rex_url_generator_profile WHERE `namespace` = 'used_sale_machine_id';");
		$sql->setQuery("DELETE FROM ". \rex::getTablePrefix() ."rex_url_generator_profile WHERE `namespace` = 'used_sale_category_id';");		
	}
	else {
		$sql->setQuery("DELETE FROM `". \rex::getTablePrefix() ."url_generate` WHERE `table` LIKE '%d2u_machinery_url_used_machine%'");
	}
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