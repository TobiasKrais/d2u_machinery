<?php
$sql = rex_sql::factory();

// Delete tables
$sql->setQuery('ALTER TABLE ' . rex::getTablePrefix() . 'd2u_machinery_categories
	DROP export_machinerypark_category_id,
	DROP export_europemachinery_category_id,
	DROP export_europemachinery_category_name,
	DROP export_europemachinery_category_name;');