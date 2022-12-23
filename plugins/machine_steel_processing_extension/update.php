<?php
// 1.0.1 Update database
$sql = \rex_sql::factory();
$sql->setQuery("SHOW COLUMNS FROM ". \rex::getTablePrefix() ."d2u_machinery_steel_supply LIKE 'videomanager_id';");
if(intval($sql->getRows()) === 0) {
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_machinery_steel_supply "
		. "CHANGE `videomanager_id` `video_id` INT(10) NULL DEFAULT NULL;");
}

// 1.3.5 Database update
$sql->setQuery("SHOW COLUMNS FROM ". \rex::getTablePrefix() ."d2u_machinery_steel_supply LIKE 'priority';");
if(intval($sql->getRows()) === 0) {
	$sql->setQuery("ALTER TABLE `". \rex::getTablePrefix() ."d2u_machinery_steel_supply` ADD `priority` INT(10) NULL DEFAULT 0 AFTER `supply_id`;");
	$sql->setQuery("SELECT supply_id FROM `". \rex::getTablePrefix() ."d2u_machinery_steel_supply` ORDER BY name;");
	$update_sql = rex_sql::factory();
	for($i = 1; $i <= $sql->getRows(); $i++) {
		$update_sql->setQuery("UPDATE `". \rex::getTablePrefix() ."d2u_machinery_steel_supply` SET priority = ". $i ." WHERE supply_id = ". $sql->getValue('supply_id') .";");
		$sql->next();
	}
}

$this->includeFile(__DIR__.'/install.php'); /** @phpstan-ignore-line */