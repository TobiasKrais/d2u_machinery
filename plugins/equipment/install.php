<?php

\rex_sql_table::get(\rex::getTable('d2u_machinery_equipments'))
    ->ensureColumn(new rex_sql_column('equipment_id', 'INT(11) unsigned', false, null, 'auto_increment'))
    ->setPrimaryKey('equipment_id')
    ->ensureColumn(new \rex_sql_column('article_number', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('group_id', 'INT(11)', true))
    ->ensureColumn(new \rex_sql_column('online_status', 'VARCHAR(10)', true))
    ->ensure();
\rex_sql_table::get(\rex::getTable('d2u_machinery_equipments_lang'))
    ->ensureColumn(new rex_sql_column('equipment_id', 'INT(11)', false))
    ->ensureColumn(new \rex_sql_column('clang_id', 'INT(11)', false))
    ->setPrimaryKey(['equipment_id', 'clang_id'])
    ->ensureColumn(new \rex_sql_column('name', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('translation_needs_update', 'VARCHAR(7)', true))
    ->ensure();

\rex_sql_table::get(\rex::getTable('d2u_machinery_equipment_groups'))
    ->ensureColumn(new rex_sql_column('group_id', 'INT(11) unsigned', false, null, 'auto_increment'))
    ->setPrimaryKey('group_id')
    ->ensureColumn(new \rex_sql_column('picture', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('priority', 'INT(11)', true))
    ->ensure();
\rex_sql_table::get(\rex::getTable('d2u_machinery_equipment_groups_lang'))
    ->ensureColumn(new rex_sql_column('group_id', 'INT(11)', false))
    ->ensureColumn(new \rex_sql_column('clang_id', 'INT(11)', false))
    ->setPrimaryKey(['group_id', 'clang_id'])
    ->ensureColumn(new \rex_sql_column('name', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('description', 'TEXT'))
    ->ensureColumn(new \rex_sql_column('translation_needs_update', 'VARCHAR(7)', true))
    ->ensure();

$sql = \rex_sql::factory();

// Alter machine table
\rex_sql_table::get(
    \rex::getTable('d2u_machinery_machines'))
    ->ensureColumn(new \rex_sql_column('equipment_ids', 'TEXT'))
    ->alter();

// Insert / update language replacements
if (!class_exists('d2u_machinery_equipment_lang_helper')) {
    // Load class in case addon is deactivated
    require_once 'lib/d2u_machinery_equipment_lang_helper.php';
}
d2u_machinery_equipment_lang_helper::factory()->install();
