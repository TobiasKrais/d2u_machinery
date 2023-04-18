<?php
/**
 * @api
 * Offers helper functions for language issues.
 */
class d2u_machinery_industry_sectors_lang_helper extends d2u_machinery_lang_helper
{
    /**
     * @var array<string,string> Array with englisch replacements. Key is the wildcard,
     * value the replacement.
     */
    public $replacements_english = ['d2u_machinery_industry_sectors' => 'At Home in Your Industries',
    ];

    /**
     * @var array<string,string> Array with german replacements. Key is the wildcard,
     * value the replacement.
     */
    protected array $replacements_german = ['d2u_machinery_industry_sectors' => 'Für Ihre Branche',
    ];

    /**
     * @var array<string,string> Array with french replacements. Key is the wildcard,
     * value the replacement.
     */
    protected array $replacements_french = [
        'd2u_machinery_industry_sectors' => 'Branches',
    ];

    /**
     * @var array<string,string> Array with spanish replacements. Key is the wildcard,
     * value the replacement.
     */
    protected array $replacements_spanish = [
        'd2u_machinery_industry_sectors' => 'Industrias',
    ];

    /**
     * @var array<string,string> Array with dutch replacements. Key is the wildcard,
     * value the replacement.
     */
    protected array $replacements_dutch = [
        'd2u_machinery_industry_sectors' => 'Sectoren',
    ];

    /**
     * @var array<string,string> Array with russian replacements. Key is the wildcard,
     * value the replacement.
     */
    protected array $replacements_russian = [
        'd2u_machinery_industry_sectors' => 'Отрасли',
    ];

    /**
     * @var array<string,string> Array with portuguese replacements. Key is the wildcard,
     * value the replacement.
     */
    protected array $replacements_portuguese = [
        'd2u_machinery_industry_sectors' => 'Setores',
    ];

    /**
     * @var array<string,string> Array with chinese replacements. Key is the wildcard,
     * value the replacement.
     */
    protected array $replacements_chinese = [
        'd2u_machinery_industry_sectors' => '行业',
    ];

    /**
     * Factory method.
     * @return d2u_machinery_industry_sectors_lang_helper Object
     */
    public static function factory()
    {
        return new self();
    }
}
