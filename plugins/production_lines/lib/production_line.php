<?php
/**
 * Redaxo D2U Machines Addon.
 * @author Tobias Krais
 * @author <a href="http://www.design-to-use.de">www.design-to-use.de</a>
 */

use TobiasKrais\D2UReferences\Reference;

/**
 * @api
 * Production line
 */
class ProductionLine implements \TobiasKrais\D2UHelper\ITranslationHelper
{
    /** @var int Database ID */
    public int $production_line_id = 0;

    /** @var int Redaxo clang id */
    public int $clang_id = 0;

    /** @var string Name */
    public string $line_code = '';

    /** @var int[] Industry sectors ids */
    public array $industry_sector_ids = [];

    /** @var int[] machine ids */
    public array $machine_ids = [];

    /** @var int[] Complementary machine ids */
    public array $complementary_machine_ids = [];

    /** @var int[] machine_steel_processing_extension: Automation supply ids */
    public array $automation_supply_ids = [];

    /** @var array<string> Picture file names */
    public array $pictures = [];

    /** @var string Picture file name with links */
    public string $link_picture = '';

    /** @var int[] Array with IDs from d2u_references addon */
    public array $reference_ids = [];

    /** @var int[] Videomanager video ids */
    public array $video_ids = [];

    /** @var string Status. Either "online" or "offline". */
    public string $online_status = 'offline';

    /** @var string Name */
    public string $name = '';

    /** @var string Teaser */
    public string $teaser = '';

    /** @var string Short description */
    public string $description_short = '';

    /** @var string Long description */
    public string $description_long = '';

    /** @var int[] Unique selling proposition */
    public array $usp_ids = [];

    /** @var string "yes" if translation needs update */
    public string $translation_needs_update = 'delete';

    /** @var string URL */
    private $url = '';

    /**
     * Constructor. Reads the object stored in database.
     * @param int $production_line_id object ID
     * @param int $clang_id redaxo clang id
     */
    public function __construct($production_line_id, $clang_id)
    {
        $this->clang_id = $clang_id;
        $query = 'SELECT * FROM '. \rex::getTablePrefix() .'d2u_machinery_production_lines AS production_lines '
                .'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_production_lines_lang AS lang '
                    .'ON production_lines.production_line_id = lang.production_line_id '
                    .'AND clang_id = '. $this->clang_id .' '
                .'WHERE production_lines.production_line_id = '. $production_line_id;
        $result = \rex_sql::factory();
        $result->setQuery($query);
        $num_rows = $result->getRows();

        if ($num_rows > 0) {
            $this->production_line_id = (int) $result->getValue('production_line_id');
            $complementary_machine_ids = preg_grep('/^\s*$/s', explode('|', (string) $result->getValue('complementary_machine_ids')), PREG_GREP_INVERT);
            $this->complementary_machine_ids = is_array($complementary_machine_ids) ? array_map('intval', $complementary_machine_ids) : [];
            $this->description_long = stripslashes((string) $result->getValue('description_long'));
            $this->description_short = stripslashes((string) $result->getValue('description_short'));
            if (rex_plugin::get('d2u_machinery', 'industry_sectors')->isAvailable()) {
                $industry_sector_ids = preg_grep('/^\s*$/s', explode('|', (string) $result->getValue('industry_sector_ids')), PREG_GREP_INVERT);
                $this->industry_sector_ids = is_array($industry_sector_ids) ? array_map('intval', $industry_sector_ids) : [];
            }
            if (rex_plugin::get('d2u_machinery', 'machine_steel_processing_extension')->isAvailable()) {
                $automation_supply_ids = preg_grep('/^\s*$/s', explode('|', (string) $result->getValue('automation_supply_ids')), PREG_GREP_INVERT);
                $this->automation_supply_ids = is_array($automation_supply_ids) ? array_map('intval', $automation_supply_ids) : [];
            }
            $this->line_code = stripslashes((string) $result->getValue('line_code'));
            $machine_ids = preg_grep('/^\s*$/s', explode('|', (string) $result->getValue('machine_ids')), PREG_GREP_INVERT);
            $this->machine_ids = is_array($machine_ids) ? array_map('intval', $machine_ids) : [];
            $this->name = stripslashes((string) $result->getValue('name'));
            $this->online_status = (string) $result->getValue('online_status');
            $pictures = preg_grep('/^\s*$/s', explode(',', (string) $result->getValue('pictures')), PREG_GREP_INVERT);
            $this->pictures = is_array($pictures) ? $pictures : [];
            $this->link_picture = (string) $result->getValue('link_picture');
            $this->teaser = stripslashes((string) $result->getValue('teaser'));
            if ('' !== $result->getValue('translation_needs_update') && null !== $result->getValue('translation_needs_update')) {
                $this->translation_needs_update = (string) $result->getValue('translation_needs_update');
            }
            $usp_ids = preg_grep('/^\s*$/s', explode('|', (string) $result->getValue('usp_ids')), PREG_GREP_INVERT);
            $this->usp_ids = is_array($usp_ids) ? array_map('intval', $usp_ids) : [];
            if (rex_addon::get('d2u_videos')->isAvailable()) {
                $video_ids = preg_grep('/^\s*$/s', explode('|', (string) $result->getValue('video_ids')), PREG_GREP_INVERT);
                $this->video_ids = is_array($video_ids) ? array_map('intval', $video_ids) : [];
            }
            $reference_ids = preg_grep('/^\s*$/s', explode(',', (string) $result->getValue('reference_ids')), PREG_GREP_INVERT);
            $this->reference_ids = is_array($reference_ids) ? array_map('intval', array_filter($reference_ids, 'is_numeric')) : [];
        }
    }

