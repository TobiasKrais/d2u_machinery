<?php
$sql = rex_sql::factory();

// Delete tables
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_machinery_usage_areas');
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_machinery_usage_areas_lang');

$sql->setQuery('ALTER TABLE ' . rex::getTablePrefix() . 'd2u_machinery_machines DROP usage_area_ids;');