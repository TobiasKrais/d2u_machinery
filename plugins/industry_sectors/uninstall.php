<?php
$sql = rex_sql::factory();

// Delete views
$sql->setQuery('DROP VIEW IF EXISTS ' . rex::getTablePrefix() . 'd2u_machinery_url_industry_sectors');
// Delete url scheme
if(rex_addon::get('url')->isAvailable()) {
	$sql->setQuery("DELETE FROM `". rex::getTablePrefix() ."url_generate` WHERE `table` LIKE '%d2u_machinery_url_industry_sectors%'");
}

// Delete tables
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_machinery_industry_sectors');
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_machinery_industry_sectors_lang');

$sql->setQuery('ALTER TABLE ' . rex::getTablePrefix() . 'd2u_machinery_machines DROP industry_sector_ids;');

// Delete language replacements
d2u_machinery_industry_sectors_lang_helper::factory()->uninstall();