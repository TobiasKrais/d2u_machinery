<?php
// Create tables
\rex_sql_table::get(\rex::getTable('d2u_machinery_features'))
	->ensureColumn(new rex_sql_column('feature_id', 'INT(11) unsigned', false, null, 'auto_increment'))
	->setPrimaryKey('feature_id')
	->ensureColumn(new \rex_sql_column('priority', 'INT(11)', true))
    ->ensureColumn(new \rex_sql_column('category_ids', 'VARCHAR(255)', true))
    ->ensureColumn(new \rex_sql_column('pic', 'VARCHAR(100)', true))
    ->ensureColumn(new \rex_sql_column('video_id', 'INT(11)', true))
    ->ensure();
\rex_sql_table::get(\rex::getTable('d2u_machinery_features_lang'))
	->ensureColumn(new rex_sql_column('feature_id', 'INT(11)'))
    ->ensureColumn(new \rex_sql_column('clang_id', 'INT(11)', false))
	->setPrimaryKey(['feature_id', 'clang_id'])
    ->ensureColumn(new \rex_sql_column('name', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('description', 'TEXT', true))
    ->ensureColumn(new \rex_sql_column('translation_needs_update', 'VARCHAR(7)'))
    ->ensureColumn(new \rex_sql_column('updatedate', 'DATETIME'))
    ->ensureColumn(new \rex_sql_column('updateuser', 'VARCHAR(255)'))
    ->ensure();
\rex_sql_table::get(
    \rex::getTable('d2u_machinery_features_lang'))
    ->removeColumn('title')
    ->ensure();

// Alter machine table
\rex_sql_table::get(
    \rex::getTable('d2u_machinery_machines'))
    ->ensureColumn(new \rex_sql_column('feature_ids', 'TEXT'))
    ->alter();

// Insert frontend translations
if(!class_exists('machine_features_extension_lang_helper')) {
	// Load class in case addon is deactivated
	require_once 'lib/machine_features_extension_lang_helper.php';
}
d2u_machinery_machine_features_extension_lang_helper::factory()->install();