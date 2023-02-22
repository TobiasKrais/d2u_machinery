<?php

if (rex_plugin::get('d2u_machinery', 'used_machines') instanceof rex_plugin && rex_version::compare(rex_plugin::get('d2u_machinery', 'used_machines')->getVersion(), '1.2.6', '<')) {
    $sql = rex_sql::factory();
    $sql->setQuery('ALTER TABLE '. \rex::getTablePrefix() .'d2u_machinery_used_machines_lang ADD COLUMN `updatedate_new` DATETIME NOT NULL AFTER `updatedate`;');
    $sql->setQuery('UPDATE '. \rex::getTablePrefix() .'d2u_machinery_used_machines_lang SET `updatedate_new` = FROM_UNIXTIME(`updatedate`);');
    $sql->setQuery('ALTER TABLE '. \rex::getTablePrefix() .'d2u_machinery_used_machines_lang DROP updatedate;');
    $sql->setQuery('ALTER TABLE '. \rex::getTablePrefix() .'d2u_machinery_used_machines_lang CHANGE `updatedate_new` `updatedate` DATETIME NOT NULL;');
}

// use path relative to __DIR__ to get correct path in update temp dir
$this->includeFile(__DIR__.'/install.php'); /** @phpstan-ignore-line */
