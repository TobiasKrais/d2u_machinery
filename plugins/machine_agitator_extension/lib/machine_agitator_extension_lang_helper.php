<?php
/**
 * Offers helper functions for language issues
 */
class machine_agitator_extension_lang_helper extends d2u_machinery_lang_helper {
	/**
	 * @var string[] Array with englisch replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_english = ['d2u_machinery_agitator' => 'Agitator',
		'd2u_machinery_agitators_mpas' => 'mPas',
		'd2u_machinery_agitators_viscosity' => 'Viscosity up to'
	   ];

	/**
	 * @var string[] Array with englisch replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_german = ['d2u_machinery_agitator' => 'Rührwerk',
		'd2u_machinery_agitators_mpas' => 'mPas',
		'd2u_machinery_agitators_viscosity' => 'Viskosität bis zu'
	   ];
	
	/**
	 * Factory method.
	 * @return machine_agitator_extension_lang_helper Object
	 */
	public static function factory() {
		return new machine_agitator_extension_lang_helper();
	}
}