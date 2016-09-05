<?php
$sql = rex_sql::factory();

// Create tables
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". rex::getTablePrefix() ."d2u_machinery_industry_sectors (
	industry_sector_id int(10) unsigned NOT NULL auto_increment,
	online_status varchar(7) collate utf8_general_ci default NULL,
	pic varchar(100) collate utf8_general_ci default NULL,
	PRIMARY KEY (industry_sector_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1;");
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". rex::getTablePrefix() ."d2u_machinery_industry_sectors_lang (
	industry_sector_id int(10) NOT NULL,
	clang_id int(10) NOT NULL,
	name varchar(255) collate utf8_general_ci default NULL,
	teaser varchar(255) collate utf8_general_ci default NULL,
	translation_needs_update varchar(7) collate utf8_general_ci default NULL,
	PRIMARY KEY (industry_sector_id, clang_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1;");
// Alter machine table
$sql->setQuery("ALTER TABLE ". rex::getTablePrefix() ."d2u_machinery_machines "
	. "ADD industry_sector_ids VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;");