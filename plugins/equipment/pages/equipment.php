<?php
$func = rex_request('func', 'string');
$entry_id = rex_request('entry_id', 'int');
$message = rex_get('message', 'string');

// messages
if($message != "") {
	print rex_view::success(rex_i18n::msg($message));
}

// save settings
if (filter_input(INPUT_POST, "btn_save") == 1 || filter_input(INPUT_POST, "btn_apply") == 1) {
	$form = (array) rex_post('form', 'array', []);

	$success = TRUE;
	$equipment = FALSE;
	$equipment_id = $form['equipment_id'];
	foreach(rex_clang::getAll() as $rex_clang) {
		if($equipment === FALSE) {
			$equipment = new Equipment($equipment_id, $rex_clang->getId());
			$equipment->equipment_id = $equipment_id; // Ensure correct ID in case first language has no object
			$equipment->article_number = $form['article_number'];
			if(isset($form['group_id'])) {
				$equipment->group = new EquipmentGroup($form['group_id'], $rex_clang->getId());
			}
			$equipment->online_status = array_key_exists('online_status', $form) ? "online" : "offline";
		}
		else {
			$equipment->clang_id = $rex_clang->getId();
		}
		$equipment->name = $form['lang'][$rex_clang->getId()]['name'];
		$equipment->translation_needs_update = $form['lang'][$rex_clang->getId()]['translation_needs_update'];

		if($equipment->translation_needs_update == "delete") {
			$equipment->delete(FALSE);
		}
		else if($equipment->save()){
			// remember id, for each database lang object needs same id
			$equipment_id = $equipment->equipment_id;
		}
		else {
			$success = FALSE;
		}
	}

	// message output
	$message = 'form_save_error';
	if($success) {
		$message = 'form_saved';
	}
	
	// Redirect to make reload and thus double save impossible
	if(filter_input(INPUT_POST, "btn_apply") == 1 && $equipment !== FALSE) {
		header("Location: ". rex_url::currentBackendPage(array("entry_id"=>$equipment->equipment_id, "func"=>'edit', "message"=>$message), FALSE));
	}
	else {
		header("Location: ". rex_url::currentBackendPage(array("message"=>$message), FALSE));
	}
	exit;
}
// Delete
else if(filter_input(INPUT_POST, "btn_delete") == 1 || $func == 'delete') {
	$equipment_id = $entry_id;
	if($equipment_id == 0) {
		$form = (array) rex_post('form', 'array', []);
		$equipment_id = $form['equipment_id'];
	}
	$equipment = new Equipment($equipment_id, rex_config::get("d2u_helper", "default_lang"));
	$equipment->equipment_id = $equipment_id; // Ensure correct ID in case language has no object
	
	// Check if object is used
	$referring_machines = $equipment->getReferringMachines();

	// If not used, delete
	if(count($referring_machines) == 0) {
		$equipment->delete(TRUE);
	}
	else {
		$message = '<ul>';
		foreach($referring_machines as $referring_machine) {
			$message .= '<li><a href="index.php?page=d2u_machinery/machine&func=edit&entry_id='. $referring_machine->machine_id .'">'. $referring_machine->name.'</a></li>';
		}
		$message .= '</ul>';

		print rex_view::error(rex_i18n::msg('d2u_helper_could_not_delete') . $message);
	}	
	$func = '';
}
// Change online status of machine
else if($func == 'changestatus') {
	$equipment = new Equipment($entry_id, rex_config::get("d2u_helper", "default_lang"));
	$equipment->equipment_id = $entry_id; // Ensure correct ID in case language has no object
	$equipment->changeStatus();
	
	header("Location: ". rex_url::currentBackendPage());
	exit;
}

