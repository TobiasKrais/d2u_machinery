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