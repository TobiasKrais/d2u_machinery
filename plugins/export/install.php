<?php

\rex_sql_table::get(\rex::getTable('d2u_machinery_export_provider'))
    ->ensureColumn(new rex_sql_column('provider_id', 'INT(11) unsigned', false, null, 'auto_increment'))
    ->setPrimaryKey('provider_id')
    ->ensureColumn(new \rex_sql_column('name', 'VARCHAR(50)'))
    ->ensureColumn(new \rex_sql_column('type', 'VARCHAR(50)', true))
    ->ensureColumn(new \rex_sql_column('clang_id', 'INT(11)', true))
    ->ensureColumn(new \rex_sql_column('company_name', 'VARCHAR(100)', true))
    ->ensureColumn(new \rex_sql_column('company_email', 'VARCHAR(191)', true))
    ->ensureColumn(new \rex_sql_column('customer_number', 'VARCHAR(50)', true))
    ->ensureColumn(new \rex_sql_column('media_manager_type', 'VARCHAR(191)', true))
    ->ensureColumn(new \rex_sql_column('online_status', 'VARCHAR(10)', true))
    ->ensureColumn(new \rex_sql_column('ftp_server', 'VARCHAR(100)', true))
    ->ensureColumn(new \rex_sql_column('ftp_username', 'VARCHAR(50)', true))
    ->ensureColumn(new \rex_sql_column('ftp_password', 'VARCHAR(50)', true))
    ->ensureColumn(new \rex_sql_column('ftp_filename', 'VARCHAR(50)', true))
    ->ensureColumn(new \rex_sql_column('social_app_id', 'VARCHAR(191)', true))
    ->ensureColumn(new \rex_sql_column('social_app_secret', 'VARCHAR(191)', true))
    ->ensureColumn(new \rex_sql_column('social_access_token', 'TEXT', true))
    ->ensureColumn(new \rex_sql_column('social_access_token_valid_until', 'INT(11)', true))
    ->ensureColumn(new \rex_sql_column('social_refresh_token', 'TEXT', true))
    ->ensureColumn(new \rex_sql_column('social_refresh_token_valid_until', 'INT(11)', true))
    ->ensureColumn(new \rex_sql_column('linkedin_type', 'VARCHAR(10)', true))
    ->ensureColumn(new \rex_sql_column('linkedin_id', 'VARCHAR(40)', true))
    ->ensure();

// remove former columns
\rex_sql_table::get(
    \rex::getTable('d2u_machinery_export_provider'))
    ->removeColumn('facebook_email')
    ->removeColumn('facebook_pageid')
    ->removeColumn('linkedin_email')
    ->removeColumn('linkedin_groupid')
    ->removeColumn('social_oauth_token')
    ->removeColumn('social_oauth_token_secret')
    ->removeColumn('social_oauth_token_valid_until')
    ->removeColumn('twitter_id')
    ->ensure();

\rex_sql_table::get(\rex::getTable('d2u_machinery_export_machines'))
    ->ensureColumn(new rex_sql_column('used_machine_id', 'INT(11)', false))
    ->ensureColumn(new \rex_sql_column('provider_id', 'INT(11)', false))
    ->setPrimaryKey(['used_machine_id', 'provider_id'])
    ->ensureColumn(new \rex_sql_column('export_action', 'VARCHAR(10)'))
    ->ensureColumn(new \rex_sql_column('provider_import_id', 'VARCHAR(255)', true))
    ->ensureColumn(new \rex_sql_column('export_timestamp', 'DATETIME', true))
    ->ensure();

// Extend category table
\rex_sql_table::get(
    \rex::getTable('d2u_machinery_categories'))
    ->ensureColumn(new \rex_sql_column('export_machinerypark_category_id', 'INT(10)'))
    ->ensureColumn(new \rex_sql_column('export_europemachinery_category_id', 'INT(10)'))
    ->ensureColumn(new \rex_sql_column('export_europemachinery_category_name', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('export_mascus_category_name', 'VARCHAR(255)'))
    ->alter();

// Insert frontend translations
d2u_machinery_export_lang_helper::factory()->install();
