<?php
/**
 * @api
 * Offers helper functions for language issues.
 */
class d2u_machinery_machine_features_extension_lang_helper extends d2u_machinery_lang_helper
{
    /**
     * @var array<string, string> Array with englisch replacements. Key is the wildcard,
     * value the replacement.
     */
    public $replacements_english = [
        'd2u_machinery_features' => 'Features',
    ];

    /**
     * @var array<string, string> Array with german replacements. Key is the wildcard,
     * value the replacement.
     */
    protected array $replacements_german = [
        'd2u_machinery_features' => 'Features',
    ];

    /**
     * @var array<string, string> Array with french replacements. Key is the wildcard,
     * value the replacement.
     */
    protected array $replacements_french = [
        'd2u_machinery_features' => 'Caractéristiques',
    ];

    /**
     * @var array<string, string> Array with spanish replacements. Key is the wildcard,
     * value the replacement.
     */
    protected array $replacements_spanish = [
        'd2u_machinery_features' => 'Características',
    ];

    /**
     * @var array<string, string> Array with dutch replacements. Key is the wildcard,
     * value the replacement.
     */
    protected array $replacements_dutch = [
        'd2u_machinery_features' => 'Kenmerken',
    ];

    /**
     * @var array<string, string> Array with russian replacements. Key is the wildcard,
     * value the replacement.
     */
    protected array $replacements_russian = [
        'd2u_machinery_features' => 'Функциональные возможности',
    ];

    /**
     * @var array<string, string> Array with portuguese replacements. Key is the wildcard,
     * value the replacement.
     */
    protected array $replacements_portuguese = [
        'd2u_machinery_features' => 'Características',
    ];

    /**
     * @var array<string, string> Array with chinese replacements. Key is the wildcard,
     * value the replacement.
     */
    protected array $replacements_chinese = [
        'd2u_machinery_features' => '特点',
    ];

    /**
     * Factory method.
     * @return d2u_machinery_machine_features_extension_lang_helper Object
     */
    public static function factory()
    {
        return new self();
    }
}
