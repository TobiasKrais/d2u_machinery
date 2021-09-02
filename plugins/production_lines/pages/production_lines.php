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
	$input_media_list = (array) rex_post('REX_INPUT_MEDIALIST', 'array', []);

	$success = TRUE;
	$production_line = FALSE;
	$production_line_id = $form['production_line_id'];
	foreach(rex_clang::getAll() as $rex_clang) {
		if($production_line === FALSE) {
			$production_line = new ProductionLine($production_line_id, $rex_clang->getId());
			$production_line->production_line_id = $production_line_id; // Ensure correct ID in case first language has no object
			$production_line->complementary_machine_ids = isset($form['complementary_machine_ids']) ? $form['complementary_machine_ids'] : [];
			$production_line->industry_sector_ids = isset($form['industry_sector_ids']) ? $form['industry_sector_ids'] : [];
			$production_line->line_code = $form['line_code'];
			$production_line->machine_ids = isset($form['machine_ids']) ? $form['machine_ids'] : [];
			$production_line->pictures = preg_grep('/^\s*$/s', explode(",", $input_media_list[1]), PREG_GREP_INVERT);
			$production_line->usp_ids = isset($form['usp_ids']) ? $form['usp_ids'] : [];
			$production_line->video_ids = isset($form['video_ids']) ? $form['video_ids'] : [];
			$production_line->online_status = $form['online_status'] == '' ? 'offline' : $form['online_status'];
		}
		else {
			$production_line->clang_id = $rex_clang->getId();
		}
		$production_line->description_long = $form['lang'][$rex_clang->getId()]['description_long'];
		$production_line->description_short = $form['lang'][$rex_clang->getId()]['description_short'];
		$production_line->name = $form['lang'][$rex_clang->getId()]['name'];
		$production_line->teaser = $form['lang'][$rex_clang->getId()]['teaser'];
		$production_line->translation_needs_update = $form['lang'][$rex_clang->getId()]['translation_needs_update'];

		if($production_line->translation_needs_update == "delete") {
			$production_line->delete(FALSE);
		}
		else if($production_line->save()) {
			// remember id, for each database lang object needs same id
			$production_line_id = $production_line->production_line_id;
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
	if(filter_input(INPUT_POST, "btn_apply") == 1 && $production_line !== FALSE) {
		header("Location: ". rex_url::currentBackendPage(["entry_id"=>$production_line->production_line_id, "func"=>'edit', "message"=>$message], FALSE));
	}
	else {
		header("Location: ". rex_url::currentBackendPage(["message"=>$message], FALSE));
	}
	exit;
}
// Delete
else if(filter_input(INPUT_POST, "btn_delete") == 1 || $func == 'delete') {
	$production_line_id = $entry_id;
	if($production_line_id == 0) {
		$form = (array) rex_post('form', 'array', []);
		$production_line_id = $form['production_line_id'];
	}
	$production_line = new ProductionLine($production_line_id, rex_config::get("d2u_helper", "default_lang"));
	$production_line->production_line_id = $production_line_id; // Ensure correct ID in case language has no object
	$production_line->delete(TRUE);
	
	$func = '';
}
// Change online status of machine
else if($func == 'changestatus') {
	$production_line = new ProductionLine($entry_id, rex_config::get("d2u_helper", "default_lang"));
	$production_line->production_line_id = $entry_id;
	$production_line->changeStatus();

	header("Location: ". rex_url::currentBackendPage());
	exit;
}

// Eingabeformular
if ($func == 'edit' || $func == 'add') {
?>
	<form action="<?php print rex_url::currentBackendPage(); ?>" method="post">
		<div class="panel panel-edit">
			<header class="panel-heading"><div class="panel-title"><?php print rex_i18n::msg('d2u_machinery_production_lines'); ?></div></header>
			<div class="panel-body">
				<input type="hidden" name="form[production_line_id]" value="<?php echo $entry_id; ?>">
				<fieldset>
					<legend><?php echo rex_i18n::msg('d2u_helper_data_all_lang'); ?></legend>
					<div class="panel-body-wrapper slide">
						<?php
							// Do not use last object from translations, because you don't know if it exists in DB
							$production_line = new ProductionLine($entry_id, rex_config::get("d2u_helper", "default_lang"));
							$readonly = TRUE;
							if(\rex::getUser()->isAdmin() || \rex::getUser()->hasPerm('d2u_machinery[edit_data]')) {
								$readonly = FALSE;
							}

							d2u_addon_backend_helper::form_input('d2u_machinery_production_lines_line_code', "form[line_code]", $production_line->line_code, FALSE, $readonly, "text");
							d2u_addon_backend_helper::form_medialistfield('d2u_helper_pictures', 1, $production_line->pictures, $readonly);
							if(\rex_addon::get("d2u_videos")->isAvailable()) {
								$options = [];
								foreach(Video::getAll(rex_config::get("d2u_helper", "default_lang")) as $video) {
									$options[$video->video_id] = $video->name;
								}
								d2u_addon_backend_helper::form_select('d2u_machinery_category_videos', 'form[video_ids][]', $options, $production_line->video_ids, 10, TRUE, $readonly);
							}
							$option_machines = [];
							foreach(Machine::getAll(rex_config::get("d2u_helper", "default_lang")) as $machine) {
								$option_machines[$machine->machine_id] = $machine->name;
							}
							d2u_addon_backend_helper::form_select('d2u_machinery_meta_machines', 'form[machine_ids][]', $option_machines, $production_line->machine_ids, 10, TRUE, $readonly);
							d2u_addon_backend_helper::form_select('d2u_machinery_production_lines_complementary_machines', 'form[complementary_machine_ids][]', $option_machines, $production_line->complementary_machine_ids, 10, TRUE, $readonly);
							if(rex_plugin::get("d2u_machinery", "industry_sectors")->isAvailable()) {
								$options_industry_sectors = [];
								foreach (IndustrySector::getAll(rex_config::get("d2u_helper", "default_lang")) as $industry_sector) {
									$options_industry_sectors[$industry_sector->industry_sector_id] = $industry_sector->name;
								}
								d2u_addon_backend_helper::form_select('d2u_machinery_industry_sectors', 'form[industry_sector_ids][]', $options_industry_sectors, $production_line->industry_sector_ids, 10, TRUE, $readonly);
							}
							$option_usps = [];
							foreach(USP::getAll(rex_config::get("d2u_helper", "default_lang")) as $usp) {
								$option_usps[$usp->usp_id] = $usp->name;
							}
							d2u_addon_backend_helper::form_select('d2u_machinery_production_lines_usp', 'form[usp_ids][]', $option_usps, $production_line->usp_ids, 10, TRUE, $readonly);
							d2u_addon_backend_helper::form_checkbox('d2u_helper_online_status', 'form[online_status]', 'online', $production_line->online_status == "online", $readonly);
						?>
					</div>
				</fieldset>
				<?php
					foreach(rex_clang::getAll() as $rex_clang) {
						$production_line = new ProductionLine($entry_id, $rex_clang->getId());
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
									d2u_addon_backend_helper::form_select('d2u_helper_translation', 'form[lang]['. $rex_clang->getId() .'][translation_needs_update]', $options_translations, [$production_line->translation_needs_update], 1, FALSE, $readonly_lang);
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
									d2u_addon_backend_helper::form_input('d2u_helper_name', "form[lang][". $rex_clang->getId() ."][name]", $production_line->name, $required, $readonly_lang, "text");
									d2u_addon_backend_helper::form_input('d2u_machinery_machine_teaser', "form[lang][". $rex_clang->getId() ."][teaser]", $production_line->teaser, FALSE, $readonly_lang, "text");
									d2u_addon_backend_helper::form_textarea('d2u_helper_description', "form[lang][". $rex_clang->getId() ."][description_short]", $production_line->description_short, 5, FALSE, $readonly_lang, TRUE);
									d2u_addon_backend_helper::form_textarea('d2u_helper_description_long', "form[lang][". $rex_clang->getId() ."][description_long]", $production_line->description_long, 5, FALSE, $readonly_lang, TRUE);
								?>
							</div>
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
	$query = 'SELECT production_lines.production_line_id, name, line_code, online_status '
		. 'FROM '. \rex::getTablePrefix() .'d2u_machinery_production_lines AS production_lines '
		. 'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_production_lines_lang AS lang '
			. 'ON production_lines.production_line_id = lang.production_line_id AND lang.clang_id = '. rex_config::get("d2u_helper", "default_lang") .' '
		. 'ORDER BY name ASC';
    $list = rex_list::factory($query, 1000);

    $list->addTableAttribute('class', 'table-striped table-hover');

    $tdIcon = '<i class="rex-icon fa-arrows-h"></i>';
 	$thIcon = "";
	if(\rex::getUser()->isAdmin() || \rex::getUser()->hasPerm('d2u_machinery[edit_data]')) {
		$thIcon = '<a href="' . $list->getUrl(['func' => 'add']) . '" title="' . rex_i18n::msg('add') . '"><i class="rex-icon rex-icon-add-module"></i></a>';
	}
    $list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
    $list->setColumnParams($thIcon, ['func' => 'edit', 'entry_id' => '###production_line_id###']);

    $list->setColumnLabel('production_line_id', rex_i18n::msg('id'));
    $list->setColumnLayout('production_line_id', ['<th class="rex-table-id">###VALUE###</th>', '<td class="rex-table-id">###VALUE###</td>']);

    $list->setColumnLabel('name', rex_i18n::msg('d2u_helper_name'));
    $list->setColumnParams('name', ['func' => 'edit', 'entry_id' => '###production_line_id###']);

    $list->setColumnLabel('line_code', rex_i18n::msg('d2u_machinery_production_lines_line_code'));
    $list->setColumnParams('line_code', ['func' => 'edit', 'entry_id' => '###production_line_id###']);

	$list->addColumn(rex_i18n::msg('module_functions'), '<i class="rex-icon rex-icon-edit"></i> ' . rex_i18n::msg('edit'));
    $list->setColumnLayout(rex_i18n::msg('module_functions'), ['<th class="rex-table-action" colspan="2">###VALUE###</th>', '<td class="rex-table-action">###VALUE###</td>']);
    $list->setColumnParams(rex_i18n::msg('module_functions'), ['func' => 'edit', 'entry_id' => '###production_line_id###']);

	$list->removeColumn('online_status');
	if(\rex::getUser()->isAdmin() || \rex::getUser()->hasPerm('d2u_machinery[edit_data]')) {
		$list->addColumn(rex_i18n::msg('status_online'), '<a class="rex-###online_status###" href="' . rex_url::currentBackendPage(['func' => 'changestatus']) . '&entry_id=###production_line_id###"><i class="rex-icon rex-icon-###online_status###"></i> ###online_status###</a>');
		$list->setColumnLayout(rex_i18n::msg('status_online'), ['', '<td class="rex-table-action">###VALUE###</td>']);

		$list->addColumn(rex_i18n::msg('delete_module'), '<i class="rex-icon rex-icon-delete"></i> ' . rex_i18n::msg('delete'));
		$list->setColumnLayout(rex_i18n::msg('delete_module'), ['', '<td class="rex-table-action">###VALUE###</td>']);
		$list->setColumnParams(rex_i18n::msg('delete_module'), ['func' => 'delete', 'entry_id' => '###production_line_id###']);
		$list->addLinkAttribute(rex_i18n::msg('delete_module'), 'data-confirm', rex_i18n::msg('d2u_helper_confirm_delete'));
	}

	$list->setNoRowsMessage(rex_i18n::msg('d2u_machinery_production_lines_no_production_lines_found'));

    $fragment = new rex_fragment();
    $fragment->setVar('title', rex_i18n::msg('d2u_machinery_production_lines'), false);
    $fragment->setVar('content', $list->get(), false);
    echo $fragment->parse('core/page/section.php');
}