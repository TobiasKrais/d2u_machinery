<?php
/**
 * Offers helper functions for language issues
 */
class d2u_machinery_service_options_lang_helper extends d2u_machinery_lang_helper {
	/**
	 * @var array<string, string> Array with englisch replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	var $replacements_english = [
		'd2u_machinery_construction_equipment_service' => 'Service',
	];

	/**
	 * @var array<string, string> Array with german replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected array $replacements_german = [
		'd2u_machinery_construction_equipment_service' => 'Service',
	];

	/**
	 * @var array<string, string> Array with french replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected array $replacements_french = [
		'd2u_machinery_construction_equipment_service' => 'Service',
	];
	
	/**
	 * Factory method.
	 * @return d2u_machinery_service_options_lang_helper Object
	 */
	public static function factory() {
		return new d2u_machinery_service_options_lang_helper();
	}
}