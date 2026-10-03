<?php

namespace TobiasKrais\D2UMachinery;

use rex;
use rex_sql;

/**
 * Category consultation help box.
 *
 * Owned by exactly one category (category_id). Picture and link target are language-independent
 * and live in the main table `category_consultations`; heading and text are per language in
 * `category_consultations_lang`. The link target is either a REDAXO article (article_id) or
 * another machinery category (target_category_id), chosen via link_type.
 */
class CategoryConsultation implements \TobiasKrais\D2UHelper\ITranslationHelper, \TobiasKrais\D2UHelper\ITranslateable
{
    /** @var int Database ID */
    public int $consultation_id = 0;

    /** @var int Redaxo clang id */
    public int $clang_id = 0;

    /** @var int Category this consultation box belongs to */
    public int $category_id = 0;

    /** @var int Sort priority (global, so each priority is unique) */
    public int $priority = 0;

    /** @var string Picture (media file name, shared across languages) */
    public string $pic = '';

    /** @var string Link type: "article" or "category" */
    public string $link_type = 'article';

    /** @var int Redaxo article id (link target if link_type = "article") */
    public int $article_id = 0;

    /** @var int Target category id (link target if link_type = "category") */
    public int $target_category_id = 0;

    /** @var string Heading (per language) */
    public string $heading = '';

    /** @var string Text (per language) */
    public string $text = '';

    /** @var string Link button label (per language) */
    public string $link_label = '';

    /** @var string "yes" if translation needs update */
    public string $translation_needs_update = 'delete';

    /**
     * Constructor. Reads a consultation box stored in database.
     * @param int $consultation_id consultation ID
     * @param int $clang_id redaxo clang id
     */
    public function __construct($consultation_id, $clang_id)
    {
        $this->clang_id = (int) $clang_id;
        $query = 'SELECT * FROM '. \rex::getTablePrefix() .'d2u_machinery_category_consultations AS consultations '
                .'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_category_consultations_lang AS lang '
                    .'ON consultations.consultation_id = lang.consultation_id AND clang_id = '. $this->clang_id .' '
                .'WHERE consultations.consultation_id = '. (int) $consultation_id;
        $result = \rex_sql::factory();
        $result->setQuery($query);

        if ($result->getRows() > 0) {
            $this->consultation_id = (int) $result->getValue('consultations.consultation_id');
            $this->category_id = (int) $result->getValue('category_id');
            $this->priority = (int) $result->getValue('priority');
            $this->pic = (string) $result->getValue('pic');
            $linkType = (string) $result->getValue('link_type');
            $this->link_type = 'category' === $linkType ? 'category' : 'article';
            $this->article_id = (int) $result->getValue('article_id');
            $this->target_category_id = (int) $result->getValue('target_category_id');
            $this->heading = stripslashes(htmlspecialchars_decode((string) $result->getValue('heading')));
            $this->text = stripslashes(htmlspecialchars_decode((string) $result->getValue('text')));
            $this->link_label = stripslashes(htmlspecialchars_decode((string) $result->getValue('link_label')));
            if ('' !== (string) $result->getValue('translation_needs_update') && null !== $result->getValue('translation_needs_update')) {
                $this->translation_needs_update = (string) $result->getValue('translation_needs_update');
            }
        }
    }

