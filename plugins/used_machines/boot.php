<?php
if(\rex::isBackend() && is_object(\rex::getUser())) {
	rex_perm::register('d2u_machinery[used_machines]', rex_i18n::msg('d2u_machinery_used_machines_rights'));
}

if(\rex::isBackend()) {
	rex_extension::register('CLANG_DELETED', 'rex_d2u_machinery_used_machines_clang_deleted');
	rex_extension::register('MEDIA_IS_IN_USE', 'rex_d2u_machinery_used_machines_media_is_in_use');
	rex_extension::register('ART_PRE_DELETED', 'rex_d2u_machinery_used_machines_article_is_in_use');
}
else {
	rex_extension::register('PACKAGES_INCLUDED', 'd2u_machinery_used_machines_add_open_graph_call', rex_extension::LATE);
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
			 rex_i18n::msg('d2u_machinery_used_machines') ." - ". rex_i18n::msg('d2u_helper_settings') . '</a>';
	}

	if(count($warning) > 0) {
		throw new rex_api_exception(rex_i18n::msg('d2u_helper_rex_article_cannot_delete') ."<ul><li>". implode("</li><li>", $warning) ."</li></ul>");
	}
	else {
		return "";
	}
}


/**
 * Deletes language specific configurations and objects
 * @param rex_extension_point $ep Redaxo extension point
 * @return string[] Warning message as array
 */
function rex_d2u_machinery_used_machines_clang_deleted(rex_extension_point $ep) {
	$warning = $ep->getSubject();
	$params = $ep->getParams();
	$clang_id = $params['id'];

	// Delete
	$used_machines = UsedMachine::getAll($clang_id);
	foreach ($used_machines as $used_machine) {
		$used_machine->delete(FALSE);
	}
	
	// Delete language replacements
	d2u_machinery_used_machines_lang_helper::factory()->uninstall($clang_id);

	return $warning;
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
	$sql = \rex_sql::factory();
	$sql->setQuery('SELECT lang.used_machine_id, manufacturer, name FROM `' . \rex::getTablePrefix() . 'd2u_machinery_used_machines_lang` AS lang '
		.'LEFT JOIN `' . \rex::getTablePrefix() . 'd2u_machinery_used_machines` AS used_machines ON lang.used_machine_id = used_machines.used_machine_id '
		.'WHERE FIND_IN_SET("'. $filename .'", pics) AND clang_id = '. \rex_config::get("d2u_helper", "default_lang", \rex_clang::getStartId()));
	
	// Prepare warnings
	// Used machines
	for($i = 0; $i < $sql->getRows(); $i++) {
		$message = '<a href="javascript:openPage(\'index.php?page=d2u_machinery/used_machines&func=edit&entry_id='.
			$sql->getValue('used_machine_id') .'\')">'. rex_i18n::msg('d2u_machinery_used_machines') ." - ". rex_i18n::msg('d2u_machinery_used_machines') .': '. $sql->getValue('manufacturer') .' '. $sql->getValue('name') .'</a>';
		if(!in_array($message, $warning)) {
			$warning[] = $message;
		}
		$sql->next();
    }

	return $warning;
}

/**
 * Call Open Graph method when all functions are available <head>.
 */
function d2u_machinery_used_machines_add_open_graph_call() {
	rex_extension::register('OUTPUT_FILTER', 'd2u_machinery_used_machines_add_open_graph');	
}

/**
 * Add Open Graph to used machine sites
 * @param rex_extension_point $ep Redaxo extension point
 */
function d2u_machinery_used_machines_add_open_graph(rex_extension_point $ep) {
	$url_namespace = d2u_addon_frontend_helper::getUrlNamespace();
	$url_id = d2u_addon_frontend_helper::getUrlId();
	
	if((filter_input(INPUT_GET, 'used_rent_machine_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || $url_namespace === "used_rent_machine_id")
			|| (filter_input(INPUT_GET, 'used_sale_machine_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || $url_namespace === "used_sale_machine_id")) {
		$used_machine_id = filter_input(INPUT_GET, 'used_sale_machine_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 ? filter_input(INPUT_GET, 'used_sale_machine_id', FILTER_VALIDATE_INT) : filter_input(INPUT_GET, 'used_rent_machine_id', FILTER_VALIDATE_INT);
		if(\rex_addon::get("url")->isAvailable() && $url_id > 0) {
			$used_machine_id = $url_id;
		}
		
		if($used_machine_id > 0) { 
			$used_machine = new UsedMachine($used_machine_id, rex_clang::getCurrentId());
			$og_head = '
				<meta property="og:url" content="'. $used_machine->getURL(TRUE) .'" />
				<meta property="og:type" content="article" />
				<meta property="og:title" content="'. $used_machine->manufacturer .' '. $used_machine->name .'" />
				<meta property="og:description"	content="'. $used_machine->getExtendedTeaser() .'" />'. PHP_EOL;
			if(count($used_machine->pics) > 0) {
				$og_head .= '<meta property="og:image" content="'. rex_url::media($used_machine->pics[0]).'" />'. PHP_EOL;
			}
			$ep->setSubject(str_replace('</head>', $og_head .'</head>', $ep->getSubject()));
		}
	}
}