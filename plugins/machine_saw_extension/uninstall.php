<?php
$sql = rex_sql::factory();

// Delete tables
$sql->setQuery('ALTER TABLE ' . rex::getTablePrefix() . 'd2u_machinery_categories
	DROP sawing_machines_cutting_range_title,
	DROP sawing_machines_cutting_range_file;');