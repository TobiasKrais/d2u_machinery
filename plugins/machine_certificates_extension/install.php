<?php
$sql = \rex_sql::factory();

// Create tables
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". \rex::getTablePrefix() ."d2u_machinery_certificates (
	certificate_id int(10) unsigned NOT NULL auto_increment,
	pic varchar(100) collate utf8mb4_unicode_ci default NULL,
	PRIMARY KEY (certificate_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;");
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". \rex::getTablePrefix() ."d2u_machinery_certificates_lang (
	certificate_id int(10) NOT NULL,
	clang_id int(10) NOT NULL,
	name varchar(255) collate utf8mb4_unicode_ci default NULL,
	description varchar(255) collate utf8mb4_unicode_ci default NULL,
	translation_needs_update varchar(7) collate utf8mb4_unicode_ci default NULL,
	PRIMARY KEY (certificate_id, clang_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;");

// Alter machine table
$sql->setQuery("SHOW COLUMNS FROM ". \rex::getTablePrefix() ."d2u_machinery_machines LIKE 'certificate_ids';");
if($sql->getRows() == 0) {
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_machinery_machines "
		. "ADD certificate_ids TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;");
}