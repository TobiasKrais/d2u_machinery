<?php
$sql = rex_sql::factory();

// Create database
// Extend category table
$sql->setQuery("ALTER TABLE ". rex::getTablePrefix() ."d2u_machinery_categories "
	. "ADD export_machinerypark_category_id INT(10) NULL DEFAULT NULL AFTER pic_usage, "
	. "ADD export_europemachinery_category_id INT(10) NULL DEFAULT NULL AFTER export_machinerypark_category_id, "
	. "ADD export_europemachinery_category_name VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER export_europemachinery_category_id, "
	. "ADD export_mascus_category_name VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER export_europemachinery_category_name;");