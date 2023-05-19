<?php
/**
 * Redaxo D2U Machines Addon.
 * @author Tobias Krais
 * @author <a href="http://www.design-to-use.de">www.design-to-use.de</a>
 */

/**
 * Agitator.
 */
class Agitator implements \D2U_Helper\ITranslationHelper
{
    /** @var int Database ID */
    public int $agitator_id = 0;

    /** @var int Redaxo clang id */
    public int $clang_id = 0;

    /** @var string Name */
    public string $name = '';

    /** @var string Name */
    public string $description = '';

    /** @var string Preview picture file name */
    public string $pic = '';

    /** @var string "yes" if translation needs update */
    public string $translation_needs_update = 'delete';

    /**
     * Constructor. Reads an agitator stored in database.
     * @param int $agitator_id object ID
     * @param int $clang_id redaxo clang id
     */
    public function __construct($agitator_id, $clang_id)
    {
        $this->clang_id = $clang_id;
        $query = 'SELECT * FROM '. \rex::getTablePrefix() .'d2u_machinery_agitators AS agitators '
                .'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_agitators_lang AS lang '
                    .'ON agitators.agitator_id = lang.agitator_id '
                    .'AND clang_id = '. $this->clang_id .' '
                .'WHERE agitators.agitator_id = '. $agitator_id;
        $result = \rex_sql::factory();
        $result->setQuery($query);
        $num_rows = $result->getRows();

        if ((int) $num_rows > 0) {
            $this->agitator_id = (int) $result->getValue('agitator_id');
            $this->name = stripslashes((string) $result->getValue('name'));
            $this->pic = (string) $result->getValue('pic');
            $this->description = stripslashes(htmlspecialchars_decode((string) $result->getValue('description')));
            if ('' !== $result->getValue('translation_needs_update') && null !== $result->getValue('translation_needs_update')) {
                $this->translation_needs_update = (string) $result->getValue('translation_needs_update');
            }
        }
    }

    /**
     * Deletes the object.
     * @param bool $delete_all If true, all translations and main object are deleted. If
     * false, only this translation will be deleted.
     */
    public function delete($delete_all = true): void
    {
        $query_lang = 'DELETE FROM '. \rex::getTablePrefix() .'d2u_machinery_agitators_lang '
            .'WHERE agitator_id = '. $this->agitator_id
            . ($delete_all ? '' : ' AND clang_id = '. $this->clang_id);
        $result_lang = \rex_sql::factory();
        $result_lang->setQuery($query_lang);

        // If no more lang objects are available, delete
        $query_main = 'SELECT * FROM '. \rex::getTablePrefix() .'d2u_machinery_agitators_lang '
            .'WHERE agitator_id = '. $this->agitator_id;
        $result_main = \rex_sql::factory();
        $result_main->setQuery($query_main);
        if (0 === (int) $result_main->getRows()) {
            $query = 'DELETE FROM '. \rex::getTablePrefix() .'d2u_machinery_agitators '
                .'WHERE agitator_id = '. $this->agitator_id;
            $result = \rex_sql::factory();
            $result->setQuery($query);
        }
    }

    /**
     * Get all Agitators.
     * @param int $clang_id redaxo clang id
     * @return Agitator[] array with Agitator objects
     */
    public static function getAll($clang_id)
    {
        $query = 'SELECT agitator_id FROM '. \rex::getTablePrefix() .'d2u_machinery_agitators_lang '
            .'WHERE clang_id = '. $clang_id .' ';
        $query .= 'ORDER BY name';

        $result = \rex_sql::factory();
        $result->setQuery($query);

        $agitators = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $agitators[] = new self((int) $result->getValue('agitator_id'), $clang_id);
            $result->next();
        }
        return $agitators;
    }

    /**
     * Gets the AgitatorTypes referring to this object.
     * @return AgitatorType[] agitatorType referring to this object
     */
    public function getReferringAgitatorTypes()
    {
        $query = 'SELECT agitator_type_id FROM '. \rex::getTablePrefix() .'d2u_machinery_agitator_types '
            ."WHERE agitator_ids LIKE '%|". $this->agitator_id ."|%'";
        $result = \rex_sql::factory();
        $result->setQuery($query);

        $agitator_types = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $agitator_types[] = new AgitatorType((int) $result->getValue('agitator_type_id'), $this->clang_id);
            $result->next();
        }
        return $agitator_types;
    }

    /**
     * Get objects concerning translation updates.
     * @param int $clang_id Redaxo language ID
     * @param string $type 'update' or 'missing'
     * @return Agitator[] array with Agitator objects
     */
    public static function getTranslationHelperObjects($clang_id, $type)
    {
        $query = 'SELECT agitator_id FROM '. \rex::getTablePrefix() .'d2u_machinery_agitators_lang '
                .'WHERE clang_id = '. $clang_id ." AND translation_needs_update = 'yes' "
                .'ORDER BY name';
        if ('missing' === $type) {
            $query = 'SELECT main.agitator_id FROM '. \rex::getTablePrefix() .'d2u_machinery_agitators AS main '
                    .'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_agitators_lang AS target_lang '
                        .'ON main.agitator_id = target_lang.agitator_id AND target_lang.clang_id = '. $clang_id .' '
                    .'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_agitators_lang AS default_lang '
                        .'ON main.agitator_id = default_lang.agitator_id AND default_lang.clang_id = '. \rex_config::get('d2u_helper', 'default_lang') .' '
                    .'WHERE target_lang.agitator_id IS NULL '
                    .'ORDER BY default_lang.name';
            $clang_id = (int) \rex_config::get('d2u_helper', 'default_lang');
        }
        $result = \rex_sql::factory();
        $result->setQuery($query);

        $objects = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $objects[] = new self((int) $result->getValue('agitator_id'), $clang_id);
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
        $pre_save_agitator = new self($this->agitator_id, $this->clang_id);

        // saving the rest
        if (0 === $this->agitator_id || $pre_save_agitator !== $this) {
            $query = \rex::getTablePrefix() .'d2u_machinery_agitators SET '
                    ."pic = '". $this->pic ."' ";

            if (0 === $this->agitator_id) {
                $query = 'INSERT INTO '. $query;
            } else {
                $query = 'UPDATE '. $query .' WHERE agitator_id = '. $this->agitator_id;
            }

            $result = \rex_sql::factory();
            $result->setQuery($query);
            if (0 === $this->agitator_id) {
                $this->agitator_id = (int) $result->getLastId();
                $error = $result->hasError();
            }
        }

        if (false === $error) {
            // Save the language specific part
            $pre_save_agitator = new self($this->agitator_id, $this->clang_id);
            if ($pre_save_agitator !== $this) {
                $query = 'REPLACE INTO '. \rex::getTablePrefix() .'d2u_machinery_agitators_lang SET '
                        ."agitator_id = '". $this->agitator_id ."', "
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
}
