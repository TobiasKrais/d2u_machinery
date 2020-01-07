<?php
// Update language replacements
if(!class_exists('d2u_machinery_machine_features_extension_lang_helper')) {
	// Load class in case addon is deactivated
	require_once 'lib/d2u_machinery_machine_features_extension_lang_helper.php';
}
d2u_machinery_machine_features_extension_lang_helper::factory()->install();

// 1.0.1 Update database
$sql = \rex_sql::factory();
$sql->setQuery("SHOW COLUMNS FROM ". \rex::getTablePrefix() ."d2u_machinery_features_lang LIKE 'name';");
if($sql->getRows() == 0) {
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_machinery_features_lang "
		. "ADD `name` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER `clang_id`");
}

// 1.0.2 Update database
$sql->setQuery("SHOW COLUMNS FROM ". \rex::getTablePrefix() ."d2u_machinery_features LIKE 'video_id';");
if($sql->getRows() == 0) {
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_machinery_features "
		. "ADD `video_id` INT(10) NULL DEFAULT NULL AFTER `pic`");
}

// Update database to 1.2.6
$sql->setQuery("ALTER TABLE `". rex::getTablePrefix() ."d2u_machinery_features` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
$sql->setQuery("ALTER TABLE `". rex::getTablePrefix() ."d2u_machinery_features_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");

\rex_sql_table::get(
    \rex::getTable('d2u_machinery_features_lang'))
    ->removeColumn('title')
    ->ensure();
