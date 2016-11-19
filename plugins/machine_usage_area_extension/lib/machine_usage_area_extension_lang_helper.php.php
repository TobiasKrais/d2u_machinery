<?php
/**
 * Offers helper functions for language issues
 */
class machine_usage_area_extension_lang_helper extends d2u_machinery_lang_helper {
	/**
	 * @var string[] Array with englisch replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_english = ['d2u_machinery_usage_areas' => 'Usage Areas'
		];

	/**
	 * @var string[] Array with german replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_german = ['d2u_machinery_usage_areas' => 'Anwendungsgebiete'
		];

	/**
	 * Factory method.
	 * @return machine_usage_area_extension_lang_helper Object
	 */
	public static function factory() {
		return new machine_usage_area_extension_lang_helper();
	}
}