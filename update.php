<?php
// Update language replacements
d2u_machinery_lang_helper::factory()->install();

// Update modules
if(class_exists(D2UModuleManager) && class_exists(D2UMachineryModules)) {
	$d2u_module_manager = new D2UModuleManager(D2UMachineryModules::getD2UMachineryModules(), "", "d2u_machinery");
	$d2u_module_manager->autoupdate();
}

// 1.0.1 Update database
$sql = rex_sql::factory();
$sql->setQuery("SHOW COLUMNS FROM ". rex::getTablePrefix() ."d2u_machinery_machines LIKE 'priority';");
if($sql->getRows() == 0) {
	$sql->setQuery("ALTER TABLE ". rex::getTablePrefix() ."d2u_machinery_machines "
		. "ADD priority INT(10) NULL DEFAULT NULL AFTER machine_id;");
}
// 1.0.2 Update database
$sql->setQuery("SHOW COLUMNS FROM ". rex::getTablePrefix() ."d2u_machinery_machines_lang LIKE 'lang_name';");
if($sql->getRows() == 0) {
	$sql->setQuery("ALTER TABLE ". rex::getTablePrefix() ."d2u_machinery_machines_lang "
		. "ADD lang_name varchar(255) collate utf8_general_ci default NULL AFTER clang_id;");
}

// 1.0.2 Update machine URL view and regenerate path file
$sql->setQuery('CREATE OR REPLACE VIEW '. rex::getTablePrefix() .'d2u_machinery_url_machines AS
	SELECT lang.machine_id, lang.clang_id, IF(lang.lang_name IS NULL or lang.lang_name = "", machines.name, lang.lang_name) as name, CONCAT(IF(lang.lang_name IS NULL or lang.lang_name = "", machines.name, lang.lang_name), " - ", categories.name) AS seo_title, lang.teaser AS seo_description, machines.category_id, lang.updatedate
	FROM '. rex::getTablePrefix() .'d2u_machinery_machines_lang AS lang
	LEFT JOIN '. rex::getTablePrefix() .'d2u_machinery_machines AS machines ON lang.machine_id = machines.machine_id
	LEFT JOIN '. rex::getTablePrefix() .'d2u_machinery_categories_lang AS categories ON machines.category_id = categories.category_id AND lang.clang_id = categories.clang_id
	WHERE machines.online_status = "online"');
if(rex_addon::get("url")->isAvailable()) {
	UrlGenerator::generatePathFile([]);
}