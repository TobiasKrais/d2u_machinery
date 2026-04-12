<?php

namespace TobiasKrais\D2UMachinery;

/**
 * Class managing modules published by www.design-to-use.de.
 *
 * @author Tobias Krais
 */
class Module
{
    /**
     * Get modules offered by this addon.
     * @return array<int,TobiasKrais\D2UHelper\Module> Modules offered by this addon
     */
    public static function getModules()
    {
        $modules = [];
        $modules[] = new \TobiasKrais\D2UHelper\Module('90-1',
            'D2U Machinery Addon - Hauptausgabe (BS4, deprecated)',
            19);
        if (Extension::isActive('industry_sectors')) {
            $modules[] = new \TobiasKrais\D2UHelper\Module('90-2',
                'D2U Machinery Addon - Branchen (BS4, deprecated)',
                2);
        }
        $modules[] = new \TobiasKrais\D2UHelper\Module('90-3',
            'D2U Machinery Addon - Kategorien (BS4, deprecated)',
            6);
        if (Extension::isActive('used_machines')) {
            $modules[] = new \TobiasKrais\D2UHelper\Module('90-4',
                'D2U Machinery Addon - Gebrauchtmaschinen (BS4, deprecated)',
                23);
        }
        $modules[] = new \TobiasKrais\D2UHelper\Module('90-5',
            'D2U Machinery Addon - Box Beratungshinweis (BS4, deprecated)',
            2);
        if (Extension::isActive('used_machines')) {
            $modules[] = new \TobiasKrais\D2UHelper\Module('90-6',
                'D2U Machinery Addon - Gebrauchtmaschinen Topangebote (BS4, deprecated)',
                11);
        }
        $modules[] = new \TobiasKrais\D2UHelper\Module('90-7',
            'D2U Machinery Addon - Hauptausgabe (BS5)',
            1);
        if (Extension::isActive('industry_sectors')) {
            $modules[] = new \TobiasKrais\D2UHelper\Module('90-8',
                'D2U Machinery Addon - Branchen (BS5)',
                1);
        }
        $modules[] = new \TobiasKrais\D2UHelper\Module('90-9',
            'D2U Machinery Addon - Kategorien (BS5)',
            1);
        if (Extension::isActive('used_machines')) {
            $modules[] = new \TobiasKrais\D2UHelper\Module('90-10',
                'D2U Machinery Addon - Gebrauchtmaschinen (BS5)',
                1);
        }
        $modules[] = new \TobiasKrais\D2UHelper\Module('90-11',
            'D2U Machinery Addon - Box Beratungshinweis (BS5)',
            1);
        if (Extension::isActive('used_machines')) {
            $modules[] = new \TobiasKrais\D2UHelper\Module('90-12',
                'D2U Machinery Addon - Gebrauchtmaschinen Topangebote (BS5)',
                1);
        }
        return $modules;
    }
}
