<?php

use TobiasKrais\D2UHelper\BackendHelper;
use TobiasKrais\D2UMachinery\Category;
use TobiasKrais\D2UMachinery\CategoryUsp;

if (!\TobiasKrais\D2UMachinery\Extension::isActive('machine_usps_extension')) {
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
    || in_array($func, ['delete'], true)
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

    $success = true;
    $usp = false;
    $usp_id = (int) ($form['usp_id'] ?? 0);
    foreach (rex_clang::getAll() as $rex_clang) {
        if (!$usp instanceof CategoryUsp) {
            $usp = new CategoryUsp($usp_id, $rex_clang->getId());
            $usp->usp_id = $usp_id;
            $usp->category_id = (int) ($form['category_id'] ?? 0);
            $usp->priority = (int) ($form['priority'] ?? 0);
            $usp->icon_light = (string) ($input_media[1] ?? '');
            $usp->icon_dark = (string) ($input_media[2] ?? '');
        } else {
            $usp->clang_id = $rex_clang->getId();
        }
        $usp->heading = (string) ($form['lang'][$rex_clang->getId()]['heading'] ?? '');
        $usp->text = (string) ($form['lang'][$rex_clang->getId()]['text'] ?? '');
        $usp->translation_needs_update = (string) ($form['lang'][$rex_clang->getId()]['translation_needs_update'] ?? 'no');

        if ('delete' === $usp->translation_needs_update) {
            $usp->delete(false);
        } elseif ($usp->save()) {
            $usp_id = $usp->usp_id;
        } else {
            $success = false;
        }
    }

    $message = $success ? 'form_saved' : 'form_save_error';
    if (1 === (int) filter_input(INPUT_POST, 'btn_apply', FILTER_VALIDATE_INT) && $usp instanceof CategoryUsp) {
        header('Location: '. rex_url::currentBackendPage(['entry_id' => $usp->usp_id, 'func' => 'edit', 'message' => $message], false));
    } else {
        header('Location: '. rex_url::currentBackendPage(['message' => $message], false));
    }
    exit;
}

// Delete
if ((!$invalidCsrf && 1 === (int) filter_input(INPUT_POST, 'btn_delete', FILTER_VALIDATE_INT)) || 'delete' === $func) {
    $usp_id = $entry_id;
    if (0 === $usp_id) {
        $form = rex_post('form', 'array', []);
        $usp_id = (int) ($form['usp_id'] ?? 0);
    }
    $usp = new CategoryUsp($usp_id, (int) rex_config::get('d2u_helper', 'default_lang'));
    if ($usp->usp_id > 0) {
        $usp->delete();
        $message = 'd2u_helper_deleted';
    }
    header('Location: '. rex_url::currentBackendPage(['message' => $message], false));
    exit;
}

// Clone: opens the add form prefilled with the source data (usp_id forced to 0 on save)
if ('clone' === $func) {
    $func = 'add';
}

