<?php

if(rex::isBackend() && is_object(rex::getUser())) {
	rex_perm::register('d2u_machinery[used_machines]', rex_i18n::msg('d2u_machinery_rights_all'));
}

if(rex::isBackend()) {
	rex_extension::register('MEDIA_IS_IN_USE', 'rex_d2u_machinery_used_machines_media_is_in_use');
	rex_extension::register('ART_PRE_DELETED', 'rex_d2u_machinery_used_machines_article_is_in_use');
}

/**
 * Checks if article is used by this addon
 * @param rex_extension_point $ep Redaxo extension point
 * @return string[] Warning message as array
 * @throws rex_api_exception If article is used
*/
function rex_d2u_machinery_used_machines_article_is_in_use(rex_extension_point $ep) {
	$warning = [];
	$params = $ep->getParams();
	$article_id = $params['id'];

	// Prepare warnings
	// Settings
	$addon = rex_addon::get("d2u_machinery");
	if($addon->hasConfig("used_machine_article_id_rent") && $addon->getConfig("used_machine_article_id_rent") == $article_id ||
			$addon->hasConfig("used_machine_article_id_sale") && $addon->getConfig("used_machine_article_id_sale") == $article_id) {
		$warning[] = '<a href="javascript:openPage(\'index.php?page=d2u_machinery/settings\')">'.
			 rex_i18n::msg('d2u_machinery_rights_all') ." - ". rex_i18n::msg('d2u_machinery_meta_settings') . '</a>';
	}

	if(count($warning) > 0) {
		throw new rex_api_exception(rex_i18n::msg('d2u_helper_rex_article_cannot_delete') ."<br> ". implode("<br>", $warning));
	}
	else {
		return explode("<br>", $warning);
	}
}

/**
 * Checks if media is used by this addon
 * @param rex_extension_point $ep Redaxo extension point
 * @return string[] Warning message as array
 */
function rex_d2u_machinery_used_machines_media_is_in_use(rex_extension_point $ep) {
	$warning = $ep->getSubject();
	$params = $ep->getParams();
	$filename = addslashes($params['filename']);

	// Used machines
	$sql = rex_sql::factory();
	$sql->setQuery('SELECT lang.used_machine_id, manufacturer, name FROM `' . rex::getTablePrefix() . 'd2u_machinery_used_machines_lang` AS lang '
		.'LEFT JOIN `' . rex::getTablePrefix() . 'd2u_machinery_used_machines` AS used_machines ON lang.used_machine_id = used_machines.used_machine_id '
		.'WHERE pics LIKE "%'. $filename .'%" AND clang_id = '. rex_config::get("d2u_helper", "default_lang"));
	
	// Prepare warnings
	// Used machines
	for($i = 0; $i < $sql->getRows(); $i++) {
		$message = '<a href="javascript:openPage(\'index.php?page=d2u_machinery/used_machines&func=edit&entry_id='.
			$sql->getValue('used_machine_id') .'\')">'. rex_i18n::msg('d2u_machinery_used_machines_rights_used_machines') ." - ". rex_i18n::msg('d2u_machinery_used_machines') .': '. $sql->getValue('manufacturer') .' '. $sql->getValue('name') .'</a>';
		if(!in_array($message, $warning)) {
			$warning[] = $message;
		}
    }

	return $warning;
}