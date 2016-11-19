<?php
/**
 * Offers helper functions for language issues
 */
class machine_features_extension_lang_helper extends d2u_machinery_lang_helper {
	/**
	 * @var string[] Array with englisch replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_english = ['d2u_machinery_features' => 'Features'
		];

	/**
	 * @var string[] Array with german replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_german = ['d2u_machinery_features' => 'Features'
		];

	/**
	 * Factory method.
	 * @return machine_features_extension_lang_helper Object
	 */
	public static function factory() {
		return new machine_features_extension_lang_helper();
	}
}