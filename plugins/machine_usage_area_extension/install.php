<?php
\rex_sql_table::get(\rex::getTable('d2u_machinery_usage_areas'))
	->ensureColumn(new rex_sql_column('usage_area_id', 'INT(11) unsigned', false, null, 'auto_increment'))
	->setPrimaryKey('usage_area_id')
	->ensureColumn(new \rex_sql_column('priority', 'INT(11)', true))
    ->ensureColumn(new \rex_sql_column('category_ids', 'VARCHAR(255)', true))
    ->ensure();
\rex_sql_table::get(\rex::getTable('d2u_machinery_usage_areas_lang'))
	->ensureColumn(new rex_sql_column('usage_area_id', 'INT(11)', false))
    ->ensureColumn(new \rex_sql_column('clang_id', 'INT(11)', false))
	->setPrimaryKey(['usage_area_id', 'clang_id'])
    ->ensureColumn(new \rex_sql_column('name', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('translation_needs_update', 'VARCHAR(7)', true))
    ->ensure();

// Alter machine table
\rex_sql_table::get(
    \rex::getTable('d2u_machinery_machines'))
    ->ensureColumn(new \rex_sql_column('usage_area_ids', 'TEXT'))
    ->alter();

// Insert frontend translations
if(!class_exists('d2u_machinery_machine_usage_area_extension_lang_helper')) {
	// Load class in case addon is deactivated
	require_once 'lib/d2u_machinery_machine_usage_area_extension_lang_helper.php';
}
if(class_exists('d2u_machinery_machine_usage_area_extension_lang_helper')) {
	d2u_machinery_machine_usage_area_extension_lang_helper::factory()->install();
}