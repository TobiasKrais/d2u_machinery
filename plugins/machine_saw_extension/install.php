<?php
$sql = rex_sql::factory();

// Create database
// Extend category table
$sql->setQuery("ALTER TABLE ". rex::getTablePrefix() ."d2u_machinery_categories "
	. "ADD sawing_machines_cutting_range_title VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER pic_usage, "
	. "ADD sawing_machines_cutting_range_file VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER cutting_range_title;");