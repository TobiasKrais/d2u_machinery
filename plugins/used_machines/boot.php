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
	$d2u_video = rex_addon::get('d2u_videos');
	if($d2u_video->isAvailable() && rex_version::compare($d2u_video->getVersion(), '1.1', '>=')) {
		rex_extension::register('YREWRITE_SITEMAP', 'rex_d2u_machinery_used_machines_video_sitemap');	
	}
}

/**
 * Checks if article is used by this addon
 * @param rex_extension_point<string> $ep Redaxo extension point
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
	if($addon->hasConfig("used_machine_article_id_rent") && intval($addon->getConfig("used_machine_article_id_rent")) === $article_id ||
			$addon->hasConfig("used_machine_article_id_sale") && intval($addon->getConfig("used_machine_article_id_sale")) === $article_id) {
		$warning[] = '<a href="javascript:openPage(\'index.php?page=d2u_machinery/settings\')">'.
			 rex_i18n::msg('d2u_machinery_used_machines') ." - ". rex_i18n::msg('d2u_helper_settings') . '</a>';
	}

	if(count($warning) > 0) {
		throw new rex_api_exception(rex_i18n::msg('d2u_helper_rex_article_cannot_delete') ."<ul><li>". implode("</li><li>", $warning) ."</li></ul>");
	}
	else {
		return [];
	}
}


/**
 * Deletes language specific configurations and objects
 * @param rex_extension_point<string> $ep Redaxo extension point
 * @return string[] Warning message as array
 */
function rex_d2u_machinery_used_machines_clang_deleted(rex_extension_point $ep) {
	/** @var string[] $warning */
	$warning = $ep->getSubject();
	$params = $ep->getParams();
	$clang_id = $params['id'];

	// Delete
	$used_machines = UsedMachine::getAll($clang_id);
	foreach ($used_machines as $used_machine) {
		$used_machine->delete(false);
	}
	
	// Delete language replacements
	d2u_machinery_used_machines_lang_helper::factory()->uninstall($clang_id);

	return $warning;
}

/**
 * Checks if media is used by this addon
 * @param rex_extension_point<string> $ep Redaxo extension point
 * @return string[] Warning message as array
 */
function rex_d2u_machinery_used_machines_media_is_in_use(rex_extension_point $ep) {
	/** @var string[] $warning */
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
		if(!in_array($message, $warning, true)) {
			$warning[] = $message;
		}
		$sql->next();
    }

	return $warning;
}

/**
 * Adds videos to sitemap
 * @param rex_extension_point<string> $ep Redaxo extension point
 * @return string[] updated sitemap entries
 */
function rex_d2u_machinery_used_machines_video_sitemap(rex_extension_point $ep) {
	/** @var string[] $sitemap_entries */
	$sitemap_entries = $ep->getSubject();

	foreach(rex_clang::getAllIds(true) as $clang_id) {		
		$used_machines = UsedMachine::getAll($clang_id, true);

		foreach($used_machines as $used_machine) {
			$video_entry = '';
			// Get sitemap entry for videos
			foreach($used_machine->videos as $video) {
				$video_entry .= $video->getSitemapEntry();
			}
			// insert into sitemap
			foreach($sitemap_entries as $sitemap_key => $sitemap_entry) {
				if(str_contains($sitemap_entry, $used_machine->getURL() .'</loc>')) {
					$sitemap_entries[$sitemap_key] = str_replace('</url>', $video_entry .'</url>', $sitemap_entry);
				}
			}
		}
	}	
	
	return $sitemap_entries;
}