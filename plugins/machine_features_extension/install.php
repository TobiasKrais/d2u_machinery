<?php
$sql = \rex_sql::factory();

// Create tables
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". \rex::getTablePrefix() ."d2u_machinery_features (
	feature_id int(10) unsigned NOT NULL auto_increment,
	priority int(10) default NULL,
	category_ids varchar(255) collate utf8mb4_unicode_ci default NULL,
	pic varchar(100) collate utf8mb4_unicode_ci default NULL,
	video_id int(10) default NULL,
	PRIMARY KEY (feature_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;");
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". \rex::getTablePrefix() ."d2u_machinery_features_lang (
	feature_id int(10) NOT NULL,
	clang_id int(10) NOT NULL,
	name varchar(255) collate utf8mb4_unicode_ci default NULL,
	description text collate utf8mb4_unicode_ci default NULL,
	translation_needs_update varchar(7) collate utf8mb4_unicode_ci default NULL,
	PRIMARY KEY (feature_id, clang_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;");

// Alter machine table
\rex_sql_table::get(
    \rex::getTable('d2u_machinery_machines'))
    ->ensureColumn(new \rex_sql_column('feature_ids', 'TEXT'))
    ->alter();

// Insert frontend translations
if(class_exists('machine_features_extension_lang_helper')) {
	machine_features_extension_lang_helper::factory()->install();
}