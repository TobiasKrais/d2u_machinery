<?php

// use path relative to __DIR__ to get correct path in update temp dir
$this->includeFile(__DIR__.'/install.php'); /** @phpstan-ignore-line */

$sql = \rex_sql::factory();
if (rex_plugin::get('d2u_machinery', 'industry_sectors') instanceof rex_plugin && rex_version::compare(rex_plugin::get('d2u_machinery', 'industry_sectors')->getVersion(), '1.2.6', '<')) {
    $sql->setQuery('ALTER TABLE '. \rex::getTablePrefix() .'d2u_machinery_industry_sectors_lang ADD COLUMN `updatedate_new` DATETIME NOT NULL AFTER `updatedate`;');
    $sql->setQuery('UPDATE '. \rex::getTablePrefix() .'d2u_machinery_industry_sectors_lang SET `updatedate_new` = FROM_UNIXTIME(`updatedate`);');
    $sql->setQuery('ALTER TABLE '. \rex::getTablePrefix() .'d2u_machinery_industry_sectors_lang DROP updatedate;');
    $sql->setQuery('ALTER TABLE '. \rex::getTablePrefix() .'d2u_machinery_industry_sectors_lang CHANGE `updatedate_new` `updatedate` DATETIME NOT NULL;');
}
