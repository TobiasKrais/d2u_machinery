<?php
if(rex::isBackend()) {
	rex_extension::register('MEDIA_IS_IN_USE', 'rex_d2u_machinery_agitators_media_is_in_use');
}

/**
 * Checks if media is used by this plugin
 * @param rex_extension_point $ep Redaxo extension point
 * @return string[] Warning message as array
 */
function rex_d2u_machinery_agitators_media_is_in_use(rex_extension_point $ep) {
	$warning = $ep->getSubject();
	$params = $ep->getParams();
	$filename = addslashes($params['filename']);

	// Mechanical Constructions
	$sql_mechanical = rex_sql::factory();
	$sql_mechanical->setQuery('SELECT lang.mechanical_construction_id, name FROM `' . rex::getTablePrefix() . 'd2u_machinery_mechanical_constructions_lang` AS lang '
		.'LEFT JOIN `' . rex::getTablePrefix() . 'd2u_machinery_mechanical_constructions` AS constructions ON lang.mechanical_construction_id = constructions.mechanical_construction_id '
		.'WHERE pic = "'. $filename .'" AND clang_id = '. rex_config::get("d2u_machinery", "default_lang"));
	
	// Agitator types
	$sql_agitator_types = rex_sql::factory();
	$sql_agitator_types->setQuery('SELECT lang.agitator_type_id, name FROM `' . rex::getTablePrefix() . 'd2u_machinery_agitator_types_lang` AS lang '
		.'LEFT JOIN `' . rex::getTablePrefix() . 'd2u_machinery_agitator_types` AS types ON lang.agitator_type_id = types.agitator_type_id '
		.'WHERE pic = "'. $filename .'" AND clang_id = '. rex_config::get("d2u_machinery", "default_lang"));
	
	// Agitator organs
	$sql_agitators = rex_sql::factory();
	$sql_agitators->setQuery('SELECT lang.agitator_id, name FROM `' . rex::getTablePrefix() . 'd2u_machinery_agitators_lang` AS lang '
		.'LEFT JOIN `' . rex::getTablePrefix() . 'd2u_machinery_agitators` AS types ON lang.agitator_id = types.agitator_id '
		.'WHERE pic = "'. $filename .'" AND clang_id = '. rex_config::get("d2u_machinery", "default_lang"));
	
	// Prepare warnings
	// Mechanical Constructions
	for($i = 0; $i < $sql_mechanical->getRows(); $i++) {
		$warning[] = '<a href="javascript:openPage(\'index.php?page=d2u_machinery/machine_agitator_extension/mechanical_construction&func=edit&entry_id='.
			$sql_mechanical->getValue('mechanical_construction_id') .'\')">'. rex_i18n::msg('d2u_machinery_rights_all') ." - ". rex_i18n::msg('d2u_machinery_mechanical_constructions') .': '. $sql_mechanical->getValue('name') .'</a>';
    }
	// Agitator types
	for($i = 0; $i < $sql_agitator_types->getRows(); $i++) {
		$warning[] = '<a href="javascript:openPage(\'index.php?page=d2u_machinery/machine_agitator_extension/agitator_type&func=edit&entry_id='.
			$sql_mechanical->getValue('agitator_type_id') .'\')">'. rex_i18n::msg('d2u_machinery_rights_all') ." - ". rex_i18n::msg('d2u_machinery_agitator_types') .': '. $sql_mechanical->getValue('name') .'</a>';
    }
	// Agitators
	for($i = 0; $i < $sql_agitators->getRows(); $i++) {
		$warning[] = '<a href="javascript:openPage(\'index.php?page=d2u_machinery/machine_agitator_extension/agitator&func=edit&entry_id='.
			$sql_mechanical->getValue('agitator_id') .'\')">'. rex_i18n::msg('d2u_machinery_rights_all') ." - ". rex_i18n::msg('d2u_machinery_agitators') .': '. $sql_mechanical->getValue('name') .'</a>';
    }
	
	return $warning;
}