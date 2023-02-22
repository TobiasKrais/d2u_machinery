<?php
/**
 * Redaxo D2U Machines Addon.
 * @author Tobias Krais
 * @author <a href="http://www.design-to-use.de">www.design-to-use.de</a>
 */

/**
 * Unique selling propositions.
 */
class USP implements \D2U_Helper\ITranslationHelper
{
    /** @var int Database ID */
    public int $usp_id = 0;

    /** @var int Redaxo clang id */
    public int $clang_id = 0;

    /** @var string Picture file names */
    public string $picture = '';

    /** @var string Name */
    public string $name = '';

    /** @var string Teaser */
    public string $teaser = '';
    /** @var string "yes" if translation needs update */
    public $translation_needs_update = 'delete';

    /**
     * Constructor. Reads the object stored in database.
     * @param int $usp_id object ID
     * @param int $clang_id redaxo clang id
     */
    public function __construct($usp_id, $clang_id)
    {
        $this->clang_id = $clang_id;
        $query = 'SELECT * FROM '. \rex::getTablePrefix() .'d2u_machinery_production_lines_usps AS usps '
                .'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_production_lines_usps_lang AS lang '
                    .'ON usps.usp_id = lang.usp_id '
                    .'AND clang_id = '. $this->clang_id .' '
                .'WHERE usps.usp_id = '. $usp_id;
        $result = \rex_sql::factory();
        $result->setQuery($query);
        $num_rows = $result->getRows();

        if ($num_rows > 0) {
            $this->usp_id = (int) $result->getValue('usp_id');
            $this->name = stripslashes((string) $result->getValue('name'));
            $this->picture = (string) $result->getValue('picture');
            $this->teaser = stripslashes((string) $result->getValue('teaser'));
            if ('' !== $result->getValue('translation_needs_update')) {
                $this->translation_needs_update = (string) $result->getValue('translation_needs_update');
            }
        }
    }

    /**
     * Deletes the object in all languages.
     * @param bool $delete_all If true, all translations and main object are deleted. If
     * false, only this translation will be deleted.
     */
    public function delete($delete_all = true): void
    {
        $query_lang = 'DELETE FROM '. \rex::getTablePrefix() .'d2u_machinery_production_lines_usps_lang '
            .'WHERE usp_id = '. $this->usp_id
            . ($delete_all ? '' : ' AND clang_id = '. $this->clang_id);
        $result_lang = \rex_sql::factory();
        $result_lang->setQuery($query_lang);

        // If no more lang objects are available, delete
        $query_main = 'SELECT * FROM '. \rex::getTablePrefix() .'d2u_machinery_production_lines_usps_lang '
            .'WHERE usp_id = '. $this->usp_id;
        $result_main = \rex_sql::factory();
        $result_main->setQuery($query_main);
        if (0 === (int) $result_main->getRows()) {
            $query = 'DELETE FROM '. \rex::getTablePrefix() .'d2u_machinery_production_lines_usps '
                .'WHERE usp_id = '. $this->usp_id;
            $result = \rex_sql::factory();
            $result->setQuery($query);
        }
    }

    /**
     * Get all objects.
     * @param int $clang_id redaxo clang id
     * @return USP[] array with production line objects
     */
    public static function getAll($clang_id)
    {
        $query = 'SELECT usp_id FROM '. \rex::getTablePrefix() .'d2u_machinery_production_lines_usps_lang '
            .'WHERE clang_id = '. $clang_id .' ';
        $query .= 'ORDER BY name';

        $result = \rex_sql::factory();
        $result->setQuery($query);

        $usps = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $usps[] = new self((int) $result->getValue('usp_id'), $clang_id);
            $result->next();
        }
        return $usps;
    }

    /**
     * Gets the machines referring to this object.
     * @param bool $online_only true if only online production lines should be returned
     * @return ProductionLine[] production lines referring to this object
     */
    public function getReferringProductionLines($online_only = false)
    {
        $query = 'SELECT production_lines.production_line_id FROM '. \rex::getTablePrefix() .'d2u_machinery_production_lines AS production_lines '
            .'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_production_lines_lang AS lang '
                .' ON production_lines.production_line_id = lang.production_line_id AND lang.clang_id = '. $this->clang_id .' '
            ."WHERE usp_ids LIKE '%|". $this->usp_id ."|%' ";
        $query .= 'ORDER BY name';

        $result = \rex_sql::factory();
        $result->setQuery($query);

        $production_lines = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $production_line = new ProductionLine((int) $result->getValue('production_line_id'), $this->clang_id);
            if (false === $online_only || (true === $online_only && $production_line->isOnline())) {
                $production_lines[] = $production_line;
            }
            $result->next();
        }
        return $production_lines;
    }

    /**
     * Get objects concerning translation updates.
     * @param int $clang_id Redaxo language ID
     * @param string $type 'update' or 'missing'
     * @return USP[] array with USP objects
     */
    public static function getTranslationHelperObjects($clang_id, $type)
    {
        $query = 'SELECT usp_id FROM '. \rex::getTablePrefix() .'d2u_machinery_production_lines_usps_lang '
                .'WHERE clang_id = '. $clang_id ." AND translation_needs_update = 'yes' "
                .'ORDER BY name';
        if ('missing' === $type) {
            $query = 'SELECT main.usp_id FROM '. \rex::getTablePrefix() .'d2u_machinery_production_lines_usps AS main '
                    .'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_production_lines_usps_lang AS target_lang '
                        .'ON main.usp_id = target_lang.usp_id AND target_lang.clang_id = '. $clang_id .' '
                    .'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_production_lines_usps_lang AS default_lang '
                        .'ON main.usp_id = default_lang.usp_id AND default_lang.clang_id = '. \rex_config::get('d2u_helper', 'default_lang') .' '
                    .'WHERE target_lang.usp_id IS NULL '
                    .'ORDER BY default_lang.name';
            $clang_id = (int) \rex_config::get('d2u_helper', 'default_lang');
        }
        $result = \rex_sql::factory();
        $result->setQuery($query);

        $objects = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $objects[] = new self((int) $result->getValue('usp_id'), $clang_id);
            $result->next();
        }

        return $objects;
    }

    /**
     * Updates or inserts the object into database.
     * @return bool true if successful
     */
    public function save()
    {
        $error = false;

        // Save the not language specific part
        $pre_save_object = new self($this->usp_id, $this->clang_id);

        // saving the rest
        if (0 === $this->usp_id || $pre_save_object !== $this) {
            $query = \rex::getTablePrefix() .'d2u_machinery_production_lines_usps SET '
                    ."picture = '". $this->picture ."' ";

            if (0 === $this->usp_id) {
                $query = 'INSERT INTO '. $query;
            } else {
                $query = 'UPDATE '. $query .' WHERE usp_id = '. $this->usp_id;
            }

            $result = \rex_sql::factory();
            $result->setQuery($query);
            if (0 === $this->usp_id) {
                $this->usp_id = (int) $result->getLastId();
                $error = $result->hasError();
            }
        }

        $regenerate_urls = false;
        if (false === $error) {
            // Save the language specific part
            $pre_save_object = new self($this->usp_id, $this->clang_id);
            if ($pre_save_object !== $this) {
                $query = 'REPLACE INTO '. \rex::getTablePrefix() .'d2u_machinery_production_lines_usps_lang SET '
                        ."usp_id = '". $this->usp_id ."', "
                        ."clang_id = '". $this->clang_id ."', "
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
        return !$error;
    }
}