// Eingabeformular
if ($func == 'edit' || $func == 'clone' || $func == 'add') {
?>
	<form action="<?php print rex_url::currentBackendPage(); ?>" method="post">
		<div class="panel panel-edit">
			<header class="panel-heading"><div class="panel-title"><?php print rex_i18n::msg('d2u_machinery_equipments'); ?></div></header>
			<div class="panel-body">
				<input type="hidden" name="form[equipment_id]" value="<?php echo ($func == 'edit' ? $entry_id : 0); ?>">
				<?php
					foreach(rex_clang::getAll() as $rex_clang) {
						$equipment = new Equipment($entry_id, $rex_clang->getId());
						$required = $rex_clang->getId() == rex_config::get("d2u_helper", "default_lang") ? TRUE : FALSE;
						
						$readonly_lang = TRUE;
						if(\rex::getUser()->isAdmin() || (\rex::getUser()->hasPerm('d2u_machinery[edit_lang]') && \rex::getUser()->getComplexPerm('clang')->hasPerm($rex_clang->getId()))) {
							$readonly_lang = FALSE;
						}
				?>
					<fieldset>
						<legend><?php echo rex_i18n::msg('d2u_helper_text_lang') .' "'. $rex_clang->getName() .'"'; ?></legend>
						<div class="panel-body-wrapper slide">
							<?php
								if($rex_clang->getId() != rex_config::get("d2u_helper", "default_lang")) {
									$options_translations = [];
									$options_translations["yes"] = rex_i18n::msg('d2u_helper_translation_needs_update');
									$options_translations["no"] = rex_i18n::msg('d2u_helper_translation_is_uptodate');
									$options_translations["delete"] = rex_i18n::msg('d2u_helper_translation_delete');
									d2u_addon_backend_helper::form_select('d2u_helper_translation', 'form[lang]['. $rex_clang->getId() .'][translation_needs_update]', $options_translations, array($equipment->translation_needs_update), 1, FALSE, $readonly_lang);
								}
								else {
									print '<input type="hidden" name="form[lang]['. $rex_clang->getId() .'][translation_needs_update]" value="">';
								}
								
								d2u_addon_backend_helper::form_input('d2u_helper_name', "form[lang][". $rex_clang->getId() ."][name]", $equipment->name, $required, $readonly_lang, "text");
							?>
						</div>
					</fieldset>
				<?php
					}
				?>
				<fieldset>
					<legend><?php echo rex_i18n::msg('d2u_helper_data_all_lang'); ?></legend>
					<div class="panel-body-wrapper slide">
						<?php
							// Do not use last object from translations, because you don't know if it exists in DB
							$equipment = new Equipment($entry_id, rex_config::get("d2u_helper", "default_lang"));
							$readonly = TRUE;
							if(\rex::getUser()->isAdmin() || \rex::getUser()->hasPerm('d2u_machinery[edit_data]')) {
								$readonly = FALSE;
							}

							d2u_addon_backend_helper::form_input('d2u_machinery_machine_product_number', "form[article_number]", $equipment->article_number, FALSE, $readonly, "text");
							$options = [];
							foreach(EquipmentGroup::getAll(rex_config::get("d2u_helper", "default_lang")) as $equipment_group) {
								if($equipment_group->name != "") {
									$options[$equipment_group->group_id] = $equipment_group->name;
								}
							}
							d2u_addon_backend_helper::form_select('d2u_machinery_equipment_group', 'form[group_id]', $options, $equipment->group !== FALSE ? [$equipment->group->group_id] : [], 1, FALSE, $readonly);
							d2u_addon_backend_helper::form_checkbox('d2u_helper_online_status', 'form[online_status]', 'online', $equipment->online_status == "online", $readonly);
						?>
					</div>
				</fieldset>
			</div>
			<footer class="panel-footer">
				<div class="rex-form-panel-footer">
					<div class="btn-toolbar">
						<button class="btn btn-save rex-form-aligned" type="submit" name="btn_save" value="1"><?php echo rex_i18n::msg('form_save'); ?></button>
						<button class="btn btn-apply" type="submit" name="btn_apply" value="1"><?php echo rex_i18n::msg('form_apply'); ?></button>
						<button class="btn btn-abort" type="submit" name="btn_abort" formnovalidate="formnovalidate" value="1"><?php echo rex_i18n::msg('form_abort'); ?></button>
						<?php
							if(\rex::getUser()->isAdmin() || \rex::getUser()->hasPerm('d2u_machinery[edit_data]')) {
								print '<button class="btn btn-delete" type="submit" name="btn_delete" formnovalidate="formnovalidate" data-confirm="'. rex_i18n::msg('form_delete') .'?" value="1">'. rex_i18n::msg('form_delete') .'</button>';
							}
						?>
					</div>
				</div>
			</footer>
		</div>
	</form>
	<br>
	<?php
		print d2u_addon_backend_helper::getCSS();
		print d2u_addon_backend_helper::getJS();
}

