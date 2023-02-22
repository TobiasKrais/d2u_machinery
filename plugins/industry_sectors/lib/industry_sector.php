<?php
/**
 * Redaxo D2U Machines Addon.
 * @author Tobias Krais
 * @author <a href="http://www.design-to-use.de">www.design-to-use.de</a>
 */

/**
 * Industry sector.
 */
class IndustrySector implements \D2U_Helper\ITranslationHelper
{
    /** @var int Database ID */
    public int $industry_sector_id = 0;

    /** @var int Redaxo clang id */
    public int $clang_id = 0;

    /** @var string Name */
    public string $name = '';

    /** @var string Teaser */
    public string $teaser = '';

    /** @var string Description */
    public string $description = '';

    /** @var string Icon file name */
    public string $icon = '';

    /** @var string Preview picture file name */
    public string $pic = '';

    /** @var string Status. Either "online" or "offline". */
    public string $online_status = 'offline';

    /** @var string "yes" if translation needs update */
    public string $translation_needs_update = 'delete';

    /** @var string URL */
    private string $url = '';

    /**
     * Constructor. Reads an Industry Sector stored in database.
     * @param int $industry_sector_id object ID
     * @param int $clang_id redaxo clang id
     */
    public function __construct($industry_sector_id, $clang_id)
    {
        $this->clang_id = $clang_id;
        $query = 'SELECT * FROM '. \rex::getTablePrefix() .'d2u_machinery_industry_sectors AS industry_sectors '
                .'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_industry_sectors_lang AS lang '
                    .'ON industry_sectors.industry_sector_id = lang.industry_sector_id '
                    .'AND clang_id = '. $this->clang_id .' '
                .'WHERE industry_sectors.industry_sector_id = '. $industry_sector_id;
        $result = \rex_sql::factory();
        $result->setQuery($query);
        $num_rows = $result->getRows();

        if ($num_rows > 0) {
            $this->industry_sector_id = (int) $result->getValue('industry_sector_id');
            $this->name = stripslashes((string) $result->getValue('name'));
            $this->teaser = stripslashes(htmlspecialchars_decode((string) $result->getValue('teaser')));
            $this->description = stripslashes(htmlspecialchars_decode((string) $result->getValue('description')));
            $this->icon = (string) $result->getValue('icon');
            $this->pic = (string) $result->getValue('pic');
            $this->online_status = (string) $result->getValue('online_status');
            if ('' !== (string) $result->getValue('translation_needs_update')) {
                $this->translation_needs_update = (string) $result->getValue('translation_needs_update');
            }
        }
    }

    /**
     * Changes the online status.
     */
    public function changeStatus(): void
    {
        if ('online' === $this->online_status) {
            if ($this->industry_sector_id > 0) {
                $query = 'UPDATE '. \rex::getTablePrefix() .'d2u_machinery_industry_sectors '
                    ."SET online_status = 'offline' "
                    .'WHERE industry_sector_id = '. $this->industry_sector_id;
                $result = \rex_sql::factory();
                $result->setQuery($query);
            }
            $this->online_status = 'offline';
        } else {
            if ($this->industry_sector_id > 0) {
                $query = 'UPDATE '. \rex::getTablePrefix() .'d2u_machinery_industry_sectors '
                    ."SET online_status = 'online' "
                    .'WHERE industry_sector_id = '. $this->industry_sector_id;
                $result = \rex_sql::factory();
                $result->setQuery($query);
            }
            $this->online_status = 'online';
        }

        // Don't forget to regenerate URL cache / search_it index
        \d2u_addon_backend_helper::generateUrlCache('industry_sector_id');
    }

    /**
     * Deletes the object in all languages.
     * @param bool $delete_all If true, all translations and main object are deleted. If
     * false, only this translation will be deleted.
     */
    public function delete($delete_all = true): void
    {
        $query_lang = 'DELETE FROM '. \rex::getTablePrefix() .'d2u_machinery_industry_sectors_lang '
            .'WHERE industry_sector_id = '. $this->industry_sector_id
            . ($delete_all ? '' : ' AND clang_id = '. $this->clang_id);
        $result_lang = \rex_sql::factory();
        $result_lang->setQuery($query_lang);

        // If no more lang objects are available, delete
        $query_main = 'SELECT * FROM '. \rex::getTablePrefix() .'d2u_machinery_industry_sectors_lang '
            .'WHERE industry_sector_id = '. $this->industry_sector_id;
        $result_main = \rex_sql::factory();
        $result_main->setQuery($query_main);
        if (0 === (int) $result_main->getRows()) {
            $query = 'DELETE FROM '. \rex::getTablePrefix() .'d2u_machinery_industry_sectors '
                .'WHERE industry_sector_id = '. $this->industry_sector_id;
            $result = \rex_sql::factory();
            $result->setQuery($query);
        }

        // Don't forget to regenerate URL cache / search_it index
        \d2u_addon_backend_helper::generateUrlCache('industry_sector_id');

        // Delete from YRewrite forward list
        if (rex_addon::get('yrewrite')->isAvailable()) {
            if ($delete_all) {
                foreach (rex_clang::getAllIds() as $clang_id) {
                    $lang_object = new self($this->industry_sector_id, $clang_id);
                    $query_forward = 'DELETE FROM '. \rex::getTablePrefix() .'yrewrite_forward '
                        ."WHERE extern = '". $lang_object->getURL(true) ."'";
                    $result_forward = \rex_sql::factory();
                    $result_forward->setQuery($query_forward);
                }
            } else {
                $query_forward = 'DELETE FROM '. \rex::getTablePrefix() .'yrewrite_forward '
                    ."WHERE extern = '". $this->getURL(true) ."'";
                $result_forward = \rex_sql::factory();
                $result_forward->setQuery($query_forward);
            }
        }
    }

