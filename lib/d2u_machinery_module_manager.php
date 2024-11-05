<?php
/**
 * Class managing modules published by www.design-to-use.de.
 *
 * @author Tobias Krais
 */
class D2UMachineryModules
{
    /**
     * Get modules offered by this addon.
     * @return \TobiasKrais\D2UHelper\Module[] Modules offered by this addon
     */
    public static function getModules()
    {
        $modules = [];
        $modules[] = new \TobiasKrais\D2UHelper\Module('90-1',
            'D2U Machinery Addon - Hauptausgabe',
            18);
        if (rex_plugin::get('d2u_machinery', 'industry_sectors')->isAvailable()) {
            $modules[] = new \TobiasKrais\D2UHelper\Module('90-2',
                'D2U Machinery Addon - Branchen',
                2);
        }
        $modules[] = new \TobiasKrais\D2UHelper\Module('90-3',
            'D2U Machinery Addon - Kategorien',
            6);
        if (rex_plugin::get('d2u_machinery', 'used_machines')->isAvailable()) {
            $modules[] = new \TobiasKrais\D2UHelper\Module('90-4',
                'D2U Machinery Addon - Gebrauchtmaschinen',
                22);
        }
        $modules[] = new \TobiasKrais\D2UHelper\Module('90-5',
            'D2U Machinery Addon - Box Beratungshinweis',
            2);
        if (rex_plugin::get('d2u_machinery', 'used_machines')->isAvailable()) {
            $modules[] = new \TobiasKrais\D2UHelper\Module('90-6',
                'D2U Machinery Addon - Gebrauchtmaschinen Topangebote',
                11);
        }
        return $modules;
    }
}
