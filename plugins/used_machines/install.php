<?php
$sql = rex_sql::factory();

// Create database
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". rex::getTablePrefix() ."d2u_machinery_used_machines (
	used_machine_id int(10) unsigned NOT NULL auto_increment,
	name varchar(255) collate utf8_general_ci default NULL,
	category_id int(10) default NULL,
	contact_id int(10) default NULL,
	product_number varchar(255) collate utf8_general_ci default NULL,
	product_number_dealer varchar(255) collate utf8_general_ci default NULL,
	manufacturer varchar(255) collate utf8_general_ci default NULL,
	year_built int(4) default NULL,
	price decimal(10,2) default NULL,
	currency_code varchar(3) collate utf8_general_ci default NULL,
	vat int(2) default NULL,
	online_status varchar(10) collate utf8_general_ci default 'online',
	pics text collate utf8_general_ci default NULL,
	machine_id int(10) default NULL,
	location varchar(255) collate utf8_general_ci default NULL,
	external_url varchar(255) collate utf8_general_ci default NULL,
	PRIMARY KEY (used_machine_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1;");
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". rex::getTablePrefix() ."d2u_machinery_used_machines_lang (
	used_machines_id int(10) NOT NULL,
	clang_id int(10) NOT NULL,
	teaser varchar(255) collate utf8_general_ci default NULL,
	description text collate utf8_general_ci default NULL,
	translation_needs_update varchar(3) collate utf8_general_ci default NULL,
	updatedate int(11) default NULL,
	updateuser varchar(255) collate utf8_general_ci default NULL,
	PRIMARY KEY (used_machines_id, clang_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1;");

// Create standard configuration
if (!$this->hasConfig()) {
    $this->setConfig('article_id', rex_article::getSiteStartArticleId());
}