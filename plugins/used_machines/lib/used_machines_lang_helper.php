<?php
/**
 * Offers helper functions for language issues
 */
class used_machines_lang_helper extends d2u_machinery_lang_helper {
	/**
	 * @var string[] Array with englisch replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_english = array(
		'd2u_machinery_used_machines' => 'Used machines'
	);

	/**
	 * @var string[] Array with englisch replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_german = array(
		'd2u_machinery_used_machines' => 'Gebrauchtmaschinen'
	);

	/**
	 * Factory method.
	 * @return used_machines_lang_helper Object
	 */
	public static function factory() {
		return new used_machines_lang_helper();
	}
}