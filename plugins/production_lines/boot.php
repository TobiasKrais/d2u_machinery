<?php
if(\rex::isBackend()) {
	rex_extension::register('CLANG_DELETED', 'rex_d2u_machinery_production_lines_clang_deleted');
	rex_extension::register('MEDIA_IS_IN_USE', 'rex_d2u_machinery_production_lines_media_is_in_use');
}

/**
 * Deletes language specific configurations and objects
 * @param rex_extension_point $ep Redaxo extension point
 * @return string[] Warning message as array
 */
function rex_d2u_machinery_production_lines_clang_deleted(rex_extension_point $ep) {
	$warning = $ep->getSubject();
	$params = $ep->getParams();
	$clang_id = $params['id'];

	// Delete
	$production_lines = ProductionLine::getAll($clang_id, FALSE);
	foreach ($production_lines as $$production_lines) {
		$$production_lines->delete(FALSE);
	}

	// Delete language replacements
	d2u_machinery_production_lines_lang_helper::factory()->uninstall($clang_id);

	return $warning;
}

/**
 * Checks if media is used by this plugin
 * @param rex_extension_point $ep Redaxo extension point
 * @return string[] Warning message as array
 */
function rex_d2u_machinery_production_lines_media_is_in_use(rex_extension_point $ep) {
	$warning = $ep->getSubject();
	$params = $ep->getParams();
	$filename = addslashes($params['filename']);

	// Production lines
	$sql = \rex_sql::factory();
	$sql->setQuery('SELECT lang.production_line_id, name FROM `' . \rex::getTablePrefix() . 'd2u_machinery_production_lines_lang` AS lang '
		.'LEFT JOIN `' . \rex::getTablePrefix() . 'd2u_machinery_production_lines` AS `lines` ON lang.production_line_id = `lines`.production_line_id '
		.'WHERE FIND_IN_SET("'. $filename .'", pictures) OR FIND_IN_SET("'. $filename .'", pictures) OR description_long LIKE "%'. $filename .'%" OR description_short LIKE "%'. $filename .'%"'
		.'GROUP BY production_line_id');

	// Prepare warnings
	// Production lines
	for($i = 0; $i < $sql->getRows(); $i++) {
		$message =  '<a href="javascript:openPage(\'index.php?page=d2u_machinery/production_lines&func=edit&entry_id='.
			$sql->getValue('production_line_id') .'\')">'. rex_i18n::msg('d2u_machinery_rights_all') ." - ". rex_i18n::msg('d2u_machinery_production_lines') .': '. $sql->getValue('name') .'</a>';
 		if(!in_array($message, $warning)) {
			$warning[] = $message;
		}
   }

	return $warning;
}