    /**
     * Get all industry sectors.
     * @param int $clang_id redaxo clang id
     * @param bool $online_only true if only online objects should be returned
     * @return IndustrySector[] array with IndustrySector objects
     */
    public static function getAll($clang_id, $online_only = false)
    {
        $query = 'SELECT lang.industry_sector_id FROM '. \rex::getTablePrefix() .'d2u_machinery_industry_sectors_lang AS lang '
            .'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_industry_sectors AS sectors ON lang.industry_sector_id = sectors.industry_sector_id '
            .'WHERE clang_id = '. $clang_id .' '. ($online_only ? " AND online_status = 'online' " : '');
        $query .= 'ORDER BY name';

        $result = \rex_sql::factory();
        $result->setQuery($query);

        $industry_sectors = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $industry_sector = new self((int) $result->getValue('industry_sector_id'), $clang_id);
            $industry_sectors[] = $industry_sector;
            $result->next();
        }
        return $industry_sectors;
    }

    /**
     * Gets the machines referring to this object.
     * @param bool $online_only true if only online machines should be returned
     * @return Machine[] machines referring to this object
     */
    public function getMachines($online_only = false)
    {
        $query = 'SELECT machine_id FROM '. \rex::getTablePrefix() .'d2u_machinery_machines '
            ."WHERE industry_sector_ids LIKE '%|". $this->industry_sector_id ."|%'";
        $result = \rex_sql::factory();
        $result->setQuery($query);

        $machines = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $machine = new Machine((int) $result->getValue('machine_id'), $this->clang_id);
            if (false === $online_only || ($online_only && 'online' === $machine->online_status)) {
                $machines[] = $machine;
            }
            $result->next();
        }
        return $machines;
    }

    /**
     * @api
     * Gets the production lines referring to this object. Plugin production_lines
     * needs to be installed.
     * @param bool $online_only true if only online production lines should be returned
     * @return ProductionLine[] production lines referring to this object
     */
    public function getProductionLines($online_only = false)
    {
        $production_lines = [];
        if (rex_plugin::get('d2u_machinery', 'production_lines')->isAvailable()) {
            $query = 'SELECT production_line_id FROM '. \rex::getTablePrefix() .'d2u_machinery_production_lines '
                ."WHERE industry_sector_ids LIKE '%|". $this->industry_sector_id ."|%'";
            $result = \rex_sql::factory();
            $result->setQuery($query);

            for ($i = 0; $i < $result->getRows(); ++$i) {
                $production_line = new ProductionLine((int) $result->getValue('production_line_id'), $this->clang_id);
                if (false === $online_only || ($online_only && 'online' === $production_line->online_status)) {
                    $production_lines[] = $production_line;
                }
                $result->next();
            }
        }
        return $production_lines;
    }

    /**
     * Get objects concerning translation updates.
     * @param int $clang_id Redaxo language ID
     * @param string $type 'update' or 'missing'
     * @return IndustrySector[] array with IndustrySector objects
     */
    public static function getTranslationHelperObjects($clang_id, $type)
    {
        $query = 'SELECT industry_sector_id FROM '. \rex::getTablePrefix() .'d2u_machinery_industry_sectors_lang '
                .'WHERE clang_id = '. $clang_id ." AND translation_needs_update = 'yes' "
                .'ORDER BY name';
        if ('missing' === $type) {
            $query = 'SELECT main.industry_sector_id FROM '. \rex::getTablePrefix() .'d2u_machinery_industry_sectors AS main '
                    .'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_industry_sectors_lang AS target_lang '
                        .'ON main.industry_sector_id = target_lang.industry_sector_id AND target_lang.clang_id = '. $clang_id .' '
                    .'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_industry_sectors_lang AS default_lang '
                        .'ON main.industry_sector_id = default_lang.industry_sector_id AND default_lang.clang_id = '. \rex_config::get('d2u_helper', 'default_lang') .' '
                    .'WHERE target_lang.industry_sector_id IS NULL '
                    .'ORDER BY default_lang.name';
            $clang_id = (int) \rex_config::get('d2u_helper', 'default_lang');
        }
        $result = \rex_sql::factory();
        $result->setQuery($query);

        $objects = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $objects[] = new self((int) $result->getValue('industry_sector_id'), $clang_id);
            $result->next();
        }

        return $objects;
    }

    /**
     * Returns the URL of this object.
     * @param bool $including_domain true if Domain name should be included
     * @return string URL
     */
    public function getURL($including_domain = false)
    {
        if ('' === $this->url) {
            $d2u_machinery = rex_addon::get('d2u_machinery');

            $parameterArray = [];
            $parameterArray['industry_sector_id'] = $this->industry_sector_id;
            $this->url = rex_getUrl((int) $d2u_machinery->getConfig('article_id'), $this->clang_id, $parameterArray, '&');
        }

        if ($including_domain) {
            if (\rex_addon::get('yrewrite') instanceof rex_addon && \rex_addon::get('yrewrite')->isAvailable()) {
                return str_replace(\rex_yrewrite::getCurrentDomain()->getUrl() .'/', \rex_yrewrite::getCurrentDomain()->getUrl(), \rex_yrewrite::getCurrentDomain()->getUrl() . $this->url);
            }

            return str_replace(\rex::getServer(). '/', \rex::getServer(), \rex::getServer() . $this->url);

        }

        return $this->url;

    }

    /**
     * @api
     * Detects if machines for this object exist
     * @param bool $ignore_offlines Ignore offline machines, default is false
     * @return bool true if machines exist, otherwise false
     */
    public function hasMachines($ignore_offlines = false)
    {
        $query = 'SELECT lang.machine_id '
            . 'FROM '. \rex::getTablePrefix() .'d2u_machinery_machines_lang AS lang '
            .'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_machines AS machines '
                    .'ON lang.machine_id = machines.machine_id '
            ."WHERE industry_sector_ids LIKE '%|". $this->industry_sector_id ."|%' AND clang_id = ". $this->clang_id
            .($ignore_offlines ? ' AND online_status = "online"' : '');
        $result = \rex_sql::factory();
        $result->setQuery($query);

        if ($result->getRows() > 0) {
            return true;
        }

        return false;

    }

    /**
     * Updates or inserts the object into database.
     * @return bool true if successful
     */
    public function save()
    {
        $error = false;

        // Save the not language specific part
        $pre_save_object = new self($this->industry_sector_id, $this->clang_id);

        // saving the rest
        if (0 === $this->industry_sector_id || $pre_save_object !== $this) {
            $query = \rex::getTablePrefix() .'d2u_machinery_industry_sectors SET '
                    ."online_status = '". $this->online_status ."', "
                    ."icon = '". $this->icon ."', "
                    ."pic = '". $this->pic ."' ";

            if (0 === $this->industry_sector_id) {
                $query = 'INSERT INTO '. $query;
            } else {
                $query = 'UPDATE '. $query .' WHERE industry_sector_id = '. $this->industry_sector_id;
            }

            $result = \rex_sql::factory();
            $result->setQuery($query);
            if (0 === $this->industry_sector_id) {
                $this->industry_sector_id = (int) $result->getLastId();
                $error = $result->hasError();
            }
        }

        $regenerate_urls = false;
        if (false === $error) {
            // Save the language specific part
            $pre_save_object = new self($this->industry_sector_id, $this->clang_id);
            if ($pre_save_object !== $this) {
                $query = 'REPLACE INTO '. \rex::getTablePrefix() .'d2u_machinery_industry_sectors_lang SET '
                        ."industry_sector_id = '". $this->industry_sector_id ."', "
                        ."clang_id = '". $this->clang_id ."', "
                        ."name = '". addslashes($this->name) ."', "
                        ."teaser = '". addslashes(htmlspecialchars($this->teaser)) ."', "
                        ."description = '". addslashes(htmlspecialchars($this->description)) ."', "
                        ."translation_needs_update = '". $this->translation_needs_update ."', "
                        .'updatedate = CURRENT_TIMESTAMP, '
                        ."updateuser = '". (\rex::getUser() instanceof rex_user ? \rex::getUser()->getLogin() : '') ."' ";

                $result = \rex_sql::factory();
                $result->setQuery($query);
                $error = $result->hasError();

                if (!$error && $pre_save_object->name !== $this->name) {
                    $regenerate_urls = true;
                }
            }
        }

        // Update URLs
        if ($regenerate_urls) {
            \d2u_addon_backend_helper::generateUrlCache('industry_sector_id');
        }

        return !$error;
    }
}
