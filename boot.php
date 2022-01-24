<?php
if(\rex::isBackend() && is_object(\rex::getUser())) {
	rex_perm::register('d2u_machinery[]', rex_i18n::msg('d2u_machinery_rights_all'));
	rex_perm::register('d2u_machinery[machine]', rex_i18n::msg('d2u_machinery_rights_all') .": ". rex_i18n::msg('d2u_machinery_meta_machines'));
	rex_perm::register('d2u_machinery[category]', rex_i18n::msg('d2u_machinery_rights_all') .": ". rex_i18n::msg('d2u_helper_categories'));
	rex_perm::register('d2u_machinery[edit_lang]', rex_i18n::msg('d2u_machinery_rights_edit_lang'), rex_perm::OPTIONS);
	rex_perm::register('d2u_machinery[edit_data]', rex_i18n::msg('d2u_machinery_rights_edit_data'), rex_perm::OPTIONS);
	rex_perm::register('d2u_machinery[settings]', rex_i18n::msg('d2u_machinery_rights_settings'), rex_perm::OPTIONS);	
}

if(\rex::isBackend()) {
	rex_extension::register('ART_PRE_DELETED', 'rex_d2u_machinery_article_is_in_use');
	rex_extension::register('CLANG_DELETED', 'rex_d2u_machinery_clang_deleted');
	rex_extension::register('MEDIA_IS_IN_USE', 'rex_d2u_machinery_media_is_in_use');
}
// Call this extension point also in frontend
rex_extension::register('URL_PRE_SAVE', 'rex_d2u_machinery_url_shortener');

/**
 * Checks if article is used by this addon
 * @param rex_extension_point $ep Redaxo extension point
 * @return string Warning message as array
 * @throws rex_api_exception If article is used
 */
