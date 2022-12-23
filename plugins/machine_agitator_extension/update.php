<?php
// Update language replacements
if(!class_exists('d2u_machinery_machine_agitator_extension_lang_helper')) {
	// Load class in case addon is deactivated
	require_once 'lib/d2u_machinery_machine_agitator_extension_lang_helper.php';
}
d2u_machinery_machine_agitator_extension_lang_helper::factory()->install();

$sql = rex_sql::factory();
// Alter category table
$sql->setQuery("SHOW COLUMNS FROM ". \rex::getTablePrefix() ."d2u_machinery_categories LIKE 'show_agitators';");
if(intval($sql->getRows()) === 0) {
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_machinery_categories "
		. "ADD show_agitators VARCHAR(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
}

// Update database to 1.2.6
$sql->setQuery("ALTER TABLE `". rex::getTablePrefix() ."d2u_machinery_agitator_types` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
$sql->setQuery("ALTER TABLE `". rex::getTablePrefix() ."d2u_machinery_agitator_types_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");