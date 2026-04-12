<?php

use TobiasKrais\D2UMachinery\Extension;

// use path relative to __DIR__ to get correct path in update temp dir
$this->includeFile(__DIR__.'/install.php'); /** @phpstan-ignore-line */
Extension::migrateLegacyStates((string) $this->getVersion()); /** @phpstan-ignore-line */
Extension::runLegacyVersionMigrations((string) $this->getVersion()); /** @phpstan-ignore-line */

$sql = \rex_sql::factory();
$sql->setQuery('SHOW TABLES LIKE "'. \rex::getTable('d2u_machinery_steel_supply') .'"');
if ($sql->getRows() > 0) {
    $sql->setQuery('SHOW COLUMNS FROM '. \rex::getTablePrefix() ."d2u_machinery_steel_supply LIKE 'videomanager_id';");
    if ($sql->getRows() > 0) {
        $sql->setQuery('ALTER TABLE '. \rex::getTablePrefix() .'d2u_machinery_steel_supply '
            . 'CHANGE `videomanager_id` `video_id` INT(10) NULL DEFAULT NULL;');
    }

    $sql->setQuery('SHOW COLUMNS FROM '. \rex::getTablePrefix() ."d2u_machinery_steel_supply LIKE 'priority';");
    if (0 === $sql->getRows()) {
        $sql->setQuery('ALTER TABLE `'. \rex::getTablePrefix() .'d2u_machinery_steel_supply` ADD `priority` INT(10) NULL DEFAULT 0 AFTER `supply_id`;');
        $sql->setQuery('SELECT supply_id FROM `'. \rex::getTablePrefix() .'d2u_machinery_steel_supply` ORDER BY supply_id;');
        $update_sql = rex_sql::factory();
        for ($i = 1; $i <= $sql->getRows(); ++$i) {
            $update_sql->setQuery('UPDATE `'. \rex::getTablePrefix() .'d2u_machinery_steel_supply` SET priority = '. $i .' WHERE supply_id = '. $sql->getValue('supply_id') .';');
            $sql->next();
        }
    }
}

// 1.1.3 Update database
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

// Remove not needed sprog translations
if (rex_version::compare($this->getVersion(), '1.3.5', '<')) { /** @phpstan-ignore-line */
    $wildcards = [
        'd2u_machinery_form_captcha',
        'd2u_machinery_form_validate_captcha',
        'd2u_machinery_form_city',
        'd2u_machinery_form_address',
        'd2u_machinery_form_country',
        'd2u_machinery_form_email',
        'd2u_machinery_form_message',
        'd2u_machinery_form_name',
        'd2u_machinery_form_phone',
        'd2u_machinery_form_please_call',
        'd2u_machinery_form_privacy_policy',
        'd2u_machinery_form_required',
        'd2u_machinery_form_send',
        'd2u_machinery_form_thanks',
        'd2u_machinery_form_validate_address',
        'd2u_machinery_form_validate_city',
        'd2u_machinery_form_validate_email',
        'd2u_machinery_form_validate_name',
        'd2u_machinery_form_validate_phone',
        'd2u_machinery_form_validate_privacy_policy',
        'd2u_machinery_form_validate_spambots',
        'd2u_machinery_form_validate_spam_detected',
        'd2u_machinery_form_validate_title',
        'd2u_machinery_form_validate_zip',
        'd2u_machinery_form_vorname',
        'd2u_machinery_form_zip',
    ];

    foreach ($wildcards as $wildcard) {
        $sql->setQuery('DELETE FROM '. \rex::getTablePrefix() ."sprog_wildcard WHERE `wildcard` = '". $wildcard ."';");
    }
}