    /**
     * Changes the online status.
     */
    public function changeStatus(): void
    {
        if ('online' === $this->online_status) {
            if ($this->production_line_id > 0) {
                $query = 'UPDATE '. \rex::getTablePrefix() .'d2u_machinery_production_lines '
                    ."SET online_status = 'offline' "
                    .'WHERE production_line_id = '. $this->production_line_id;
                $result = \rex_sql::factory();
                $result->setQuery($query);
            }
            $this->online_status = 'offline';
        } else {
            if ($this->production_line_id > 0) {
                $query = 'UPDATE '. \rex::getTablePrefix() .'d2u_machinery_production_lines '
                    ."SET online_status = 'online' "
                    .'WHERE production_line_id = '. $this->production_line_id;
                $result = \rex_sql::factory();
                $result->setQuery($query);
            }
            $this->online_status = 'online';
        }

        // Don't forget to regenerate URL cache / search_it index
        \TobiasKrais\D2UHelper\BackendHelper::generateUrlCache('production_line_id');
    }

    /**
     * Deletes the object in all languages.
     * @param bool $delete_all If true, all translations and main object are deleted. If
     * false, only this translation will be deleted.
     */
    public function delete($delete_all = true): void
    {
        $query_lang = 'DELETE FROM '. \rex::getTablePrefix() .'d2u_machinery_production_lines_lang '
            .'WHERE production_line_id = '. $this->production_line_id
            . ($delete_all ? '' : ' AND clang_id = '. $this->clang_id);
        $result_lang = \rex_sql::factory();
        $result_lang->setQuery($query_lang);

        // If no more lang objects are available, delete
        $query_main = 'SELECT * FROM '. \rex::getTablePrefix() .'d2u_machinery_production_lines_lang '
            .'WHERE production_line_id = '. $this->production_line_id;
        $result_main = \rex_sql::factory();
        $result_main->setQuery($query_main);
        if (0 === $result_main->getRows()) {
            $query = 'DELETE FROM '. \rex::getTablePrefix() .'d2u_machinery_production_lines '
                .'WHERE production_line_id = '. $this->production_line_id;
            $result = \rex_sql::factory();
            $result->setQuery($query);
        }

        // Don't forget to regenerate URL cache / search_it index
        \TobiasKrais\D2UHelper\BackendHelper::generateUrlCache('production_line_id');

        // Delete from YRewrite forward list
        if (rex_addon::get('yrewrite')->isAvailable()) {
            if ($delete_all) {
                foreach (rex_clang::getAllIds() as $clang_id) {
                    $lang_object = new self($this->production_line_id, $clang_id);
                    $query_forward = 'DELETE FROM '. \rex::getTablePrefix() .'yrewrite_forward '
                        ."WHERE extern = '". $lang_object->getUrl(true) ."'";
                    $result_forward = \rex_sql::factory();
                    $result_forward->setQuery($query_forward);
                }
            } else {
                $query_forward = 'DELETE FROM '. \rex::getTablePrefix() .'yrewrite_forward '
                    ."WHERE extern = '". $this->getUrl(true) ."'";
                $result_forward = \rex_sql::factory();
                $result_forward->setQuery($query_forward);
            }
        }
    }

