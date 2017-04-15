<?php
$sql = rex_sql::factory();

// Create tables
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". rex::getTablePrefix() ."d2u_machinery_features (
	feature_id int(10) unsigned NOT NULL auto_increment,
	priority int(10) default NULL,
	category_ids varchar(255) collate utf8_general_ci default NULL,
	pic varchar(100) collate utf8_general_ci default NULL,
	PRIMARY KEY (feature_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1;");
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". rex::getTablePrefix() ."d2u_machinery_features_lang (
	feature_id int(10) NOT NULL,
	clang_id int(10) NOT NULL,
	name varchar(255) collate utf8_general_ci default NULL,
	title varchar(255) collate utf8_general_ci default NULL,
	description text collate utf8_general_ci default NULL,
	translation_needs_update varchar(7) collate utf8_general_ci default NULL,
	PRIMARY KEY (feature_id, clang_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1;");

// Alter machine table
$sql->setQuery("SHOW COLUMNS FROM ". rex::getTablePrefix() ."d2u_machinery_machines LIKE 'feature_ids';");
if($sql->getRows() == 0) {
	$sql->setQuery("ALTER TABLE ". rex::getTablePrefix() ."d2u_machinery_machines "
		. "ADD feature_ids VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;");
}

// Insert frontend translations
machine_features_extension_lang_helper::factory()->install();