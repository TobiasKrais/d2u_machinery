<?php
\rex_sql_table::get(\rex::getTable('d2u_machinery_service_options'))
	->ensureColumn(new rex_sql_column('service_option_id', 'int(10) unsigned', false, null, 'auto_increment'))
	->setPrimaryKey('service_option_id')
    ->ensureColumn(new \rex_sql_column('online_status', 'VARCHAR(10)', true))
    ->ensureColumn(new \rex_sql_column('picture', 'VARCHAR(255)', true))
    ->ensure();
\rex_sql_table::get(\rex::getTable('d2u_machinery_service_options_lang'))
	->ensureColumn(new rex_sql_column('service_option_id', 'int(10) unsigned', false, null, 'auto_increment'))
    ->ensureColumn(new \rex_sql_column('clang_id', 'INT(11)', false))
	->setPrimaryKey(['service_option_id', 'clang_id'])
    ->ensureColumn(new \rex_sql_column('name', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('description', 'TEXT'))
    ->ensureColumn(new \rex_sql_column('translation_needs_update', 'VARCHAR(7)'))
    ->ensureColumn(new \rex_sql_column('updatedate', 'DATETIME'))
    ->ensureColumn(new \rex_sql_column('updateuser', 'VARCHAR(255)'))
    ->ensure();

// Extend machine table
\rex_sql_table::get(
    \rex::getTable('d2u_machinery_machines'))
    ->ensureColumn(new \rex_sql_column('service_option_ids', 'TEXT'))
    ->alter();

// Insert frontend translations
if(!class_exists('d2u_machinery_service_options_lang_helper')) {
	// Load class in case addon is deactivated
	require_once 'lib/d2u_machinery_service_options_lang_helper.php';
}
if(class_exists('d2u_machinery_service_options_lang_helper')) {
	d2u_machinery_service_options_lang_helper::factory()->install();
}