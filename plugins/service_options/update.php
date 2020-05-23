<?php
// Update language replacements
if(!class_exists('d2u_machinery_service_options_lang_helper')) {
	// Load class in case addon is deactivated
	require_once 'lib/d2u_machinery_service_options_lang_helper.php';
}
d2u_machinery_service_options_lang_helper::factory()->install();

$sql = \rex_sql::factory();
// Update database to 1.2.6
$sql->setQuery("ALTER TABLE `". rex::getTablePrefix() ."d2u_machinery_service_options` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
$sql->setQuery("ALTER TABLE `". rex::getTablePrefix() ."d2u_machinery_service_options_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");

if (rex_version::compare($this->getVersion(), '1.2.6', '<')) {
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_machinery_service_options_lang ADD COLUMN `updatedate_new` DATETIME NOT NULL AFTER `updatedate`;");
	$sql->setQuery("UPDATE ". \rex::getTablePrefix() ."d2u_machinery_service_options_lang SET `updatedate_new` = FROM_UNIXTIME(`updatedate`);");
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_machinery_service_options_lang DROP updatedate;");
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_machinery_service_options_lang CHANGE `updatedate_new` `updatedate` DATETIME NOT NULL;");
}