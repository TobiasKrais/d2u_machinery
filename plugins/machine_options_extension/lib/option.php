<?php
/**
 * Redaxo D2U Machines Addon.
 * @author Tobias Krais
 * @author <a href="http://www.design-to-use.de">www.design-to-use.de</a>
 */

/**
 * Machine Options.
 */
class Option implements \TobiasKrais\D2UHelper\ITranslationHelper
{
    /** @var int Database ID */
    public int $option_id = 0;

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

    /** @var int[] Category ids in which the option will be available */
    public array $category_ids = [];

    /** @var string Detailed description */
    public string $description = '';

    /** @var string "yes" if translation needs update */
    public string $translation_needs_update = 'delete';

    /**
     * Constructor. Reads a option stored in database.
     * @param int $option_id option ID
     * @param int $clang_id redaxo clang id
     */
    public function __construct($option_id, $clang_id)
    {
        $this->clang_id = $clang_id;
        $query = 'SELECT * FROM '. \rex::getTablePrefix() .'d2u_machinery_options AS options '
                .'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_options_lang AS lang '
                    .'ON options.option_id = lang.option_id '
                    .'AND clang_id = '. $this->clang_id .' '
                .'WHERE options.option_id = '. $option_id;
        $result = \rex_sql::factory();
        $result->setQuery($query);
        $num_rows = $result->getRows();

        if ($num_rows > 0) {
            $this->option_id = (int) $result->getValue('option_id');
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
        $query_lang = 'DELETE FROM '. \rex::getTablePrefix() .'d2u_machinery_options_lang '
            .'WHERE option_id = '. $this->option_id
            . ($delete_all ? '' : ' AND clang_id = '. $this->clang_id);
        $result_lang = \rex_sql::factory();
        $result_lang->setQuery($query_lang);

        // If no more lang objects are available, delete
        $query_main = 'SELECT * FROM '. \rex::getTablePrefix() .'d2u_machinery_options_lang '
            .'WHERE option_id = '. $this->option_id;
        $result_main = \rex_sql::factory();
        $result_main->setQuery($query_main);
        if (0 === $result_main->getRows()) {
            $query = 'DELETE FROM '. \rex::getTablePrefix() .'d2u_machinery_options '
                .'WHERE option_id = '. $this->option_id;
            $result = \rex_sql::factory();
            $result->setQuery($query);

            // reset priorities
            $this->setPriority(true);
        }
    }

    /**
     * Get all options.
     * @param int $clang_id redaxo clang id
     * @param int $category_id return only options available in this category
     * @return Option[] array with Option objects
     */
    public static function getAll($clang_id, $category_id = 0)
    {
        $query = 'SELECT lang.option_id FROM '. \rex::getTablePrefix() .'d2u_machinery_options_lang AS lang '
            .'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_options AS options '
                .'ON lang.option_id = options.option_id '
            .'WHERE clang_id = '. $clang_id .' ';
        if ($category_id > 0) {
            $query .= "AND category_ids LIKE '%|". $category_id ."|%' ";
        }
        $query .= 'ORDER BY priority';
        $result = \rex_sql::factory();
        $result->setQuery($query);

        $options = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $options[$result->getValue('option_id')] = new self((int) $result->getValue('option_id'), $clang_id);
            $result->next();
        }
        return $options;
    }