if ($func == '') {
	$query = 'SELECT equipments.equipment_id, name, article_number, online_status '
		. 'FROM '. \rex::getTablePrefix() .'d2u_machinery_equipments AS equipments '
		. 'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_equipments_lang AS lang '
			. 'ON equipments.equipment_id = lang.equipment_id AND lang.clang_id = '. rex_config::get("d2u_helper", "default_lang") .' '
		. 'ORDER BY name ASC';
    $list = rex_list::factory($query);

    $list->addTableAttribute('class', 'table-striped table-hover');

    $tdIcon = '<i class="rex-icon fa-plus"></i>';
 	$thIcon = "";
	if(\rex::getUser()->isAdmin() || \rex::getUser()->hasPerm('d2u_machinery[edit_data]')) {
		$thIcon = '<a href="' . $list->getUrl(['func' => 'add']) . '" title="' . rex_i18n::msg('add') . '"><i class="rex-icon rex-icon-add-module"></i></a>';
	}
    $list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
    $list->setColumnParams($thIcon, ['func' => 'edit', 'entry_id' => '###equipment_id###']);

    $list->setColumnLabel('equipment_id', rex_i18n::msg('id'));
    $list->setColumnLayout('equipment_id', ['<th class="rex-table-id">###VALUE###</th>', '<td class="rex-table-id">###VALUE###</td>']);

    $list->setColumnLabel('name', rex_i18n::msg('d2u_helper_name'));
    $list->setColumnParams('name', ['func' => 'edit', 'entry_id' => '###equipment_id###']);

    $list->setColumnLabel('article_number', rex_i18n::msg('d2u_machinery_machine_product_number'));
    $list->setColumnParams('article_number', ['func' => 'edit', 'entry_id' => '###equipment_id###']);

    $list->addColumn(rex_i18n::msg('module_functions'), '<i class="rex-icon rex-icon-edit"></i> ' . rex_i18n::msg('edit'));
    $list->setColumnLayout(rex_i18n::msg('module_functions'), ['<th class="rex-table-action" colspan="2">###VALUE###</th>', '<td class="rex-table-action">###VALUE###</td>']);
    $list->setColumnParams(rex_i18n::msg('module_functions'), ['func' => 'edit', 'entry_id' => '###equipment_id###']);

	$list->removeColumn('online_status');
	if(\rex::getUser()->isAdmin() || \rex::getUser()->hasPerm('d2u_machinery[edit_data]')) {
		$list->addColumn(rex_i18n::msg('status_online'), '<a class="rex-###online_status###" href="' . rex_url::currentBackendPage(['func' => 'changestatus']) . '&entry_id=###equipment_id###"><i class="rex-icon rex-icon-###online_status###"></i> ###online_status###</a>');
		$list->setColumnLayout(rex_i18n::msg('status_online'), ['', '<td class="rex-table-action">###VALUE###</td>']);

		$list->addColumn(rex_i18n::msg('d2u_helper_clone'), '<i class="rex-icon fa-copy"></i> ' . rex_i18n::msg('d2u_helper_clone'));
		$list->setColumnLayout(rex_i18n::msg('d2u_helper_clone'), ['', '<td class="rex-table-action">###VALUE###</td>']);
		$list->setColumnParams(rex_i18n::msg('d2u_helper_clone'), ['func' => 'clone', 'entry_id' => '###equipment_id###']);

		$list->addColumn(rex_i18n::msg('delete_module'), '<i class="rex-icon rex-icon-delete"></i> ' . rex_i18n::msg('delete'));
		$list->setColumnLayout(rex_i18n::msg('delete_module'), ['', '<td class="rex-table-action">###VALUE###</td>']);
		$list->setColumnParams(rex_i18n::msg('delete_module'), ['func' => 'delete', 'entry_id' => '###equipment_id###']);
		$list->addLinkAttribute(rex_i18n::msg('delete_module'), 'data-confirm', rex_i18n::msg('d2u_helper_confirm_delete'));
	}

	$list->setNoRowsMessage(rex_i18n::msg('d2u_machinery_equipments_no_equipments_found'));

    $fragment = new rex_fragment();
    $fragment->setVar('title', rex_i18n::msg('d2u_machinery_equipments'), false);
    $fragment->setVar('content', $list->get(), false);
    echo $fragment->parse('core/page/section.php');
}