    /**
     * Get all objects.
     * @param int $clang_id redaxo clang id
     * @param bool $online_only true if only online objects should be returned
     * @return ProductionLine[] array with production line objects
     */
    public static function getAll($clang_id, $online_only = false)
    {
        $query = 'SELECT production_line_id FROM '. \rex::getTablePrefix() .'d2u_machinery_production_lines_lang '
            .'WHERE clang_id = '. $clang_id .' ';
        $query .= 'ORDER BY name';

        $result = \rex_sql::factory();
        $result->setQuery($query);

        $production_lines = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $production_line = new self((int) $result->getValue('production_line_id'), $clang_id);
            if ($online_only && $production_line->isOnline()) {
                $production_lines[] = $production_line;
            } elseif (false === $online_only) {
                $production_lines[] = $production_line;
            }
            $result->next();
        }
        return $production_lines;
    }

    /**
     * Gets the machines referring to this object.
     * @param bool $online_only true if only online machines should be returned
     * @return Machine[] machines referring to this object
     */
    public function getMachines($online_only = false)
    {
        $machines = [];
        foreach ($this->machine_ids as $machine_id) {
            $machine = new Machine($machine_id, $this->clang_id);
            if (false === $online_only || ('online' === $machine->online_status)) {
                $machines[] = $machine;
            }
        }
        return $machines;
    }

        /**
     * Get reference objects reffering to this machine.
     * @return array<int,Reference> array with Reference objects
     * @throws InvalidArgumentException 
     */
    public function getReferences(): array
    {
        if (!rex_addon::get('d2u_references')->isAvailable()) {
            return [];
        }

        $references = [];
        foreach ($this->reference_ids as $reference_id) {
            $reference = new Reference($reference_id, $this->clang_id);
            if ($reference instanceof Reference && $reference->reference_id > 0) {
                $references[$reference->reference_id] = $reference;
            }
        }
        ksort($references);
        return $references;
    }

    /**
     * Get objects concerning translation updates.
     * @param int $clang_id Redaxo language ID
     * @param string $type 'update' or 'missing'
     * @return ProductionLine[] array with ProductionLine objects
     */
    public static function getTranslationHelperObjects($clang_id, $type)
    {
        $query = 'SELECT production_line_id FROM '. \rex::getTablePrefix() .'d2u_machinery_production_lines_lang '
                .'WHERE clang_id = '. $clang_id ." AND translation_needs_update = 'yes' "
                .'ORDER BY name';
        if ('missing' === $type) {
            $query = 'SELECT main.production_line_id FROM '. \rex::getTablePrefix() .'d2u_machinery_production_lines AS main '
                    .'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_production_lines_lang AS target_lang '
                        .'ON main.production_line_id = target_lang.production_line_id AND target_lang.clang_id = '. $clang_id .' '
                    .'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_production_lines_lang AS default_lang '
                        .'ON main.production_line_id = default_lang.production_line_id AND default_lang.clang_id = '. \rex_config::get('d2u_helper', 'default_lang') .' '
                    .'WHERE target_lang.production_line_id IS NULL '
                    .'ORDER BY default_lang.name';
            $clang_id = (int) \rex_config::get('d2u_helper', 'default_lang');
        }
        $result = \rex_sql::factory();
        $result->setQuery($query);

        $objects = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $objects[] = new self((int) $result->getValue('production_line_id'), $clang_id);
            $result->next();
        }

        return $objects;
    }

    /**
     * Returns the URL of this object.
     * @param bool $including_domain true if Domain name should be included
     * @return string URL
     */
    public function getUrl($including_domain = false)
    {
        if ('' === $this->url) {
            $d2u_machinery = rex_addon::get('d2u_machinery');

            $parameterArray = [];
            $parameterArray['production_line_id'] = $this->production_line_id;
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
     * Returns if object is used and thus online.
     * @return bool true if object is online, otherwise false
     */
    public function isOnline()
    {
        return 'online' === $this->online_status;
    }

    /**
     * Updates or inserts the object into database.
     * @return bool true if successful
     */
    public function save(): bool
    {
        $error = false;

        // Save the not language specific part
        $pre_save_object = new self($this->production_line_id, $this->clang_id);

        // saving the rest
        if (0 === $this->production_line_id || $pre_save_object !== $this) {
            $query = \rex::getTablePrefix() .'d2u_machinery_production_lines SET '
                    ."complementary_machine_ids = '|". implode('|', $this->complementary_machine_ids) ."|', "
                    ."line_code = '". $this->line_code ."', "
                    ."machine_ids = '|". implode('|', $this->machine_ids) ."|', "
                    ."online_status = '". $this->online_status ."', "
                    ."pictures = '". implode(',', $this->pictures) ."', "
                    ."link_picture = '". $this->link_picture ."', "
                    ."usp_ids = '|". implode('|', $this->usp_ids) ."|', "
                    ."video_ids = '|". implode('|', $this->video_ids) ."|', "
                    ."reference_ids = '". implode(',', $this->reference_ids) ."' ";
            if (rex_plugin::get('d2u_machinery', 'industry_sectors')->isAvailable()) {
                $query .= ", industry_sector_ids = '|". implode('|', $this->industry_sector_ids) ."|' ";
            }
            if (rex_plugin::get('d2u_machinery', 'machine_steel_processing_extension')->isAvailable()) {
                $query .= ", automation_supply_ids = '|". implode('|', $this->automation_supply_ids) ."|' ";
            }

            if (0 === $this->production_line_id) {
                $query = 'INSERT INTO '. $query;
            } else {
                $query = 'UPDATE '. $query .' WHERE production_line_id = '. $this->production_line_id;
            }

            $result = \rex_sql::factory();
            $result->setQuery($query);
            if (0 === $this->production_line_id) {
                $this->production_line_id = (int) $result->getLastId();
                $error = $result->hasError();
            }
        }

        $regenerate_urls = false;
        if (false === $error) {
            // Save the language specific part
            $pre_save_object = new self($this->production_line_id, $this->clang_id);
            if ($pre_save_object !== $this) {
                $query = 'REPLACE INTO '. \rex::getTablePrefix() .'d2u_machinery_production_lines_lang SET '
                        ."production_line_id = '". $this->production_line_id ."', "
                        ."clang_id = '". $this->clang_id ."', "
                        ."description_long = '". addslashes($this->description_long) ."', "
                        ."description_short = '". addslashes($this->description_short) ."', "
                        ."name = '". addslashes($this->name) ."', "
                        ."teaser = '". addslashes($this->teaser) ."', "
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
            \TobiasKrais\D2UHelper\BackendHelper::generateUrlCache('production_line_id');
        }

        return !$error;
    }
}
