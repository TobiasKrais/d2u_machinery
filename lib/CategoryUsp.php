<?php

namespace TobiasKrais\D2UMachinery;

use rex;
use rex_sql;

/**
 * Category USP (unique selling point).
 *
 * Owned by exactly one category (category_id). Icons are language-independent and live in
 * the main table `category_usps`; heading and text are per language in `category_usps_lang`.
 * The general per-category/per-language USP heading is stored on `categories_lang.usps_heading`
 * (see {@see Category}), not here.
 */
class CategoryUsp implements \TobiasKrais\D2UHelper\ITranslationHelper, \TobiasKrais\D2UHelper\ITranslateable
{
    /** @var int Database ID */
    public int $usp_id = 0;

    /** @var int Redaxo clang id */
    public int $clang_id = 0;

    /** @var int Category this USP belongs to */
    public int $category_id = 0;

    /** @var int Sort priority (within the category) */
    public int $priority = 0;

    /** @var string Icon for light mode (media file name, shared across languages) */
    public string $icon_light = '';

    /** @var string Icon for dark mode (media file name, shared across languages) */
    public string $icon_dark = '';

    /** @var string Heading (per language) */
    public string $heading = '';

    /** @var string Text (per language) */
    public string $text = '';

    /** @var string "yes" if translation needs update */
    public string $translation_needs_update = 'delete';

    /**
     * Constructor. Reads a USP stored in database.
     * @param int $usp_id USP ID
     * @param int $clang_id redaxo clang id
     */
    public function __construct($usp_id, $clang_id)
    {
        $this->clang_id = (int) $clang_id;
        $query = 'SELECT * FROM '. \rex::getTablePrefix() .'d2u_machinery_category_usps AS usps '
                .'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_category_usps_lang AS lang '
                    .'ON usps.usp_id = lang.usp_id AND clang_id = '. $this->clang_id .' '
                .'WHERE usps.usp_id = '. (int) $usp_id;
        $result = \rex_sql::factory();
        $result->setQuery($query);

        if ($result->getRows() > 0) {
            $this->usp_id = (int) $result->getValue('usps.usp_id');
            $this->category_id = (int) $result->getValue('category_id');
            $this->priority = (int) $result->getValue('priority');
            $this->icon_light = (string) $result->getValue('icon_light');
            $this->icon_dark = (string) $result->getValue('icon_dark');
            $this->heading = stripslashes(htmlspecialchars_decode((string) $result->getValue('heading')));
            $this->text = stripslashes(htmlspecialchars_decode((string) $result->getValue('text')));
            if ('' !== (string) $result->getValue('translation_needs_update') && null !== $result->getValue('translation_needs_update')) {
                $this->translation_needs_update = (string) $result->getValue('translation_needs_update');
            }
        }
    }

    /**
     * Get all USPs of a category, ordered by priority.
     * @param int $category_id category id
     * @param int $clang_id redaxo clang id
     * @return CategoryUsp[]
     */
    public static function getByCategory($category_id, $clang_id): array
    {
        $query = 'SELECT usp_id FROM '. \rex::getTablePrefix() .'d2u_machinery_category_usps '
            .'WHERE category_id = '. (int) $category_id .' ORDER BY priority';
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
     * Get all USPs.
     * @param int $clang_id redaxo clang id
     * @return CategoryUsp[]
     */
    public static function getAll($clang_id): array
    {
        $query = 'SELECT lang.usp_id FROM '. \rex::getTablePrefix() .'d2u_machinery_category_usps_lang AS lang '
            .'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_category_usps AS usps ON lang.usp_id = usps.usp_id '
            .'WHERE clang_id = '. (int) $clang_id .' ORDER BY category_id, priority';
        $result = \rex_sql::factory();
        $result->setQuery($query);

        $usps = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $usps[(int) $result->getValue('usp_id')] = new self((int) $result->getValue('usp_id'), $clang_id);
            $result->next();
        }
        return $usps;
    }

    /**
     * Get objects concerning translation updates.
     * @param int $clang_id Redaxo language ID
     * @param string $type 'update' or 'missing'
     * @return CategoryUsp[]
     */
    public static function getTranslationHelperObjects($clang_id, $type)
    {
        $query = 'SELECT usp_id FROM '. \rex::getTablePrefix() .'d2u_machinery_category_usps_lang '
                .'WHERE clang_id = '. (int) $clang_id ." AND translation_needs_update = 'yes' "
                .'ORDER BY heading';
        if ('missing' === $type) {
            $query = 'SELECT main.usp_id FROM '. \rex::getTablePrefix() .'d2u_machinery_category_usps AS main '
                    .'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_category_usps_lang AS target_lang '
                        .'ON main.usp_id = target_lang.usp_id AND target_lang.clang_id = '. (int) $clang_id .' '
                    .'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_category_usps_lang AS default_lang '
                        .'ON main.usp_id = default_lang.usp_id AND default_lang.clang_id = :default_lang '
                    .'WHERE target_lang.usp_id IS NULL '
                    .'ORDER BY default_lang.heading';
            $clang_id = (int) \rex_config::get('d2u_helper', 'default_lang');
        }
        $result = \rex_sql::factory();
        $result->setQuery($query, 'missing' === $type ? [':default_lang' => \rex_config::get('d2u_helper', 'default_lang')] : []);

        $objects = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $id = (int) $result->getValue('usp_id');
            $result->next();
            if ($id <= 0) {
                continue;
            }
            $object = new self($id, $clang_id);
            if ($object->usp_id > 0) {
                $objects[] = $object;
            }
        }

