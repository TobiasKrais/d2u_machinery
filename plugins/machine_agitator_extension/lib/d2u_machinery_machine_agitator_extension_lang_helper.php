<?php
/**
 * @api
 * Offers helper functions for language issues.
 */
class d2u_machinery_machine_agitator_extension_lang_helper extends d2u_machinery_lang_helper
{
    /**
     * @var array<string,string> Array with englisch replacements. Key is the wildcard,
     * value the replacement.
     */
    public $replacements_english = ['d2u_machinery_agitator' => 'Agitator',
        'd2u_machinery_agitators_mpas' => 'mPas',
        'd2u_machinery_agitators_viscosity' => 'Viscosity up to',
    ];

    /**
     * @var array<string,string> Array with german replacements. Key is the wildcard,
     * value the replacement.
     */
    protected array $replacements_german = ['d2u_machinery_agitator' => 'Rührwerk',
        'd2u_machinery_agitators_mpas' => 'mPas',
        'd2u_machinery_agitators_viscosity' => 'Viskosität bis zu',
    ];

    /**
     * Factory method.
     * @return d2u_machinery_machine_agitator_extension_lang_helper Object
     */
    public static function factory()
    {
        return new self();
    }
}
