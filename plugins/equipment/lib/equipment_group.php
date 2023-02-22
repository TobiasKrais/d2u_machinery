<?php
/**
 * Redaxo D2U Machines Addon.
 * @author Tobias Krais
 * @author <a href="http://www.design-to-use.de">www.design-to-use.de</a>
 */

/**
 * Machine Equipment Group.
 */
class EquipmentGroup implements \D2U_Helper\ITranslationHelper
{
    /** @var int Database ID */
    public int $group_id = 0;

    /** @var int Redaxo clang id */
    public int $clang_id = 0;

    /** @var string Preview picture file name */
    public string $picture = '';

    /** @var int Sort Priority */
    public int $priority = 0;

    /** @var string Name */
    public string $name = '';

    /** @var string Detailed description */
    public string $description = '';

    /** @var string "yes" if translation needs update */
    public string $translation_needs_update = 'delete';

    /**
     * Constructor. Reads a object stored in database.
     * @param int $group_id equipmentGroup ID
     * @param int $clang_id redaxo clang id
     */
    public function __construct($group_id, $clang_id)
    {
        $this->clang_id = $clang_id;
        $query = 'SELECT * FROM '. \rex::getTablePrefix() .'d2u_machinery_equipment_groups AS equipment_groups '
                .'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_equipment_groups_lang AS lang '
                    .'ON equipment_groups.group_id = lang.group_id '
                    .'AND clang_id = '. $this->clang_id .' '
                .'WHERE equipment_groups.group_id = '. $group_id;
        $result = \rex_sql::factory();
        $result->setQuery($query);
        $num_rows = $result->getRows();

        if ($num_rows > 0) {
            $this->group_id = (int) $result->getValue('group_id');
            $this->priority = (int) $result->getValue('priority');
            $this->picture = (string) $result->getValue('picture');
            $this->name = stripslashes((string) $result->getValue('name'));
            $this->description = stripslashes(htmlspecialchars_decode((string) $result->getValue('description')));
            if ('' !== (string) $result->getValue('translation_needs_update')) {
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
        $query_lang = 'DELETE FROM '. \rex::getTablePrefix() .'d2u_machinery_equipment_groups_lang '
            .'WHERE group_id = '. $this->group_id
            . ($delete_all ? '' : ' AND clang_id = '. $this->clang_id);
        $result_lang = \rex_sql::factory();
        $result_lang->setQuery($query_lang);

        // If no more lang objects are available, delete
        $query_main = 'SELECT * FROM '. \rex::getTablePrefix() .'d2u_machinery_equipment_groups_lang '
            .'WHERE group_id = '. $this->group_id;
        $result_main = \rex_sql::factory();
        $result_main->setQuery($query_main);
        if (0 === (int) $result_main->getRows()) {
            $query = 'DELETE FROM '. \rex::getTablePrefix() .'d2u_machinery_equipment_groups '
                .'WHERE group_id = '. $this->group_id;
            $result = \rex_sql::factory();
            $result->setQuery($query);

            // reset priorities
            $this->setPriority(true);
        }
    }

    /**
     * Get all equipment_groups.
     * @param int $clang_id redaxo clang id
     * @return EquipmentGroup[] array with EquipmentGroup objects
     */
    public static function getAll($clang_id)
    {
        $query = 'SELECT lang.group_id FROM '. \rex::getTablePrefix() .'d2u_machinery_equipment_groups_lang AS lang '
            .'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_equipment_groups AS equipment_groups '
                .'ON lang.group_id = equipment_groups.group_id '
            .'WHERE clang_id = '. $clang_id .' '
            .'ORDER BY lang.name';
        $result = \rex_sql::factory();
        $result->setQuery($query);

        $equipment_groups = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $equipment_groups[] = new self((int) $result->getValue('group_id'), $clang_id);
            $result->next();
        }
        return $equipment_groups;
    }

    /**
     * Gets the equipments referring to this object.
     * @return Equipment[] equipment referring to this object
     */
    public function getReferringEquipments()
    {
        $query = 'SELECT equipment_id FROM '. \rex::getTablePrefix() .'d2u_machinery_equipments '
            .'WHERE group_id = '. $this->group_id;
        $result = \rex_sql::factory();
        $result->setQuery($query);

        $equipments = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $equipments[] = new Equipment((int) $result->getValue('equipment_id'), $this->clang_id);
            $result->next();
        }
        return $equipments;
    }

