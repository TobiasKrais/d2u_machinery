<?php
/**
 * Redaxo D2U Machines Addon.
 * @author Tobias Krais
 * @author <a href="http://www.design-to-use.de">www.design-to-use.de</a>
 */

/**
 * Machine Feature.
 */
class Feature implements \TobiasKrais\D2UHelper\ITranslationHelper
{
    /** @var int Database ID */
    public int $feature_id = 0;

    /** @var int Redaxo clang id */
    public int $clang_id = 0;

    /** @var int Sort Priority */
    public int $priority = 0;

    /** @var string Name */
    public string $name = '';

    /** @var string Preview picture file name */
    public string $pic = '';

    /** @var TobiasKrais\D2UVideos\Video|bool Videomanager video */
    public TobiasKrais\D2UVideos\Video|bool $video = false;

    /** @var int[] Category ids in which the feature will be available */
    public array $category_ids = [];

    /** @var string Detailed description */
    public string $description = '';

    /** @var string "yes" if translation needs update */
    public string $translation_needs_update = 'delete';

    /**
     * Constructor. Reads a feature stored in database.
     * @param int $feature_id feature ID
     * @param int $clang_id redaxo clang id
     */
    public function __construct($feature_id, $clang_id)
    {
        $this->clang_id = $clang_id;
        $query = 'SELECT * FROM '. \rex::getTablePrefix() .'d2u_machinery_features AS features '
                .'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_features_lang AS lang '
                    .'ON features.feature_id = lang.feature_id '
                    .'AND clang_id = '. $this->clang_id .' '
                .'WHERE features.feature_id = '. $feature_id;
        $result = \rex_sql::factory();
        $result->setQuery($query);
        $num_rows = $result->getRows();

        if ($num_rows > 0) {
            $this->feature_id = (int) $result->getValue('feature_id');
            $this->priority = (int) $result->getValue('priority');
            $this->name = stripslashes((string) $result->getValue('name'));
            $this->pic = (string) $result->getValue('pic');
            $category_ids = preg_grep('/^\s*$/s', explode('|', (string) $result->getValue('category_ids')), PREG_GREP_INVERT);
            $this->category_ids = is_array($category_ids) ? array_map('intval', $category_ids) : [];
            $this->description = htmlspecialchars_decode(stripslashes((string) $result->getValue('description')));
            if ('' !== $result->getValue('translation_needs_update') && null !== $result->getValue('translation_needs_update')) {
                $this->translation_needs_update = (string) $result->getValue('translation_needs_update');
            }

            if (\rex_addon::get('d2u_videos') instanceof rex_addon && \rex_addon::get('d2u_videos')->isAvailable() && $result->getValue('video_id') > 0) {
                $this->video = new \TobiasKrais\D2UVideos\Video((int) $result->getValue('video_id'), $clang_id);
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
        $query_lang = 'DELETE FROM '. \rex::getTablePrefix() .'d2u_machinery_features_lang '
            .'WHERE feature_id = '. $this->feature_id
            . ($delete_all ? '' : ' AND clang_id = '. $this->clang_id);
        $result_lang = \rex_sql::factory();
        $result_lang->setQuery($query_lang);

        // If no more lang objects are available, delete
        $query_main = 'SELECT * FROM '. \rex::getTablePrefix() .'d2u_machinery_features_lang '
            .'WHERE feature_id = '. $this->feature_id;
        $result_main = \rex_sql::factory();
        $result_main->setQuery($query_main);
        if (0 === $result_main->getRows()) {
            $query = 'DELETE FROM '. \rex::getTablePrefix() .'d2u_machinery_features '
                .'WHERE feature_id = '. $this->feature_id;
            $result = \rex_sql::factory();
            $result->setQuery($query);

            // reset priorities
            $this->setPriority(true);
        }
    }

    /**
     * Get all features.
     * @param int $clang_id redaxo clang id
     * @param int $category_id return only features available in this category
     * @return Feature[] array with Feature objects
     */
    public static function getAll($clang_id, $category_id = 0)
    {
        $query = 'SELECT lang.feature_id FROM '. \rex::getTablePrefix() .'d2u_machinery_features_lang AS lang '
            .'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_features AS features '
                .'ON lang.feature_id = features.feature_id '
            .'WHERE clang_id = '. $clang_id .' ';
        if ($category_id > 0) {
            $query .= "AND category_ids LIKE '%|". $category_id ."|%' ";
        }
        $query .= 'ORDER BY priority';
        $result = \rex_sql::factory();
        $result->setQuery($query);

        $features = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $features[$result->getValue('feature_id')] = new self((int) $result->getValue('feature_id'), $clang_id);
            $result->next();
        }
        return $features;
    }

    /**
     * Gets the machines referring to this object.
     * @return Machine[] machines referring to this object
     */
    public function getReferringMachines()
    {
        $query = 'SELECT machine_id FROM '. \rex::getTablePrefix() .'d2u_machinery_machines '
            ."WHERE feature_ids LIKE '%|". $this->feature_id ."|%'";
        $result = \rex_sql::factory();
        $result->setQuery($query);

        $machines = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $machines[] = new Machine((int) $result->getValue('machine_id'), $this->clang_id);
            $result->next();
        }
        return $machines;
    }

