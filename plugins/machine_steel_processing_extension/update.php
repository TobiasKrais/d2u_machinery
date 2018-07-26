<?php
// 1.0.1 Update database
$sql = \rex_sql::factory();
$sql->setQuery("SHOW COLUMNS FROM ". \rex::getTablePrefix() ."d2u_machinery_steel_supply LIKE 'videomanager_id';");
if($sql->getRows() == 0) {
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_machinery_steel_supply "
		. "CHANGE `videomanager_id` `video_id` INT(10) NULL DEFAULT NULL;");
}

// Insert frontend translations
d2u_machinery_machine_steel_processing_extension_lang_helper::factory()->install();