// Priority up/down
if ('priority_up' === $func || 'priority_down' === $func) {
    $usp = new CategoryUsp($entry_id, (int) rex_config::get('d2u_helper', 'default_lang'));
    if ($usp->usp_id > 0) {
        if ('priority_down' === $func) {
            ++$usp->priority;
            $usp->save();
        } elseif ($usp->priority > 1) {
            --$usp->priority;
            $usp->save();
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

    $usp_default = new CategoryUsp($entry_id, $default_lang);
    echo '<input type="hidden" name="form[usp_id]" value="'. ('edit' === $func ? (int) $usp_default->usp_id : 0) .'">';

    echo '<div class="panel panel-edit"><header class="panel-heading"><div class="panel-title">'. rex_i18n::msg('d2u_machinery_usp_entry') .'</div></header><div class="panel-body">';

    // Language-independent fields (icons shared across languages)
    echo '<fieldset><legend><small><i class="rex-icon fa-star"></i></small> '. rex_i18n::msg('d2u_machinery_usps') .'</legend><div class="panel-body-wrapper slide">';
    BackendHelper::form_select('d2u_helper_category', 'form[category_id]', $category_options, [$usp_default->category_id], 1, false, false);
    BackendHelper::form_input('header_priority', 'form[priority]', (string) $usp_default->priority, true, false, 'number');
    BackendHelper::form_mediafield('d2u_machinery_usp_icon_light', '1', $usp_default->icon_light, false);
    BackendHelper::form_mediafield('d2u_machinery_usp_icon_dark', '2', $usp_default->icon_dark, false);
    echo '</div></fieldset>';

    // Language-specific fields (heading + text, with translation status)
    foreach (rex_clang::getAll() as $rex_clang) {
        $usp_lang = new CategoryUsp($entry_id, $rex_clang->getId());
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
            BackendHelper::form_select('d2u_helper_translation', 'form[lang]['. $rex_clang->getId() .'][translation_needs_update]', $options_translations, [$usp_lang->translation_needs_update], 1, false, $readonly_lang);
        } else {
            echo '<input type="hidden" name="form[lang]['. $rex_clang->getId() .'][translation_needs_update]" value="no">';
        }
        echo '<script>$(document).ready(function(){ toggleClangDetailsView('. $rex_clang->getId() .'); }); $("select[name=\'form[lang]['. $rex_clang->getId() .'][translation_needs_update]\']").on("change", function(){ toggleClangDetailsView('. $rex_clang->getId() .'); });</script>';
        echo '<div id="details_clang_'. $rex_clang->getId() .'">';
        BackendHelper::form_input('d2u_machinery_usp_heading', 'form[lang]['. $rex_clang->getId() .'][heading]', $usp_lang->heading, $rex_clang->getId() === $default_lang, $readonly_lang, 'text');
        BackendHelper::form_textarea('d2u_machinery_usp_text', 'form[lang]['. $rex_clang->getId() .'][text]', $usp_lang->text, 3, false, $readonly_lang, false);
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
    $query = 'SELECT usps.usp_id, lang.heading AS heading, cat_lang.name AS categoryname, usps.priority, '
        .'(SELECT MAX(priority) FROM '. \rex::getTablePrefix() .'d2u_machinery_category_usps) AS max_priority '
        .'FROM '. \rex::getTablePrefix() .'d2u_machinery_category_usps AS usps '
        .'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_category_usps_lang AS lang ON usps.usp_id = lang.usp_id AND lang.clang_id = '. $default_lang .' '
        .'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_categories_lang AS cat_lang ON usps.category_id = cat_lang.category_id AND cat_lang.clang_id = '. $default_lang .' ';
    if ('' === rex_request('sort', 'string', '')) {
        $query .= 'ORDER BY usps.priority';
    }

    $list = rex_list::factory(query: $query, rowsPerPage: 1000);
    $list->addTableAttribute('class', 'table-striped table-hover');

    $thIcon = '';
    if (\rex::getUser() instanceof rex_user && (\rex::getUser()->isAdmin() || \rex::getUser()->hasPerm('d2u_machinery[edit_data]'))) {
        $thIcon = '<a href="'. $list->getUrl(['func' => 'add']) .'" title="'. rex_i18n::msg('add') .'"><i class="rex-icon rex-icon-add-module"></i></a>';
    }
    $list->addColumn($thIcon, '<i class="rex-icon fa-star"></i>', 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
    $list->setColumnParams($thIcon, ['func' => 'edit', 'entry_id' => '###usp_id###']);

    $list->setColumnLabel('usp_id', rex_i18n::msg('id'));
    $list->setColumnLayout('usp_id', ['<th class="rex-table-id">###VALUE###</th>', '<td class="rex-table-id" style="vertical-align: middle;">###VALUE###</td>']);
    $list->setColumnSortable('usp_id');

    $list->setColumnLabel('categoryname', rex_i18n::msg('d2u_helper_category'));
    $list->setColumnSortable('categoryname');

    $list->setColumnLabel('heading', rex_i18n::msg('d2u_machinery_usp_heading'));
    $list->setColumnParams('heading', ['func' => 'edit', 'entry_id' => '###usp_id###']);
    $list->setColumnSortable('heading');

    $list->setColumnLabel('priority', rex_i18n::msg('header_priority'));
    $list->setColumnSortable('priority');
    $list->setColumnFormat('priority', 'custom', static function ($params) {
        $listParams = $params['list'];
        return BackendHelper::getPriorityButtons((int) $listParams->getValue('usp_id'), (int) $listParams->getValue('priority'), (int) $listParams->getValue('max_priority'));
    });
    $list->removeColumn('max_priority');

    $list->addColumn(rex_i18n::msg('module_functions'), '<i class="rex-icon rex-icon-edit"></i> '. rex_i18n::msg('edit'));
    $list->setColumnLayout(rex_i18n::msg('module_functions'), ['<th class="rex-table-action" colspan="3">###VALUE###</th>', '<td class="rex-table-action">###VALUE###</td>']);
    $list->setColumnParams(rex_i18n::msg('module_functions'), ['func' => 'edit', 'entry_id' => '###usp_id###']);

    if (\rex::getUser() instanceof rex_user && (\rex::getUser()->isAdmin() || \rex::getUser()->hasPerm('d2u_machinery[edit_data]'))) {
        $list->addColumn(rex_i18n::msg('d2u_machinery_usp_clone'), '<i class="rex-icon fa-copy"></i> '. rex_i18n::msg('d2u_machinery_usp_clone'));
        $list->setColumnLayout(rex_i18n::msg('d2u_machinery_usp_clone'), ['', '<td class="rex-table-action">###VALUE###</td>']);
        $list->setColumnParams(rex_i18n::msg('d2u_machinery_usp_clone'), ['func' => 'clone', 'entry_id' => '###usp_id###']);

        $list->addColumn(rex_i18n::msg('delete_module'), '<i class="rex-icon rex-icon-delete"></i> '. rex_i18n::msg('delete'));
        $list->setColumnLayout(rex_i18n::msg('delete_module'), ['', '<td class="rex-table-action">###VALUE###</td>']);
        $list->setColumnParams(rex_i18n::msg('delete_module'), ['func' => 'delete', 'entry_id' => '###usp_id###'] + $csrfToken->getUrlParams());
        $list->addLinkAttribute(rex_i18n::msg('delete_module'), 'data-confirm', rex_i18n::msg('d2u_helper_confirm_delete'));
    }

    $list->setNoRowsMessage(rex_i18n::msg('d2u_machinery_usps'));

    $fragment = new rex_fragment();
    $fragment->setVar('title', rex_i18n::msg('d2u_machinery_usps'), false);
    $fragment->setVar('content', $list->get(), false);
    echo $fragment->parse('core/page/section.php');
}
