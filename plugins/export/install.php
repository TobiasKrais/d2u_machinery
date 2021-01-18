<?php
$sql = \rex_sql::factory();

// Create database
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". \rex::getTablePrefix() ."d2u_machinery_export_provider (
	provider_id int(10) unsigned NOT NULL auto_increment,
	name varchar(50) collate utf8mb4_unicode_ci default NULL,
	type varchar(50) collate utf8mb4_unicode_ci default NULL,
	clang_id int(10) default NULL,
	company_name varchar(100) collate utf8mb4_unicode_ci default NULL,
	company_email varchar(255) collate utf8mb4_unicode_ci default NULL,
	customer_number varchar(50) collate utf8mb4_unicode_ci default NULL,
	media_manager_type varchar(255) collate utf8mb4_unicode_ci default NULL,
	online_status varchar(10) collate utf8mb4_unicode_ci default NULL,
	ftp_server varchar(100) collate utf8mb4_unicode_ci default NULL,
	ftp_username varchar(50) collate utf8mb4_unicode_ci default NULL,
	ftp_password varchar(20) collate utf8mb4_unicode_ci default NULL,
	ftp_filename varchar(50) collate utf8mb4_unicode_ci default NULL,
	social_app_id varchar(255) collate utf8mb4_unicode_ci default NULL,
	social_app_secret varchar(255) collate utf8mb4_unicode_ci default NULL,
	social_oauth_token varchar(255) collate utf8mb4_unicode_ci default NULL,
	social_oauth_token_secret varchar(255) collate utf8mb4_unicode_ci default NULL,
	social_oauth_token_valid_until int(11) collate utf8mb4_unicode_ci default NULL,
	linkedin_email varchar(255) collate utf8mb4_unicode_ci default NULL,
	linkedin_groupid varchar(255) collate utf8mb4_unicode_ci default NULL,
	PRIMARY KEY (provider_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;");
 
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". \rex::getTablePrefix() ."d2u_machinery_export_machines (
	used_machine_id int(10) NOT NULL,
	provider_id int(10) NOT NULL,
	export_action varchar(10) collate utf8mb4_unicode_ci default 'online',
	provider_import_id varchar(255) collate utf8mb4_unicode_ci default NULL,
	export_timestamp DATETIME NULL,
	PRIMARY KEY (used_machine_id, provider_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;");

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