function rex_d2u_machinery_article_is_in_use(rex_extension_point $ep) {
	$warning = [];
	$params = $ep->getParams();
	$article_id = $params['id'];

	// Machines
	$sql_machine = \rex_sql::factory();
	$sql_machine->setQuery('SELECT lang.machine_id, name FROM `' . \rex::getTablePrefix() . 'd2u_machinery_machines_lang` AS lang '
		.'LEFT JOIN `' . \rex::getTablePrefix() . 'd2u_machinery_machines` AS machines ON lang.machine_id = machines.machine_id '
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
		$sql_machine->next();
    }
	
	// Settings
	$addon = rex_addon::get("d2u_machinery");
	if($addon->hasConfig("article_id") && $addon->getConfig("article_id") == $article_id) {
		$message = '<a href="index.php?page=d2u_machinery/settings">'.
			 rex_i18n::msg('d2u_machinery_rights_all') ." - ". rex_i18n::msg('d2u_helper_settings') . '</a>';
		if(!in_array($message, $warning)) {
			$warning[] = $message;
		}
	}

	if(count($warning) > 0) {
		throw new rex_api_exception(rex_i18n::msg('d2u_helper_rex_article_cannot_delete')."<ul><li>". implode("</li><li>", $warning) ."</li></ul>");
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
function rex_d2u_machinery_clang_deleted(rex_extension_point $ep) {
	$warning = $ep->getSubject();
	$params = $ep->getParams();
	$clang_id = $params['id'];

	// Delete
	$categories = Category::getAll($clang_id);
	foreach ($categories as $category) {
		$category->delete(FALSE);
	}
	$machines = Machine::getAll($clang_id, FALSE);
	foreach ($machines as $machine) {
		$machine->delete(FALSE);
	}
	
	// Delete language settings
	if(rex_config::has('d2u_machinery', 'lang_replacement_'. $clang_id)) {
		rex_config::remove('d2u_machinery', 'lang_replacement_'. $clang_id);
	}
	// Delete language replacements
	d2u_machinery_lang_helper::factory()->uninstall($clang_id);

	return $warning;
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
	$sql_machine = \rex_sql::factory();
	$sql_machine->setQuery('SELECT lang.machine_id, name FROM `' . \rex::getTablePrefix() . 'd2u_machinery_machines_lang` AS lang '
		.'LEFT JOIN `' . \rex::getTablePrefix() . 'd2u_machinery_machines` AS machines ON lang.machine_id = machines.machine_id '
		.'WHERE FIND_IN_SET("'. $filename .'", pdfs) OR FIND_IN_SET("'. $filename .'", pics) OR description LIKE "%'. $filename .'%" OR benefits_short LIKE "%'. $filename .'%" OR benefits_long LIKE "%'. $filename .'%" OR leaflet = "'. $filename .'"'
		. (rex_plugin::get('d2u_machinery', 'machine_construction_equipment_extension')->isAvailable() ? 'OR FIND_IN_SET("'. $filename .'", pictures_delivery_set) ' : '')
		.'GROUP BY machine_id');
	
	// Categories
	$sql_categories = \rex_sql::factory();
	$sql_categories->setQuery('SELECT lang.category_id, name FROM `' . \rex::getTablePrefix() . 'd2u_machinery_categories_lang` AS lang '
		.'LEFT JOIN `' . \rex::getTablePrefix() . 'd2u_machinery_categories` AS categories ON lang.category_id = categories.category_id '
		.'WHERE pic = "'. $filename .'" OR pic_usage = "'. $filename .'" OR pic_lang = "'. $filename .'" OR FIND_IN_SET("'. $filename .'", pdfs) OR description LIKE "%'. $filename .'%"');  

	// Prepare warnings
	// Machines
	for($i = 0; $i < $sql_machine->getRows(); $i++) {
		$message = '<a href="javascript:openPage(\'index.php?page=d2u_machinery/machine&func=edit&entry_id='.
			$sql_machine->getValue('machine_id') .'\')">'. rex_i18n::msg('d2u_machinery_rights_all') ." - ". rex_i18n::msg('d2u_machinery_meta_machines') .': '. $sql_machine->getValue('name') .'</a>';
		if(!in_array($message, $warning)) {
			$warning[] = $message;
		}
		$sql_machine->next();
    }

	// Categories
	for($i = 0; $i < $sql_categories->getRows(); $i++) {
		$message = '<a href="javascript:openPage(\'index.php?page=d2u_machinery/category&func=edit&entry_id='. $sql_categories->getValue('category_id') .'\')">'.
			 rex_i18n::msg('d2u_machinery_rights_all') ." - ". rex_i18n::msg('d2u_helper_categories') .': '. $sql_categories->getValue('name') . '</a>';
		if(!in_array($message, $warning)) {
			$warning[] = $message;
		}
		$sql_categories->next();
    }
	
	// Settings
	$addon = rex_addon::get("d2u_machinery");
	if(($addon->hasConfig("consultation_pic") && $addon->getConfig("consultation_pic") == $filename)
		|| ($addon->hasConfig("consultation_pics") && str_contains($addon->getConfig("consultation_pics"), $filename))) {
		$message = '<a href="javascript:openPage(\'index.php?page=d2u_machinery/settings\')">'.
			 rex_i18n::msg('d2u_machinery_rights_all') ." - ". rex_i18n::msg('d2u_helper_settings') . '</a>';
		if(!in_array($message, $warning)) {
			$warning[] = $message;
		}
	}

	return $warning;
}

/**
 * Shortens URL by removing article or category name 
 * @param rex_extension_point $ep Redaxo extension point
 * @return Url New URL
 */
function rex_d2u_machinery_url_shortener(rex_extension_point $ep) {
	$params = $ep->getParams();
	$url = $params['object'];
	$article_id = $params['article_id'];
	$clang_id = $params['clang_id'];
	
	// Only shorten URLs that are not start article and articles of this addon
	if($article_id != rex_yrewrite::getDomainByArticleId($article_id, $clang_id)->getStartId() &&
			($article_id == rex_config::get('d2u_machinery', 'article_id'))
		) {
		$domain = rex_yrewrite::getDomainByArticleId($article_id);

		// First: delete forwarder, if exists - there should be no forwarder to an existing URL
		if(rex_config::get('d2u_machinery', 'short_urls_forward', "false") === "false") {
			$query = "DELETE FROM ". \rex::getTablePrefix() ."yrewrite_forward "
				."WHERE `url` = '". trim(str_replace($domain->getName(), "/", $url->__toString()), "/") ."'";
			$result = \rex_sql::factory();
			$result->setQuery($query);

			// Don't forget to regenerate YRewrtie path file this way
			// rex_yrewrite_forward::init();
			// rex_yrewrite_forward::generatePathFile();
			// This cannot be done here, because method would be called to often
		}

		// Second: make URL shorter
		if(rex_config::get('d2u_machinery', 'short_urls', 'false') === 'true') {
			$article_url = rex_getUrl($article_id, $clang_id);
			$start_article_url = rex_getUrl(rex_yrewrite::getDomainByArticleId($article_id, $clang_id)->getStartId(), $clang_id);
			$article_url_without_lang_slug = '';
			if(strlen($start_article_url) <= 1) {
				$article_url_without_lang_slug = str_replace('/'. strtolower(rex_clang::get($clang_id)->getCode()) .'/', '/', $article_url);
			}
			else {
				$article_url_without_lang_slug = str_replace($start_article_url, '/', $article_url);
			}
			
			// In case $url is urlencoded, encode your url, too
			$article_url_without_lang_slug_split = explode("/", $article_url_without_lang_slug);
			for($i = 0; $i < count($article_url_without_lang_slug_split); $i++) {
				$article_url_without_lang_slug_split[$i] = urlencode($article_url_without_lang_slug_split[$i]);
			}
			$article_url_without_lang_slug_split_encoded = implode("/", $article_url_without_lang_slug_split);

			// Replace
			$new_url = new \Url\Url(str_replace($article_url_without_lang_slug_split_encoded, '/', $url->__toString()));

			// Add forwarders
			if(rex_config::get('d2u_machinery', 'short_urls_forward', "false") === "true") {
				$query = "SELECT id FROM ". \rex::getTablePrefix() ."yrewrite_forward "
					."WHERE extern = '". str_replace("///", "", $domain->getUrl() . str_replace($domain->getName(), '', urldecode($new_url->__toString()))) ."' "
					. "OR url = '". trim(str_replace($domain->getName(), "/", urldecode($url->__toString())), "/") ."'";
				$result = \rex_sql::factory();
				$result->setQuery($query);

				// Add only if not already existing
				if($result->getRows() == 0 && $domain->getId() > 0) {
					$query_forward = "INSERT INTO `". \rex::getTablePrefix() ."yrewrite_forward` (`domain_id`, `status`, `url`, `type`, `article_id`, `clang`, `extern`, `movetype`, `expiry_date`) "
						."VALUES (". $domain->getId() .", 1, '". trim(str_replace($domain->getName(), "/", urldecode($url->__toString())), "/") ."', 'extern', ". $article_id .", ". $clang_id .", '". str_replace("///", "", $domain->getUrl() . str_replace($domain->getName(), '', urldecode($new_url->__toString()))) ."', '301', '0000-00-00');";
					$result_forward = \rex_sql::factory();
					$result_forward->setQuery($query_forward);

					// Don't forget to regenerate YRewrite path file this way
					// rex_yrewrite_forward::init();
					// rex_yrewrite_forward::generatePathFile();
					// This cannot be done here, because method would be called to often
				}
			}

			return $new_url;
		}
	}
	
	return $url;
}