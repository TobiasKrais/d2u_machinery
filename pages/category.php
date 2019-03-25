<?php
$func = rex_request('func', 'string');
$entry_id = rex_request('entry_id', 'int');
$message = rex_get('message', 'string');

// Print comments
if($message != "") {
	print rex_view::success(rex_i18n::msg($message));
}

// save settings
if (filter_input(INPUT_POST, "btn_save") == 1 || filter_input(INPUT_POST, "btn_apply") == 1) {
	$form = (array) rex_post('form', 'array', []);

	// Media fields and links need special treatment
	$input_media = (array) rex_post('REX_INPUT_MEDIA', 'array', []);
	$input_media_list = (array) rex_post('REX_INPUT_MEDIALIST', 'array', []);

	$success = TRUE;
	$category = FALSE;
	$category_id = $form['category_id'];
	foreach(rex_clang::getAll() as $rex_clang) {
		if($category === FALSE) {
			$category = new Category($category_id, $rex_clang->getId());
			$category->category_id = $category_id; // Ensure correct ID in case first language has no object
			if(isset($form['parent_category_id']) && $form['parent_category_id'] > 0) {
				$category->parent_category = new Category($form['parent_category_id'], $rex_clang->getId());
			}
			else {
				$category->parent_category = FALSE;
			}
			$category->priority = $form['priority'];
			$category->pic = $input_media[1];
			$category->pic_usage = $input_media[2];

			if(\rex_addon::get("d2u_videos")->isAvailable()) {
				$video_ids = isset($form['video_ids']) ? $form['video_ids'] : [];
				$category->videos = []; // Clear video array
				foreach($video_ids as $video_id) {
					$category->videos[$video_id] = new Video($video_id, rex_config::get("d2u_helper", "default_lang"));
				}
			}

			if(rex_plugin::get("d2u_machinery", "export")->isAvailable()) {
				$category->export_machinerypark_category_id = $form['export_machinerypark_category_id'];
				$category->export_europemachinery_category_id = $form['export_europemachinery_category_id'];
				$category->export_europemachinery_category_name = $form['export_europemachinery_category_name'];
				$category->export_mascus_category_name = $form['export_mascus_category_name'];
			}
		}
		else {
			$category->clang_id = $rex_clang->getId();
		}
		$category->name = $form['lang'][$rex_clang->getId()]['name'];
		$category->teaser = $form['lang'][$rex_clang->getId()]['teaser'];
		$category->translation_needs_update = $form['lang'][$rex_clang->getId()]['translation_needs_update'];
		$category->pdfs = preg_grep('/^\s*$/s', explode(",", $input_media_list['1'. $rex_clang->getId()]), PREG_GREP_INVERT);
		$category->pic_lang = $input_media['pic_lang_'. $rex_clang->getId()];
		$category->usage_area = $form['lang'][$rex_clang->getId()]['usage_area'];
		if(rex_plugin::get("d2u_machinery", "machine_agitator_extension")->isAvailable()) {
			// Checkbox also need special treatment if empty
			$category->show_agitators = array_key_exists('show_agitators', $form) ? 'show' : 'hide';
		}
		// Sawing machines plugin fields
		if(rex_plugin::get("d2u_machinery", "machine_steel_processing_extension")->isAvailable()) {
			$category->steel_processing_saw_cutting_range_title = $form['lang'][$rex_clang->getId()]['steel_processing_saw_cutting_range_title'];
			$category->steel_processing_saw_cutting_range_file = $input_media['cutting_range_'. $rex_clang->getId()];
		}
		
		if($category->translation_needs_update == "delete") {
			$category->delete(FALSE);
		}
		else if($category->save()){
			// remember id, for each database lang object needs same id
			$category_id = $category->category_id;
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
	if(filter_input(INPUT_POST, "btn_apply") == 1 && $category !== FALSE) {
		header("Location: ". rex_url::currentBackendPage(array("entry_id"=>$category->category_id, "func"=>'edit', "message"=>$message), FALSE));
	}
	else {
		header("Location: ". rex_url::currentBackendPage(array("message"=>$message), FALSE));
	}
	exit;
}
// Delete
else if(filter_input(INPUT_POST, "btn_delete") == 1 || $func == 'delete') {
	$category_id = $entry_id;
	if($category_id == 0) {
		$form = (array) rex_post('form', 'array', []);
		$category_id = $form['category_id'];
	}
	$category = new Category($category_id, rex_config::get("d2u_helper", "default_lang"));
	$category->category_id = $category_id; // Ensure correct ID in case language has no object
	
	// Check if category is used
	$uses_machines = $category->getMachines();
	$uses_used_machines = [];
	if(rex_plugin::get("d2u_machinery", "used_machines")->isAvailable()) {
		$uses_used_machines = $category->getUsedMachines();
	}
	$uses_categories = $category->getChildren();
	
	// If not used, delete
	if(count($uses_machines) == 0 && count($uses_used_machines) == 0 && count($uses_categories) == 0) {
		$category->delete(TRUE);
	}
	else {
		$message = '<ul>';
		foreach($uses_categories as $uses_category) {
			$message .= '<li><a href="index.php?page=d2u_machinery/category&func=edit&entry_id='. $uses_category->category_id .'">'. $uses_category->name.'</a></li>';
		}
		foreach($uses_machines as $uses_machine) {
			$message .= '<li><a href="index.php?page=d2u_machinery/machine&func=edit&entry_id='. $uses_machine->machine_id .'">'. $uses_machine->name.'</a></li>';
		}
		if(rex_plugin::get("d2u_machinery", "used_machines")->isAvailable()) {
			foreach($uses_used_machines as $uses_used_machine) {
				$message .= '<li><a href="index.php?page=d2u_machinery/used_machines&func=edit&entry_id='. $uses_used_machine->used_machine_id .'">'. $uses_used_machine->name.'</a></li>';
			}
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
			<header class="panel-heading"><div class="panel-title"><?php print rex_i18n::msg('d2u_helper_category'); ?></div></header>
			<div class="panel-body">
				<input type="hidden" name="form[category_id]" value="<?php echo $entry_id; ?>">
				<?php
					foreach(rex_clang::getAll() as $rex_clang) {
						$category = new Category($entry_id, $rex_clang->getId());
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
									d2u_addon_backend_helper::form_select('d2u_helper_translation', 'form[lang]['. $rex_clang->getId() .'][translation_needs_update]', $options_translations, [$category->translation_needs_update], 1, FALSE, $readonly_lang);
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
									d2u_addon_backend_helper::form_input('d2u_helper_name', "form[lang][". $rex_clang->getId() ."][name]", $category->name, $required, $readonly_lang, "text");
									d2u_addon_backend_helper::form_input('d2u_machinery_machine_teaser', "form[lang][". $rex_clang->getId() ."][teaser]", $category->teaser, FALSE, $readonly_lang, "text");
									d2u_addon_backend_helper::form_input('d2u_machinery_category_usage_area', "form[lang][". $rex_clang->getId() ."][usage_area]", $category->usage_area, FALSE, $readonly_lang, "text");
									d2u_addon_backend_helper::form_mediafield('d2u_machinery_category_pic_lang', 'pic_lang_'. $rex_clang->getId(), $category->pic_lang, $readonly_lang);
									d2u_addon_backend_helper::form_medialistfield('d2u_machinery_category_pdfs', intval('1'. $rex_clang->getId()), $category->pdfs, $readonly_lang);
									// Sawing machines plugin fields
									if(rex_plugin::get("d2u_machinery", "machine_steel_processing_extension")->isAvailable()) {
										d2u_addon_backend_helper::form_input('d2u_machinery_steel_cutting_range_configurator_title', "form[lang][". $rex_clang->getId() ."][steel_processing_saw_cutting_range_title]", $category->steel_processing_saw_cutting_range_title, FALSE, $readonly_lang, "text");
										d2u_addon_backend_helper::form_mediafield('d2u_machinery_steel_cutting_range_configurator_file', 'cutting_range_'.$rex_clang->getId(), $category->steel_processing_saw_cutting_range_file, $readonly_lang);
									}
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
							$category = new Category($entry_id, rex_config::get("d2u_helper", "default_lang"));
							$readonly = TRUE;
							if(\rex::getUser()->isAdmin() || \rex::getUser()->hasPerm('d2u_machinery[edit_data]')) {
								$readonly = FALSE;
							}
							
							$options = array("-1"=>rex_i18n::msg('d2u_machinery_category_parent_none'));
							$selected_values = [];
							foreach(Category::getAll(rex_config::get("d2u_helper", "default_lang")) as $parent_category) {
								if(!$parent_category->isChild() && $parent_category->category_id != $category->category_id) {
									$options[$parent_category->category_id] = $parent_category->name;
								}
							}
							d2u_addon_backend_helper::form_select('d2u_machinery_category_parent', 'form[parent_category_id]', $options, ($category->parent_category === FALSE ? [] : [$category->parent_category->category_id]), 1, FALSE, $readonly);
							d2u_addon_backend_helper::form_input('header_priority', 'form[priority]', $category->priority, TRUE, $readonly, 'number');
							d2u_addon_backend_helper::form_mediafield('d2u_helper_picture', '1', $category->pic, $readonly);
							d2u_addon_backend_helper::form_mediafield('d2u_machinery_category_pic_usage', '2', $category->pic_usage, $readonly);

							if(\rex_addon::get("d2u_videos")->isAvailable()) {
								$options = [];
								foreach(Video::getAll(rex_config::get("d2u_helper", "default_lang")) as $video) {
									$options[$video->video_id] = $video->name;
								}
								d2u_addon_backend_helper::form_select('d2u_machinery_category_videos', 'form[video_ids][]', $options, array_keys($category->videos), 10, TRUE, $readonly);
							}
						?>
					</div>
				</fieldset>
				<?php
					if(rex_plugin::get("d2u_machinery", "export")->isAvailable()) {
				?>
					<fieldset>
						<legend><?php echo rex_i18n::msg('d2u_machinery_category_export'); ?></legend>
						<div class="panel-body-wrapper slide">
							<?php
								d2u_addon_backend_helper::form_input('d2u_machinery_category_export_machinerypark_category_id', "form[export_machinerypark_category_id]", $category->export_machinerypark_category_id, FALSE, $readonly, "number");
								d2u_addon_backend_helper::form_input('d2u_machinery_category_export_europemachinery_category_id', "form[export_europemachinery_category_id]", $category->export_europemachinery_category_id, FALSE, $readonly, "number");
								d2u_addon_backend_helper::form_input('d2u_machinery_category_export_europemachinery_category_name', "form[export_europemachinery_category_name]", $category->export_europemachinery_category_name, FALSE, $readonly, "text");
								d2u_addon_backend_helper::form_input('d2u_machinery_category_export_mascus_category_name', "form[export_mascus_category_name]", $category->export_mascus_category_name, FALSE, $readonly, "text");
							?>
						</div>
					</fieldset>
				<?php
					}
				?>
				<?php
					if(rex_plugin::get("d2u_machinery", "machine_agitator_extension")->isAvailable()) {
				?>
					<fieldset>
						<legend><small><i class="rex-icon fa-spoon"></i></small> <?php echo rex_i18n::msg('d2u_machinery_agitator_extension'); ?></legend>
						<div class="panel-body-wrapper slide">
							<?php
								d2u_addon_backend_helper::form_checkbox('d2u_machinery_category_show_agitators', 'form[show_agitators]', 'show', $category->show_agitators == 'show');
							?>
						</div>
					</fieldset>
				<?php
					}
				?>
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
	$query = 'SELECT categories.category_id, lang.name AS categoryname, parents_lang.name AS parentname, priority '
		. 'FROM '. \rex::getTablePrefix() .'d2u_machinery_categories AS categories '
		. 'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_categories_lang AS lang '
			. 'ON categories.category_id = lang.category_id AND lang.clang_id = '. rex_config::get("d2u_helper", "default_lang") .' '
		. 'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_categories_lang AS parents_lang '
			. 'ON categories.parent_category_id = parents_lang.category_id AND parents_lang.clang_id = '. rex_config::get("d2u_helper", "default_lang") .' ';
	if($this->getConfig('default_category_sort') == 'priority') {
		$query .= 'ORDER BY priority ASC';
	}
	else {
		$query .= 'ORDER BY categoryname ASC';
	}
    $list = rex_list::factory($query, 1000);

    $list->addTableAttribute('class', 'table-striped table-hover');

    $tdIcon = '<i class="rex-icon rex-icon-open-category"></i>';
 	$thIcon = "";
	if(\rex::getUser()->isAdmin() || \rex::getUser()->hasPerm('d2u_machinery[edit_data]')) {
		$thIcon = '<a href="' . $list->getUrl(['func' => 'add']) . '" title="' . rex_i18n::msg('add') . '"><i class="rex-icon rex-icon-add-module"></i></a>';
	}
    $list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
    $list->setColumnParams($thIcon, ['func' => 'edit', 'entry_id' => '###category_id###']);

    $list->setColumnLabel('category_id', rex_i18n::msg('id'));
    $list->setColumnLayout('category_id', ['<th class="rex-table-id">###VALUE###</th>', '<td class="rex-table-id">###VALUE###</td>']);

    $list->setColumnLabel('categoryname', rex_i18n::msg('d2u_helper_name'));
    $list->setColumnParams('categoryname', ['func' => 'edit', 'entry_id' => '###category_id###']);

    $list->setColumnLabel('parentname', rex_i18n::msg('d2u_machinery_category_parent'));

	$list->setColumnLabel('priority', rex_i18n::msg('header_priority'));

    $list->addColumn(rex_i18n::msg('module_functions'), '<i class="rex-icon rex-icon-edit"></i> ' . rex_i18n::msg('edit'));
    $list->setColumnLayout(rex_i18n::msg('module_functions'), ['<th class="rex-table-action" colspan="2">###VALUE###</th>', '<td class="rex-table-action">###VALUE###</td>']);
    $list->setColumnParams(rex_i18n::msg('module_functions'), ['func' => 'edit', 'entry_id' => '###category_id###']);

	if(\rex::getUser()->isAdmin() || \rex::getUser()->hasPerm('d2u_machinery[edit_data]')) {
		$list->addColumn(rex_i18n::msg('delete_module'), '<i class="rex-icon rex-icon-delete"></i> ' . rex_i18n::msg('delete'));
		$list->setColumnLayout(rex_i18n::msg('delete_module'), ['', '<td class="rex-table-action">###VALUE###</td>']);
		$list->setColumnParams(rex_i18n::msg('delete_module'), ['func' => 'delete', 'entry_id' => '###category_id###']);
		$list->addLinkAttribute(rex_i18n::msg('delete_module'), 'data-confirm', rex_i18n::msg('d2u_helper_confirm_delete'));
	}

    $list->setNoRowsMessage(rex_i18n::msg('d2u_helper_no_categories_found'));

    $fragment = new rex_fragment();
    $fragment->setVar('title', rex_i18n::msg('d2u_helper_categories'), false);
    $fragment->setVar('content', $list->get(), false);
    echo $fragment->parse('core/page/section.php');
}