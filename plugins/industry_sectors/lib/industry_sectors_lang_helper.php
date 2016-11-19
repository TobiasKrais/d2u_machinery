<?php
/**
 * Offers helper functions for language issues
 */
class industry_sectors_lang_helper extends d2u_machinery_lang_helper {
	/**
	 * @var string[] Array with englisch replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_english = ['d2u_machinery_industry_sectors' => 'Industry sectors'
		];

	/**
	 * @var string[] Array with german replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_german = ['d2u_machinery_industry_sectors' => 'Branchen'
		];

	/**
	 * Factory method.
	 * @return industry_sectors_lang_helper Object
	 */
	public static function factory() {
		return new industry_sectors_lang_helper();
	}
}