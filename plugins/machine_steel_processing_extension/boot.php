<?php
if(\rex::isBackend()) {
	rex_extension::register('CLANG_DELETED', 'rex_d2u_machinery_steel_processing_clang_deleted');
	rex_extension::register('MEDIA_IS_IN_USE', 'rex_d2u_machinery_steel_processing_media_is_in_use');
}

/**
 * Deletes language specific configurations and objects
 * @param rex_extension_point $ep Redaxo extension point
 * @return string[] Warning message as array
 */
function rex_d2u_machinery_steel_processing_clang_deleted(rex_extension_point $ep) {
	$warning = $ep->getSubject();
	$params = $ep->getParams();
	$clang_id = $params['id'];

	// Delete
	$automations = Automation::getAll($clang_id);
	foreach ($automations as $automation) {
		$automation->delete(FALSE);
	}
	$materials = Material::getAll($clang_id);
	foreach ($materials as $material) {
		$material->delete(FALSE);
	}
	$procedures = Procedure::getAll($clang_id);
	foreach ($procedures as $procedure) {
		$procedure->delete(FALSE);
	}
	$processes = Process::getAll($clang_id);
	foreach ($processes as $process) {
		$process->delete(FALSE);
	}
	$profiles = Profile::getAll($clang_id);
	foreach ($profiles as $profile) {
		$profile->delete(FALSE);
	}
	$supplies = Supply::getAll($clang_id);
	foreach ($supplies as $supply) {
		$supply->delete(FALSE);
	}
	$tools = Tool::getAll($clang_id);
	foreach ($tools as $tool) {
		$tool->delete(FALSE);
	}
	$weldings = Welding::getAll($clang_id);
	foreach ($weldings as $welding) {
		$welding->delete(FALSE);
	}
	
	// Delete language replacements
	d2u_machinery_machine_steel_processing_extension_lang_helper::factory()->uninstall($clang_id);

	return $warning;
}

/**
 * Checks if media is used by this addon
 * @param rex_extension_point $ep Redaxo extension point
 * @return string[] Warning message as array
 */
function rex_d2u_machinery_steel_processing_media_is_in_use(rex_extension_point $ep) {
	$warning = $ep->getSubject();
	$params = $ep->getParams();
	$filename = addslashes($params['filename']);

	// In-/Outfeed side (supplies)
	$sql_machine = \rex_sql::factory();
	$sql_machine->setQuery('SELECT lang.supply_id, name FROM `' . \rex::getTablePrefix() . 'd2u_machinery_steel_supply_lang` AS lang '
		.'LEFT JOIN `' . \rex::getTablePrefix() . 'd2u_machinery_steel_supply` AS supplies ON lang.supply_id = supplies.supply_id '
		.'WHERE pic = "'. $filename .'" '
		.'GROUP BY supply_id');
	
	// Prepare warnings
	// Machines
	for($i = 0; $i < $sql_machine->getRows(); $i++) {
		$message = '<a href="javascript:openPage(\'index.php?page=d2u_machinery/machine_steel_processing_extension/supply&func=edit&entry_id='.
			$sql_machine->getValue('supply_id') .'\')">'.rex_i18n::msg('d2u_machinery_meta_title') .' '. rex_i18n::msg('d2u_machinery_machine_steel_extension') ." - ". rex_i18n::msg('d2u_machinery_steel_supply') .': '. $sql_machine->getValue('name') .'</a>';
		if(!in_array($message, $warning)) {
			$warning[] = $message;
		}
		$sql_machine->next();
    }

	return $warning;
}