    /**
     * Gets the machines referring to this object.
     * @return Machine[] machines referring to this object
     */
    public function getReferringMachines()
    {
        $query = 'SELECT machine_id FROM '. \rex::getTablePrefix() .'d2u_machinery_machines '
            ."WHERE option_ids LIKE '%|". $this->option_id ."|%'";
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
     * @return Option[] array with Option objects
     */
    public static function getTranslationHelperObjects($clang_id, $type)
    {
        $query = 'SELECT option_id FROM '. \rex::getTablePrefix() .'d2u_machinery_options_lang '
                .'WHERE clang_id = '. $clang_id ." AND translation_needs_update = 'yes' "
                .'ORDER BY name';
        if ('missing' === $type) {
            $query = 'SELECT main.option_id FROM '. \rex::getTablePrefix() .'d2u_machinery_options AS main '
                    .'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_options_lang AS target_lang '
                        .'ON main.option_id = target_lang.option_id AND target_lang.clang_id = '. $clang_id .' '
                    .'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_options_lang AS default_lang '
                        .'ON main.option_id = default_lang.option_id AND default_lang.clang_id = '. \rex_config::get('d2u_helper', 'default_lang') .' '
                    .'WHERE target_lang.option_id IS NULL '
                    .'ORDER BY default_lang.name';
            $clang_id = (int) \rex_config::get('d2u_helper', 'default_lang');
        }
        $result = \rex_sql::factory();
        $result->setQuery($query);

        $objects = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $objects[] = new self((int) $result->getValue('option_id'), $clang_id);
            $result->next();
        }

        return $objects;
    }

    /**
     * returned all unused objects in database.
     * @return bool|Option[] Array with unused options
     */
    public static function getUnused()
    {
        $options = self::getAll((int) rex_config::get('d2u_helper', 'default_lang', rex_clang::getStartId()));

        foreach ($options as $option) {
            if (count($option->getReferringMachines()) > 0) {
                unset($options[$option->option_id]);
            }
        }

        if (count($options) > 0) {
            return $options;
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
        $pre_save_option = new self($this->option_id, $this->clang_id);

        // save priority, but only if new or changed
        if ($this->priority !== $pre_save_option->priority || 0 === $this->option_id) {
            $this->setPriority();
        }

        // saving the rest
        if (0 === $this->option_id || $pre_save_option !== $this) {
            $query = \rex::getTablePrefix() .'d2u_machinery_options SET '
                    ."pic = '". $this->pic ."', "
                    ."category_ids = '|". implode('|', $this->category_ids) ."|', "
                    .'priority = '. $this->priority .' ';
            if (\rex_addon::get('d2u_videos') instanceof rex_addon && \rex_addon::get('d2u_videos')->isAvailable() && false !== $this->video) {
                $query .= ', video_id = '. ($this->video instanceof TobiasKrais\D2UVideos\Video ? $this->video->video_id : 0);
            } else {
                $query .= ', video_id = NULL';
            }

            if (0 === $this->option_id) {
                $query = 'INSERT INTO '. $query;
            } else {
                $query = 'UPDATE '. $query .' WHERE option_id = '. $this->option_id;
            }

            $result = \rex_sql::factory();
            $result->setQuery($query);
            if (0 === $this->option_id) {
                $this->option_id = (int) $result->getLastId();
                $error = $result->hasError();
            }
        }

        if (false === $error) {
            // Save the language specific part
            $pre_save_option = new self($this->option_id, $this->clang_id);
            if ($pre_save_option !== $this) {
                $query = 'REPLACE INTO '. \rex::getTablePrefix() .'d2u_machinery_options_lang SET '
                        ."option_id = '". $this->option_id ."', "
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
        $query = 'SELECT option_id, priority FROM '. \rex::getTablePrefix() .'d2u_machinery_options '
            .'WHERE option_id <> '. $this->option_id .' ORDER BY priority';
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

        $options = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $options[$result->getValue('priority')] = $result->getValue('option_id');
            $result->next();
        }
        array_splice($options, $this->priority - 1, 0, [$this->option_id]);

        // Save all prios
        foreach ($options as $prio => $option_id) {
            $query = 'UPDATE '. \rex::getTablePrefix() .'d2u_machinery_options '
                    .'SET priority = '. ((int) $prio + 1) .' ' // +1 because array_splice recounts at zero
                    .'WHERE option_id = '. $option_id;
            $result = \rex_sql::factory();
            $result->setQuery($query);
        }
    }
}
