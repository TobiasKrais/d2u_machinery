<?php
$sql = \rex_sql::factory();

// Create tables: agitator types
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". \rex::getTablePrefix() ."d2u_machinery_agitator_types (
	agitator_type_id int(10) unsigned NOT NULL auto_increment,
	agitator_ids varchar(255) collate utf8_general_ci default NULL,
	pic varchar(100) collate utf8_general_ci default NULL,
	PRIMARY KEY (agitator_type_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1;");
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". \rex::getTablePrefix() ."d2u_machinery_agitator_types_lang (
	agitator_type_id int(10) NOT NULL,
	clang_id int(10) NOT NULL,
	name varchar(255) collate utf8_general_ci default NULL,
	translation_needs_update varchar(7) collate utf8_general_ci default NULL,
	PRIMARY KEY (agitator_type_id, clang_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1;");
// Alter machine table
$sql->setQuery("SHOW COLUMNS FROM ". \rex::getTablePrefix() ."d2u_machinery_machines LIKE 'agitator_type_id';");
if($sql->getRows() == 0) {
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_machinery_machines "
		. "ADD agitator_type_id INT(10) NULL DEFAULT NULL;");
}
$sql->setQuery("SHOW COLUMNS FROM ". \rex::getTablePrefix() ."d2u_machinery_machines LIKE 'viscosity';");
if($sql->getRows() == 0) {
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_machinery_machines "
		. "ADD viscosity INT(10) NULL DEFAULT NULL;");
}
// Alter category table
$sql->setQuery("SHOW COLUMNS FROM ". \rex::getTablePrefix() ."d2u_machinery_categories LIKE 'show_agitators';");
if($sql->getRows() == 0) {
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_machinery_categories "
		. "ADD show_agitators VARCHAR(4) CHARACTER SET utf8 COLLATE utf8_general_ci;");
}

// Create tables: agitators
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". \rex::getTablePrefix() ."d2u_machinery_agitators (
	agitator_id int(10) unsigned NOT NULL auto_increment,
	pic varchar(100) collate utf8_general_ci default NULL,
	PRIMARY KEY (agitator_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1;");
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". \rex::getTablePrefix() ."d2u_machinery_agitators_lang (
	agitator_id int(10) NOT NULL,
	clang_id int(10) NOT NULL,
	name varchar(255) collate utf8_general_ci default NULL,
	description text collate utf8_general_ci default NULL,
	translation_needs_update varchar(7) collate utf8_general_ci default NULL,
	PRIMARY KEY (agitator_id, clang_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1;");

// Insert frontend translations
if(class_exists(d2u_machinery_machine_agitator_extension_lang_helper)) {
	d2u_machinery_machine_agitator_extension_lang_helper::factory()->install();
}