<?php
// 1.0.1 Update database
$sql = \rex_sql::factory();
$sql->setQuery("SHOW COLUMNS FROM ". \rex::getTablePrefix() ."d2u_machinery_steel_supply LIKE 'videomanager_id';");
if($sql->getRows() == 0) {
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_machinery_steel_supply "
		. "CHANGE `videomanager_id` `video_id` INT(10) NULL DEFAULT NULL;");
}

// Update database to 1.2.6
$sql->setQuery("ALTER TABLE `". rex::getTablePrefix() ."d2u_machinery_steel_automation` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
$sql->setQuery("ALTER TABLE `". rex::getTablePrefix() ."d2u_machinery_steel_automation_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
$sql->setQuery("ALTER TABLE `". rex::getTablePrefix() ."d2u_machinery_steel_material` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
$sql->setQuery("ALTER TABLE `". rex::getTablePrefix() ."d2u_machinery_steel_material_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
$sql->setQuery("ALTER TABLE `". rex::getTablePrefix() ."d2u_machinery_steel_process` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
$sql->setQuery("ALTER TABLE `". rex::getTablePrefix() ."d2u_machinery_steel_process_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
$sql->setQuery("ALTER TABLE `". rex::getTablePrefix() ."d2u_machinery_steel_procedure` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
$sql->setQuery("ALTER TABLE `". rex::getTablePrefix() ."d2u_machinery_steel_procedure_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
$sql->setQuery("ALTER TABLE `". rex::getTablePrefix() ."d2u_machinery_steel_profile` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
$sql->setQuery("ALTER TABLE `". rex::getTablePrefix() ."d2u_machinery_steel_profile_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
$sql->setQuery("ALTER TABLE `". rex::getTablePrefix() ."d2u_machinery_steel_supply` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
$sql->setQuery("ALTER TABLE `". rex::getTablePrefix() ."d2u_machinery_steel_supply_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
$sql->setQuery("ALTER TABLE `". rex::getTablePrefix() ."d2u_machinery_steel_tool` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
$sql->setQuery("ALTER TABLE `". rex::getTablePrefix() ."d2u_machinery_steel_tool_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
$sql->setQuery("ALTER TABLE `". rex::getTablePrefix() ."d2u_machinery_steel_welding` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
$sql->setQuery("ALTER TABLE `". rex::getTablePrefix() ."d2u_machinery_steel_welding_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");

\rex_sql_table::get(
    \rex::getTable('d2u_machinery_machines'))
    ->ensureColumn(new \rex_sql_column('drilling_unit_below', 'INT(10)', TRUE))
    ->alter();

// Insert frontend translations
if(!class_exists('d2u_machinery_machine_steel_processing_extension_lang_helper')) {
	// Load class in case addon is deactivated
	require_once 'lib/d2u_machinery_machine_steel_processing_extension_lang_helper.php';
}
d2u_machinery_machine_steel_processing_extension_lang_helper::factory()->install();