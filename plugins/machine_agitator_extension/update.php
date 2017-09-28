<?php
// Update language replacements
d2u_machinery_machine_agitator_extension_lang_helper::factory()->install();

// Alter category table
$sql->setQuery("SHOW COLUMNS FROM ". rex::getTablePrefix() ."d2u_machinery_categories LIKE 'show_agitators';");
if($sql->getRows() == 0) {
	$sql->setQuery("ALTER TABLE ". rex::getTablePrefix() ."d2u_machinery_categories "
		. "ADD show_agitators VARCHAR(4) CHARACTER SET utf8 COLLATE utf8_general_ci;");
}