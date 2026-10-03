<?php

use TobiasKrais\D2UHelper\BackendHelper;
use TobiasKrais\D2UMachinery\Category;
use TobiasKrais\D2UMachinery\CategoryConsultation;

if (!\TobiasKrais\D2UMachinery\Extension::isActive('category_consultation_extension')) {
    return;
}

$func = rex_request('func', 'string');
$entry_id = rex_request('entry_id', 'int');
$message = rex_get('message', 'string');

$csrfToken = BackendHelper::getPageCsrfToken();
$invalidCsrf = false;
if ((
    1 === (int) filter_input(INPUT_POST, 'btn_save', FILTER_VALIDATE_INT)
    || 1 === (int) filter_input(INPUT_POST, 'btn_apply', FILTER_VALIDATE_INT)
    || 1 === (int) filter_input(INPUT_POST, 'btn_delete', FILTER_VALIDATE_INT)
    || in_array($func, ['delete', 'clone'], true)
) && !$csrfToken->isValid()) {
    echo rex_view::error(rex_i18n::msg('csrf_token_invalid'));
    $invalidCsrf = true;
    if ('POST' !== rex_request::server('REQUEST_METHOD', 'string')) {
        $func = '';
    }
}

if ('' !== $message) {
    echo rex_view::success(rex_i18n::msg($message));
}

if (1 === (int) filter_input(INPUT_POST, 'btn_abort', FILTER_VALIDATE_INT)) {
    header('Location: '. BackendHelper::getCurrentBackendPage([], ['entry_id', 'func', 'message']));
    exit;
}

// Save
if (!$invalidCsrf && (1 === (int) filter_input(INPUT_POST, 'btn_save', FILTER_VALIDATE_INT) || 1 === (int) filter_input(INPUT_POST, 'btn_apply', FILTER_VALIDATE_INT))) {
    $form = rex_post('form', 'array', []);
    $input_media = rex_post('REX_INPUT_MEDIA', 'array', []);
    $input_link = rex_post('REX_INPUT_LINK', 'array', []);

    $success = true;
    $consultation = false;
    $consultation_id = (int) ($form['consultation_id'] ?? 0);
    foreach (rex_clang::getAll() as $rex_clang) {
        if (!$consultation instanceof CategoryConsultation) {
            $consultation = new CategoryConsultation($consultation_id, $rex_clang->getId());
            $consultation->consultation_id = $consultation_id;
            $consultation->category_id = (int) ($form['category_id'] ?? 0);
            $consultation->priority = (int) ($form['priority'] ?? 0);
            $consultation->pic = (string) ($input_media[1] ?? '');
            $consultation->link_type = 'category' === ($form['link_type'] ?? 'article') ? 'category' : 'article';
            $consultation->article_id = (int) ($input_link['article_id'] ?? 0);
            $consultation->target_category_id = (int) ($form['target_category_id'] ?? 0);
        } else {
            $consultation->clang_id = $rex_clang->getId();
        }
        $consultation->heading = (string) ($form['lang'][$rex_clang->getId()]['heading'] ?? '');
        $consultation->text = (string) ($form['lang'][$rex_clang->getId()]['text'] ?? '');
        $consultation->link_label = (string) ($form['lang'][$rex_clang->getId()]['link_label'] ?? '');
        $consultation->translation_needs_update = (string) ($form['lang'][$rex_clang->getId()]['translation_needs_update'] ?? 'no');

        if ('delete' === $consultation->translation_needs_update) {
            $consultation->delete(false);
        } elseif ($consultation->save()) {
            $consultation_id = $consultation->consultation_id;
        } else {
            $success = false;
        }
    }

    $message = $success ? 'form_saved' : 'form_save_error';
    if (1 === (int) filter_input(INPUT_POST, 'btn_apply', FILTER_VALIDATE_INT) && $consultation instanceof CategoryConsultation) {
        header('Location: '. rex_url::currentBackendPage(['entry_id' => $consultation->consultation_id, 'func' => 'edit', 'message' => $message], false));
    } else {
        header('Location: '. rex_url::currentBackendPage(['message' => $message], false));
    }
    exit;
}

// Delete
if ((!$invalidCsrf && 1 === (int) filter_input(INPUT_POST, 'btn_delete', FILTER_VALIDATE_INT)) || 'delete' === $func) {
    $consultation_id = $entry_id;
    if (0 === $consultation_id) {
        $form = rex_post('form', 'array', []);
        $consultation_id = (int) ($form['consultation_id'] ?? 0);
    }
    $consultation = new CategoryConsultation($consultation_id, (int) rex_config::get('d2u_helper', 'default_lang'));
    if ($consultation->consultation_id > 0) {
        $consultation->delete();
        $message = 'd2u_helper_deleted';
    }
    header('Location: '. rex_url::currentBackendPage(['message' => $message], false));
    exit;
}

