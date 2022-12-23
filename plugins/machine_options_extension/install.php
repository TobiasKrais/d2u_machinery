<?php
// Create tables
\rex_sql_table::get(\rex::getTable('d2u_machinery_options'))
	->ensureColumn(new rex_sql_column('option_id', 'INT(11) unsigned', false, null, 'auto_increment'))
	->setPrimaryKey('option_id')
	->ensureColumn(new \rex_sql_column('priority', 'INT(11)', true))
    ->ensureColumn(new \rex_sql_column('category_ids', 'VARCHAR(255)', true))
    ->ensureColumn(new \rex_sql_column('pic', 'VARCHAR(100)', true))
    ->ensureColumn(new \rex_sql_column('video_id', 'INT(11)', true))
    ->ensure();
\rex_sql_table::get(\rex::getTable('d2u_machinery_options_lang'))
	->ensureColumn(new rex_sql_column('option_id', 'INT(11)'))
    ->ensureColumn(new \rex_sql_column('clang_id', 'INT(11)', false))
	->setPrimaryKey(['option_id', 'clang_id'])
    ->ensureColumn(new \rex_sql_column('name', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('description', 'TEXT', true))
    ->ensureColumn(new \rex_sql_column('translation_needs_update', 'VARCHAR(7)'))
    ->ensureColumn(new \rex_sql_column('updatedate', 'DATETIME'))
    ->ensureColumn(new \rex_sql_column('updateuser', 'VARCHAR(255)'))
    ->ensure();

// Alter machine table
\rex_sql_table::get(
    \rex::getTable('d2u_machinery_machines'))
    ->ensureColumn(new \rex_sql_column('option_ids', 'TEXT'))
    ->alter();

// Insert / Update language replacements
if(!class_exists('d2u_machinery_machine_options_extension_lang_helper')) {
	// Load class in case addon is deactivated
	require_once 'lib/d2u_machinery_machine_options_extension_lang_helper.php';
}
d2u_machinery_machine_options_extension_lang_helper::factory()->install();