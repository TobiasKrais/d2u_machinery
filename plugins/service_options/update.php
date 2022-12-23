<?php
$this->includeFile(__DIR__.'/install.php'); /** @phpstan-ignore-line */

$sql = \rex_sql::factory();
if (rex_plugin::get('d2u_machinery', 'service_options') instanceof rex_plugin && rex_version::compare(rex_plugin::get('d2u_machinery', 'service_options')->getVersion(), '1.2.6', '<')) {
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_machinery_service_options_lang ADD COLUMN `updatedate_new` DATETIME NOT NULL AFTER `updatedate`;");
	$sql->setQuery("UPDATE ". \rex::getTablePrefix() ."d2u_machinery_service_options_lang SET `updatedate_new` = FROM_UNIXTIME(`updatedate`);");
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_machinery_service_options_lang DROP updatedate;");
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_machinery_service_options_lang CHANGE `updatedate_new` `updatedate` DATETIME NOT NULL;");
}