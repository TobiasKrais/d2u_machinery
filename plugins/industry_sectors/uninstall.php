<?php
$sql = rex_sql::factory();

// Delete tables
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_machinery_industry_sectors');
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_machinery_industry_sectors_lang');

$sql->setQuery('ALTER TABLE ' . rex::getTablePrefix() . 'd2u_machinery_machines DROP industry_sector_ids;');