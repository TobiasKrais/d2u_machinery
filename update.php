<?php

// use path relative to __DIR__ to get correct path in update temp dir
$this->includeFile(__DIR__.'/install.php'); /** @phpstan-ignore-line */

// 1.1.3 Update database
$sql = \rex_sql::factory();
$sql->setQuery('SHOW COLUMNS FROM '. \rex::getTablePrefix() ."d2u_machinery_categories LIKE 'videomanager_ids';");
if ($sql->getRows() > 0) {
    $sql->setQuery('ALTER TABLE '. \rex::getTablePrefix() .'d2u_machinery_categories '
        . 'CHANGE videomanager_ids video_ids varchar(255) collate utf8mb4_unicode_ci default NULL;');
}
$sql->setQuery('SHOW COLUMNS FROM '. \rex::getTablePrefix() ."d2u_machinery_machines LIKE 'videomanager_ids';");
if ($sql->getRows() > 0) {
    $sql->setQuery('ALTER TABLE '. \rex::getTablePrefix() .'d2u_machinery_machines '
        . 'CHANGE videomanager_ids video_ids varchar(255) collate utf8mb4_unicode_ci default NULL;');
}

// 1.2.6
if (rex_version::compare($this->getVersion(), '1.2.6', '<')) { /** @phpstan-ignore-line */
    $sql->setQuery('ALTER TABLE '. \rex::getTablePrefix() .'d2u_machinery_machines_lang ADD COLUMN `updatedate_new` DATETIME NOT NULL AFTER `updatedate`;');
    $sql->setQuery('UPDATE '. \rex::getTablePrefix() .'d2u_machinery_machines_lang SET `updatedate_new` = FROM_UNIXTIME(`updatedate`);');
    $sql->setQuery('ALTER TABLE '. \rex::getTablePrefix() .'d2u_machinery_machines_lang DROP updatedate;');
    $sql->setQuery('ALTER TABLE '. \rex::getTablePrefix() .'d2u_machinery_machines_lang CHANGE `updatedate_new` `updatedate` DATETIME NOT NULL;');

    $sql->setQuery('ALTER TABLE '. \rex::getTablePrefix() .'d2u_machinery_categories_lang ADD COLUMN `updatedate_new` DATETIME NOT NULL AFTER `updatedate`;');
    $sql->setQuery('UPDATE '. \rex::getTablePrefix() .'d2u_machinery_categories_lang SET `updatedate_new` = FROM_UNIXTIME(`updatedate`);');
    $sql->setQuery('ALTER TABLE '. \rex::getTablePrefix() .'d2u_machinery_categories_lang DROP updatedate;');
    $sql->setQuery('ALTER TABLE '. \rex::getTablePrefix() .'d2u_machinery_categories_lang CHANGE `updatedate_new` `updatedate` DATETIME NOT NULL;');
}

// Remove not needed sprog translations
if (rex_version::compare($this->getVersion(), '1.3.5', '<')) { /** @phpstan-ignore-line */
    $sql->setQuery('DELETE FROM '. \rex::getTablePrefix() ."sprog_wildcard WHERE `wildcard` = 'd2u_machinery_form_captcha';");
    $sql->setQuery('DELETE FROM '. \rex::getTablePrefix() ."sprog_wildcard WHERE `wildcard` = 'd2u_machinery_form_validate_captcha';");
    $sql->setQuery('DELETE FROM '. \rex::getTablePrefix() ."sprog_wildcard WHERE `wildcard` = 'd2u_machinery_form_city';");
    $sql->setQuery('DELETE FROM '. \rex::getTablePrefix() ."sprog_wildcard WHERE `wildcard` = 'd2u_machinery_form_address';");
    $sql->setQuery('DELETE FROM '. \rex::getTablePrefix() ."sprog_wildcard WHERE `wildcard` = 'd2u_machinery_form_country';");
    $sql->setQuery('DELETE FROM '. \rex::getTablePrefix() ."sprog_wildcard WHERE `wildcard` = 'd2u_machinery_form_email';");
    $sql->setQuery('DELETE FROM '. \rex::getTablePrefix() ."sprog_wildcard WHERE `wildcard` = 'd2u_machinery_form_message';");
    $sql->setQuery('DELETE FROM '. \rex::getTablePrefix() ."sprog_wildcard WHERE `wildcard` = 'd2u_machinery_form_name';");
    $sql->setQuery('DELETE FROM '. \rex::getTablePrefix() ."sprog_wildcard WHERE `wildcard` = 'd2u_machinery_form_phone';");
    $sql->setQuery('DELETE FROM '. \rex::getTablePrefix() ."sprog_wildcard WHERE `wildcard` = 'd2u_machinery_form_please_call';");
    $sql->setQuery('DELETE FROM '. \rex::getTablePrefix() ."sprog_wildcard WHERE `wildcard` = 'd2u_machinery_form_privacy_policy';");
    $sql->setQuery('DELETE FROM '. \rex::getTablePrefix() ."sprog_wildcard WHERE `wildcard` = 'd2u_machinery_form_required';");
    $sql->setQuery('DELETE FROM '. \rex::getTablePrefix() ."sprog_wildcard WHERE `wildcard` = 'd2u_machinery_form_send';");
    $sql->setQuery('DELETE FROM '. \rex::getTablePrefix() ."sprog_wildcard WHERE `wildcard` = 'd2u_machinery_form_thanks';");
    $sql->setQuery('DELETE FROM '. \rex::getTablePrefix() ."sprog_wildcard WHERE `wildcard` = 'd2u_machinery_form_validate_address';");
    $sql->setQuery('DELETE FROM '. \rex::getTablePrefix() ."sprog_wildcard WHERE `wildcard` = 'd2u_machinery_form_validate_city';");
    $sql->setQuery('DELETE FROM '. \rex::getTablePrefix() ."sprog_wildcard WHERE `wildcard` = 'd2u_machinery_form_validate_email';");
    $sql->setQuery('DELETE FROM '. \rex::getTablePrefix() ."sprog_wildcard WHERE `wildcard` = 'd2u_machinery_form_validate_name';");
    $sql->setQuery('DELETE FROM '. \rex::getTablePrefix() ."sprog_wildcard WHERE `wildcard` = 'd2u_machinery_form_validate_phone';");
    $sql->setQuery('DELETE FROM '. \rex::getTablePrefix() ."sprog_wildcard WHERE `wildcard` = 'd2u_machinery_form_validate_privacy_policy';");
    $sql->setQuery('DELETE FROM '. \rex::getTablePrefix() ."sprog_wildcard WHERE `wildcard` = 'd2u_machinery_form_validate_spambots';");
    $sql->setQuery('DELETE FROM '. \rex::getTablePrefix() ."sprog_wildcard WHERE `wildcard` = 'd2u_machinery_form_validate_spam_detected';");
    $sql->setQuery('DELETE FROM '. \rex::getTablePrefix() ."sprog_wildcard WHERE `wildcard` = 'd2u_machinery_form_validate_title';");
    $sql->setQuery('DELETE FROM '. \rex::getTablePrefix() ."sprog_wildcard WHERE `wildcard` = 'd2u_machinery_form_validate_zip';");
    $sql->setQuery('DELETE FROM '. \rex::getTablePrefix() ."sprog_wildcard WHERE `wildcard` = 'd2u_machinery_form_vorname';");
    $sql->setQuery('DELETE FROM '. \rex::getTablePrefix() ."sprog_wildcard WHERE `wildcard` = 'd2u_machinery_form_zip';");
}
