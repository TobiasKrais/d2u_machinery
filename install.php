<?php
$sql = rex_sql::factory();
// Install database
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". rex::getTablePrefix() ."d2u_machinery_machines (
	machine_id int(10) unsigned NOT NULL auto_increment,
	internal_name varchar(255) collate utf8_general_ci default NULL,
	name varchar(255) collate utf8_general_ci default NULL,
	pics text collate utf8_general_ci default NULL,
	category_id int(10) default NULL,
	usage_area_ids varchar(255) collate utf8_general_ci default NULL,
	alternative_machine_ids varchar(255) collate utf8_general_ci default NULL,
	product_number varchar(255) collate utf8_general_ci default NULL,
	article_id_software int(10) default NULL,
	article_id_service int(10) default NULL,
	article_ids_references varchar(255) collate utf8_general_ci default NULL,
	online_status varchar(10) collate utf8_general_ci default 'online',
	engine_power varchar(255) collate utf8_general_ci default NULL,
	length int(10) default NULL,
	width int(10) default NULL,
	height int(10) default NULL,
	depth int(10) default NULL,
	weight varchar(255) collate utf8_general_ci default NULL,
	operating_voltage_v varchar(255) collate utf8_general_ci default NULL,
	operating_voltage_hz varchar(255) collate utf8_general_ci default NULL,
	operating_voltage_a varchar(255) collate utf8_general_ci default NULL,
	PRIMARY KEY (machine_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1;");
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". rex::getTablePrefix() ."d2u_machinery_machines_lang (
	machine_id int(10) NOT NULL,
	clang_id int(10) NOT NULL,
	teaser varchar(255) collate utf8_general_ci default NULL,
	description text collate utf8_general_ci default NULL,
	pdfs varchar(255) collate utf8_general_ci default NULL,
	translation_needs_update varchar(7) collate utf8_general_ci default NULL,
	updatedate int(11) default NULL,
	updateuser varchar(255) collate utf8_general_ci default NULL,
	PRIMARY KEY (machine_id, clang_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1;");

$sql->setQuery("CREATE TABLE IF NOT EXISTS ". rex::getTablePrefix() ."d2u_machinery_categories (
	category_id int(10) unsigned NOT NULL auto_increment,
	parent_category_id varchar(255) collate utf8_general_ci default NULL,
	pic varchar(255) collate utf8_general_ci default NULL,
	pic_usage varchar(255) collate utf8_general_ci default NULL,
	videomanager_ids varchar(255) collate utf8_general_ci default NULL,
	PRIMARY KEY (category_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1;");
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". rex::getTablePrefix() ."d2u_machinery_categories_lang (
	category_id int(10) NOT NULL,
	clang_id int(10) NOT NULL,
	name varchar(255) collate utf8_general_ci default NULL,
	usage_area varchar(255) collate utf8_general_ci default NULL,
	pic_lang varchar(255) collate utf8_general_ci default NULL,
	pdfs varchar(255) collate utf8_general_ci default NULL,
	translation_needs_update varchar(7) collate utf8_general_ci default NULL,
	updatedate int(11) default NULL,
	updateuser varchar(255) collate utf8_general_ci default NULL,
	PRIMARY KEY (category_id, clang_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1;");

// Standard settings
if (!$this->hasConfig()) {
	// Find first Redaxo lang an set later as default lang
	$langs = rex_clang::getAll();
	$default_clang_id = 1;
	foreach ($langs as $lang) {
		$default_clang_id = $lang->getId();
		break;
	}
	
    $this->setConfig('article_id', rex_article::getSiteStartArticleId());
	$this->setConfig('default_lang', $default_clang_id);
	$this->setConfig('show_categories_usage_area', 'hide');
}