    /**
     * Get objects concerning translation updates.
     * @param int $clang_id Redaxo language ID
     * @param string $type 'update' or 'missing'
     * @return Feature[] array with Feature objects
     */
    public static function getTranslationHelperObjects($clang_id, $type)
    {
        $query = 'SELECT feature_id FROM '. \rex::getTablePrefix() .'d2u_machinery_features_lang '
                .'WHERE clang_id = '. $clang_id ." AND translation_needs_update = 'yes' "
                .'ORDER BY name';
        if ('missing' === $type) {
            $query = 'SELECT main.feature_id FROM '. \rex::getTablePrefix() .'d2u_machinery_features AS main '
                    .'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_features_lang AS target_lang '
                        .'ON main.feature_id = target_lang.feature_id AND target_lang.clang_id = '. $clang_id .' '
                    .'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_features_lang AS default_lang '
                        .'ON main.feature_id = default_lang.feature_id AND default_lang.clang_id = '. \rex_config::get('d2u_helper', 'default_lang') .' '
                    .'WHERE target_lang.feature_id IS NULL '
                    .'ORDER BY default_lang.name';
            $clang_id = (int) \rex_config::get('d2u_helper', 'default_lang');
        }
        $result = \rex_sql::factory();
        $result->setQuery($query);

        $objects = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $objects[] = new self((int) $result->getValue('feature_id'), $clang_id);
            $result->next();
        }

        return $objects;
    }

    /**
     * returned all unused objects in database.
     * @return bool|Feature[] Array with unused features
     */
    public static function getUnused()
    {
        $features = self::getAll((int) rex_config::get('d2u_helper', 'default_lang', rex_clang::getStartId()));

        foreach ($features as $feature) {
            if (count($feature->getReferringMachines()) > 0) {
                unset($features[$feature->feature_id]);
            }
        }

        if (count($features) > 0) {
            return $features;
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
        $pre_save_feature = new self($this->feature_id, $this->clang_id);

        // save priority, but only if new or changed
        if ($this->priority !== $pre_save_feature->priority || 0 === $this->feature_id) {
            $this->setPriority();
        }

        // saving the rest
        if (0 === $this->feature_id || $pre_save_feature !== $this) {
            $query = \rex::getTablePrefix() .'d2u_machinery_features SET '
                    ."pic = '". $this->pic ."', "
                    ."category_ids = '|". implode('|', $this->category_ids) ."|', "
                    .'priority = '. $this->priority .' ';
            if (\rex_addon::get('d2u_videos') instanceof rex_addon && \rex_addon::get('d2u_videos')->isAvailable() && false !== $this->video) {
                $query .= ', video_id = '. ($this->video instanceof TobiasKrais\D2UVideos\Video ? $this->video->video_id : 0);
            } else {
                $query .= ', video_id = NULL';
            }

            if (0 === $this->feature_id) {
                $query = 'INSERT INTO '. $query;
            } else {
                $query = 'UPDATE '. $query .' WHERE feature_id = '. $this->feature_id;
            }

            $result = \rex_sql::factory();
            $result->setQuery($query);
            if (0 === $this->feature_id) {
                $this->feature_id = (int) $result->getLastId();
                $error = $result->hasError();
            }
        }

        if (false === $error) {
            // Save the language specific part
            $pre_save_feature = new self($this->feature_id, $this->clang_id);
            if ($pre_save_feature !== $this) {
                $query = 'REPLACE INTO '. \rex::getTablePrefix() .'d2u_machinery_features_lang SET '
                        ."feature_id = '". $this->feature_id ."', "
                        ."clang_id = '". $this->clang_id ."', "
                        ."name = '". addslashes($this->name) ."', "
                        ."description = '". addslashes(htmlspecialchars($this->description)) ."', "
                        ."translation_needs_update = '". $this->translation_needs_update ."' ";

                $result = \rex_sql::factory();
                $result->setQuery($query);
                $error = $result->hasError();
            }
        }

        return !$error;
    }

    /**
     * Reassigns priorities in database.
     * @param bool $delete Reorder priority after deletion
     */
    private function setPriority($delete = false): void
    {
        // Pull priorities from database
        $query = 'SELECT feature_id, priority FROM '. \rex::getTablePrefix() .'d2u_machinery_features '
            .'WHERE feature_id <> '. $this->feature_id .' ORDER BY priority';
        $result = \rex_sql::factory();
        $result->setQuery($query);

        // When prio is too small, set at beginning
        if ($this->priority <= 0) {
            $this->priority = 1;
        }

        // When prio is too high or was deleted, simply add at end
        if ($this->priority > $result->getRows() || $delete) {
            $this->priority = $result->getRows() + 1;
        }

        $features = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $features[$result->getValue('priority')] = $result->getValue('feature_id');
            $result->next();
        }
        array_splice($features, $this->priority - 1, 0, [$this->feature_id]);

        // Save all prios
        foreach ($features as $prio => $feature_id) {
            $query = 'UPDATE '. \rex::getTablePrefix() .'d2u_machinery_features '
                    .'SET priority = '. ((int) $prio + 1) .' ' // +1 because array_splice recounts at zero
                    .'WHERE feature_id = '. $feature_id;
            $result = \rex_sql::factory();
            $result->setQuery($query);
        }
    }
}