    /**
     * Get objects concerning translation updates.
     * @param int $clang_id Redaxo language ID
     * @param string $type 'update' or 'missing'
     * @return EquipmentGroup[] array with EquipmentGroup objects
     */
    public static function getTranslationHelperObjects($clang_id, $type)
    {
        $query = 'SELECT group_id FROM '. \rex::getTablePrefix() .'d2u_machinery_equipment_groups_lang '
                .'WHERE clang_id = '. $clang_id ." AND translation_needs_update = 'yes' "
                .'ORDER BY name';
        if ('missing' === $type) {
            $query = 'SELECT main.group_id FROM '. \rex::getTablePrefix() .'d2u_machinery_equipment_groups AS main '
                    .'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_equipment_groups_lang AS target_lang '
                        .'ON main.group_id = target_lang.group_id AND target_lang.clang_id = '. $clang_id .' '
                    .'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_equipment_groups_lang AS default_lang '
                        .'ON main.group_id = default_lang.group_id AND default_lang.clang_id = '. \rex_config::get('d2u_helper', 'default_lang') .' '
                    .'WHERE target_lang.group_id IS NULL '
                    .'ORDER BY default_lang.name';
            $clang_id = (int) \rex_config::get('d2u_helper', 'default_lang');
        }
        $result = \rex_sql::factory();
        $result->setQuery($query);

        $objects = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $objects[] = new self((int) $result->getValue('group_id'), $clang_id);
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
        $pre_save_object = new self($this->group_id, $this->clang_id);

        // save priority, but only if new or changed
        if ($this->priority !== $pre_save_object->priority || 0 === $this->group_id) {
            $this->setPriority();
        }

        // saving the rest
        if (0 === $this->group_id || $pre_save_object !== $this) {
            $query = \rex::getTablePrefix() .'d2u_machinery_equipment_groups SET '
                    ."picture = '". $this->picture ."', "
                    ."priority = '". $this->priority ."' ";

            if (0 === $this->group_id) {
                $query = 'INSERT INTO '. $query;
            } else {
                $query = 'UPDATE '. $query .' WHERE group_id = '. $this->group_id;
            }

            $result = \rex_sql::factory();
            $result->setQuery($query);
            if (0 === $this->group_id) {
                $this->group_id = (int) $result->getLastId();
                $error = $result->hasError();
            }
        }

        if (false === $error) {
            // Save the language specific part
            $pre_save_object = new self($this->group_id, $this->clang_id);
            if ($pre_save_object !== $this) {
                $query = 'REPLACE INTO '. \rex::getTablePrefix() .'d2u_machinery_equipment_groups_lang SET '
                        ."group_id = '". $this->group_id ."', "
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
        $query = 'SELECT group_id, priority FROM '. \rex::getTablePrefix() .'d2u_machinery_equipment_groups '
            .'WHERE group_id <> '. $this->group_id .' ORDER BY priority';
        $result = \rex_sql::factory();
        $result->setQuery($query);

        // When prio is too small, set at beginning
        if ($this->priority <= 0) {
            $this->priority = 1;
        }

        // When prio is too high or was deleted, simply add at end
        if ($this->priority > $result->getRows() || $delete) {
            $this->priority = (int) $result->getRows() + 1;
        }

        $equipment_groups = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $equipment_groups[$result->getValue('priority')] = $result->getValue('group_id');
            $result->next();
        }
        array_splice($equipment_groups, $this->priority - 1, 0, [$this->group_id]);

        // Save all prios
        foreach ($equipment_groups as $prio => $group_id) {
            $query = 'UPDATE '. \rex::getTablePrefix() .'d2u_machinery_equipment_groups '
                    .'SET priority = '. ((int) $prio + 1) .' ' // +1 because array_splice recounts at zero
                    .'WHERE group_id = '. $group_id;
            $result = \rex_sql::factory();
            $result->setQuery($query);
        }
    }
}
