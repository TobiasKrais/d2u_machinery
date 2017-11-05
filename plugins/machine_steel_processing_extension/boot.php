<?php
if(rex::isBackend() && is_object(rex::getUser())) {
	rex_perm::register('d2u_machinery[machine_steel_extension]', rex_i18n::msg('d2u_machinery_steel_rights'));
}

if(rex::isBackend()) {
	rex_extension::register('CLANG_DELETED', 'rex_d2u_machinery_steel_processing_clang_deleted');
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