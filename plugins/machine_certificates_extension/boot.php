<?php
if(rex::isBackend()) {
	rex_extension::register('MEDIA_IS_IN_USE', 'rex_d2u_machinery_certificates_media_is_in_use');
}

/**
 * Checks if media is used by this plugin
 * @param rex_extension_point $ep Redaxo extension point
 * @return string[] Warning message as array
 */
function rex_d2u_machinery_certificates_media_is_in_use(rex_extension_point $ep) {
	$warning = $ep->getSubject();
	$params = $ep->getParams();
	$filename = addslashes($params['filename']);

	// Certificates
	$sql = rex_sql::factory();
	$sql->setQuery('SELECT lang.certificate_id, name FROM `' . rex::getTablePrefix() . 'd2u_machinery_certificates_lang` AS lang '
		.'LEFT JOIN `' . rex::getTablePrefix() . 'd2u_machinery_certificates` AS certificates ON lang.certificate_id = certificates.certificate_id '
		.'WHERE pic = "'. $filename .'" AND clang_id = '. rex_config::get("d2u_machinery", "default_lang"));
	
	// Prepare warnings
	// Certificates
	for($i = 0; $i < $sql->getRows(); $i++) {
		$message =  '<a href="javascript:openPage(\'index.php?page=d2u_machinery/machine_certificates_extension&func=edit&entry_id='.
			$sql->getValue('certificate_id') .'\')">'. rex_i18n::msg('d2u_machinery_rights_all') ." - ". rex_i18n::msg('d2u_machinery_certificates') .': '. $sql->getValue('name') .'</a>';
  		if(!in_array($message, $warning)) {
			$warning[] = $message;
		}
	}

	return $warning;
}