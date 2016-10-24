<?php
$sql = rex_sql::factory();

// Create tables
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". rex::getTablePrefix() ."d2u_machinery_usage_areas (
	usage_area_id int(10) unsigned NOT NULL auto_increment,
	priority int(10) default NULL,
	category_ids varchar(255) collate utf8_general_ci default NULL,
	PRIMARY KEY (usage_area_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1;");
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". rex::getTablePrefix() ."d2u_machinery_usage_areas_lang (
	usage_area_id int(10) NOT NULL,
	clang_id int(10) NOT NULL,
	name varchar(255) collate utf8_general_ci default NULL,
	translation_needs_update varchar(7) collate utf8_general_ci default NULL,
	PRIMARY KEY (usage_area_id, clang_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1;");

// Alter machine table
$sql->setQuery("SHOW COLUMNS FROM ". rex::getTablePrefix() ."d2u_machinery_machines LIKE 'usage_area_ids';");
if($sql->getRows() == 0) {
	$sql->setQuery("ALTER TABLE ". rex::getTablePrefix() ."d2u_machinery_machines "
		. "ADD usage_area_ids VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;");
}

// Insert frontend translations
machine_usage_area_extension_lang_helper::factory()->install();