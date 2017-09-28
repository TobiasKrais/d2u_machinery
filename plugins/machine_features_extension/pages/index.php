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
	$feature = FALSE;
	$feature_id = $form['feature_id'];
	foreach(rex_clang::getAll() as $rex_clang) {
		if($feature === FALSE) {
			$feature = new Feature($feature_id, $rex_clang->getId());
			$feature->feature_id = $feature_id; // Ensure correct ID in case first language has no object
			$feature->category_ids = $form['category_ids'];
			$feature->priority = $form['priority'];
			$feature->pic = $input_media[1];
			if(rex_addon::get("d2u_videos")->isAvailable() && isset($form['video_id']) && $form['video_id'] > 0) {
				$supply->video = new Video($form['video_id'], rex_config::get("d2u_helper", "default_lang"));
			}
			else {
				$supply->video = FALSE;
			}
		}
		else {
			$feature->clang_id = $rex_clang->getId();
		}
		$feature->name = $form['lang'][$rex_clang->getId()]['name'];
		$feature->title = $form['lang'][$rex_clang->getId()]['title'];
		$feature->translation_needs_update = $form['lang'][$rex_clang->getId()]['translation_needs_update'];
		$feature->description = $form['lang'][$rex_clang->getId()]['description'];

		if($feature->translation_needs_update == "delete") {
			$feature->delete(FALSE);
		}
		else if($feature->save() > 0){
			$success = FALSE;
		}
		else {
			// remember id, for each database lang object needs same id
			$feature_id = $feature->feature_id;
		}
	}

	// message output
	$message = 'form_save_error';
	if($success) {
		$message = 'form_saved';
	}
	
	// Redirect to make reload and thus double save impossible
	if(filter_input(INPUT_POST, "btn_apply") == 1 && $feature !== FALSE) {
		header("Location: ". rex_url::currentBackendPage(array("entry_id"=>$feature->feature_id, "func"=>'edit', "message"=>$message), FALSE));
	}
	else {
		header("Location: ". rex_url::currentBackendPage(array("message"=>$message), FALSE));
	}
	exit;
}
// Delete
else if(filter_input(INPUT_POST, "btn_delete") == 1 || $func == 'delete') {
	$feature_id = $entry_id;
	if($feature_id == 0) {
		$form = (array) rex_post('form', 'array', []);
		$feature_id = $form['feature_id'];
	}
	$feature = new Feature($feature_id, rex_config::get("d2u_helper", "default_lang"));
	$feature->feature_id = $feature_id; // Ensure correct ID in case language has no object
	
	// Check if object is used
	$reffering_machines = $feature->getRefferingMachines();

	// If not used, delete
	if(count($reffering_machines) == 0) {
		$feature->delete(TRUE);
	}
	else {
		$message = '<ul>';
		foreach($reffering_machines as $reffering_machine) {
			$message .= '<li><a href="index.php?page=d2u_machinery/machine&func=edit&entry_id='. $reffering_machine->machine_id .'">'. $reffering_machine->name.'</a></li>';
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
			<header class="panel-heading"><div class="panel-title"><?php print rex_i18n::msg('d2u_machinery_features'); ?></div></header>
			<div class="panel-body">
				<input type="hidden" name="form[feature_id]" value="<?php echo $entry_id; ?>">
				<?php
					foreach(rex_clang::getAll() as $rex_clang) {
						$feature = new Feature($entry_id, $rex_clang->getId());
						$required = $rex_clang->getId() == rex_config::get("d2u_helper", "default_lang") ? TRUE : FALSE;
						
						$readonly_lang = TRUE;
						if(rex::getUser()->isAdmin() || (rex::getUser()->hasPerm('d2u_machinery[edit_lang]') && rex::getUser()->getComplexPerm('clang')->hasPerm($rex_clang->getId()))) {
							$readonly_lang = FALSE;
						}
				?>
					<fieldset>
						<legend><?php echo rex_i18n::msg('d2u_machinery_helper_lang') .' "'. $rex_clang->getName() .'"'; ?></legend>
						<div class="panel-body-wrapper slide">
							<?php
								if($rex_clang->getId() != rex_config::get("d2u_helper", "default_lang")) {
									$options_translations = [];
									$options_translations["yes"] = rex_i18n::msg('d2u_helper_translation_needs_update');
									$options_translations["no"] = rex_i18n::msg('d2u_helper_translation_is_uptodate');
									$options_translations["delete"] = rex_i18n::msg('d2u_helper_translation_delete');
									d2u_addon_backend_helper::form_select('d2u_helper_translation', 'form[lang]['. $rex_clang->getId() .'][translation_needs_update]', $options_translations, array($feature->translation_needs_update), 1, FALSE, $readonly_lang);
								}
								else {
									print '<input type="hidden" name="form[lang]['. $rex_clang->getId() .'][translation_needs_update]" value="">';
								}
								
								d2u_addon_backend_helper::form_input('d2u_machinery_name', "form[lang][". $rex_clang->getId() ."][name]", $feature->name, $required, $readonly_lang, "text");
								d2u_addon_backend_helper::form_input('d2u_machinery_features_title', "form[lang][". $rex_clang->getId() ."][title]", $feature->title, $required, $readonly_lang, "text");
								d2u_addon_backend_helper::form_textarea('d2u_machinery_features_description', "form[lang][". $rex_clang->getId() ."][description]", $feature->description, 10, FALSE, $readonly_lang, TRUE)
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
							$feature = new Feature($entry_id, rex_config::get("d2u_helper", "default_lang"));
							$readonly = TRUE;
							if(rex::getUser()->isAdmin() || rex::getUser()->hasPerm('d2u_machinery[edit_tech_data]')) {
								$readonly = FALSE;
							}

							d2u_addon_backend_helper::form_input('header_priority', 'form[priority]', $feature->priority, TRUE, $readonly, 'number');
							d2u_addon_backend_helper::form_mediafield('d2u_machinery_features_pic', 1, $feature->pic, $readonly);
							
							$options = [];
							foreach(Category::getAll(rex_config::get("d2u_helper", "default_lang")) as $category) {
								$options[$category->category_id] = $category->name;
							}
							d2u_addon_backend_helper::form_select('d2u_machinery_features_categories', 'form[category_ids][]', $options, $feature->category_ids, 10, TRUE, $readonly);

							if(rex_addon::get("d2u_videos")->isAvailable()) {
								$options_video = [0 => rex_i18n::msg('d2u_machinery_video_no')];
								foreach (Video::getAll(rex_config::get("d2u_helper", "default_lang")) as $video) {
									$options_video[$video->video_id] = $video->name;
								}
								d2u_addon_backend_helper::form_select('d2u_machinery_video', 'form[video_id]', $options_video, $supply->video !== FALSE ? [$supply->video->video_id] : [], 1, FALSE, $readonly);
							}
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
	$query = 'SELECT features.feature_id, title, priority '
		. 'FROM '. rex::getTablePrefix() .'d2u_machinery_features AS features '
		. 'LEFT JOIN '. rex::getTablePrefix() .'d2u_machinery_features_lang AS lang '
			. 'ON features.feature_id = lang.feature_id AND lang.clang_id = '. rex_config::get("d2u_helper", "default_lang") .' '
		. 'ORDER BY priority ASC';
    $list = rex_list::factory($query);

    $list->addTableAttribute('class', 'table-striped table-hover');

    $tdIcon = '<i class="rex-icon  fa-plug"></i>';
    $thIcon = '<a href="' . $list->getUrl(['func' => 'add']) . '" title="' . rex_i18n::msg('add') . '"><i class="rex-icon rex-icon-add-module"></i></a>';
    $list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
    $list->setColumnParams($thIcon, ['func' => 'edit', 'entry_id' => '###feature_id###']);

    $list->setColumnLabel('feature_id', rex_i18n::msg('id'));
    $list->setColumnLayout('feature_id', ['<th class="rex-table-id">###VALUE###</th>', '<td class="rex-table-id">###VALUE###</td>']);

    $list->setColumnLabel('title', rex_i18n::msg('d2u_machinery_features_title'));
    $list->setColumnParams('title', ['func' => 'edit', 'entry_id' => '###feature_id###']);

    $list->setColumnLabel('priority', rex_i18n::msg('header_priority'));

    $list->addColumn(rex_i18n::msg('module_functions'), '<i class="rex-icon rex-icon-edit"></i> ' . rex_i18n::msg('system_update'));
    $list->setColumnLayout(rex_i18n::msg('module_functions'), ['<th class="rex-table-action" colspan="2">###VALUE###</th>', '<td class="rex-table-action">###VALUE###</td>']);
    $list->setColumnParams(rex_i18n::msg('module_functions'), ['func' => 'edit', 'entry_id' => '###feature_id###']);

    $list->addColumn(rex_i18n::msg('delete_module'), '<i class="rex-icon rex-icon-delete"></i> ' . rex_i18n::msg('delete'));
    $list->setColumnLayout(rex_i18n::msg('delete_module'), ['', '<td class="rex-table-action">###VALUE###</td>']);
    $list->setColumnParams(rex_i18n::msg('delete_module'), ['func' => 'delete', 'entry_id' => '###feature_id###']);
    $list->addLinkAttribute(rex_i18n::msg('delete_module'), 'data-confirm', rex_i18n::msg('d2u_helper_confirm_delete'));

    $list->setNoRowsMessage(rex_i18n::msg('d2u_machinery_features_no_features_found'));

    $fragment = new rex_fragment();
    $fragment->setVar('title', rex_i18n::msg('d2u_machinery_features'), false);
    $fragment->setVar('content', $list->get(), false);
    echo $fragment->parse('core/page/section.php');
}