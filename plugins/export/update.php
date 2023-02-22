<?php

// use path relative to __DIR__ to get correct path in update temp dir
$this->includeFile(__DIR__.'/install.php'); /** @phpstan-ignore-line */

$sql = \rex_sql::factory();
if (rex_plugin::get('d2u_machinery', 'export') instanceof rex_plugin && rex_version::compare(rex_plugin::get('d2u_machinery', 'export')->getVersion(), '1.2.6', '<')) {
    $sql->setQuery('ALTER TABLE '. \rex::getTablePrefix() .'d2u_machinery_export_machines ADD COLUMN `export_timestamp_new` DATETIME NOT NULL AFTER `export_timestamp`;');
    $sql->setQuery('UPDATE '. \rex::getTablePrefix() .'d2u_machinery_export_machines SET `export_timestamp_new` = FROM_UNIXTIME(`export_timestamp`);');
    $sql->setQuery('ALTER TABLE '. \rex::getTablePrefix() .'d2u_machinery_export_machines DROP export_timestamp;');
    $sql->setQuery('ALTER TABLE '. \rex::getTablePrefix() .'d2u_machinery_export_machines CHANGE `export_timestamp_new` `export_timestamp` DATETIME NOT NULL;');
}

// 1.3.2 remove Facebook/Twitter support
\rex_sql_table::get(
    \rex::getTable('d2u_machinery_export_provider'))
    ->removeColumn('facebook_email')
    ->removeColumn('facebook_pageid')
    ->removeColumn('twitter_id')
    ->ensure();
$sql->setQuery('DELETE FROM `'. rex::getTablePrefix() ."d2u_machinery_export_provider` WHERE type = 'facebook';");
