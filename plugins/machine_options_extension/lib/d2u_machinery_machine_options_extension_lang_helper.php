<?php
/**
 * Offers helper functions for language issues
 */
class d2u_machinery_machine_options_extension_lang_helper extends d2u_machinery_lang_helper {
	/**
	 * @var string[] Array with englisch replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_english = [
		'd2u_machinery_options' => 'Options'
	];

	/**
	 * @var string[] Array with german replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_german = [
		'd2u_machinery_options' => 'Options',
	];

	/**
	 * @var string[] Array with french replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_french = [
		'd2u_machinery_options' => 'Options',
	];

	/**
	 * @var string[] Array with spanish replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_spanish = [
		'd2u_machinery_options' => 'Opciones',
	];

	/**
	 * @var string[] Array with italian replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_italian = [
		'd2u_machinery_options' => 'Opzioni',
	];

	/**
	 * @var string[] Array with polish replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_polish = [
		'd2u_machinery_options' => 'Opcje',
	];
	
	/**
	 * @var string[] Array with dutch replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_dutch = [
		'd2u_machinery_options' => 'Opties',
	];

	/**
	 * @var string[] Array with czech replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_czech = [
		'd2u_machinery_options' => 'Možnosti',
	];

	/**
	 * @var string[] Array with russian replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_russian = [
		'd2u_machinery_options' => 'Параметры',
	];

	/**
	 * @var string[] Array with portuguese replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_portuguese = [
		'd2u_machinery_options' => 'Opções',
	];

	/**
	 * @var string[] Array with chinese replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_chinese = [
		'd2u_machinery_options' => '选项',
	];

	/**
	 * Factory method.
	 * @return d2u_machinery_machine_options_extension_lang_helper Object
	 */
	public static function factory() {
		return new d2u_machinery_machine_options_extension_lang_helper();
	}
}