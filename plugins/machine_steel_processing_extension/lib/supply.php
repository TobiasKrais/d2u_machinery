<?php
/**
 * Redaxo D2U Machines Addon.
 * @author Tobias Krais
 * @author <a href="http://www.design-to-use.de">www.design-to-use.de</a>
 */

/**
 * Supply.
 */
class Supply implements \TobiasKrais\D2UHelper\ITranslationHelper
{
    /** @var int Database ID */
    public int $supply_id = 0;

    /** @var int Redaxo clang id */
    public int $clang_id = 0;

    /** @var int Sort Priority */
    public int $priority = 0;

    /** @var string Status. Either "online" or "offline". */
    public string $online_status = 'online';

    /** @var string Name */
    public string $name = '';

    /** @var string Description */
    public string $description = '';

    /** @var string Picture */
    public string $pic = '';

    /** @var TobiasKrais\D2UVideos\Video|bool Videomanager video */
    public TobiasKrais\D2UVideos\Video|bool $video = false;

    /** @var string "yes" if translation needs update */
    public string $translation_needs_update = 'delete';

    /**
     * Constructor. Reads object stored in database.
     * @param int $supply_id object ID
     * @param int $clang_id redaxo clang id
     */
    public function __construct($supply_id, $clang_id)
    {
        $this->clang_id = $clang_id;
        $query = 'SELECT * FROM '. \rex::getTablePrefix() .'d2u_machinery_steel_supply AS supplys '
                .'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_steel_supply_lang AS lang '
                    .'ON supplys.supply_id = lang.supply_id '
                    .'AND clang_id = '. $this->clang_id .' '
                .'WHERE supplys.supply_id = '. $supply_id;
        $result = \rex_sql::factory();
        $result->setQuery($query);
        $num_rows = $result->getRows();

        if ($num_rows > 0) {
            $this->supply_id = (int) $result->getValue('supply_id');
            $this->priority = (int) $result->getValue('priority');
            $this->online_status = (string) $result->getValue('online_status');
            $this->name = stripslashes((string) $result->getValue('name'));
            $this->description = stripslashes((string) $result->getValue('description'));
            $this->pic = (string) $result->getValue('pic');
            if ('' !== $result->getValue('translation_needs_update') && null !== $result->getValue('translation_needs_update')) {
                $this->translation_needs_update = (string) $result->getValue('translation_needs_update');
            }

            if (\rex_addon::get('d2u_videos') instanceof rex_addon && \rex_addon::get('d2u_videos')->isAvailable() && $result->getValue('video_id') > 0) {
                $this->video = new \TobiasKrais\D2UVideos\Video((int) $result->getValue('video_id'), $clang_id);
            }
        }
    }

    /**
     * Changes the status of the object.
     */
    public function changeStatus(): void
    {
        if ('online' === $this->online_status) {
            if ($this->supply_id > 0) {
                $query = 'UPDATE '. \rex::getTablePrefix() .'d2u_machinery_steel_supply '
                    ."SET online_status = 'offline' "
                    .'WHERE supply_id = '. $this->supply_id;
                $result = \rex_sql::factory();
                $result->setQuery($query);
            }
            $this->online_status = 'offline';
        } else {
            if ($this->supply_id > 0) {
                $query = 'UPDATE '. \rex::getTablePrefix() .'d2u_machinery_steel_supply '
                    ."SET online_status = 'online' "
                    .'WHERE supply_id = '. $this->supply_id;
                $result = \rex_sql::factory();
                $result->setQuery($query);
            }
            $this->online_status = 'online';
        }
    }

    /**
     * Deletes the object in all languages.
     * @param bool $delete_all If true, all translations and main object are deleted. If
     * false, only this translation will be deleted.
     */
    public function delete($delete_all = true): void
    {
        $query_lang = 'DELETE FROM '. \rex::getTablePrefix() .'d2u_machinery_steel_supply_lang '
            .'WHERE supply_id = '. $this->supply_id
            . ($delete_all ? '' : ' AND clang_id = '. $this->clang_id);
        $result_lang = \rex_sql::factory();
        $result_lang->setQuery($query_lang);

        // If no more lang objects are available, delete
        $query_main = 'SELECT * FROM '. \rex::getTablePrefix() .'d2u_machinery_steel_supply_lang '
            .'WHERE supply_id = '. $this->supply_id;
        $result_main = \rex_sql::factory();
        $result_main->setQuery($query_main);
        if (0 === $result_main->getRows()) {
            $query = 'DELETE FROM '. \rex::getTablePrefix() .'d2u_machinery_steel_supply '
                .'WHERE supply_id = '. $this->supply_id;
            $result = \rex_sql::factory();
            $result->setQuery($query);

            // reset priorities
            $this->setPriority(true);
        }
    }

