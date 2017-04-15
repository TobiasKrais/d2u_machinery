<?php
// Update language replacements
machine_features_extension_lang_helper::factory()->install();

// 1.0.1 Update database
$sql = rex_sql::factory();
$sql->setQuery("SHOW COLUMNS FROM ". rex::getTablePrefix() ."d2u_machinery_features_lang LIKE 'name';");
if($sql->getRows() == 0) {
	$sql->setQuery("ALTER TABLE ". rex::getTablePrefix() ."d2u_machinery_features_lang "
		. "ADD `name` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `clang_id`");
}