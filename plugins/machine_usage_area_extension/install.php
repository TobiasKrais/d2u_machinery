<?php
$sql = \rex_sql::factory();

// Create tables
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". \rex::getTablePrefix() ."d2u_machinery_usage_areas (
	usage_area_id int(10) unsigned NOT NULL auto_increment,
	priority int(10) default NULL,
	category_ids varchar(255) collate utf8mb4_unicode_ci default NULL,
	PRIMARY KEY (usage_area_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;");
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". \rex::getTablePrefix() ."d2u_machinery_usage_areas_lang (
	usage_area_id int(10) NOT NULL,
	clang_id int(10) NOT NULL,
	name varchar(255) collate utf8mb4_unicode_ci default NULL,
	translation_needs_update varchar(7) collate utf8mb4_unicode_ci default NULL,
	PRIMARY KEY (usage_area_id, clang_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;");

// Alter machine table
\rex_sql_table::get(
    \rex::getTable('d2u_machinery_machines'))
    ->ensureColumn(new \rex_sql_column('usage_area_ids', 'TEXT'))
    ->alter();

// Insert frontend translations
if(class_exists('d2u_machinery_machine_usage_area_extension_lang_helper')) {
	d2u_machinery_machine_usage_area_extension_lang_helper::factory()->install();
}