// Clone
if (!$invalidCsrf && 'clone' === $func) {
    $default_lang = (int) rex_config::get('d2u_helper', 'default_lang');
    $source_default = new CategoryConsultation($entry_id, $default_lang);
    if ($source_default->consultation_id > 0) {
        $new_id = 0;
        foreach (rex_clang::getAll() as $rex_clang) {
            $source = new CategoryConsultation($entry_id, $rex_clang->getId());
            if ($rex_clang->getId() !== $default_lang && '' === trim($source->heading) && '' === trim($source->text)) {
                continue; // no translation for this language
            }
            $clone = new CategoryConsultation($new_id, $rex_clang->getId());
            $clone->consultation_id = $new_id;
            $clone->category_id = $source_default->category_id;
            $clone->pic = $source_default->pic;
            $clone->link_type = $source_default->link_type;
            $clone->article_id = $source_default->article_id;
            $clone->target_category_id = $source_default->target_category_id;
            $clone->heading = $source->heading;
            $clone->text = $source->text;
            $clone->link_label = $source->link_label;
            $clone->translation_needs_update = 'no';
            if ($clone->save()) {
                $new_id = $clone->consultation_id;
            }
        }
    }
    header('Location: '. BackendHelper::getCurrentBackendPage(['message' => 'form_saved'], ['func', 'entry_id']));
    exit;
}

// Priority up/down
if ('priority_up' === $func || 'priority_down' === $func) {
    $consultation = new CategoryConsultation($entry_id, (int) rex_config::get('d2u_helper', 'default_lang'));
    if ($consultation->consultation_id > 0) {
        if ('priority_down' === $func) {
            ++$consultation->priority;
            $consultation->save();
        } elseif ($consultation->priority > 1) {
            --$consultation->priority;
            $consultation->save();
        }
    }
    header('Location: '. BackendHelper::getCurrentBackendPage(['message' => 'd2u_helper_priority_changed'], ['func', 'entry_id']));
    exit;
}

