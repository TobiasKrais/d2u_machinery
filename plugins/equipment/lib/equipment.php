<?php
/**
 * Redaxo D2U Machines Addon.
 * @author Tobias Krais
 * @author <a href="http://www.design-to-use.de">www.design-to-use.de</a>
 */

/**
 * Machine Equipment.
 */
class Equipment implements \TobiasKrais\D2UHelper\ITranslationHelper
{
    /** @var int Database ID */
    public int $equipment_id = 0;

    /** @var int Redaxo clang id */
    public int $clang_id = 0;

    /** @var string Article number */
    public string $article_number = '';

    /** @var EquipmentGroup|bool Equipment group */
    public EquipmentGroup|bool $group = false;

    /** @var string Status. Either "online" or "offline". */
    public string $online_status = 'offline';

    /** @var string Name */
    public string $name = '';

    /** @var string "yes" if translation needs update */
    public string $translation_needs_update = 'delete';

    /**
     * Constructor. Reads a object stored in database.
     * @param int $equipment_id equipment ID
     * @param int $clang_id redaxo clang id
     */
    public function __construct($equipment_id, $clang_id)
    {
        $this->clang_id = $clang_id;
        $query = 'SELECT * FROM '. \rex::getTablePrefix() .'d2u_machinery_equipments AS equipments '
                .'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_equipments_lang AS lang '
                    .'ON equipments.equipment_id = lang.equipment_id '
                    .'AND clang_id = '. $this->clang_id .' '
                .'WHERE equipments.equipment_id = '. $equipment_id;
        $result = \rex_sql::factory();
        $result->setQuery($query);
        $num_rows = $result->getRows();

        if ($num_rows > 0) {
            $this->equipment_id = (int) $result->getValue('equipment_id');
            $this->article_number = (string) $result->getValue('article_number');
            $this->online_status = (string) $result->getValue('online_status');
            $this->name = stripslashes((string) $result->getValue('name'));
            if ($result->getValue('group_id') > 0) {
                $this->group = new EquipmentGroup((int) $result->getValue('group_id'), $clang_id);
            }
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
            if ($this->equipment_id > 0) {
                $query = 'UPDATE '. \rex::getTablePrefix() .'d2u_machinery_equipments '
                    ."SET online_status = 'offline' "
                    .'WHERE equipment_id = '. $this->equipment_id;
                $result = \rex_sql::factory();
                $result->setQuery($query);
            }
            $this->online_status = 'offline';
        } else {
            if ($this->equipment_id > 0) {
                $query = 'UPDATE '. \rex::getTablePrefix() .'d2u_machinery_equipments '
                    ."SET online_status = 'online' "
                    .'WHERE equipment_id = '. $this->equipment_id;
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
        $query_lang = 'DELETE FROM '. \rex::getTablePrefix() .'d2u_machinery_equipments_lang '
            .'WHERE equipment_id = '. $this->equipment_id
            . ($delete_all ? '' : ' AND clang_id = '. $this->clang_id);
        $result_lang = \rex_sql::factory();
        $result_lang->setQuery($query_lang);

        // If no more lang objects are available, delete
        $query_main = 'SELECT * FROM '. \rex::getTablePrefix() .'d2u_machinery_equipments_lang '
            .'WHERE equipment_id = '. $this->equipment_id;
        $result_main = \rex_sql::factory();
        $result_main->setQuery($query_main);
        if (0 === $result_main->getRows()) {
            $query = 'DELETE FROM '. \rex::getTablePrefix() .'d2u_machinery_equipments '
                .'WHERE equipment_id = '. $this->equipment_id;
            $result = \rex_sql::factory();
            $result->setQuery($query);
        }
    }

    /**
     * Get all equipments.
     * @param int $clang_id redaxo clang id
     * @return Equipment[] array with Equipment objects
     */
    public static function getAll($clang_id)
    {
        $query = 'SELECT lang.equipment_id FROM '. \rex::getTablePrefix() .'d2u_machinery_equipments_lang AS lang '
            .'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_equipments AS equipments '
                .'ON lang.equipment_id = equipments.equipment_id '
            .'WHERE clang_id = '. $clang_id .' ';
        $query .= 'ORDER BY name';
        $result = \rex_sql::factory();
        $result->setQuery($query);

        $equipments = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $equipments[] = new self((int) $result->getValue('equipment_id'), $clang_id);
            $result->next();
        }
        return $equipments;
    }

    /**
     * Gets the machines referring to this object.
     * @return Machine[] machines referring to this object
     */
    public function getReferringMachines()
    {
        $query = 'SELECT machine_id FROM '. \rex::getTablePrefix() .'d2u_machinery_machines '
            ."WHERE equipment_ids LIKE '%|". $this->equipment_id ."|%'";
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
     * @return Equipment[] array with Equipment objects
     */
    public static function getTranslationHelperObjects($clang_id, $type)
    {
        $query = 'SELECT equipment_id FROM '. \rex::getTablePrefix() .'d2u_machinery_equipments_lang '
                .'WHERE clang_id = '. $clang_id ." AND translation_needs_update = 'yes' "
                .'ORDER BY name';
        if ('missing' === $type) {
            $query = 'SELECT main.equipment_id FROM '. \rex::getTablePrefix() .'d2u_machinery_equipments AS main '
                    .'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_equipments_lang AS target_lang '
                        .'ON main.equipment_id = target_lang.equipment_id AND target_lang.clang_id = '. $clang_id .' '
                    .'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_equipments_lang AS default_lang '
                        .'ON main.equipment_id = default_lang.equipment_id AND default_lang.clang_id = '. \rex_config::get('d2u_helper', 'default_lang') .' '
                    .'WHERE target_lang.equipment_id IS NULL '
                    .'ORDER BY default_lang.name';
            $clang_id = (int) \rex_config::get('d2u_helper', 'default_lang');
        }
        $result = \rex_sql::factory();
        $result->setQuery($query);

        $objects = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $objects[] = new self((int) $result->getValue('equipment_id'), $clang_id);
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
        $pre_save_object = new self($this->equipment_id, $this->clang_id);

        // saving the rest
        if (0 === $this->equipment_id || $pre_save_object !== $this) {
            $query = \rex::getTablePrefix() .'d2u_machinery_equipments SET '
                    ."article_number = '". $this->article_number ."', "
                    .'group_id = '. ($this->group instanceof EquipmentGroup ? $this->group->group_id : 0) .', '
                    ."online_status = '". $this->online_status ."' ";

            if (0 === $this->equipment_id) {
                $query = 'INSERT INTO '. $query;
            } else {
                $query = 'UPDATE '. $query .' WHERE equipment_id = '. $this->equipment_id;
            }

            $result = \rex_sql::factory();
            $result->setQuery($query);
            if (0 === $this->equipment_id) {
                $this->equipment_id = (int) $result->getLastId();
                $error = $result->hasError();
            }
        }

        if (false === $error) {
            // Save the language specific part
            $pre_save_object = new self($this->equipment_id, $this->clang_id);
            if ($pre_save_object !== $this) {
                $query = 'REPLACE INTO '. \rex::getTablePrefix() .'d2u_machinery_equipments_lang SET '
                        ."equipment_id = '". $this->equipment_id ."', "
                        ."clang_id = '". $this->clang_id ."', "
                        ."name = '". addslashes($this->name) ."', "
                        ."translation_needs_update = '". $this->translation_needs_update ."' ";

                $result = \rex_sql::factory();
                $result->setQuery($query);
                $error = $result->hasError();
            }
        }

        return !$error;
    }
}
