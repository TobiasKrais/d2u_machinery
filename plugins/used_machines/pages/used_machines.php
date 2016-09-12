<?php
$func = rex_request('func', 'string');
$entry_id = rex_request('entry_id', 'int');

// save settings
if (filter_input(INPUT_POST, "btn_save") == 'save') {
	// TODO
}

// Eingabeformular
if ($func == 'edit' || $func == 'add') {
	// TODO
}
// Eintrag löschen
else if ($func == 'delete') {
	// TODO
}

if ($func == '') {
    $list = rex_list::factory('SELECT used_machine_id, machines.name AS machinename, categories.name AS categoryname '
		. 'FROM '. rex::getTablePrefix() .'d2u_machinery_used_machines AS machines '
		. 'LEFT JOIN '. rex::getTablePrefix() .'d2u_machinery_categories_lang AS categories '
			. 'ON machines.category_id = categories.category_id AND categories.clang_id = '. rex_config::get("d2u_machinery", "default_lang") .' '
		. 'ORDER BY machines.name ASC');
    $list->addTableAttribute('class', 'table-striped table-hover');

    $tdIcon = '<i class="rex-icon fa-truck"></i>';
    $thIcon = '<a href="' . $list->getUrl(['func' => 'add']) . '" title="' . rex_i18n::msg('add') . '"><i class="rex-icon rex-icon-add-module"></i></a>';
    $list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
    $list->setColumnParams($thIcon, ['func' => 'edit', 'entry_id' => '###used_machine_id###']);

    $list->setColumnLabel('used_machine_id', rex_i18n::msg('id'));
    $list->setColumnLayout('used_machine_id', ['<th class="rex-table-id">###VALUE###</th>', '<td class="rex-table-id" data-title="' . rex_i18n::msg('id') . '">###VALUE###</td>']);

    $list->setColumnLabel('machinename', rex_i18n::msg('d2u_machinery_used_machines_name'));
    $list->setColumnParams('machinename', ['func' => 'edit', 'entry_id' => '###used_machine_id###']);

    $list->setColumnLabel('categoryname', rex_i18n::msg('d2u_machinery_used_machines_category'));

    $list->addColumn(rex_i18n::msg('module_functions'), '<i class="rex-icon rex-icon-edit"></i> ' . rex_i18n::msg('edit'));
    $list->setColumnLayout(rex_i18n::msg('module_functions'), ['<th class="rex-table-action" colspan="2">###VALUE###</th>', '<td class="rex-table-action">###VALUE###</td>']);
    $list->setColumnParams(rex_i18n::msg('module_functions'), ['func' => 'edit', 'entry_id' => '###used_machine_id###']);

    $list->addColumn(rex_i18n::msg('delete_module'), '<i class="rex-icon rex-icon-delete"></i> ' . rex_i18n::msg('delete'));
    $list->setColumnLayout(rex_i18n::msg('delete_module'), ['', '<td class="rex-table-action">###VALUE###</td>']);
    $list->setColumnParams(rex_i18n::msg('delete_module'), ['func' => 'delete', 'entry_id' => '###used_machine_id###']);
    $list->addLinkAttribute(rex_i18n::msg('delete_module'), 'data-confirm', rex_i18n::msg('confirm_delete_module'));

    $list->setNoRowsMessage(rex_i18n::msg('d2u_machinery_used_machines_no_used_machines_found'));

    $fragment = new rex_fragment();
    $fragment->setVar('title', rex_i18n::msg('d2u_machinery_used_machines'), false);
    $fragment->setVar('content', $list->get(), false);
    echo $fragment->parse('core/page/section.php');
}