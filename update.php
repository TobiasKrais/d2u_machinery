<?php
// Update language replacements
d2u_machinery_lang_helper::factory()->install();

// Update modules
if(class_exists(D2UModuleManager) && class_exists(D2UMachineryModules)) {
	$d2u_module_manager = new D2UModuleManager(D2UMachineryModules::getD2UMachineryModules(), "", "d2u_machinery");
	$d2u_module_manager->autoupdate();
}

// Update database
$sql = rex_sql::factory();
$sql->setQuery("SHOW COLUMNS FROM ". rex::getTablePrefix() ."d2u_machinery_machines LIKE 'priority';");
if($sql->getRows() == 0) {
	$sql->setQuery("ALTER TABLE ". rex::getTablePrefix() ."d2u_machinery_machines "
		. "ADD priority INT(10) NULL DEFAULT NULL AFTER machine_id;");
}
$sql->setQuery("SHOW COLUMNS FROM ". rex::getTablePrefix() ."d2u_machinery_machines_lang LIKE 'lang_name';");
if($sql->getRows() == 0) {
	$sql->setQuery("ALTER TABLE ". rex::getTablePrefix() ."d2u_machinery_machines_lang "
		. "ADD lang_namevarchar(255) collate utf8_general_ci default NULL AFTER clang_id;");
}