        return $objects;
    }

    /**
     * Deletes the object.
     * @param bool $delete_all If true, delete all translations and the main object.
     */
    public function delete($delete_all = true): void
    {
        $query_lang = 'DELETE FROM '. \rex::getTablePrefix() .'d2u_machinery_category_usps_lang '
            .'WHERE usp_id = '. $this->usp_id
            . ($delete_all ? '' : ' AND clang_id = '. $this->clang_id);
        $result = \rex_sql::factory();
        $result->setQuery($query_lang);

        $query_check = 'SELECT * FROM '. \rex::getTablePrefix() .'d2u_machinery_category_usps_lang WHERE usp_id = '. $this->usp_id;
        $result = \rex_sql::factory();
        $result->setQuery($query_check);
        if (0 === $result->getRows()) {
            $query_main = 'DELETE FROM '. \rex::getTablePrefix() .'d2u_machinery_category_usps WHERE usp_id = '. $this->usp_id;
            $result = \rex_sql::factory();
            $result->setQuery($query_main);
            $this->setPriority(true);
        }
    }

    /**
     * Translate heading + text from another language via the AI translation helper.
     */
    public function translateFrom(int $sourceClangId): bool
    {
        if ($this->usp_id <= 0 || $sourceClangId === $this->clang_id) {
            return false;
        }
        $source = new self($this->usp_id, $sourceClangId);
        if ($source->usp_id <= 0 || ('' === $source->heading && '' === $source->text)) {
            return false;
        }
        try {
            $translated = \TobiasKrais\D2UHelper\AiTranslationHelper::translateFields([
                'heading' => ['value' => $source->heading, 'html' => false],
                'text' => ['value' => $source->text, 'html' => false],
            ], $sourceClangId, $this->clang_id);
        } catch (\Throwable $e) {
            \rex_logger::logException($e);
            return false;
        }
        $this->heading = $translated['heading'];
        $this->text = $translated['text'];
        $this->translation_needs_update = 'no';
        return $this->save();
    }

    /**
     * Updates or inserts the object into database.
     * @return bool true if successful
     */
    public function save(): bool
    {
        $error = false;

        $pre_save_object = new self($this->usp_id, $this->clang_id);

        if ($this->priority !== $pre_save_object->priority || 0 === $this->usp_id) {
            $this->setPriority();
        }

        if (0 === $this->usp_id || $pre_save_object !== $this) {
            $query = \rex::getTablePrefix() .'d2u_machinery_category_usps SET '
                    .'category_id = '. (int) $this->category_id .', '
                    .'priority = '. (int) $this->priority .', '
                    .'icon_light = :icon_light, '
                    .'icon_dark = :icon_dark ';
            $params = [':icon_light' => $this->icon_light, ':icon_dark' => $this->icon_dark];
            if (0 === $this->usp_id) {
                $query = 'INSERT INTO '. $query;
            } else {
                $query = 'UPDATE '. $query .' WHERE usp_id = '. (int) $this->usp_id;
            }
            $result = \rex_sql::factory();
            $result->setQuery($query, $params);
            if (0 === $this->usp_id) {
                $this->usp_id = (int) $result->getLastId();
            }
            $error = $result->hasError();
        }

        if (false === $error) {
            $pre_save_lang = new self($this->usp_id, $this->clang_id);
            if ($pre_save_lang !== $this) {
                $query = 'REPLACE INTO '. \rex::getTablePrefix() .'d2u_machinery_category_usps_lang SET '
                        .'usp_id = :usp_id, '
                        .'clang_id = :clang_id, '
                        .'heading = :heading, '
                        .'text = :text, '
                        .'translation_needs_update = :tnu, '
                        .'updatedate = CURRENT_TIMESTAMP, '
                        .'updateuser = :updateuser ';
                $result = \rex_sql::factory();
                $result->setQuery($query, [
                    ':usp_id' => $this->usp_id,
                    ':clang_id' => $this->clang_id,
                    ':heading' => htmlspecialchars($this->heading),
                    ':text' => htmlspecialchars($this->text),
                    ':tnu' => $this->translation_needs_update,
                    ':updateuser' => \rex::getUser() instanceof \rex_user ? \rex::getUser()->getLogin() : '',
                ]);
                $error = $result->hasError();
            }
        }

        return !$error;
    }

    /**
     * Reassigns priorities (global across all categories, so each priority is unique).
     * @param bool $delete Reorder priority after deletion
     */
    private function setPriority(bool $delete = false): void
    {
        $query = 'SELECT usp_id FROM '. \rex::getTablePrefix() .'d2u_machinery_category_usps '
            .'WHERE usp_id <> '. (int) $this->usp_id .' '
            .'ORDER BY priority, usp_id';
        $result = \rex_sql::factory();
        $result->setQuery($query);

        if ($this->priority <= 0) {
            $this->priority = 1;
        }
        if ($this->priority > $result->getRows() || $delete) {
            $this->priority = $result->getRows() + 1;
        }

        $usps = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $usps[] = (int) $result->getValue('usp_id');
            $result->next();
        }
        array_splice($usps, $this->priority - 1, 0, [$this->usp_id]);

        foreach ($usps as $prio => $usp_id) {
            $update = 'UPDATE '. \rex::getTablePrefix() .'d2u_machinery_category_usps '
                .'SET priority = '. ((int) $prio + 1) .' WHERE usp_id = '. (int) $usp_id;
            $result = \rex_sql::factory();
            $result->setQuery($update);
        }
    }
}
