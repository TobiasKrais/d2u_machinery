<?php
/**
 * Redaxo D2U Machines Addon.
 * @author Tobias Krais
 * @author <a href="http://www.design-to-use.de">www.design-to-use.de</a>
 */

/**
 * Service option.
 */
class ServiceOption implements \D2U_Helper\ITranslationHelper
{
    /** @var int Database ID */
    public int $service_option_id = 0;

    /** @var int Redaxo clang id */
    public int $clang_id = 0;

    /** @var string Name */
    public string $name = '';

    /** @var string Description */
    public string $description = '';

    /** @var string Picture file name */
    public string $picture = '';

    /** @var string Status. Either "online" or "offline". */
    public string $online_status = 'offline';

    /** @var string "yes" if translation needs update */
    public string $translation_needs_update = 'delete';

    /**
     * Constructor. Reads an Service Option stored in database.
     * @param int $service_option_id object ID
     * @param int $clang_id redaxo clang id
     */
    public function __construct($service_option_id, $clang_id)
    {
        $this->clang_id = $clang_id;
        $query = 'SELECT * FROM '. \rex::getTablePrefix() .'d2u_machinery_service_options AS service_options '
                .'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_service_options_lang AS lang '
                    .'ON service_options.service_option_id = lang.service_option_id '
                    .'AND clang_id = '. $this->clang_id .' '
                .'WHERE service_options.service_option_id = '. $service_option_id;
        $result = \rex_sql::factory();
        $result->setQuery($query);
        $num_rows = $result->getRows();

        if ($num_rows > 0) {
            $this->service_option_id = (int) $result->getValue('service_option_id');
            $this->name = stripslashes((string) $result->getValue('name'));
            $this->description = stripslashes(htmlspecialchars_decode((string) $result->getValue('description')));
            $this->picture = (string) $result->getValue('picture');
            $this->online_status = (string) $result->getValue('online_status');
            if ('' !== $result->getValue('translation_needs_update') && null !== $result->getValue('translation_needs_update')) {
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
            if ($this->service_option_id > 0) {
                $query = 'UPDATE '. \rex::getTablePrefix() .'d2u_machinery_service_options '
                    ."SET online_status = 'offline' "
                    .'WHERE service_option_id = '. $this->service_option_id;
                $result = \rex_sql::factory();
                $result->setQuery($query);
            }
            $this->online_status = 'offline';
        } else {
            if ($this->service_option_id > 0) {
                $query = 'UPDATE '. \rex::getTablePrefix() .'d2u_machinery_service_options '
                    ."SET online_status = 'online' "
                    .'WHERE service_option_id = '. $this->service_option_id;
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
        $query_lang = 'DELETE FROM '. \rex::getTablePrefix() .'d2u_machinery_service_options_lang '
            .'WHERE service_option_id = '. $this->service_option_id
            . ($delete_all ? '' : ' AND clang_id = '. $this->clang_id);
        $result_lang = \rex_sql::factory();
        $result_lang->setQuery($query_lang);

        // If no more lang objects are available, delete
        $query_main = 'SELECT * FROM '. \rex::getTablePrefix() .'d2u_machinery_service_options_lang '
            .'WHERE service_option_id = '. $this->service_option_id;
        $result_main = \rex_sql::factory();
        $result_main->setQuery($query_main);
        if (0 === (int) $result_main->getRows()) {
            $query = 'DELETE FROM '. \rex::getTablePrefix() .'d2u_machinery_service_options '
                .'WHERE service_option_id = '. $this->service_option_id;
            $result = \rex_sql::factory();
            $result->setQuery($query);
        }
    }

    /**
     * Get all Service Options.
     * @param int $clang_id redaxo clang id
     * @param bool $online_only true if only online objects should be returned
     * @return ServiceOption[] array with ServiceOption objects
     */
    public static function getAll($clang_id, $online_only = false)
    {
        $query = 'SELECT service_option_id FROM '. \rex::getTablePrefix() .'d2u_machinery_service_options_lang '
            .'WHERE clang_id = '. $clang_id .' ';
        $query .= 'ORDER BY name';

        $result = \rex_sql::factory();
        $result->setQuery($query);

        $service_options = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $service_option = new self((int) $result->getValue('service_option_id'), $clang_id);
            if ($online_only && 'online' === $service_option->online_status) {
                $service_options[] = $service_option;
            } elseif (false === $online_only) {
                $service_options[] = $service_option;
            }
            $result->next();
        }
        return $service_options;
    }

    /**
     * Gets the machines referring to this object.
     * @param bool $online_only true if only online machines should be returned
     * @return Machine[] machines referring to this object
     */
    public function getReferringMachines($online_only = false)
    {
        $query = 'SELECT machine_id FROM '. \rex::getTablePrefix() .'d2u_machinery_machines '
            ."WHERE service_option_ids LIKE '%|". $this->service_option_id ."|%'";
        $result = \rex_sql::factory();
        $result->setQuery($query);

        $machines = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $machine = new Machine((int) $result->getValue('machine_id'), $this->clang_id);
            if (false === $online_only || ('online' === $machine->online_status)) {
                $machines[] = $machine;
            }
            $result->next();
        }
        return $machines;
    }

    /**
     * Get objects concerning translation updates.
     * @param int $clang_id Redaxo language ID
     * @param string $type 'update' or 'missing'
     * @return ServiceOption[] array with ServiceOption objects
     */
    public static function getTranslationHelperObjects($clang_id, $type)
    {
        $query = 'SELECT service_option_id FROM '. \rex::getTablePrefix() .'d2u_machinery_service_options_lang '
                .'WHERE clang_id = '. $clang_id ." AND translation_needs_update = 'yes' "
                .'ORDER BY name';
        if ('missing' === $type) {
            $query = 'SELECT main.service_option_id FROM '. \rex::getTablePrefix() .'d2u_machinery_service_options AS main '
                    .'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_service_options_lang AS target_lang '
                        .'ON main.service_option_id = target_lang.service_option_id AND target_lang.clang_id = '. $clang_id .' '
                    .'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_service_options_lang AS default_lang '
                        .'ON main.service_option_id = default_lang.service_option_id AND default_lang.clang_id = '. \rex_config::get('d2u_helper', 'default_lang') .' '
                    .'WHERE target_lang.service_option_id IS NULL '
                    .'ORDER BY default_lang.name';
            $clang_id = (int) \rex_config::get('d2u_helper', 'default_lang');
        }
        $result = \rex_sql::factory();
        $result->setQuery($query);

        $objects = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $objects[] = new self((int) $result->getValue('service_option_id'), $clang_id);
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
        $pre_save_service_option = new self($this->service_option_id, $this->clang_id);

        // saving the rest
        if (0 === $this->service_option_id || $pre_save_service_option !== $this) {
            $query = \rex::getTablePrefix() .'d2u_machinery_service_options SET '
                    ."online_status = '". $this->online_status ."', "
                    ."picture = '". $this->picture ."' ";

            if (0 === $this->service_option_id) {
                $query = 'INSERT INTO '. $query;
            } else {
                $query = 'UPDATE '. $query .' WHERE service_option_id = '. $this->service_option_id;
            }

            $result = \rex_sql::factory();
            $result->setQuery($query);
            if (0 === $this->service_option_id) {
                $this->service_option_id = (int) $result->getLastId();
                $error = $result->hasError();
            }
        }

        if (false === $error) {
            // Save the language specific part
            $pre_save_service_option = new self($this->service_option_id, $this->clang_id);
            if ($pre_save_service_option !== $this) {
                $query = 'REPLACE INTO '. \rex::getTablePrefix() .'d2u_machinery_service_options_lang SET '
                        ."service_option_id = '". $this->service_option_id ."', "
                        ."clang_id = '". $this->clang_id ."', "
                        ."name = '". addslashes($this->name) ."', "
                        ."description = '". addslashes(htmlspecialchars($this->description)) ."', "
                        ."translation_needs_update = '". $this->translation_needs_update ."', "
                        .'updatedate = CURRENT_TIMESTAMP, '
                        ."updateuser = '". (\rex::getUser() instanceof rex_user ? \rex::getUser()->getLogin() : '') ."' ";

                $result = \rex_sql::factory();
                $result->setQuery($query);
                $error = $result->hasError();
            }
        }

        return !$error;
    }
}
