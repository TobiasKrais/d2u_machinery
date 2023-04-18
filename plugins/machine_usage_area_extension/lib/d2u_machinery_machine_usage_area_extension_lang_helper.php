<?php
/**
 * @api
 * Offers helper functions for language issues.
 */
class d2u_machinery_machine_usage_area_extension_lang_helper extends d2u_machinery_lang_helper
{
    /**
     * @var array<string,string> Array with englisch replacements. Key is the wildcard,
     * value the replacement.
     */
    public $replacements_english = ['d2u_machinery_usage_areas' => 'Usage Areas',
    ];

    /**
     * @var array<string,string> Array with german replacements. Key is the wildcard,
     * value the replacement.
     */
    protected array $replacements_german = ['d2u_machinery_usage_areas' => 'Anwendungsgebiete',
    ];

    /**
     * Factory method.
     * @return d2u_machinery_machine_usage_area_extension_lang_helper Object
     */
    public static function factory()
    {
        return new self();
    }
}
