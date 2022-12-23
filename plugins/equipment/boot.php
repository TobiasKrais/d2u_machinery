<?php
if(\rex::isBackend()) {
	rex_extension::register('CLANG_DELETED', 'rex_d2u_machinery_equipment_clang_deleted');
	rex_extension::register('MEDIA_IS_IN_USE', 'rex_d2u_machinery_equipment_media_is_in_use');
}

/**
 * Deletes language specific configurations and objects
 * @param rex_extension_point<string> $ep Redaxo extension point
 * @return string[] Warning message as array
 */
function rex_d2u_machinery_equipment_clang_deleted(rex_extension_point $ep) {
	/** @var string[] $warning */
	$warning = $ep->getSubject();
	$params = $ep->getParams();
	$clang_id = $params['id'];

	// Delete
	$equipments = Equipment::getAll($clang_id);
	foreach ($equipments as $equipment) {
		$equipment->delete(false);
	}
	$equipment_groups = EquipmentGroup::getAll($clang_id);
	foreach ($equipment_groups as $equipment_group) {
		$equipment_group->delete(false);
	}

	// Delete language replacements
	d2u_machinery_equipment_lang_helper::factory()->uninstall($clang_id);

	return $warning;
}

/**
 * Checks if media is used by this plugin
 * @param rex_extension_point<string> $ep Redaxo extension point
 * @return string[] Warning message as array
 */
function rex_d2u_machinery_equipment_media_is_in_use(rex_extension_point $ep) {
	/** @var string[] $warning */
	$warning = $ep->getSubject();
	$params = $ep->getParams();
	$filename = addslashes($params['filename']);

	// Equipment Groups
	$sql = \rex_sql::factory();
	$sql->setQuery('SELECT lang.group_id, name FROM `' . \rex::getTablePrefix() . 'd2u_machinery_equipment_groups_lang` AS lang '
		.'LEFT JOIN `' . \rex::getTablePrefix() . 'd2u_machinery_equipment_groups` AS equipment_groups ON lang.group_id = equipment_groups.group_id '
		.'WHERE picture = "'. $filename .'" AND clang_id = '. intval(rex_config::get("d2u_helper", "default_lang")));
	
	// Prepare warnings
	// Equipment Groups
	for($i = 0; $i < $sql->getRows(); $i++) {
		$message =  '<a href="javascript:openPage(\'index.php?page=d2u_machinery/equipment/equipment_group&func=edit&entry_id='.
			$sql->getValue('group_id') .'\')">'. rex_i18n::msg('d2u_machinery_rights_all') ." - ". rex_i18n::msg('d2u_machinery_equipment_groups') .': '. $sql->getValue('name') .'</a>';
  		if(!in_array($message, $warning, true)) {
			$warning[] = $message;
		}
		$sql->next();
    }

	return $warning;
}