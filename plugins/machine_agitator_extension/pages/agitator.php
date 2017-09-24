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

	// Media fields and links need special treatment
	$input_media = (array) rex_post('REX_INPUT_MEDIA', 'array', array());

	$success = TRUE;
	$agitator = FALSE;
	$agitator_id = $form['agitator_id'];
	foreach(rex_clang::getAll() as $rex_clang) {
		if($agitator === FALSE) {
			$agitator = new Agitator($agitator_id, $rex_clang->getId());
			$agitator->pic = $input_media[1];
		}
		else {
			$agitator->clang_id = $rex_clang->getId();
		}
		$agitator->name = $form['lang'][$rex_clang->getId()]['name'];
		$agitator->description = $form['lang'][$rex_clang->getId()]['description'];
		$agitator->translation_needs_update = $form['lang'][$rex_clang->getId()]['translation_needs_update'];

		if($agitator->translation_needs_update == "delete") {
			$agitator->delete(FALSE);
		}
		else if($agitator->save() > 0){
			$success = FALSE;
		}
		else {
			// remember id, for each database lang object needs same id
			$agitator_id = $agitator->agitator_id;
		}
	}

	// message output
	$message = 'form_save_error';
	if($success) {
		$message = 'form_saved';
	}
	
	// Redirect to make reload and thus double save impossible
	if(filter_input(INPUT_POST, "btn_apply") == 1 && $agitator !== FALSE) {
		header("Location: ". rex_url::currentBackendPage(array("entry_id"=>$agitator->agitator_id, "func"=>'edit', "message"=>$message), FALSE));
	}
	else {
		header("Location: ". rex_url::currentBackendPage(array("message"=>$message), FALSE));
	}
	exit;
}
// Delete
else if(filter_input(INPUT_POST, "btn_delete") == 1 || $func == 'delete') {
	$agitator_id = $entry_id;
	if($agitator_id == 0) {
		$form = (array) rex_post('form', 'array', []);
		$agitator_id = $form['agitator_id'];
	}
	$agitator = new Agitator($agitator_id, rex_config::get("d2u_helper", "default_lang"));
	
	// Check if object is used
	$reffering_agitator_types = $agitator->getRefferingAgitatorTypes();

	// If not used, delete
	if(count($reffering_agitator_types) == 0) {
		foreach(rex_clang::getAll() as $rex_clang) {
			if($agitator === FALSE) {
				$agitator = new Agitator($agitator_id, $rex_clang->getId());
				// If object is not found in language, set agitator_id anyway to be able to delete
				$agitator->agitator_id = $agitator_id;
			}
			else {
				$agitator->clang_id = $rex_clang->getId();
			}
			$agitator->delete();
		}
	}
	else {
		$message = '<ul>';
		foreach($reffering_agitator_types as $reffering_agitator_type) {
			$message .= '<li><a href="index.php?page=d2u_machinery/machine_agitator_extension/agitator_type&func=edit&entry_id='. $reffering_agitator_type->agitator_type_id .'">'. $reffering_agitator_type->name.'</a></li>';
		}
		$message .= '</ul>';

		print rex_view::error(rex_i18n::msg('d2u_helper_could_not_delete') . $message);
	}	
	$func = '';
}

