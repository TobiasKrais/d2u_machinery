<?php
$sql = rex_sql::factory();

// Delete tables
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_machinery_agitator_types');
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_machinery_agitator_types_lang');
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_machinery_agitators');
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_machinery_agitators_lang');

$sql->setQuery('ALTER TABLE ' . rex::getTablePrefix() . 'd2u_machinery_machines DROP agitator_type_id;');
$sql->setQuery('ALTER TABLE ' . rex::getTablePrefix() . 'd2u_machinery_machines DROP viscosity;');