<?php
/**
 * @api
 * Offers helper functions for frontend
 */
class d2u_machinery_frontend_helper
{
    /**
     * Returns alternate URLs. Key is Redaxo language id, value is URL.
     * @return string[] alternate URLs
     */
    public static function getAlternateURLs()
    {
        $alternate_URLs = [];

        // Prepare objects first for sorting in correct order
        $url_namespace = d2u_addon_frontend_helper::getUrlNamespace();
        $url_id = d2u_addon_frontend_helper::getUrlId();

        if (filter_input(INPUT_GET, 'machine_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 || 'machine_id' === $url_namespace) {
            $machine_id = (int) filter_input(INPUT_GET, 'machine_id', FILTER_VALIDATE_INT);
            if (rex_addon::get('url')->isAvailable() && $url_id > 0) {
                $machine_id = $url_id;
            }

            if ($machine_id > 0) {
                foreach (rex_clang::getAllIds(true) as $this_lang_key) {
                    $lang_machine = new Machine($machine_id, $this_lang_key);
                    if ('delete' !== $lang_machine->translation_needs_update) {
                        $alternate_URLs[$this_lang_key] = $lang_machine->getUrl();
                    }
                }
            }
        } elseif (filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 || 'category_id' === $url_namespace
                || filter_input(INPUT_GET, 'used_rent_category_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 || 'used_rent_category_id' === $url_namespace
                || filter_input(INPUT_GET, 'used_sale_category_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 || 'used_sale_category_id' === $url_namespace
        ) {

            // Category for normal machines
            $category_id = (int) filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT);
            $offer_type = '';
            if (rex_plugin::get('d2u_machinery', 'used_machines')->isAvailable() && filter_input(INPUT_GET, 'used_rent_category_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0) {
                // Category for used machines (rent)
                $category_id = (int) filter_input(INPUT_GET, 'used_rent_category_id', FILTER_VALIDATE_INT);
                $offer_type = 'rent';
            } elseif (rex_plugin::get('d2u_machinery', 'used_machines')->isAvailable() && filter_input(INPUT_GET, 'used_sale_category_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0) {
                // Category for used machines (sale)
                $category_id = (int) filter_input(INPUT_GET, 'used_sale_category_id', FILTER_VALIDATE_INT);
                $offer_type = 'sale';
            }
            if (rex_addon::get('url')->isAvailable() && $url_id > 0) {
                $category_id = $url_id;
                if ('used_rent_category_id' === $url_namespace) {
                    $offer_type = 'rent';
                } elseif ('used_sale_category_id' === $url_namespace) {
                    $offer_type = 'sale';
                }
            }

            if ($category_id > 0) {
                foreach (rex_clang::getAllIds(true) as $this_lang_key) {
                    $lang_category = new Category($category_id, $this_lang_key);
                    $lang_category->setOfferType($offer_type);
                    if ('delete' !== $lang_category->translation_needs_update) {
                        $alternate_URLs[$this_lang_key] = $lang_category->getUrl();
                    }
                }
            }
        } elseif (rex_plugin::get('d2u_machinery', 'industry_sectors')->isAvailable() && (filter_input(INPUT_GET, 'industry_sector_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 || 'industry_sector_id' === $url_namespace)) {
            $industry_sector_id = (int) filter_input(INPUT_GET, 'industry_sector_id', FILTER_VALIDATE_INT);
            if (rex_addon::get('url')->isAvailable() && $url_id > 0) {
                $industry_sector_id = $url_id;
            }

            if ($industry_sector_id > 0) {
                foreach (rex_clang::getAllIds(true) as $this_lang_key) {
                    $lang_industry_sector = new IndustrySector($industry_sector_id, $this_lang_key);
                    if ('delete' !== $lang_industry_sector->translation_needs_update) {
                        $alternate_URLs[$this_lang_key] = $lang_industry_sector->getUrl();
                    }
                }
            }
        } elseif ((filter_input(INPUT_GET, 'used_rent_machine_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 || 'used_rent_machine_id' === $url_namespace)
                || (filter_input(INPUT_GET, 'used_sale_machine_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 || 'used_sale_machine_id' === $url_namespace)) {
            $used_machine_id = (int) (filter_input(INPUT_GET, 'used_sale_machine_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 ? filter_input(INPUT_GET, 'used_sale_machine_id', FILTER_VALIDATE_INT) : filter_input(INPUT_GET, 'used_rent_machine_id', FILTER_VALIDATE_INT));
            if (rex_addon::get('url')->isAvailable() && $url_id > 0) {
                $used_machine_id = $url_id;
            }

            if ($used_machine_id > 0) {
                foreach (rex_clang::getAllIds(true) as $this_lang_key) {
                    $lang_used_machine = new UsedMachine($used_machine_id, $this_lang_key);
                    if ('delete' !== $lang_used_machine->translation_needs_update) {
                        $alternate_URLs[$this_lang_key] = $lang_used_machine->getUrl();
                    }
                }
            }
        }

        return $alternate_URLs;
    }

    /**
     * Returns breadcrumbs. Not from article path, but only part from this addon.
     * @return string[] Breadcrumb elements
     */
    public static function getBreadcrumbs()
    {
        $breadcrumbs = [];

        // Prepare objects first for sorting in correct order
        $category = false;
        $machine = false;
        $used_machine = false;

        $url_namespace = d2u_addon_frontend_helper::getUrlNamespace();
        $url_id = d2u_addon_frontend_helper::getUrlId();

        if (filter_input(INPUT_GET, 'machine_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 || 'machine_id' === $url_namespace) {
            $machine_id = (int) filter_input(INPUT_GET, 'machine_id', FILTER_VALIDATE_INT);
            if (\rex_addon::get('url')->isAvailable() && $url_id > 0) {
                $machine_id = $url_id;
            }

            if ($machine_id > 0) {
                $machine = new Machine($machine_id, rex_clang::getCurrentId());
                $category = $machine->category;
            }
        }
        if (filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 || 'category_id' === $url_namespace
                || filter_input(INPUT_GET, 'used_rent_category_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 || 'used_rent_category_id' === $url_namespace
                || filter_input(INPUT_GET, 'used_sale_category_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 || 'used_sale_category_id' === $url_namespace) {
            // Category for normal machines
            $category_id = (int) filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT);
            if (rex_plugin::get('d2u_machinery', 'used_machines')->isAvailable() && filter_input(INPUT_GET, 'used_rent_category_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0) {
                // Category for used machines (rent)
                $category_id = (int) filter_input(INPUT_GET, 'used_rent_category_id', FILTER_VALIDATE_INT);
            } elseif (rex_plugin::get('d2u_machinery', 'used_machines')->isAvailable() && filter_input(INPUT_GET, 'used_sale_category_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0) {
                // Category for used machines (sale)
                $category_id = (int) filter_input(INPUT_GET, 'used_sale_category_id', FILTER_VALIDATE_INT);
            }
            if (\rex_addon::get('url')->isAvailable() && $url_id > 0) {
                $category_id = $url_id;
            }

            if ($category_id > 0) {
                $category = new Category($category_id, rex_clang::getCurrentId());
            }
        }
        if (filter_input(INPUT_GET, 'used_rent_machine_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 || filter_input(INPUT_GET, 'used_sale_machine_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 || ('used_rent_machine_id' === $url_namespace || 'used_sale_machine_id' === $url_namespace)) {
            $used_machine_id = (int) (filter_input(INPUT_GET, 'used_rent_machine_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 ? filter_input(INPUT_GET, 'used_rent_machine_id', FILTER_VALIDATE_INT) : filter_input(INPUT_GET, 'used_sale_machine_id', FILTER_VALIDATE_INT));
            if (\rex_addon::get('url')->isAvailable() && $url_id > 0) {
                $used_machine_id = $url_id;
            }

            if ($used_machine_id > 0) {
                $used_machine = new UsedMachine($used_machine_id, rex_clang::getCurrentId());
                if (false !== $used_machine->category) {
                    $category = $used_machine->category;
                    if ($category instanceof Category) {
                        $category->setOfferType($used_machine->offer_type);
                    }
                }
            }
        }

        // Breadcrumbs
        if ($category instanceof Category) {
            if ($category->parent_category instanceof Category) {
                $breadcrumbs[] = '<a href="' . $category->parent_category->getUrl() . '">' . $category->parent_category->name . '</a>';
            }
            $breadcrumbs[] = '<a href="' . $category->getUrl() . '">' . $category->name . '</a>';
        }
        if (false !== $machine) {
            $breadcrumbs[] = '<a href="' . $machine->getUrl() . '">' . ('' === $machine->lang_name ? $machine->name : $machine->lang_name) . '</a>';
        }
        if (false !== $used_machine) {
            $breadcrumbs[] = '<a href="' . $used_machine->getUrl() . '">' . $used_machine->manufacturer .' '. $used_machine->name . '</a>';
        }

        // Industry sectors
        if (rex_plugin::get('d2u_machinery', 'industry_sectors')->isAvailable() && (filter_input(INPUT_GET, 'industry_sector_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 || 'industry_sector_id' === $url_namespace)) {
            $industry_sector_id = (int) filter_input(INPUT_GET, 'industry_sector_id', FILTER_VALIDATE_INT);
            if (\rex_addon::get('url')->isAvailable() && $url_id > 0) {
                $industry_sector_id = $url_id;
            }
            $industry_sector = new IndustrySector($industry_sector_id, rex_clang::getCurrentId());
            $breadcrumbs[] = '<a href="' . $industry_sector->getUrl() . '">' . $industry_sector->name . '</a>';
        }

        // Production lines
        if (rex_plugin::get('d2u_machinery', 'production_lines')->isAvailable() && (filter_input(INPUT_GET, 'production_line_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 || 'production_line_id' === $url_namespace)) {
            $production_line_id = (int) filter_input(INPUT_GET, 'production_line_id', FILTER_VALIDATE_INT);
            if (\rex_addon::get('url')->isAvailable() && $url_id > 0) {
                $production_line_id = $url_id;
            }
            $production_line = new ProductionLine($production_line_id, rex_clang::getCurrentId());
            $breadcrumbs[] = '<a href="' . $production_line->getUrl() . '">' . $production_line->name . '</a>';
        }

        return $breadcrumbs;
    }

    /**
     * Prints Responive Multilevel Menu submenu für D2U Machinery Addon.
     */
    public static function getD2UMachineryResponsiveMultiLevelSubmenu(): void
    {
        if ('show' === (string) rex_config::get('d2u_machinery', 'show_categories_navi', 'hide')) {
            // Get current IDs
            $current_category_id = 0;
            $current_machine_id = 0;

            $url_namespace = d2u_addon_frontend_helper::getUrlNamespace();
            $url_id = d2u_addon_frontend_helper::getUrlId();

            if (filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 || 'category_id' === $url_namespace) {
                // Category for normal machines
                $current_category_id = filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT);
                if (\rex_addon::get('url')->isAvailable() && $url_id > 0) {
                    $current_category_id = $url_id;
                }
            }
            if (filter_input(INPUT_GET, 'machine_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 || 'machine_id' === $url_namespace) {
                $current_machine_id = (int) filter_input(INPUT_GET, 'machine_id', FILTER_VALIDATE_INT);
                if (\rex_addon::get('url')->isAvailable() && $url_id > 0) {
                    $current_machine_id = $url_id;
                }
                if ($current_machine_id > 0) {
                    $current_machine = new Machine($current_machine_id, rex_clang::getCurrentId());
                    $current_category_id = $current_machine->category instanceof Category ? $current_machine->category->category_id : 0;
                }
            }

            // Navi
            $categories = Category::getAll(rex_clang::getCurrentId());
            foreach ($categories as $category) {
                echo '<li'. ($category->category_id === $current_category_id ? ' class="current"' : '') .'><a href="'. $category->getUrl() .'">'. $category->name .'</a>';
                if ('show' === (string) rex_config::get('d2u_machinery', 'show_machines_navi', 'hide')) {
                    echo '<ul class="dl-submenu">';
                    echo '<li class="dl-back"><a href="#">&nbsp;</a></li>';
                    echo '<li><a href="'. $category->getUrl() .'" title="'. $category->name .'">'. strtoupper($category->name) .'</a></li>';
                    $machines = $category->getMachines(true);
                    foreach ($machines as $machine) {
                        echo '<li'. ($machine->machine_id === $current_machine_id ? ' class="current"' : '') .'><a href="'. $machine->getUrl() .'" title="'. $machine->name .'">'. $machine->name .'</a></li>';
                    }
                    echo '</ul>';
                    echo '</li>';
                }
            }
        }
    }

    /**
     * Prints Responive Multilevel Menu submenu für D2U Machinery Addon.
     */
    public static function getD2UMachinerySmartmenuSubmenu(): void
    {
        if ('show' === (string) rex_config::get('d2u_machinery', 'show_categories_navi', 'hide')) {
            // Get current IDs
            $current_category_id = 0;
            $current_machine_id = 0;

            $url_namespace = d2u_addon_frontend_helper::getUrlNamespace();
            $url_id = d2u_addon_frontend_helper::getUrlId();

            if (filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 || 'category_id' === $url_namespace) {
                // Category for normal machines
                $current_category_id = filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT);
                if (\rex_addon::get('url')->isAvailable() && $url_id > 0) {
                    $current_category_id = $url_id;
                }
            }
            if (filter_input(INPUT_GET, 'machine_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 || 'machine_id' === $url_namespace) {
                $current_machine_id = (int) filter_input(INPUT_GET, 'machine_id', FILTER_VALIDATE_INT);
                if (\rex_addon::get('url')->isAvailable() && $url_id > 0) {
                    $current_machine_id = $url_id;
                }
                if ($current_machine_id > 0) {
                    $current_machine = new Machine($current_machine_id, rex_clang::getCurrentId());
                    $current_category_id = $current_machine->category instanceof Category ? $current_machine->category->category_id : 0;
                }
            }

            // Navi
            $categories = Category::getAll(rex_clang::getCurrentId());
            foreach ($categories as $category) {
                if ($category->hasMachines()) {
                    echo '<li><a href="'. $category->getUrl() .'"'. ($category->category_id === $current_category_id ? ' class="current"' : '') .'>'. $category->name .'</a>';
                    if ('show' === (string) rex_config::get('d2u_machinery', 'show_machines_navi', 'hide')) {
                        echo '<ul>';
                        echo '<li><a href="'. $category->getUrl() .'" title="'. $category->name .'">'. strtoupper($category->name) .'</a></li>';
                        $machines = $category->getMachines(true);
                        foreach ($machines as $machine) {
                            echo '<li><a href="'. $machine->getUrl() .'" title="'. $machine->name .'"'. ($machine->machine_id === $current_machine_id ? ' class="current"' : '') .'>'. $machine->name .'</a></li>';
                        }
                        echo '</ul>';
                        echo '</li>';
                    }
                }
            }
        }
    }
}
