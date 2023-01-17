<?php
// EPs
if(rex::isBackend()) {
	rex_extension::register('MEDIA_IS_IN_USE', 'rex_d2u_machinery_contacts_media_is_in_use');
}

/**
 * Checks if media is used by this addon
 * @param rex_extension_point<string> $ep Redaxo extension point
 * @return array<string> Warning message as array
 */
function rex_d2u_machinery_contacts_media_is_in_use(rex_extension_point $ep) {
	/** @var array<string> $warning */
	$warning = $ep->getSubject();
	$params = $ep->getParams();
	$filename = addslashes($params['filename']);

	// Contacts
	$sql_contacts = rex_sql::factory();
	$sql_contacts->setQuery('SELECT contact_id, name FROM `' . rex::getTablePrefix() . 'd2u_machinery_contacts` '
		.'WHERE picture = "'. $filename .'"');  

	// Prepare warnings
	// Contacts
	for($i = 0; $i < $sql_contacts->getRows(); $i++) {
		$message = '<a href="javascript:openPage(\'index.php?page=d2u_machinery/contact&func=edit&entry_id='. $sql_contacts->getValue('contact_id') .'\')">'.
			 rex_i18n::msg('d2u_machinery_meta_title') ." - ". rex_i18n::msg('d2u_machinery_contacts') .': '. $sql_contacts->getValue('name') . '</a>';
		if(!in_array($message, $warning, true)) {
			$warning[] = $message;
		}
		$sql_contacts->next();
    }
	
	return $warning;
}