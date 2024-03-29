<?php
/**
 * Redaxo D2U Machines Addon - Export Plugin.
 * @author Tobias Krais
 * @author <a href="http://www.design-to-use.de">www.design-to-use.de</a>
 */

/**
 * Exported used machine object.
 */
class ExportedUsedMachine
{
    /** @var int Database ID */
    public int $used_machine_id = 0;

    /** @var int Export provider id */
    private int $provider_id;

    /** @var string Export status. Either "add", "update" or "delete" */
    public string $export_action = '';

    /**
     * @var string ID provider returned after import. Not all providers return
     * this value. But some so and it is needed for deleting the machines later
     * on provider website.
     */
    public string $provider_import_id = '';

    /** @var string export timestamp */
    public string $export_timestamp = '';

    /**
     * Constructor. Fetches the object from database.
     * @param int $used_machine_id Used machine ID
     * @param int $provider_id provider ID
     */
    public function __construct($used_machine_id, $provider_id)
    {
        $query = 'SELECT * FROM '. \rex::getTablePrefix() .'d2u_machinery_export_machines '
                .'WHERE used_machine_id = '. $used_machine_id .' '
                    .'AND provider_id = '. $provider_id;
        $result = \rex_sql::factory();
        $result->setQuery($query);
        $num_rows = $result->getRows();

        $this->used_machine_id = $used_machine_id;
        $this->provider_id = $provider_id;
        if ($num_rows > 0) {
            $this->export_action = (string) $result->getValue('export_action');
            $this->provider_import_id = (string) $result->getValue('provider_import_id');
            if ('' !== $result->getValue('export_timestamp')) {
                $this->export_timestamp = (string) $result->getValue('export_timestamp');
            }
        }
    }

    /**
     * Add used machine to export for this provider.
     */
    public function addToExport(): void
    {
        if ('' === $this->export_action) {
            $this->export_action = 'add';
        } elseif ('add' === $this->export_action || 'delete' === $this->export_action) {
            $this->export_action = 'update';
        }

        $this->save();
    }

    /**
     * Add all machines to export for given provider.
     * @param int $provider_id Provider id
     */
    public static function addAllToExport($provider_id): void
    {
        $provider = new Provider($provider_id);
        $used_machines = UsedMachine::getAll($provider->clang_id, true);
        foreach ($used_machines as $used_machine) {
            $exported_used_machine = new self($used_machine->used_machine_id, $provider_id);
            if ('' === $exported_used_machine->export_action && '' === $exported_used_machine->export_timestamp) {
                $exported_used_machine->export_action = 'add';
            } elseif (('' === $exported_used_machine->export_action && '' !== $exported_used_machine->export_timestamp) || 'delete' === $exported_used_machine->export_action) {
                $exported_used_machine->export_action = 'update';
            }
            $exported_used_machine->save();
        }
    }

    /**
     * Deletes the object.
     */
    public function delete(): void
    {
        $query = 'DELETE FROM '. \rex::getTablePrefix() .'d2u_machinery_export_machines '
            .'WHERE used_machine_id = '. $this->used_machine_id .' AND provider_id = '. $this->provider_id;
        $result_lang = \rex_sql::factory();
        $result_lang->setQuery($query);
    }

    /**
     * Get all exported used machines.
     * @param Provider|bool $provider Optional provider object
     * @return ExportedUsedMachine[] array with ExportedUsedMachine objects
     */
    public static function getAll($provider = false)
    {
        $query = 'SELECT used_machine_id, provider_id FROM '. \rex::getTablePrefix() .'d2u_machinery_export_machines AS export';
        if ($provider instanceof Provider && $provider->provider_id > 0) {
            $query .= ' WHERE provider_id = '. $provider->provider_id;
        }
        $result = \rex_sql::factory();
        $result->setQuery($query);

        $exported_used_machines = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $exported_used_machines[] = new self((int) $result->getValue('used_machine_id'), (int) $result->getValue('provider_id'));
            $result->next();
        }
        return $exported_used_machines;
    }

    /**
     * Proves, if UsedMachine is set for export for this provider.
     * @return bool true if set, false if not
     */
    public function isSetForExport()
    {
        if ('add' === $this->export_action || 'update' === $this->export_action || ('' === $this->export_action && '' !== $this->export_timestamp)) {
            return true;
        }

        return false;

    }

    /**
     * Set 'delete' flag for used machines that have been deleted.
     */
    public static function removeAllDeletedFromExport(): void
    {
        $query = 'SELECT exported_machines.used_machine_id FROM '. \rex::getTablePrefix() .'d2u_machinery_export_machines AS exported_machines '
            .'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_used_machines AS used_machines '
                .'ON exported_machines.used_machine_id = used_machines.used_machine_id '
            .'WHERE used_machines.used_machine_id IS NULL '
            .'GROUP BY exported_machines.used_machine_id';
        $result = \rex_sql::factory();
        $result->setQuery($query);

        for ($i = 0; $i < $result->getRows(); ++$i) {
            $query_update = 'UPDATE '. \rex::getTablePrefix() .'d2u_machinery_export_machines '
                ."SET export_action = 'delete' "
                .'WHERE used_machine_id = '. $result->getValue('exported_machines.used_machine_id');
            $result_update = \rex_sql::factory();
            $result_update->setQuery($query_update);

            $result->next();
        }
    }

    /**
     * Set 'delete' flag for all machines of a given provider.
     * @param int $provider_id Provider id
     */
    public static function removeAllFromExport($provider_id): void
    {
        $query_lang = 'UPDATE '. \rex::getTablePrefix() .'d2u_machinery_export_machines '
            ."SET export_action = 'delete' "
            .'WHERE provider_id = '. $provider_id;
        $result_lang = \rex_sql::factory();
        $result_lang->setQuery($query_lang);
    }

    /**
     * Remove a machine from export of all providers.
     * @param int $used_machine_id Used machine id
     */
    public static function removeMachineFromAllExports($used_machine_id): void
    {
        $query_lang = 'UPDATE '. \rex::getTablePrefix() .'d2u_machinery_export_machines '
            ."SET export_action = 'delete' "
            .'WHERE used_machine_id = '. $used_machine_id;
        $result_lang = \rex_sql::factory();
        $result_lang->setQuery($query_lang);
    }

    /**
     * Remove from export list for this provider.
     */
    public function removeFromExport(): void
    {
        $this->export_action = 'delete';
        $this->save();
    }

    /**
     * Updates or inserts the object into database.
     * @return bool true if successful
     */
    public function save()
    {
        $query = 'REPLACE INTO '. \rex::getTablePrefix() .'d2u_machinery_export_machines SET '
                .'used_machine_id = '. $this->used_machine_id .', '
                .'provider_id = '. $this->provider_id .', '
                ."export_action = '". $this->export_action ."', "
                ."provider_import_id = '". $this->provider_import_id ."', "
                ."export_timestamp = '". $this->export_timestamp ."'";

        $result = \rex_sql::factory();
        $result->setQuery($query);

        return !$result->hasError();
    }
}
