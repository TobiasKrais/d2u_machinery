<?php
$sql = rex_sql::factory();
$sql->setQuery('CREATE OR REPLACE VIEW '. rex::getTablePrefix() .'d2u_machinery_url_used_machines_rent AS
	SELECT lang.used_machine_id, categories.clang_id, CONCAT(machines.manufacturer, " ", machines.name) AS name, CONCAT(machines.manufacturer, " ", machines.name, " - ", categories.name) AS seo_title, lang.teaser AS seo_description, machines.category_id, lang.updatedate
	FROM '. rex::getTablePrefix() .'d2u_machinery_used_machines_lang AS lang
	LEFT JOIN '. rex::getTablePrefix() .'d2u_machinery_used_machines AS machines ON lang.used_machine_id = machines.used_machine_id
	LEFT JOIN '. rex::getTablePrefix() .'d2u_machinery_categories_lang AS categories ON machines.category_id = categories.category_id AND lang.clang_id = categories.clang_id
	LEFT JOIN '. rex::getTablePrefix() .'clang AS clang ON lang.clang_id = clang.id
	WHERE clang.status = 1 AND machines.online_status = "online" AND machines.offer_type = "rent"
	GROUP BY used_machine_id, clang_id, name, seo_title, seo_description, category_id, updatedate');
$sql->setQuery('CREATE OR REPLACE VIEW '. rex::getTablePrefix() .'d2u_machinery_url_used_machine_categories_rent AS
	SELECT machines.category_id, categories_lang.clang_id, categories_lang.name, parent_categories.name AS parent_name, CONCAT_WS(" - ", categories_lang.name, parent_categories.name) AS seo_title, categories_lang.teaser AS seo_description, categories_lang.updatedate
	FROM '. rex::getTablePrefix() .'d2u_machinery_used_machines_lang AS lang
	LEFT JOIN '. rex::getTablePrefix() .'d2u_machinery_used_machines AS machines ON lang.used_machine_id = machines.used_machine_id
	LEFT JOIN '. rex::getTablePrefix() .'d2u_machinery_categories_lang AS categories_lang ON machines.category_id = categories_lang.category_id AND lang.clang_id = categories_lang.clang_id
	LEFT JOIN '. rex::getTablePrefix() .'d2u_machinery_categories AS categories ON categories_lang.category_id = categories.category_id
	LEFT JOIN '. rex::getTablePrefix() .'d2u_machinery_categories_lang AS parent_categories ON categories.parent_category_id = parent_categories.category_id
	LEFT JOIN '. rex::getTablePrefix() .'clang AS clang ON lang.clang_id = clang.id
	WHERE clang.status = 1 AND machines.online_status = "online" AND machines.offer_type = "rent"
	GROUP BY category_id, clang_id, name, parent_name, seo_title, seo_description, updatedate');
$sql->setQuery('CREATE OR REPLACE VIEW '. rex::getTablePrefix() .'d2u_machinery_url_used_machines_sale AS
	SELECT lang.used_machine_id, categories.clang_id, CONCAT(machines.manufacturer, " ", machines.name) AS name, CONCAT(machines.manufacturer, " ", machines.name, " - ", categories.name) AS seo_title, lang.teaser AS seo_description, machines.category_id, lang.updatedate
	FROM '. rex::getTablePrefix() .'d2u_machinery_used_machines_lang AS lang
	LEFT JOIN '. rex::getTablePrefix() .'d2u_machinery_used_machines AS machines ON lang.used_machine_id = machines.used_machine_id
	LEFT JOIN '. rex::getTablePrefix() .'d2u_machinery_categories_lang AS categories ON machines.category_id = categories.category_id AND lang.clang_id = categories.clang_id
	LEFT JOIN '. rex::getTablePrefix() .'clang AS clang ON lang.clang_id = clang.id
	WHERE clang.status = 1 AND machines.online_status = "online" AND machines.offer_type = "sale"
	GROUP BY used_machine_id, clang_id, name, seo_title, seo_description, category_id, updatedate');
$sql->setQuery('CREATE OR REPLACE VIEW '. rex::getTablePrefix() .'d2u_machinery_url_used_machine_categories_sale AS
	SELECT machines.category_id, categories_lang.clang_id, categories_lang.name, parent_categories.name AS parent_name, CONCAT_WS(" - ", categories_lang.name, parent_categories.name) AS seo_title, categories_lang.teaser AS seo_description, categories_lang.updatedate
	FROM '. rex::getTablePrefix() .'d2u_machinery_used_machines_lang AS lang
	LEFT JOIN '. rex::getTablePrefix() .'d2u_machinery_used_machines AS machines ON lang.used_machine_id = machines.used_machine_id
	LEFT JOIN '. rex::getTablePrefix() .'d2u_machinery_categories_lang AS categories_lang ON machines.category_id = categories_lang.category_id AND lang.clang_id = categories_lang.clang_id
	LEFT JOIN '. rex::getTablePrefix() .'d2u_machinery_categories AS categories ON categories_lang.category_id = categories.category_id
	LEFT JOIN '. rex::getTablePrefix() .'d2u_machinery_categories_lang AS parent_categories ON categories.parent_category_id = parent_categories.category_id
	LEFT JOIN '. rex::getTablePrefix() .'clang AS clang ON lang.clang_id = clang.id
	WHERE clang.status = 1 AND machines.online_status = "online" AND machines.offer_type = "sale"
	GROUP BY category_id, clang_id, name, parent_name, seo_title, seo_description, updatedate');
if(rex_addon::get("url")->isAvailable()) {
	UrlGenerator::generatePathFile([]);
}

// Update language replacements
used_machines_lang_helper::factory()->install();