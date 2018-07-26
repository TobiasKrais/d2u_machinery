<?php
if(\rex::isBackend()) {
	rex_extension::register('CLANG_DELETED', 'rex_d2u_machinery_service_options_clang_deleted');
	rex_extension::register('MEDIA_IS_IN_USE', 'rex_d2u_machinery_service_options_media_is_in_use');
}

/**
 * Deletes language specific configurations and objects
 * @param rex_extension_point $ep Redaxo extension point
 * @return string[] Warning message as array
 */
function rex_d2u_machinery_service_options_clang_deleted(rex_extension_point $ep) {
	$warning = $ep->getSubject();
	$params = $ep->getParams();
	$clang_id = $params['id'];

	// Delete
	$service_options = ServiceOption::getAll($clang_id);
	foreach ($service_options as $service_option) {
		$service_option->delete(FALSE);
	}

	// Delete language replacements
	d2u_machinery_service_options_lang_helper::factory()->uninstall($clang_id);

	return $warning;
}

/**
 * Checks if media is used by this plugin
 * @param rex_extension_point $ep Redaxo extension point
 * @return string[] Warning message as array
 */
function rex_d2u_machinery_service_options_media_is_in_use(rex_extension_point $ep) {
	$warning = $ep->getSubject();
	$params = $ep->getParams();
	$filename = addslashes($params['filename']);

	// Service Options
	$sql = \rex_sql::factory();
	$sql->setQuery('SELECT lang.service_option_id, name FROM `' . \rex::getTablePrefix() . 'd2u_machinery_service_options_lang` AS lang '
		.'LEFT JOIN `' . \rex::getTablePrefix() . 'd2u_machinery_service_options` AS service_option ON lang.service_option_id = service_option.service_option_id '
		.'WHERE picture = "'. $filename .'" AND clang_id = '. rex_config::get("d2u_helper", "default_lang"));
	
	// Prepare warnings
	// Service Options
	for($i = 0; $i < $sql->getRows(); $i++) {
		$message =  '<a href="javascript:openPage(\'index.php?page=d2u_machinery/service_options&func=edit&entry_id='.
			$sql->getValue('service_option_id') .'\')">'. rex_i18n::msg('d2u_machinery_rights_all') ." - ". rex_i18n::msg('d2u_machinery_service_option') .': '. $sql->getValue('name') .'</a>';
  		if(!in_array($message, $warning)) {
			$warning[] = $message;
		}
    }

	return $warning;
}