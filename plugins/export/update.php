<?php
$sql = \rex_sql::factory();

// 1.1.0 Update database
$sql->setQuery("SHOW COLUMNS FROM ". \rex::getTablePrefix() ."d2u_machinery_export_provider LIKE 'online_status';");
if($sql->getRows() == 0) {
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_machinery_export_provider "
		. "ADD online_status VARCHAR(10) NULL DEFAULT 'online' AFTER media_manager_type;");
}

// 1.2.6
$sql->setQuery("ALTER TABLE `". rex::getTablePrefix() ."d2u_machinery_export_provider` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
$sql->setQuery("ALTER TABLE `". rex::getTablePrefix() ."d2u_machinery_export_machines` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");

if (rex_version::compare($this->getVersion(), '1.2.6', '<')) {
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_machinery_export_machines ADD COLUMN `export_timestamp_new` DATETIME NOT NULL AFTER `export_timestamp`;");
	$sql->setQuery("UPDATE ". \rex::getTablePrefix() ."d2u_machinery_export_machines SET `export_timestamp_new` = FROM_UNIXTIME(`export_timestamp`);");
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_machinery_export_machines DROP export_timestamp;");
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_machinery_export_machines CHANGE `export_timestamp_new` `export_timestamp` DATETIME NOT NULL;");
}

// 1.3.2 remove Facebook support
\rex_sql_table::get(
    \rex::getTable('d2u_machinery_export_provider'))
    ->removeColumn('facebook_email')
    ->removeColumn('facebook_pageid')
    ->ensure();
$sql->setQuery("DELETE FROM `". rex::getTablePrefix() ."d2u_machinery_export_provider` WHERE type = 'facebook';");