    /**
     * Get all supplys.
     * @param int $clang_id redaxo clang id
     * @param bool $only_online Show only online objects
     * @return Supply[] array with Supply objects
     */
    public static function getAll($clang_id, $only_online = false)
    {
        $query = 'SELECT lang.supply_id FROM '. \rex::getTablePrefix() .'d2u_machinery_steel_supply_lang AS lang '
            . 'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_steel_supply AS supply '
                . 'ON lang.supply_id = supply.supply_id '
            .'WHERE clang_id = '. $clang_id .' ';
        if ($only_online) {
            $query .= "WHERE online_status = 'online' ";
        }

        $query .= 'ORDER BY priority';

        $result = \rex_sql::factory();
        $result->setQuery($query);

        $supplys = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $supplys[] = new self((int) $result->getValue('supply_id'), $clang_id);
            $result->next();
        }
        return $supplys;
    }

    /**
     * Gets the machines referring to this object.
     * @return Machine[] machines referring to this object
     */
    public function getReferringMachines()
    {
        $query = 'SELECT machine_id FROM '. \rex::getTablePrefix() .'d2u_machinery_machines '
            ."WHERE automation_supply_ids LIKE '%|". $this->supply_id ."|%'";
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
     * @return Supply[] array with Supply objects
     */
    public static function getTranslationHelperObjects($clang_id, $type)
    {
        $query = 'SELECT supply_id FROM '. \rex::getTablePrefix() .'d2u_machinery_steel_supply_lang '
                .'WHERE clang_id = '. $clang_id ." AND translation_needs_update = 'yes' "
                .'ORDER BY name';
        if ('missing' === $type) {
            $query = 'SELECT main.supply_id FROM '. \rex::getTablePrefix() .'d2u_machinery_steel_supply AS main '
                    .'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_steel_supply_lang AS target_lang '
                        .'ON main.supply_id = target_lang.supply_id AND target_lang.clang_id = '. $clang_id .' '
                    .'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_steel_supply_lang AS default_lang '
                        .'ON main.supply_id = default_lang.supply_id AND default_lang.clang_id = '. \rex_config::get('d2u_helper', 'default_lang') .' '
                    .'WHERE target_lang.supply_id IS NULL '
                    .'ORDER BY default_lang.name';
            $clang_id = (int) \rex_config::get('d2u_helper', 'default_lang');
        }
        $result = \rex_sql::factory();
        $result->setQuery($query);

        $objects = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $objects[] = new self((int) $result->getValue('supply_id'), $clang_id);
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
        $pre_save_supply = new self($this->supply_id, $this->clang_id);

        // save priority, but only if new or changed
        if ($this->priority !== $pre_save_supply->priority || 0 === $this->supply_id) {
            $this->setPriority();
        }

        // saving the rest
        if (0 === $this->supply_id || $pre_save_supply !== $this) {
            $query = \rex::getTablePrefix() .'d2u_machinery_steel_supply SET '
                    ."online_status = '". $this->online_status ."', "
                    ."pic = '". $this->pic ."', "
                    .'priority = '. $this->priority .' ';
            if (\rex_addon::get('d2u_videos') instanceof rex_addon && \rex_addon::get('d2u_videos')->isAvailable() && $this->video instanceof TobiasKrais\D2UVideos\Video) {
                $query .= ', video_id = '. $this->video->video_id;
            } else {
                $query .= ', video_id = NULL';
            }

            if (0 === $this->supply_id) {
                $query = 'INSERT INTO '. $query;
            } else {
                $query = 'UPDATE '. $query .' WHERE supply_id = '. $this->supply_id;
            }

            $result = \rex_sql::factory();
            $result->setQuery($query);
            if (0 === $this->supply_id) {
                $this->supply_id = (int) $result->getLastId();
                $error = $result->hasError();
            }
        }

        if (false === $error) {
            // Save the language specific part
            $pre_save_supply = new self($this->supply_id, $this->clang_id);
            if ($pre_save_supply !== $this) {
                $query = 'REPLACE INTO '. \rex::getTablePrefix() .'d2u_machinery_steel_supply_lang SET '
                        ."supply_id = '". $this->supply_id ."', "
                        ."clang_id = '". $this->clang_id ."', "
                        ."name = '". addslashes($this->name) ."', "
                        ."description = '". addslashes($this->description) ."', "
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
        $query = 'SELECT supply_id, priority FROM '. \rex::getTablePrefix() .'d2u_machinery_steel_supply '
            .'WHERE supply_id <> '. $this->supply_id .' ORDER BY priority';
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

        $objects = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $objects[$result->getValue('priority')] = $result->getValue('supply_id');
            $result->next();
        }
        array_splice($objects, $this->priority - 1, 0, [$this->supply_id]);

        // Save all prios
        foreach ($objects as $prio => $object_id) {
            $query = 'UPDATE '. \rex::getTablePrefix() .'d2u_machinery_steel_supply '
                    .'SET priority = '. ((int) $prio + 1) .' ' // +1 because array_splice recounts at zero
                    .'WHERE supply_id = '. $object_id;
            $result = \rex_sql::factory();
            $result->setQuery($query);
        }
    }
}
