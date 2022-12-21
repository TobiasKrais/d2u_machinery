<?php
$func = rex_request('func', 'string');
$entry_id = rex_request('entry_id', 'int');
$message = rex_get('message', 'string');

// messages
if($message !== "") {
	print rex_view::success(rex_i18n::msg($message));
}

// save settings
if (intval(filter_input(INPUT_POST, "btn_save")) === 1 || intval(filter_input(INPUT_POST, "btn_apply")) === 1) {
	$form = (array) rex_post('form', 'array', []);

	// Media fields and links need special treatment
	$input_media = (array) rex_post('REX_INPUT_MEDIA', 'array', array());

	$success = TRUE;
	$equipment_group = FALSE;
	$group_id = $form['group_id'];
	foreach(rex_clang::getAll() as $rex_clang) {
		if($equipment_group === FALSE) {
			$equipment_group = new EquipmentGroup($group_id, $rex_clang->getId());
			$equipment_group->group_id = $group_id; // Ensure correct ID in case first language has no object
			$equipment_group->priority = $form['priority'];
			$equipment_group->picture = $input_media[1];
		}
		else {
			$equipment_group->clang_id = $rex_clang->getId();
		}
		$equipment_group->name = $form['lang'][$rex_clang->getId()]['name'];
		$equipment_group->description = $form['lang'][$rex_clang->getId()]['description'];
		$equipment_group->translation_needs_update = $form['lang'][$rex_clang->getId()]['translation_needs_update'];

		if($equipment_group->translation_needs_update == "delete") {
			$equipment_group->delete(FALSE);
		}
		else if($equipment_group->save()){
			// remember id, for each database lang object needs same id
			$group_id = $equipment_group->group_id;
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
	if(filter_input(INPUT_POST, "btn_apply") == 1 && $equipment_group !== FALSE) {
		header("Location: ". rex_url::currentBackendPage(array("entry_id"=>$equipment_group->group_id, "func"=>'edit', "message"=>$message), FALSE));
	}
	else {
		header("Location: ". rex_url::currentBackendPage(array("message"=>$message), FALSE));
	}
	exit;
}
// Delete
else if(filter_input(INPUT_POST, "btn_delete") == 1 || $func == 'delete') {
	$group_id = $entry_id;
	if($group_id == 0) {
		$form = (array) rex_post('form', 'array', []);
		$group_id = $form['group_id'];
	}
	$equipment_group = new EquipmentGroup($group_id, intval(rex_config::get("d2u_helper", "default_lang")));
	$equipment_group->group_id = $group_id; // Ensure correct ID in case language has no object
	
	// Check if object is used
	$referring_equipments = $equipment_group->getReferringEquipments();

	// If not used, delete
	if(count($referring_equipments) == 0) {
		$equipment_group->delete(TRUE);
	}
	else {
		$message = '<ul>';
		foreach($referring_equipments as $referring_equipments) {
			$message .= '<li><a href="index.php?page=d2u_machinery/equipment/equipment&func=edit&entry_id='. $referring_equipments->equipment_id .'">'. $referring_equipments->name.'</a></li>';
		}
		$message .= '</ul>';

		print rex_view::error(rex_i18n::msg('d2u_helper_could_not_delete') . $message);
	}
	
	$func = '';
}

// Eingabeformular
if ($func == 'edit' || $func == 'add') {
?>
	<form action="<?php print rex_url::currentBackendPage(); ?>" method="post">
		<div class="panel panel-edit">
			<header class="panel-heading"><div class="panel-title"><?php print rex_i18n::msg('d2u_machinery_equipment_groups'); ?></div></header>
			<div class="panel-body">
				<input type="hidden" name="form[group_id]" value="<?php echo $entry_id; ?>">
				<?php
					foreach(rex_clang::getAll() as $rex_clang) {
						$equipment_group = new EquipmentGroup($entry_id, $rex_clang->getId());
						$required = $rex_clang->getId() === intval(rex_config::get("d2u_helper", "default_lang")) ? TRUE : FALSE;
						
						$readonly_lang = TRUE;
						if(\rex::getUser() instanceof rex_user && (\rex::getUser()->isAdmin() || (\rex::getUser()->hasPerm('d2u_machinery[edit_lang]') && \rex::getUser()->getComplexPerm('clang') instanceof rex_clang_perm && \rex::getUser()->getComplexPerm('clang')->hasPerm($rex_clang->getId())))) {
							$readonly_lang = FALSE;
						}
				?>
					<fieldset>
						<legend><?php echo rex_i18n::msg('d2u_helper_text_lang') .' "'. $rex_clang->getName() .'"'; ?></legend>
						<div class="panel-body-wrapper slide">
							<?php
								if($rex_clang->getId() !== intval(rex_config::get("d2u_helper", "default_lang"))) {
									$options_translations = [];
									$options_translations["yes"] = rex_i18n::msg('d2u_helper_translation_needs_update');
									$options_translations["no"] = rex_i18n::msg('d2u_helper_translation_is_uptodate');
									$options_translations["delete"] = rex_i18n::msg('d2u_helper_translation_delete');
									d2u_addon_backend_helper::form_select('d2u_helper_translation', 'form[lang]['. $rex_clang->getId() .'][translation_needs_update]', $options_translations, [$equipment_group->translation_needs_update], 1, FALSE, $readonly_lang);
								}
								else {
									print '<input type="hidden" name="form[lang]['. $rex_clang->getId() .'][translation_needs_update]" value="">';
								}
							?>
							<script>
								// Hide on document load
								$(document).ready(function() {
									toggleClangDetailsView(<?php print $rex_clang->getId(); ?>);
								});

								// Hide on selection change
								$("select[name='form[lang][<?php print $rex_clang->getId(); ?>][translation_needs_update]']").on('change', function(e) {
									toggleClangDetailsView(<?php print $rex_clang->getId(); ?>);
								});
							</script>
							<div id="details_clang_<?php print $rex_clang->getId(); ?>">
								<?php
									d2u_addon_backend_helper::form_input('d2u_helper_name', "form[lang][". $rex_clang->getId() ."][name]", $equipment_group->name, $required, $readonly_lang, "text");
									d2u_addon_backend_helper::form_textarea('d2u_helper_description', "form[lang][". $rex_clang->getId() ."][description]", $equipment_group->description, 10, FALSE, $readonly_lang, TRUE);
								?>
							</div>
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
							$equipment_group = new EquipmentGroup($entry_id, intval(rex_config::get("d2u_helper", "default_lang")));
							$readonly = TRUE;
							if(\rex::getUser() instanceof rex_user && (\rex::getUser()->isAdmin() || \rex::getUser()->hasPerm('d2u_machinery[edit_data]'))) {
								$readonly = FALSE;
							}

							d2u_addon_backend_helper::form_input('header_priority', 'form[priority]', $equipment_group->priority, TRUE, $readonly, 'number');
							d2u_addon_backend_helper::form_mediafield('d2u_helper_picture', 1, $equipment_group->picture, $readonly);
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
							if(\rex::getUser() instanceof rex_user && (\rex::getUser()->isAdmin() || \rex::getUser()->hasPerm('d2u_machinery[edit_data]'))) {
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
	$query = 'SELECT equipment_groups.group_id, name, priority '
		. 'FROM '. \rex::getTablePrefix() .'d2u_machinery_equipment_groups AS equipment_groups '
		. 'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_equipment_groups_lang AS lang '
			. 'ON equipment_groups.group_id = lang.group_id AND lang.clang_id = '. intval(rex_config::get("d2u_helper", "default_lang")) .' '
		. 'ORDER BY name ASC';
    $list = rex_list::factory($query, 1000);

    $list->addTableAttribute('class', 'table-striped table-hover');

    $tdIcon = '<i class="rex-icon fa-plus-square"></i>';
 	$thIcon = "";
	if(\rex::getUser() instanceof rex_user && (\rex::getUser()->isAdmin() || \rex::getUser()->hasPerm('d2u_machinery[edit_data]'))) {
		$thIcon = '<a href="' . $list->getUrl(['func' => 'add']) . '" title="' . rex_i18n::msg('add') . '"><i class="rex-icon rex-icon-add-module"></i></a>';
	}
    $list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
    $list->setColumnParams($thIcon, ['func' => 'edit', 'entry_id' => '###group_id###']);

    $list->setColumnLabel('group_id', rex_i18n::msg('id'));
    $list->setColumnLayout('group_id', ['<th class="rex-table-id">###VALUE###</th>', '<td class="rex-table-id">###VALUE###</td>']);

    $list->setColumnLabel('name', rex_i18n::msg('d2u_helper_name'));
    $list->setColumnParams('name', ['func' => 'edit', 'entry_id' => '###group_id###']);

    $list->setColumnLabel('priority', rex_i18n::msg('header_priority'));

    $list->addColumn(rex_i18n::msg('module_functions'), '<i class="rex-icon rex-icon-edit"></i> ' . rex_i18n::msg('edit'));
    $list->setColumnLayout(rex_i18n::msg('module_functions'), ['<th class="rex-table-action" colspan="2">###VALUE###</th>', '<td class="rex-table-action">###VALUE###</td>']);
    $list->setColumnParams(rex_i18n::msg('module_functions'), ['func' => 'edit', 'entry_id' => '###group_id###']);

	if(\rex::getUser() instanceof rex_user && (\rex::getUser()->isAdmin() || \rex::getUser()->hasPerm('d2u_machinery[edit_data]'))) {
		$list->addColumn(rex_i18n::msg('delete_module'), '<i class="rex-icon rex-icon-delete"></i> ' . rex_i18n::msg('delete'));
		$list->setColumnLayout(rex_i18n::msg('delete_module'), ['', '<td class="rex-table-action">###VALUE###</td>']);
		$list->setColumnParams(rex_i18n::msg('delete_module'), ['func' => 'delete', 'entry_id' => '###group_id###']);
		$list->addLinkAttribute(rex_i18n::msg('delete_module'), 'data-confirm', rex_i18n::msg('d2u_helper_confirm_delete'));
	}

	$list->setNoRowsMessage(rex_i18n::msg('d2u_machinery_equipment_no_equipment_groups_found'));

    $fragment = new rex_fragment();
    $fragment->setVar('title', rex_i18n::msg('d2u_machinery_equipment_groups'), false);
    $fragment->setVar('content', $list->get(), false);
    echo $fragment->parse('core/page/section.php');
}