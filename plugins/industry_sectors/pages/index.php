<?php
$func = rex_request('func', 'string');
$entry_id = rex_request('entry_id', 'int');
$message = rex_get('message', 'string');

// messages
if($message !== '') {
	print rex_view::success(rex_i18n::msg($message));
}

// save settings
if (intval(filter_input(INPUT_POST, "btn_save")) === 1 || intval(filter_input(INPUT_POST, "btn_apply")) === 1) {
	$form = rex_post('form', 'array', []);

	// Media fields and links need special treatment
	$input_media = rex_post('REX_INPUT_MEDIA', 'array', []);

	$success = true;
	$industry_sector = false;
	$industry_sector_id = $form['industry_sector_id'];
	foreach(rex_clang::getAll() as $rex_clang) {
		if($industry_sector === false) {
			$industry_sector = new IndustrySector($industry_sector_id, $rex_clang->getId());
			$industry_sector->industry_sector_id = $industry_sector_id; // Ensure correct ID in case first language has no object
			$industry_sector->online_status = $form['online_status'] === '' ? 'offline' : $form['online_status'];
			$industry_sector->icon = $input_media[2];
			$industry_sector->pic = $input_media[1];
		}
		else {
			$industry_sector->clang_id = $rex_clang->getId();
		}
		$industry_sector->name = $form['lang'][$rex_clang->getId()]['name'];
		$industry_sector->teaser = $form['lang'][$rex_clang->getId()]['teaser'];
		$industry_sector->description = $form['lang'][$rex_clang->getId()]['description'];
		$industry_sector->translation_needs_update = $form['lang'][$rex_clang->getId()]['translation_needs_update'];

		if($industry_sector->translation_needs_update === "delete") {
			$industry_sector->delete(false);
		}
		else if($industry_sector->save()) {
			// remember id, for each database lang object needs same id
			$industry_sector_id = $industry_sector->industry_sector_id;
		}
		else {
			$success = false;
		}
	}

	// message output
	$message = 'form_save_error';
	if($success) {
		$message = 'form_saved';
	}
	
	// Redirect to make reload and thus double save impossible
	if(intval(filter_input(INPUT_POST, "btn_apply", FILTER_VALIDATE_INT)) === 1 &&$industry_sector !== false) {
		header("Location: ". rex_url::currentBackendPage(array("entry_id"=>$industry_sector->industry_sector_id, "func"=>'edit', "message"=>$message), false));
	}
	else {
		header("Location: ". rex_url::currentBackendPage(["message"=>$message], false));
	}
	exit;
}
// Delete
else if(intval(filter_input(INPUT_POST, "btn_delete", FILTER_VALIDATE_INT)) === 1 || $func === 'delete') {
	$industry_sector_id = $entry_id;
	if($industry_sector_id === 0) {
		$form = rex_post('form', 'array', []);
		$industry_sector_id = $form['industry_sector_id'];
	}
	$industry_sector = new IndustrySector($industry_sector_id, intval(rex_config::get("d2u_helper", "default_lang")));
	$industry_sector->industry_sector_id = $industry_sector_id; // Ensure correct ID in case language has no object
	
	// Check if object is used
	$referring_machines = $industry_sector->getMachines();

	// If not used, delete
	if(count($referring_machines) === 0) {
		$industry_sector->delete(true);
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
else if($func === 'changestatus') {
	$industry_sector = new IndustrySector($entry_id, intval(rex_config::get("d2u_helper", "default_lang")));
	$industry_sector->industry_sector_id = $entry_id;
	$industry_sector->changeStatus();

	header("Location: ". rex_url::currentBackendPage());
	exit;
}

// Eingabeformular
if ($func === 'edit' || $func === 'add') {
?>
	<form action="<?php print rex_url::currentBackendPage(); ?>" method="post">
		<div class="panel panel-edit">
			<header class="panel-heading"><div class="panel-title"><?php print rex_i18n::msg('d2u_machinery_industry_sectors'); ?></div></header>
			<div class="panel-body">
				<input type="hidden" name="form[industry_sector_id]" value="<?php echo $entry_id; ?>">
				<?php
					foreach(rex_clang::getAll() as $rex_clang) {
						$industry_sector = new IndustrySector($entry_id, $rex_clang->getId());
						$required = $rex_clang->getId() === intval(rex_config::get("d2u_helper", "default_lang")) ? true : false;
						
						$readonly_lang = true;
						if(\rex::getUser() instanceof rex_user && (\rex::getUser()->isAdmin() || (\rex::getUser()->hasPerm('d2u_machinery[edit_lang]') && \rex::getUser()->getComplexPerm('clang') instanceof rex_clang_perm && \rex::getUser()->getComplexPerm('clang')->hasPerm($rex_clang->getId())))) {
							$readonly_lang = false;
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
									d2u_addon_backend_helper::form_select('d2u_helper_translation', 'form[lang]['. $rex_clang->getId() .'][translation_needs_update]', $options_translations, [$industry_sector->translation_needs_update], 1, false, $readonly_lang);
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
									d2u_addon_backend_helper::form_input('d2u_helper_name', "form[lang][". $rex_clang->getId() ."][name]", $industry_sector->name, $required, $readonly_lang, "text");
									d2u_addon_backend_helper::form_input('d2u_machinery_machine_teaser', "form[lang][". $rex_clang->getId() ."][teaser]", $industry_sector->teaser, false, $readonly_lang, "text");
									d2u_addon_backend_helper::form_textarea('d2u_helper_description', "form[lang][". $rex_clang->getId() ."][description]", $industry_sector->description, 5, false, $readonly_lang, true);
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
							$industry_sector = new IndustrySector($entry_id, intval(rex_config::get("d2u_helper", "default_lang")));
							$readonly = true;
							if(\rex::getUser() instanceof rex_user && (\rex::getUser()->isAdmin() || \rex::getUser()->hasPerm('d2u_machinery[edit_data]'))) {
								$readonly = false;
							}

							d2u_addon_backend_helper::form_mediafield('d2u_helper_icon', '2', $industry_sector->icon, $readonly);
							d2u_addon_backend_helper::form_mediafield('d2u_helper_picture', '1', $industry_sector->pic, $readonly);
							d2u_addon_backend_helper::form_checkbox('d2u_helper_online_status', 'form[online_status]', 'online', $industry_sector->online_status === "online", $readonly);
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

if ($func === '') {
	$query = 'SELECT industry_sectors.industry_sector_id, name, online_status '
		. 'FROM '. \rex::getTablePrefix() .'d2u_machinery_industry_sectors AS industry_sectors '
		. 'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_industry_sectors_lang AS lang '
			. 'ON industry_sectors.industry_sector_id = lang.industry_sector_id AND lang.clang_id = '. intval(rex_config::get("d2u_helper", "default_lang")) .' '
		. 'ORDER BY name ASC';
    $list = rex_list::factory($query, 1000);

    $list->addTableAttribute('class', 'table-striped table-hover');

    $tdIcon = '<i class="rex-icon fa-industry"></i>';
 	$thIcon = "";
	if(\rex::getUser() instanceof rex_user && (\rex::getUser()->isAdmin() || \rex::getUser()->hasPerm('d2u_machinery[edit_data]'))) {
		$thIcon = '<a href="' . $list->getUrl(['func' => 'add']) . '" title="' . rex_i18n::msg('add') . '"><i class="rex-icon rex-icon-add-module"></i></a>';
	}
    $list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
    $list->setColumnParams($thIcon, ['func' => 'edit', 'entry_id' => '###industry_sector_id###']);

    $list->setColumnLabel('industry_sector_id', rex_i18n::msg('id'));
    $list->setColumnLayout('industry_sector_id', ['<th class="rex-table-id">###VALUE###</th>', '<td class="rex-table-id">###VALUE###</td>']);

    $list->setColumnLabel('name', rex_i18n::msg('d2u_helper_name'));
    $list->setColumnParams('name', ['func' => 'edit', 'entry_id' => '###industry_sector_id###']);

    $list->addColumn(rex_i18n::msg('module_functions'), '<i class="rex-icon rex-icon-edit"></i> ' . rex_i18n::msg('edit'));
    $list->setColumnLayout(rex_i18n::msg('module_functions'), ['<th class="rex-table-action" colspan="2">###VALUE###</th>', '<td class="rex-table-action">###VALUE###</td>']);
    $list->setColumnParams(rex_i18n::msg('module_functions'), ['func' => 'edit', 'entry_id' => '###industry_sector_id###']);

	$list->removeColumn('online_status');
	if(\rex::getUser() instanceof rex_user && (\rex::getUser()->isAdmin() || \rex::getUser()->hasPerm('d2u_machinery[edit_data]'))) {
		$list->addColumn(rex_i18n::msg('status_online'), '<a class="rex-###online_status###" href="' . rex_url::currentBackendPage(['func' => 'changestatus']) . '&entry_id=###industry_sector_id###"><i class="rex-icon rex-icon-###online_status###"></i> ###online_status###</a>');
		$list->setColumnLayout(rex_i18n::msg('status_online'), ['', '<td class="rex-table-action">###VALUE###</td>']);

		$list->addColumn(rex_i18n::msg('delete_module'), '<i class="rex-icon rex-icon-delete"></i> ' . rex_i18n::msg('delete'));
		$list->setColumnLayout(rex_i18n::msg('delete_module'), ['', '<td class="rex-table-action">###VALUE###</td>']);
		$list->setColumnParams(rex_i18n::msg('delete_module'), ['func' => 'delete', 'entry_id' => '###industry_sector_id###']);
		$list->addLinkAttribute(rex_i18n::msg('delete_module'), 'data-confirm', rex_i18n::msg('d2u_helper_confirm_delete'));
	}

	$list->setNoRowsMessage(rex_i18n::msg('d2u_machinery_industry_sectors_no_industry_sectors_found'));

    $fragment = new rex_fragment();
    $fragment->setVar('title', rex_i18n::msg('d2u_machinery_industry_sectors'), false);
    $fragment->setVar('content', $list->get(), false);
    echo $fragment->parse('core/page/section.php');
}