// Edit form
if ('add' === $func || 'edit' === $func) {
    $default_lang = (int) rex_config::get('d2u_helper', 'default_lang');

    $categories = Category::getAll($default_lang);
    $category_options = [];
    foreach ($categories as $category) {
        $category_options[$category->category_id] = $category->name;
    }

    echo '<form action="'. BackendHelper::getCurrentBackendPage([], ['message']) .'" method="post">';
    echo $csrfToken->getHiddenField();

    $consultation_default = new CategoryConsultation($entry_id, $default_lang);
    echo '<input type="hidden" name="form[consultation_id]" value="'. (int) $consultation_default->consultation_id .'">';

    echo '<div class="panel panel-edit"><header class="panel-heading"><div class="panel-title">'. rex_i18n::msg('d2u_machinery_consultation_entry') .'</div></header><div class="panel-body">';

    // Language-independent fields (picture + link target shared across languages)
    echo '<fieldset><legend><small><i class="rex-icon fa-life-ring"></i></small> '. rex_i18n::msg('d2u_machinery_consultation_help') .'</legend><div class="panel-body-wrapper slide">';
    BackendHelper::form_select('d2u_helper_category', 'form[category_id]', $category_options, [$consultation_default->category_id], 1, false, false);
    BackendHelper::form_input('header_priority', 'form[priority]', (string) $consultation_default->priority, true, false, 'number');
    BackendHelper::form_mediafield('d2u_machinery_consultation_pic', '1', $consultation_default->pic, false);

    $link_type_options = [
        'article' => rex_i18n::msg('d2u_machinery_consultation_link_article'),
        'category' => rex_i18n::msg('d2u_machinery_consultation_link_category'),
    ];
    BackendHelper::form_select('d2u_machinery_consultation_link_type', 'form[link_type]', $link_type_options, [$consultation_default->link_type], 1, false, false);
    echo '<div id="consultation_link_article">';
    BackendHelper::form_linkfield('d2u_machinery_consultation_article', 'article_id', $consultation_default->article_id, $default_lang, false);
    echo '</div>';
    echo '<div id="consultation_link_category">';
    BackendHelper::form_select('d2u_machinery_consultation_target_category', 'form[target_category_id]', $category_options, [$consultation_default->target_category_id], 1, false, false);
    echo '</div>';
    echo '<script>
        function toggleConsultationLink() {
            var sel = document.querySelector(\'select[name="form[link_type]"]\');
            if (!sel) { return; }
            var isCategory = ("category" === sel.value);
            var artBox = document.getElementById("consultation_link_article");
            var catBox = document.getElementById("consultation_link_category");
            if (artBox) { artBox.style.display = isCategory ? "none" : ""; }
            if (catBox) { catBox.style.display = isCategory ? "" : "none"; }
        }
        $(document).ready(function(){
            toggleConsultationLink();
            $(\'select[name="form[link_type]"]\').on("change", toggleConsultationLink);
        });
    </script>';
    echo '</div></fieldset>';

    // Language-specific fields (heading + text, with translation status)
    foreach (rex_clang::getAll() as $rex_clang) {
        $consultation_lang = new CategoryConsultation($entry_id, $rex_clang->getId());
        $readonly_lang = true;
        if (\rex::getUser() instanceof rex_user && (\rex::getUser()->isAdmin() || (\rex::getUser()->hasPerm('d2u_machinery[edit_lang]') && \rex::getUser()->getComplexPerm('clang')->hasPerm($rex_clang->getId())))) {
            $readonly_lang = false;
        }
        echo '<fieldset><legend>'. rex_i18n::msg('d2u_helper_text_lang') .' "'. rex_escape($rex_clang->getName()) .'"</legend><div class="panel-body-wrapper slide">';
        if ($rex_clang->getId() !== $default_lang) {
            $options_translations = [
                'yes' => rex_i18n::msg('d2u_helper_translation_needs_update'),
                'no' => rex_i18n::msg('d2u_helper_translation_is_uptodate'),
                'delete' => rex_i18n::msg('d2u_helper_translation_delete'),
            ];
            BackendHelper::form_select('d2u_helper_translation', 'form[lang]['. $rex_clang->getId() .'][translation_needs_update]', $options_translations, [$consultation_lang->translation_needs_update], 1, false, $readonly_lang);
        } else {
            echo '<input type="hidden" name="form[lang]['. $rex_clang->getId() .'][translation_needs_update]" value="no">';
        }
        echo '<script>$(document).ready(function(){ toggleClangDetailsView('. $rex_clang->getId() .'); }); $("select[name=\'form[lang]['. $rex_clang->getId() .'][translation_needs_update]\']").on("change", function(){ toggleClangDetailsView('. $rex_clang->getId() .'); });</script>';
        echo '<div id="details_clang_'. $rex_clang->getId() .'">';
        BackendHelper::form_input('d2u_machinery_consultation_heading_field', 'form[lang]['. $rex_clang->getId() .'][heading]', $consultation_lang->heading, $rex_clang->getId() === $default_lang, $readonly_lang, 'text');
        BackendHelper::form_textarea('d2u_machinery_consultation_text_field', 'form[lang]['. $rex_clang->getId() .'][text]', $consultation_lang->text, 3, false, $readonly_lang, true);
        BackendHelper::form_input('d2u_machinery_consultation_link_label', 'form[lang]['. $rex_clang->getId() .'][link_label]', $consultation_lang->link_label, false, $readonly_lang, 'text');
        echo '</div>';
        echo '</div></fieldset>';
    }

    echo '</div>';
    echo '<footer class="panel-footer"><div class="rex-form-panel-footer"><div class="btn-toolbar">';
    echo '<button class="btn btn-save rex-form-aligned" type="submit" name="btn_save" value="1">'. rex_i18n::msg('form_save') .'</button> ';
    echo '<button class="btn btn-apply" type="submit" name="btn_apply" value="1">'. rex_i18n::msg('form_apply') .'</button> ';
    echo '<button class="btn btn-abort" type="submit" name="btn_abort" formnovalidate="formnovalidate" value="1">'. rex_i18n::msg('form_abort') .'</button>';
    if (\rex::getUser() instanceof rex_user && (\rex::getUser()->isAdmin() || \rex::getUser()->hasPerm('d2u_machinery[edit_data]'))) {
        echo ' <button class="btn btn-delete" type="submit" name="btn_delete" formnovalidate="formnovalidate" data-confirm="'. rex_i18n::msg('form_delete') .'?" value="1">'. rex_i18n::msg('form_delete') .'</button>';
    }
    echo '</div></div></footer>';
    echo '</div></form><br>';
    echo BackendHelper::getCSS();
    echo BackendHelper::getJS();
}

if ('' === $func) {
    $default_lang = (int) rex_config::get('d2u_helper', 'default_lang');
    $query = 'SELECT consultations.consultation_id, lang.heading AS heading, cat_lang.name AS categoryname, consultations.priority, '
        .'(SELECT MAX(priority) FROM '. \rex::getTablePrefix() .'d2u_machinery_category_consultations) AS max_priority '
        .'FROM '. \rex::getTablePrefix() .'d2u_machinery_category_consultations AS consultations '
        .'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_category_consultations_lang AS lang ON consultations.consultation_id = lang.consultation_id AND lang.clang_id = '. $default_lang .' '
        .'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_categories_lang AS cat_lang ON consultations.category_id = cat_lang.category_id AND cat_lang.clang_id = '. $default_lang .' '
        .'ORDER BY categoryname, consultations.priority';

    $list = rex_list::factory(query: $query, rowsPerPage: 1000);
    $list->addTableAttribute('class', 'table-striped table-hover');

    $thIcon = '';
    if (\rex::getUser() instanceof rex_user && (\rex::getUser()->isAdmin() || \rex::getUser()->hasPerm('d2u_machinery[edit_data]'))) {
        $thIcon = '<a href="'. $list->getUrl(['func' => 'add']) .'" title="'. rex_i18n::msg('add') .'"><i class="rex-icon rex-icon-add-module"></i></a>';
    }
    $list->addColumn($thIcon, '<i class="rex-icon fa-life-ring"></i>', 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
    $list->setColumnParams($thIcon, ['func' => 'edit', 'entry_id' => '###consultation_id###']);

    $list->setColumnLabel('consultation_id', rex_i18n::msg('id'));
    $list->setColumnLayout('consultation_id', ['<th class="rex-table-id">###VALUE###</th>', '<td class="rex-table-id" style="vertical-align: middle;">###VALUE###</td>']);
    $list->setColumnSortable('consultation_id');

    $list->setColumnLabel('categoryname', rex_i18n::msg('d2u_helper_category'));
    $list->setColumnSortable('categoryname');

    $list->setColumnLabel('heading', rex_i18n::msg('d2u_machinery_consultation_heading_field'));
    $list->setColumnParams('heading', ['func' => 'edit', 'entry_id' => '###consultation_id###']);
    $list->setColumnSortable('heading');

    $list->setColumnLabel('priority', rex_i18n::msg('header_priority'));
    $list->setColumnFormat('priority', 'custom', static function ($params) {
        $listParams = $params['list'];
        return BackendHelper::getPriorityButtons((int) $listParams->getValue('consultation_id'), (int) $listParams->getValue('priority'), (int) $listParams->getValue('max_priority'));
    });
    $list->removeColumn('max_priority');

    $list->addColumn(rex_i18n::msg('module_functions'), '<i class="rex-icon rex-icon-edit"></i> '. rex_i18n::msg('edit'));
    $list->setColumnLayout(rex_i18n::msg('module_functions'), ['<th class="rex-table-action" colspan="3">###VALUE###</th>', '<td class="rex-table-action">###VALUE###</td>']);
    $list->setColumnParams(rex_i18n::msg('module_functions'), ['func' => 'edit', 'entry_id' => '###consultation_id###']);

    if (\rex::getUser() instanceof rex_user && (\rex::getUser()->isAdmin() || \rex::getUser()->hasPerm('d2u_machinery[edit_data]'))) {
        $list->addColumn(rex_i18n::msg('d2u_machinery_consultation_clone'), '<i class="rex-icon fa-copy"></i> '. rex_i18n::msg('d2u_machinery_consultation_clone'));
        $list->setColumnLayout(rex_i18n::msg('d2u_machinery_consultation_clone'), ['', '<td class="rex-table-action">###VALUE###</td>']);
        $list->setColumnParams(rex_i18n::msg('d2u_machinery_consultation_clone'), ['func' => 'clone', 'entry_id' => '###consultation_id###'] + $csrfToken->getUrlParams());

        $list->addColumn(rex_i18n::msg('delete_module'), '<i class="rex-icon rex-icon-delete"></i> '. rex_i18n::msg('delete'));
        $list->setColumnLayout(rex_i18n::msg('delete_module'), ['', '<td class="rex-table-action">###VALUE###</td>']);
        $list->setColumnParams(rex_i18n::msg('delete_module'), ['func' => 'delete', 'entry_id' => '###consultation_id###'] + $csrfToken->getUrlParams());
        $list->addLinkAttribute(rex_i18n::msg('delete_module'), 'data-confirm', rex_i18n::msg('d2u_helper_confirm_delete'));
    }

    $list->setNoRowsMessage(rex_i18n::msg('d2u_machinery_consultation_help'));

    $fragment = new rex_fragment();
    $fragment->setVar('title', rex_i18n::msg('d2u_machinery_consultation_help'), false);
    $fragment->setVar('content', $list->get(), false);
    echo $fragment->parse('core/page/section.php');
}
