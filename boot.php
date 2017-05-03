<?php

if(rex::isBackend() && is_object(rex::getUser())) {
	rex_perm::register('d2u_machinery[]', rex_i18n::msg('d2u_machinery_rights_all'));
	rex_perm::register('d2u_machinery[edit_lang]', rex_i18n::msg('d2u_machinery_rights_edit_lang'), rex_perm::OPTIONS);
	rex_perm::register('d2u_machinery[edit_tech_data]', rex_i18n::msg('d2u_machinery_rights_edit_tech_data'), rex_perm::OPTIONS);
	rex_perm::register('d2u_machinery[settings]', rex_i18n::msg('d2u_machinery_rights_settings'));	
}

if(rex::isBackend()) {
	rex_extension::register('MEDIA_IS_IN_USE', 'rex_d2u_machinery_media_is_in_use');
	rex_extension::register('ART_PRE_DELETED', 'rex_d2u_machinery_article_is_in_use');
}

/**
 * Checks if article is used by this addon
 * @param rex_extension_point $ep Redaxo extension point
 * @return string[] Warning message as array
 * @throws rex_api_exception If article is used
 */
function rex_d2u_machinery_article_is_in_use(rex_extension_point $ep) {
	$warning = [];
	$params = $ep->getParams();
	$article_id = $params['id'];

	// Machines
	$sql_machine = rex_sql::factory();
	$sql_machine->setQuery('SELECT lang.machine_id, name FROM `' . rex::getTablePrefix() . 'd2u_machinery_machines_lang` AS lang '
		.'LEFT JOIN `' . rex::getTablePrefix() . 'd2u_machinery_machines` AS machines ON lang.machine_id = machines.machine_id '
		.'WHERE article_id_software = "'. $article_id .'" OR article_id_service = "'. $article_id .'" '.
			'OR article_id_service = "'. $article_id .'" OR article_ids_references LIKE "%,'. $article_id .',%" OR article_ids_references LIKE "%,'. $article_id .'" OR article_ids_references LIKE "'. $article_id .',%"'
		.'GROUP BY machine_id');
	

	// Prepare warnings
	// Machines
	for($i = 0; $i < $sql_machine->getRows(); $i++) {
		$message = '<a href="javascript:openPage(\'index.php?page=d2u_machinery/machine&func=edit&entry_id='.
			$sql_machine->getValue('machine_id') .'\')">'. rex_i18n::msg('d2u_machinery_rights_all') ." - ". rex_i18n::msg('d2u_machinery_meta_machines') .': '. $sql_machine->getValue('name') .'</a>';
		if(!in_array($message, $warning)) {
			$warning[] = $message;
		}
    }
	
	// Settings
	$addon = rex_addon::get("d2u_machinery");
	if($addon->hasConfig("article_id") && $addon->getConfig("article_id") == $article_id) {
		$message = '<a href="javascript:openPage(\'index.php?page=d2u_machinery/settings\')">'.
			 rex_i18n::msg('d2u_machinery_rights_all') ." - ". rex_i18n::msg('d2u_machinery_meta_settings') . '</a>';
		if(!in_array($message, $warning)) {
			$warning[] = $message;
		}
	}

	if(count($warning) > 0) {
		throw new rex_api_exception(rex_i18n::msg('d2u_helper_rex_article_cannot_delete')."<br> ".implode("<br>", $warning));
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
function rex_d2u_machinery_media_is_in_use(rex_extension_point $ep) {
	$warning = $ep->getSubject();
	$params = $ep->getParams();
	$filename = addslashes($params['filename']);

	// Machines
	$sql_machine = rex_sql::factory();
	$sql_machine->setQuery('SELECT lang.machine_id, name FROM `' . rex::getTablePrefix() . 'd2u_machinery_machines_lang` AS lang '
		.'LEFT JOIN `' . rex::getTablePrefix() . 'd2u_machinery_machines` AS machines ON lang.machine_id = machines.machine_id '
		.'WHERE pdfs = "%'. $filename .'%" OR pics LIKE "%'. $filename .'%" '
		.'GROUP BY machine_id');
	
	// Categories
	$sql_categories = rex_sql::factory();
	$sql_categories->setQuery('SELECT lang.category_id, name FROM `' . rex::getTablePrefix() . 'd2u_machinery_categories_lang` AS lang '
		.'LEFT JOIN `' . rex::getTablePrefix() . 'd2u_machinery_categories` AS categories ON lang.category_id = categories.category_id '
		.'WHERE pic = "'. $filename .'" OR pic_usage = "'. $filename .'" OR pic_lang = "'. $filename .'" OR pdfs LIKE "%'. $filename .'%" ');  

	// Prepare warnings
	// Machines
	for($i = 0; $i < $sql_machine->getRows(); $i++) {
		$message = '<a href="javascript:openPage(\'index.php?page=d2u_machinery/machine&func=edit&entry_id='.
			$sql_machine->getValue('machine_id') .'\')">'. rex_i18n::msg('d2u_machinery_rights_all') ." - ". rex_i18n::msg('d2u_machinery_meta_machines') .': '. $sql_machine->getValue('name') .'</a>';
		if(!in_array($message, $warning)) {
			$warning[] = $message;
		}
    }

	// Categories
	for($i = 0; $i < $sql_categories->getRows(); $i++) {
		$message = '<a href="javascript:openPage(\'index.php?page=d2u_machinery/category&func=edit&entry_id='. $sql_categories->getValue('category_id') .'\')">'.
			 rex_i18n::msg('d2u_machinery_rights_all') ." - ". rex_i18n::msg('d2u_machinery_meta_categories') .': '. $sql_categories->getValue('name') . '</a>';
		if(!in_array($message, $warning)) {
			$warning[] = $message;
		}
    }
	
	// Settings
	$addon = rex_addon::get("d2u_machinery");
	if($addon->hasConfig("consultation_pic") && $addon->getConfig("consultation_pic") == $filename) {
		$message = '<a href="javascript:openPage(\'index.php?page=d2u_machinery/settings\')">'.
			 rex_i18n::msg('d2u_machinery_rights_all') ." - ". rex_i18n::msg('d2u_machinery_meta_settings') . '</a>';
		if(!in_array($message, $warning)) {
			$warning[] = $message;
		}
	}

	return $warning;
}