    /**
     * Get all consultation boxes of a category, ordered by priority.
     * @param int $category_id category id
     * @param int $clang_id redaxo clang id
     * @return CategoryConsultation[]
     */
    public static function getByCategory($category_id, $clang_id): array
    {
        $query = 'SELECT consultation_id FROM '. \rex::getTablePrefix() .'d2u_machinery_category_consultations '
            .'WHERE category_id = '. (int) $category_id .' ORDER BY priority';
        $result = \rex_sql::factory();
        $result->setQuery($query);

        $consultations = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $consultations[] = new self((int) $result->getValue('consultation_id'), $clang_id);
            $result->next();
        }
        return $consultations;
    }

    /**
     * Get all consultation boxes.
     * @param int $clang_id redaxo clang id
     * @return CategoryConsultation[]
     */
    public static function getAll($clang_id): array
    {
        $query = 'SELECT lang.consultation_id FROM '. \rex::getTablePrefix() .'d2u_machinery_category_consultations_lang AS lang '
            .'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_category_consultations AS consultations ON lang.consultation_id = consultations.consultation_id '
            .'WHERE clang_id = '. (int) $clang_id .' ORDER BY category_id, priority';
        $result = \rex_sql::factory();
        $result->setQuery($query);

        $consultations = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $consultations[(int) $result->getValue('consultation_id')] = new self((int) $result->getValue('consultation_id'), $clang_id);
            $result->next();
        }
        return $consultations;
    }

    /**
     * Get objects concerning translation updates.
     * @param int $clang_id Redaxo language ID
     * @param string $type 'update' or 'missing'
     * @return CategoryConsultation[]
     */
    public static function getTranslationHelperObjects($clang_id, $type)
    {
        $query = 'SELECT consultation_id FROM '. \rex::getTablePrefix() .'d2u_machinery_category_consultations_lang '
                .'WHERE clang_id = '. (int) $clang_id ." AND translation_needs_update = 'yes' "
                .'ORDER BY heading';
        if ('missing' === $type) {
            $query = 'SELECT main.consultation_id FROM '. \rex::getTablePrefix() .'d2u_machinery_category_consultations AS main '
                    .'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_category_consultations_lang AS target_lang '
                        .'ON main.consultation_id = target_lang.consultation_id AND target_lang.clang_id = '. (int) $clang_id .' '
                    .'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_category_consultations_lang AS default_lang '
                        .'ON main.consultation_id = default_lang.consultation_id AND default_lang.clang_id = :default_lang '
                    .'WHERE target_lang.consultation_id IS NULL '
                    .'ORDER BY default_lang.heading';
            $clang_id = (int) \rex_config::get('d2u_helper', 'default_lang');
        }
        $result = \rex_sql::factory();
        $result->setQuery($query, 'missing' === $type ? [':default_lang' => \rex_config::get('d2u_helper', 'default_lang')] : []);

        $objects = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $id = (int) $result->getValue('consultation_id');
            $result->next();
            if ($id <= 0) {
                continue;
            }
            $object = new self($id, $clang_id);
            if ($object->consultation_id > 0) {
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
        $query_lang = 'DELETE FROM '. \rex::getTablePrefix() .'d2u_machinery_category_consultations_lang '
            .'WHERE consultation_id = '. $this->consultation_id
            . ($delete_all ? '' : ' AND clang_id = '. $this->clang_id);
        $result = \rex_sql::factory();
        $result->setQuery($query_lang);

        $query_check = 'SELECT * FROM '. \rex::getTablePrefix() .'d2u_machinery_category_consultations_lang WHERE consultation_id = '. $this->consultation_id;
        $result = \rex_sql::factory();
        $result->setQuery($query_check);
        if (0 === $result->getRows()) {
            $query_main = 'DELETE FROM '. \rex::getTablePrefix() .'d2u_machinery_category_consultations WHERE consultation_id = '. $this->consultation_id;
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
        if ($this->consultation_id <= 0 || $sourceClangId === $this->clang_id) {
            return false;
        }
        $source = new self($this->consultation_id, $sourceClangId);
        if ($source->consultation_id <= 0 || ('' === $source->heading && '' === $source->text)) {
            return false;
        }
        try {
            $translated = \TobiasKrais\D2UHelper\AiTranslationHelper::translateFields([
                'heading' => ['value' => $source->heading, 'html' => false],
                'text' => ['value' => $source->text, 'html' => true],
                'link_label' => ['value' => $source->link_label, 'html' => false],
            ], $sourceClangId, $this->clang_id);
        } catch (\Throwable $e) {
            \rex_logger::logException($e);
            return false;
        }
        $this->heading = $translated['heading'];
        $this->text = $translated['text'];
        $this->link_label = $translated['link_label'];
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

        $pre_save_object = new self($this->consultation_id, $this->clang_id);

        if ($this->priority !== $pre_save_object->priority || 0 === $this->consultation_id) {
            $this->setPriority();
        }

        if (0 === $this->consultation_id || $pre_save_object !== $this) {
            $query = \rex::getTablePrefix() .'d2u_machinery_category_consultations SET '
                    .'category_id = '. (int) $this->category_id .', '
                    .'priority = '. (int) $this->priority .', '
                    .'pic = :pic, '
                    .'link_type = :link_type, '
                    .'article_id = '. (int) $this->article_id .', '
                    .'target_category_id = '. (int) $this->target_category_id .' ';
            $params = [':pic' => $this->pic, ':link_type' => $this->link_type];
            if (0 === $this->consultation_id) {
                $query = 'INSERT INTO '. $query;
            } else {
                $query = 'UPDATE '. $query .' WHERE consultation_id = '. (int) $this->consultation_id;
            }
            $result = \rex_sql::factory();
            $result->setQuery($query, $params);
            if (0 === $this->consultation_id) {
                $this->consultation_id = (int) $result->getLastId();
            }
            $error = $result->hasError();
        }

        if (false === $error) {
            $pre_save_lang = new self($this->consultation_id, $this->clang_id);
            if ($pre_save_lang !== $this) {
                $query = 'REPLACE INTO '. \rex::getTablePrefix() .'d2u_machinery_category_consultations_lang SET '
                        .'consultation_id = :consultation_id, '
                        .'clang_id = :clang_id, '
                        .'heading = :heading, '
                        .'text = :text, '
                        .'link_label = :link_label, '
                        .'translation_needs_update = :tnu, '
                        .'updatedate = CURRENT_TIMESTAMP, '
                        .'updateuser = :updateuser ';
                $result = \rex_sql::factory();
                $result->setQuery($query, [
                    ':consultation_id' => $this->consultation_id,
                    ':clang_id' => $this->clang_id,
                    ':heading' => htmlspecialchars($this->heading),
                    ':text' => htmlspecialchars($this->text),
                    ':link_label' => htmlspecialchars($this->link_label),
                    ':tnu' => $this->translation_needs_update,
                    ':updateuser' => \rex::getUser() instanceof \rex_user ? \rex::getUser()->getLogin() : '',
                ]);
                $error = $result->hasError();
            }
        }

        return !$error;
    }

    /**
     * Resolves the frontend URL of this box' link target (article or category).
     * @return string URL or empty string if no valid target
     */
    public function getLinkUrl(): string
    {
        if ('category' === $this->link_type && $this->target_category_id > 0) {
            $target = new Category($this->target_category_id, $this->clang_id);
            if ($target->category_id > 0) {
                return $target->getUrl();
            }
            return '';
        }
        if ('article' === $this->link_type && $this->article_id > 0) {
            return \rex_getUrl($this->article_id, $this->clang_id);
        }
        return '';
    }

    /**
     * Reassigns priorities (global across all categories, so each priority is unique).
     * @param bool $delete Reorder priority after deletion
     */
    private function setPriority(bool $delete = false): void
    {
        $query = 'SELECT consultation_id FROM '. \rex::getTablePrefix() .'d2u_machinery_category_consultations '
            .'WHERE consultation_id <> '. (int) $this->consultation_id .' '
            .'ORDER BY priority, consultation_id';
        $result = \rex_sql::factory();
        $result->setQuery($query);

        if ($this->priority <= 0) {
            $this->priority = 1;
        }
        if ($this->priority > $result->getRows() || $delete) {
            $this->priority = $result->getRows() + 1;
        }

        $consultations = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $consultations[] = (int) $result->getValue('consultation_id');
            $result->next();
        }
        array_splice($consultations, $this->priority - 1, 0, [$this->consultation_id]);

        foreach ($consultations as $prio => $consultation_id) {
            $update = 'UPDATE '. \rex::getTablePrefix() .'d2u_machinery_category_consultations '
                .'SET priority = '. ((int) $prio + 1) .' WHERE consultation_id = '. (int) $consultation_id;
            $result = \rex_sql::factory();
            $result->setQuery($update);
        }
    }
}
