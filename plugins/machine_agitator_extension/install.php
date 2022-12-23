<?php
\rex_sql_table::get(\rex::getTable('d2u_machinery_agitator_types'))
	->ensureColumn(new rex_sql_column('agitator_type_id', 'INT(11) unsigned', false, null, 'auto_increment'))
	->setPrimaryKey('agitator_type_id')
    ->ensureColumn(new \rex_sql_column('agitator_ids', 'TEXT', true))
    ->ensureColumn(new \rex_sql_column('pic', 'VARCHAR(255)', true))
    ->ensure();
\rex_sql_table::get(\rex::getTable('d2u_machinery_agitator_types_lang'))
	->ensureColumn(new rex_sql_column('agitator_type_id', 'INT(11)', false))
    ->ensureColumn(new \rex_sql_column('clang_id', 'INT(11)', false))
	->setPrimaryKey(['agitator_type_id', 'clang_id'])
    ->ensureColumn(new \rex_sql_column('name', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('translation_needs_update', 'VARCHAR(7)', true))
    ->ensure();

\rex_sql_table::get(\rex::getTable('d2u_machinery_agitators'))
	->ensureColumn(new rex_sql_column('agitator_id', 'INT(11) unsigned', false, null, 'auto_increment'))
	->setPrimaryKey('agitator_id')
    ->ensureColumn(new \rex_sql_column('pic', 'VARCHAR(255)', true))
    ->ensure();
\rex_sql_table::get(\rex::getTable('d2u_machinery_agitators_lang'))
	->ensureColumn(new rex_sql_column('agitator_id', 'INT(11)', false))
    ->ensureColumn(new \rex_sql_column('clang_id', 'INT(11)', false))
	->setPrimaryKey(['agitator_id', 'clang_id'])
    ->ensureColumn(new \rex_sql_column('name', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('description', 'TEXT'))
    ->ensureColumn(new \rex_sql_column('translation_needs_update', 'VARCHAR(7)', true))
    ->ensure();

// Alter machine table
\rex_sql_table::get(
    \rex::getTable('d2u_machinery_machines'))
    ->ensureColumn(new \rex_sql_column('agitator_type_id', 'INT(10)'))
    ->ensureColumn(new \rex_sql_column('viscosity', 'INT(10)'))
    ->alter();

// Alter category table
\rex_sql_table::get(
    \rex::getTable('d2u_machinery_categories'))
    ->ensureColumn(new \rex_sql_column('show_agitators', 'VARCHAR(4)'))
    ->alter();

// Insert frontend translations
if(!class_exists('d2u_machinery_machine_agitator_extension_lang_helper')) {
	// Load class in case addon is deactivated
	require_once 'lib/d2u_machinery_machine_agitator_extension_lang_helper.php';
}
d2u_machinery_machine_agitator_extension_lang_helper::factory()->install();