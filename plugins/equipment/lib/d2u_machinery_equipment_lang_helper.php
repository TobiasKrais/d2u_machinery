<?php
/**
 * Offers helper functions for language issues
 */
class d2u_machinery_equipment_lang_helper extends d2u_machinery_lang_helper {
	/**
	 * @var string[] Array with englisch replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_english = [
		'd2u_machinery_equipment' => 'Equipment',
		'd2u_machinery_equipment_artno' => 'Article No.',
	];

	/**
	 * @var string[] Array with german replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_german = [
		'd2u_machinery_equipment' => 'Zubehör',
		'd2u_machinery_equipment_artno' => 'Artikelnummer',
	];

	/**
	 * @var string[] Array with french replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_french = [
		'd2u_machinery_equipment' => 'Accessoires',
		'd2u_machinery_equipment_artno' => 'Numéro d\'article',
	];

	/**
	 * Factory method.
	 * @return d2u_machinery_equipment_lang_helper Object
	 */
	public static function factory() {
		return new d2u_machinery_equipment_lang_helper();
	}
}