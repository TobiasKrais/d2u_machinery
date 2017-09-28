<?php
if(rex::isBackend()) {
	rex_extension::register('CLANG_DELETED', 'rex_d2u_machinery_agitators_clang_deleted');
	rex_extension::register('MEDIA_IS_IN_USE', 'rex_d2u_machinery_agitators_media_is_in_use');
}

/**
 * Deletes language specific configurations and objects
 * @param rex_extension_point $ep Redaxo extension point
 * @return string[] Warning message as array
 */
function rex_d2u_machinery_agitators_clang_deleted(rex_extension_point $ep) {
	$warning = $ep->getSubject();
	$params = $ep->getParams();
	$clang_id = $params['id'];

	// Delete
	$agitators = Agitator::getAll($clang_id);
	foreach ($agitators as $agitator) {
		$agitator->delete(FALSE);
	}
	$agitator_types = AgitatorType::getAll($clang_id);
	foreach ($agitator_types as $agitator_type) {
		$agitator_type->delete(FALSE);
	}

	// Delete language replacements
	d2u_machinery_machine_agitator_extension_lang_helper::factory()->uninstall($clang_id);

	return $warning;
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

	// Agitator types
	$sql_agitator_types = rex_sql::factory();
	$sql_agitator_types->setQuery('SELECT lang.agitator_type_id, name FROM `' . rex::getTablePrefix() . 'd2u_machinery_agitator_types_lang` AS lang '
		.'LEFT JOIN `' . rex::getTablePrefix() . 'd2u_machinery_agitator_types` AS types ON lang.agitator_type_id = types.agitator_type_id '
		.'WHERE pic = "'. $filename .'" AND clang_id = '. rex_config::get("d2u_helper", "default_lang"));
	
	// Agitator organs
	$sql_agitators = rex_sql::factory();
	$sql_agitators->setQuery('SELECT lang.agitator_id, name FROM `' . rex::getTablePrefix() . 'd2u_machinery_agitators_lang` AS lang '
		.'LEFT JOIN `' . rex::getTablePrefix() . 'd2u_machinery_agitators` AS types ON lang.agitator_id = types.agitator_id '
		.'WHERE pic = "'. $filename .'" AND clang_id = '. rex_config::get("d2u_helper", "default_lang"));
	
	// Prepare warnings
	// Agitator types
	for($i = 0; $i < $sql_agitator_types->getRows(); $i++) {
		$message = '<a href="javascript:openPage(\'index.php?page=d2u_machinery/machine_agitator_extension/agitator_type&func=edit&entry_id='.
			$sql_mechanical->getValue('agitator_type_id') .'\')">'. rex_i18n::msg('d2u_machinery_rights_all') ." - ". rex_i18n::msg('d2u_machinery_agitator_types') .': '. $sql_mechanical->getValue('name') .'</a>';
 		if(!in_array($message, $warning)) {
			$warning[] = $message;
		}
    }
	// Agitators
	for($i = 0; $i < $sql_agitators->getRows(); $i++) {
		$message = '<a href="javascript:openPage(\'index.php?page=d2u_machinery/machine_agitator_extension/agitator&func=edit&entry_id='.
			$sql_mechanical->getValue('agitator_id') .'\')">'. rex_i18n::msg('d2u_machinery_rights_all') ." - ". rex_i18n::msg('d2u_machinery_agitators') .': '. $sql_mechanical->getValue('name') .'</a>';
  		if(!in_array($message, $warning)) {
			$warning[] = $message;
		}
   }
	
	return $warning;
}