<?php
\rex_sql_table::get(\rex::getTable('d2u_machinery_certificates'))
	->ensureColumn(new rex_sql_column('certificate_id', 'INT(11) unsigned', false, null, 'auto_increment'))
	->setPrimaryKey('certificate_id')
    ->ensureColumn(new \rex_sql_column('pic', 'VARCHAR(255)', true))
    ->ensure();
\rex_sql_table::get(\rex::getTable('d2u_machinery_certificates_lang'))
	->ensureColumn(new rex_sql_column('certificate_id', 'INT(11)', false))
    ->ensureColumn(new \rex_sql_column('clang_id', 'INT(11)', false))
	->setPrimaryKey(['certificate_id', 'clang_id'])
    ->ensureColumn(new \rex_sql_column('name', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('description', 'TEXT'))
    ->ensureColumn(new \rex_sql_column('translation_needs_update', 'VARCHAR(7)', true))
    ->ensure();

// Alter machine table
\rex_sql_table::get(
    \rex::getTable('d2u_machinery_machines'))
    ->ensureColumn(new \rex_sql_column('certificate_ids', 'TEXT'))
    ->alter();