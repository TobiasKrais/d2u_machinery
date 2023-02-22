<?php

\rex_sql_table::get(\rex::getTable('d2u_machinery_contacts'))
    ->ensureColumn(new rex_sql_column('contact_id', 'int(10) unsigned', false, null, 'auto_increment'))
    ->setPrimaryKey('contact_id')
    ->ensureColumn(new \rex_sql_column('name', 'VARCHAR(191)', true))
    ->ensureColumn(new \rex_sql_column('picture', 'VARCHAR(191)', true))
    ->ensureColumn(new \rex_sql_column('phone', 'VARCHAR(50)', true))
    ->ensureColumn(new \rex_sql_column('email', 'VARCHAR(50)', true))
    ->ensure();

\rex_sql_table::get(rex::getTable('d2u_machinery_machines'))
    ->ensureColumn(new \rex_sql_column('contact_id', 'int(10)', true, null))
    ->alter();