// Eingabeformular
if ($func == 'edit' || $func == 'clone' || $func == 'add') {
?>
	<form action="<?php print rex_url::currentBackendPage(); ?>" method="post">
		<div class="panel panel-edit">
			<header class="panel-heading"><div class="panel-title"><?php print rex_i18n::msg('d2u_machinery_agitators'); ?></div></header>
			<div class="panel-body">
				<input type="hidden" name="form[agitator_id]" value="<?php echo ($func == 'edit' ? $entry_id : 0); ?>">
				<?php
					foreach(rex_clang::getAll() as $rex_clang) {
						$agitator = new Agitator($entry_id, $rex_clang->getId());
						$required = $rex_clang->getId() == rex_config::get("d2u_helper", "default_lang") ? TRUE : FALSE;
						
						$readonly_lang = TRUE;
						if(rex::getUser()->isAdmin() || (rex::getUser()->hasPerm('d2u_machinery[edit_lang]') && rex::getUser()->getComplexPerm('clang')->hasPerm($rex_clang->getId()))) {
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
									d2u_addon_backend_helper::form_select('d2u_helper_translation', 'form[lang]['. $rex_clang->getId() .'][translation_needs_update]', $options_translations, array($agitator->translation_needs_update), 1, FALSE, $readonly_lang);
								}
								else {
									print '<input type="hidden" name="form[lang]['. $rex_clang->getId() .'][translation_needs_update]" value="">';
								}
								
								d2u_addon_backend_helper::form_input('d2u_machinery_name', "form[lang][". $rex_clang->getId() ."][name]", $agitator->name, $required, $readonly_lang, "text");
							d2u_addon_backend_helper::form_textarea('d2u_machinery_agitators_description', "form[lang][". $rex_clang->getId() ."][description]", $agitator->description, 10, FALSE, $readonly_lang, TRUE);
							?>
						</div>
					</fieldset>
				<?php
					}
				?>
				<fieldset>
					<legend><?php echo rex_i18n::msg('d2u_machinery_data_all_lang'); ?></legend>
					<div class="panel-body-wrapper slide">
						<?php
							// Do not use last object from translations, because you don't know if it exists in DB
							$agitator = new Agitator($entry_id, rex_config::get("d2u_helper", "default_lang"));
							$readonly = TRUE;
							if(rex::getUser()->isAdmin() || rex::getUser()->hasPerm('d2u_machinery[edit_tech_data]')) {
								$readonly = FALSE;
							}

							d2u_addon_backend_helper::form_mediafield('d2u_machinery_agitators_pic', 1, $agitator->pic, $readonly);
						?>
					</div>
				</fieldset>
			<footer class="panel-footer">
				<div class="rex-form-panel-footer">
					<div class="btn-toolbar">
						<button class="btn btn-save rex-form-aligned" type="submit" name="btn_save" value="1"><?php echo rex_i18n::msg('form_save'); ?></button>
						<button class="btn btn-apply" type="submit" name="btn_apply" value="1"><?php echo rex_i18n::msg('form_apply'); ?></button>
						<button class="btn btn-abort" type="submit" name="btn_abort" formnovalidate="formnovalidate" value="1"><?php echo rex_i18n::msg('form_abort'); ?></button>
						<button class="btn btn-delete" type="submit" name="btn_delete" formnovalidate="formnovalidate" data-confirm="<?php echo rex_i18n::msg('form_delete'); ?>?" value="1"><?php echo rex_i18n::msg('form_delete'); ?></button>
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
	$query = 'SELECT agitators.agitator_id, name '
		. 'FROM '. rex::getTablePrefix() .'d2u_machinery_agitators AS agitators '
		. 'LEFT JOIN '. rex::getTablePrefix() .'d2u_machinery_agitators_lang AS lang '
			. 'ON agitators.agitator_id = lang.agitator_id AND lang.clang_id = '. rex_config::get("d2u_helper", "default_lang") .' '
		. 'ORDER BY name ASC';
    $list = rex_list::factory($query);

    $list->addTableAttribute('class', 'table-striped table-hover');

    $tdIcon = '<i class="rex-icon fa-spoon"></i>';
    $thIcon = '<a href="' . $list->getUrl(['func' => 'add']) . '" title="' . rex_i18n::msg('add') . '"><i class="rex-icon rex-icon-add-module"></i></a>';
    $list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
    $list->setColumnParams($thIcon, ['func' => 'edit', 'entry_id' => '###agitator_id###']);

    $list->setColumnLabel('agitator_id', rex_i18n::msg('id'));
    $list->setColumnLayout('agitator_id', ['<th class="rex-table-id">###VALUE###</th>', '<td class="rex-table-id">###VALUE###</td>']);

    $list->setColumnLabel('name', rex_i18n::msg('d2u_machinery_agitators_name'));
    $list->setColumnParams('name', ['func' => 'edit', 'entry_id' => '###agitator_id###']);

    $list->addColumn(rex_i18n::msg('module_functions'), '<i class="rex-icon rex-icon-edit"></i> ' . rex_i18n::msg('system_update'));
    $list->setColumnLayout(rex_i18n::msg('module_functions'), ['<th class="rex-table-action" colspan="2">###VALUE###</th>', '<td class="rex-table-action">###VALUE###</td>']);
    $list->setColumnParams(rex_i18n::msg('module_functions'), ['func' => 'edit', 'entry_id' => '###agitator_id###']);

 	$list->addColumn(rex_i18n::msg('d2u_helper_clone'), '<i class="rex-icon fa-copy"></i> ' . rex_i18n::msg('d2u_helper_clone'));
    $list->setColumnLayout(rex_i18n::msg('d2u_helper_clone'), ['', '<td class="rex-table-action">###VALUE###</td>']);
    $list->setColumnParams(rex_i18n::msg('d2u_helper_clone'), ['func' => 'clone', 'entry_id' => '###agitator_id###']);

    $list->addColumn(rex_i18n::msg('delete_module'), '<i class="rex-icon rex-icon-delete"></i> ' . rex_i18n::msg('delete'));
    $list->setColumnLayout(rex_i18n::msg('delete_module'), ['', '<td class="rex-table-action">###VALUE###</td>']);
    $list->setColumnParams(rex_i18n::msg('delete_module'), ['func' => 'delete', 'entry_id' => '###agitator_id###']);
    $list->addLinkAttribute(rex_i18n::msg('delete_module'), 'data-confirm', rex_i18n::msg('d2u_helper_confirm_delete'));

	$list->setNoRowsMessage(rex_i18n::msg('d2u_machinery_agitators_no_agitators_found'));

    $fragment = new rex_fragment();
    $fragment->setVar('title', rex_i18n::msg('d2u_machinery_agitators'), false);
    $fragment->setVar('content', $list->get(), false);
    echo $fragment->parse('core/page/section.php');
}