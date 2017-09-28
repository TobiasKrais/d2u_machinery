<?php
$sql = rex_sql::factory();
$sql->setQuery('CREATE OR REPLACE VIEW '. rex::getTablePrefix() .'d2u_machinery_url_industry_sectors AS
	SELECT industries.industry_sector_id, industries.clang_id, industries.name, industries.name AS seo_title, industries.teaser AS seo_description, industries.updatedate
	FROM '. rex::getTablePrefix() .'d2u_machinery_industry_sectors_lang AS industries
	LEFT JOIN '. rex::getTablePrefix() .'clang AS clang ON industries.clang_id = clang.id
	WHERE clang.status = 1'
);
if(rex_addon::get("url")->isAvailable()) {
	UrlGenerator::generatePathFile([]);
}
// 1.0.1 Update database
$sql->setQuery("SHOW COLUMNS FROM ". rex::getTablePrefix() ."d2u_machinery_industry_sectors_lang LIKE 'updatedate';");
if($sql->getRows() == 0) {
	$sql->setQuery("ALTER TABLE ". rex::getTablePrefix() ."d2u_machinery_industry_sectors_lang "
		. "ADD updatedate int(11) default NULL AFTER translation_needs_update;");
	$sql->setQuery("ALTER TABLE ". rex::getTablePrefix() ."d2u_machinery_industry_sectors_lang "
		. "ADD updateuser varchar(255) collate utf8_general_ci default NULL AFTER updatedate;");
}

// Update language replacements
d2u_machinery_industry_sectors_lang_helper::factory()->install();