<?php
// Extend machine table
\rex_sql_table::get(
    \rex::getTable('d2u_machinery_machines'))
    ->ensureColumn(new \rex_sql_column('service_option_ids', 'TEXT'))
    ->alter();

$sql = \rex_sql::factory();
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". \rex::getTablePrefix() ."d2u_machinery_service_options (
	service_option_id int(10) unsigned NOT NULL auto_increment,
	online_status varchar(10) collate utf8mb4_unicode_ci default 'online',
	picture varchar(100) collate utf8mb4_unicode_ci default NULL,
	PRIMARY KEY (service_option_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;");
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". \rex::getTablePrefix() ."d2u_machinery_service_options_lang (
	service_option_id int(10) NOT NULL,
	clang_id int(10) NOT NULL,
	name varchar(255) collate utf8mb4_unicode_ci default NULL,
	description varchar(255) collate utf8mb4_unicode_ci default NULL,
	translation_needs_update varchar(7) collate utf8mb4_unicode_ci default NULL,
	updatedate DATETIME default NULL,
	updateuser varchar(255) collate utf8mb4_unicode_ci default NULL,
	PRIMARY KEY (service_option_id, clang_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;");

// Insert frontend translations
if(class_exists('d2u_machinery_service_options_lang_helper')) {
	d2u_machinery_service_options_lang_helper::factory()->install();
}