<?php
/**
 * Redaxo D2U Machines Addon.
 * @author Tobias Krais
 * @author <a href="http://www.design-to-use.de">www.design-to-use.de</a>
 */

/**
 * Certificates.
 */
class Certificate implements \D2U_Helper\ITranslationHelper
{
    /** @var int Database ID */
    public int $certificate_id = 0;

    /** @var int Redaxo clang id */
    public int $clang_id = 0;

    /** @var string Name */
    public string $name = '';

    /** @var string Description */
    public string $description = '';

    /** @var string Preview picture file name */
    public string $pic = '';

    /** @var string "yes" if translation needs update */
    public string $translation_needs_update = 'delete';

    /**
     * Constructor. Reads an Certificate stored in database.
     * @param int $certificate_id object ID
     * @param int $clang_id redaxo clang id
     */
    public function __construct($certificate_id, $clang_id)
    {
        $this->clang_id = $clang_id;
        $query = 'SELECT * FROM '. \rex::getTablePrefix() .'d2u_machinery_certificates AS certificates '
                .'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_certificates_lang AS lang '
                    .'ON certificates.certificate_id = lang.certificate_id '
                    .'AND clang_id = '. $this->clang_id .' '
                .'WHERE certificates.certificate_id = '. $certificate_id;
        $result = \rex_sql::factory();
        $result->setQuery($query);
        $num_rows = $result->getRows();

        if ($num_rows > 0) {
            $this->certificate_id = (int) $result->getValue('certificate_id');
            $this->name = stripslashes((string) $result->getValue('name'));
            $this->description = (string) $result->getValue('description');
            $this->pic = (string) $result->getValue('pic');
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
        $query_lang = 'DELETE FROM '. \rex::getTablePrefix() .'d2u_machinery_certificates_lang '
            .'WHERE certificate_id = '. $this->certificate_id
            . ($delete_all ? '' : ' AND clang_id = '. $this->clang_id);
        $result_lang = \rex_sql::factory();
        $result_lang->setQuery($query_lang);

        // If no more lang objects are available, delete
        $query_main = 'SELECT * FROM '. \rex::getTablePrefix() .'d2u_machinery_certificates_lang '
            .'WHERE certificate_id = '. $this->certificate_id;
        $result_main = \rex_sql::factory();
        $result_main->setQuery($query_main);
        if (0 === (int) $result_main->getRows()) {
            $query = 'DELETE FROM '. \rex::getTablePrefix() .'d2u_machinery_certificates '
                .'WHERE certificate_id = '. $this->certificate_id;
            $result = \rex_sql::factory();
            $result->setQuery($query);
        }
    }

    /**
     * Get all certificates.
     * @param int $clang_id redaxo clang id
     * @return Certificate[] array with Certificate objects
     */
    public static function getAll($clang_id)
    {
        $query = 'SELECT certificate_id FROM '. \rex::getTablePrefix() .'d2u_machinery_certificates_lang '
            .'WHERE clang_id = '. $clang_id .' ';
        $query .= 'ORDER BY name';

        $result = \rex_sql::factory();
        $result->setQuery($query);

        $certificates = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $certificates[] = new self((int) $result->getValue('certificate_id'), $clang_id);
            $result->next();
        }
        return $certificates;
    }

    /**
     * Gets the machines referring to this object.
     * @return Machine[] machines referring to this object
     */
    public function getReferringMachines()
    {
        $query = 'SELECT machine_id FROM '. \rex::getTablePrefix() .'d2u_machinery_machines '
            ."WHERE certificate_ids LIKE '%|". $this->certificate_id ."|%'";
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
     * @return Certificate[] array with Certificate objects
     */
    public static function getTranslationHelperObjects($clang_id, $type)
    {
        $query = 'SELECT certificate_id FROM '. \rex::getTablePrefix() .'d2u_machinery_certificates_lang '
                .'WHERE clang_id = '. $clang_id ." AND translation_needs_update = 'yes' "
                .'ORDER BY name';
        if ('missing' === $type) {
            $query = 'SELECT main.certificate_id FROM '. \rex::getTablePrefix() .'d2u_machinery_certificates AS main '
                    .'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_certificates_lang AS target_lang '
                        .'ON main.certificate_id = target_lang.certificate_id AND target_lang.clang_id = '. $clang_id .' '
                    .'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_certificates_lang AS default_lang '
                        .'ON main.certificate_id = default_lang.certificate_id AND default_lang.clang_id = '. \rex_config::get('d2u_helper', 'default_lang') .' '
                    .'WHERE target_lang.certificate_id IS NULL '
                    .'ORDER BY default_lang.name';
            $clang_id = (int) \rex_config::get('d2u_helper', 'default_lang');
        }
        $result = \rex_sql::factory();
        $result->setQuery($query);

        $objects = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $objects[] = new self((int) $result->getValue('certificate_id'), $clang_id);
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
        $pre_save_certificate = new self($this->certificate_id, $this->clang_id);

        // saving the rest
        if (0 === $this->certificate_id || $pre_save_certificate !== $this) {
            $query = \rex::getTablePrefix() .'d2u_machinery_certificates SET '
                    ."pic = '". $this->pic ."' ";

            if (0 === $this->certificate_id) {
                $query = 'INSERT INTO '. $query;
            } else {
                $query = 'UPDATE '. $query .' WHERE certificate_id = '. $this->certificate_id;
            }

            $result = \rex_sql::factory();
            $result->setQuery($query);
            if (0 === $this->certificate_id) {
                $this->certificate_id = (int) $result->getLastId();
                $error = $result->hasError();
            }
        }

        if (false === $error) {
            // Save the language specific part
            $pre_save_certificate = new self($this->certificate_id, $this->clang_id);
            if ($pre_save_certificate !== $this) {
                $query = 'REPLACE INTO '. \rex::getTablePrefix() .'d2u_machinery_certificates_lang SET '
                        ."certificate_id = '". $this->certificate_id ."', "
                        ."clang_id = '". $this->clang_id ."', "
                        ."name = '". addslashes($this->name) ."', "
                        ."description = '". $this->description ."', "
                        ."translation_needs_update = '". $this->translation_needs_update ."' ";

                $result = \rex_sql::factory();
                $result->setQuery($query);
                $error = $result->hasError();
            }
        }

        return !$error;
    }
}
