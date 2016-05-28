<?php
$func = rex_request('func', 'string');
$entry_id = rex_request('entry_id', 'int');
$message = rex_get('message', 'string');

// 
if($message != "") {
	print rex_view::success(rex_i18n::msg($message));
}

// save settings
if (filter_input(INPUT_POST, "btn_save") == 1 || filter_input(INPUT_POST, "btn_apply") == 1) {
	$form = (array) rex_post('form', 'array', array());

	// Media fields and links need special treatment
	$input_media = (array) rex_post('REX_INPUT_MEDIA', 'array', array());
	$input_media_list = (array) rex_post('REX_INPUT_MEDIALIST', 'array', array());
	$input_link = (array) rex_post('REX_INPUT_LINK', 'array', array());
	$input_link_list = (array) rex_post('REX_INPUT_LINKLIST', 'array', array());

	$success = TRUE;
	$machine = FALSE;
	$machine_id = $form['machine_id'];
	foreach(rex_clang::getAll() as $rex_clang) {
		if($machine === FALSE) {
			$machine = new Machine($machine_id, $rex_clang->getId());
			$machine->name = $form['name'];
			$machine->internal_name = $form['internal_name'];
			$machine->product_number = $form['product_number'];
			$machine->pics = preg_grep('/^\s*$/s', explode(",", $input_media_list[1]), PREG_GREP_INVERT);
			$machine->pic_usage = $input_media[1];
			$machine->category = new Category($form['category_id'], $rex_clang->getId());
			$machine->alternative_machine_ids = preg_grep('/^\s*$/s', explode("|", $form['alternative_machine_ids']), PREG_GREP_INVERT);
			$machine->online_status = $form['online_status'];
			$machine->article_id_service = $input_link['article_id_service'];
			$machine->article_id_software = $input_link['article_id_software'];
			$machine->article_ids_references = preg_grep('/^\s*$/s', explode(",", $input_link_list[1]), PREG_GREP_INVERT);
			$machine->engine_power = $form['engine_power'];
			$machine->length = $form['length'];
			$machine->width = $form['width'];
			$machine->height = $form['height'];
			$machine->depth = $form['depth'];
			$machine->weight = $form['weight'];
			$machine->weight_empty = $form['weight_empty'];
			$machine->operating_voltage_v = $form['operating_voltage_v'];
			$machine->operating_voltage_hz = $form['operating_voltage_hz'];
			$machine->operating_voltage_a = $form['operating_voltage_a'];
			$machine->internal_name = $form['name'];
			$machine->internal_name = $form['name'];
			$machine->internal_name = $form['name'];
		}
		else {
			$machine->clang_id = $rex_clang->getId();
		}
		$machine->translation_needs_update = $form['lang'][$rex_clang->getId()]['translation_needs_update'];
		$machine->teaser = $form['lang'][$rex_clang->getId()]['teaser'];
		$machine->description = $form['lang'][$rex_clang->getId()]['description'];
		$machine->description_technical = $form['lang'][$rex_clang->getId()]['description_technical'];
		$machine->pdfs = preg_grep('/^\s*$/s', explode(",", $input_media_list['1'. $rex_clang->getId()]), PREG_GREP_INVERT);
		$machine->connection_infos = $form['lang'][$rex_clang->getId()]['connection_infos'];
		$machine->delivery_set_basic = $form['lang'][$rex_clang->getId()]['delivery_set_basic'];
		$machine->delivery_set_conversion = $form['lang'][$rex_clang->getId()]['delivery_set_conversion'];
		$machine->delivery_set_full = $form['lang'][$rex_clang->getId()]['delivery_set_full'];

		if($machine->translation_needs_update == "delete") {
			$machine->delete(FALSE);
		}
		else if($machine->save() > 0){
			$success = FALSE;
		}
		else {
			// remember category id, for each database lang object needs same category id
			$machine_id = $machine->category_id;
		}
	}

	// message output
	$message = 'form_save_error';
	if($success) {
		$message = 'form_saved';
	}
	
	// Redirect to make reload and thus double save impossible
	if(filter_input(INPUT_POST, "btn_apply") == 1 && $machine !== FALSE) {
		header("Location: ". rex_url::currentBackendPage(array("entry_id"=>$machine->machine_id, "func"=>'edit', "message"=>$message), FALSE));
	}
	else {
		header("Location: ". rex_url::currentBackendPage(array("message"=>$message), FALSE));
	}
	exit;
}
// Delete
else if(filter_input(INPUT_POST, "btn_delete") == 1 || $func == 'delete') {
	$machine_id = $entry_id;
	if($machine_id == 0) {
		$form = (array) rex_post('form', 'array', array());
		$machine_id = $form['machine_id'];
	}
	$machine = FALSE;
	foreach(rex_clang::getAll() as $rex_clang) {
		if($machine === FALSE) {
			$machine = new Machine($machine_id, $rex_clang->getId());
			// If object is not found in language, set category_id anyway to be able to delete
			$machine->machine_id = $machine_id;
		}
		else {
			$machine->clang_id = $rex_clang->getId();
		}
		$machine->delete();
	}
	
	$func = '';
}
// Change online status of machine
else if($func == 'changestatus') {
	$machine = new Machine($entry_id, rex_config::get("d2u_machinery", "default_lang"));
	$machine->changeStatus();
	
	$func = '';
}

// Eingabeformular
if ($func == 'edit' || $func == 'add') {
	$machine = new Machine($entry_id, rex_config::get("d2u_machinery", "default_lang"));
?>
	<form action="<?php print rex_url::currentBackendPage(); ?>" method="post">
		<div class="panel panel-edit">
			<header class="panel-heading"><div class="panel-title"><?php print rex_i18n::msg('d2u_machinery_machine'); ?></div></header>
			<div class="panel-body">
				<input type="hidden" name="form[machine_id]" value="<?php echo $entry_id; ?>">
				<fieldset>
					<legend><?php echo rex_i18n::msg('d2u_machinery_category_data_all_lang'); ?></legend>
					<div class="panel-body-wrapper slide">
						<?php
							$readonly = (rex::getUser()->isAdmin() || rex::getUser()->hasPerm('d2u_machinery[edit_tech_data]')) ? FALSE : TRUE;
							
							d2u_addon_backend_helper::form_input('d2u_machinery_name', "form[name]", $machine->name, $required, $readonly_lang, "text");
							d2u_addon_backend_helper::form_input('d2u_machinery_machine_internal_name', "form[internal_name]", $machine->internal_name, $required, $readonly_lang, "text");
							d2u_addon_backend_helper::form_input('d2u_machinery_machine_product_number', "form[product_number]", $machine->product_number, $required, $readonly_lang, "text");
							d2u_addon_backend_helper::form_medialistfield('d2u_machinery_machine_pics', 1, $machine->pics, $readonly_lang);
							d2u_addon_backend_helper::form_mediafield('d2u_machinery_machine_pic_usage', 1, $machine->pic_usage, $readonly);
							$options = array();
							foreach(Category::getAll(rex_config::get("d2u_machinery", "default_lang")) as $category) {
								if($category->name != "") {
									$options[$category->category_id] = $category->name;
								}
							}
							d2u_addon_backend_helper::form_select('d2u_machinery_category', 'form[category_id]', $options, $machine->category->category_id, 1, FALSE, $readonly);
							$options_alt_machines = array();
							foreach(Machine::getAll(rex_config::get("d2u_machinery", "default_lang")) as $alt_machine) {
								if($alt_machine->machine_id != $machine->machine_id) {
									$options_alt_machines[$alt_machine->machine_id] = $alt_machine->name;
								}
							}
							d2u_addon_backend_helper::form_select('d2u_machinery_machine_alternatives', 'form[alternative_machine_ids]', $options_alt_machines, $machine->alternative_machine_ids, 10, TRUE, $readonly);
							d2u_addon_backend_helper::form_checkbox('d2u_machinery_online_status', 'form[online_status]', 'online', $machine->online_status == "online", $readonly);
							d2u_addon_backend_helper::form_linkfield('d2u_machinery_machine_software', 'article_id_software', $machine->article_id_software, rex_config::get("d2u_machinery", "default_lang"), $readonly);
							d2u_addon_backend_helper::form_linkfield('d2u_machinery_machine_service', 'article_id_service', $machine->article_id_service, rex_config::get("d2u_machinery", "default_lang"), $readonly);
							d2u_addon_backend_helper::form_linklistfield('d2u_machinery_machine_references', 1, $machine->article_ids_references, rex_config::get("d2u_machinery", "default_lang"), $readonly);
							d2u_addon_backend_helper::form_input('d2u_machinery_machine_engine_power', "form[engine_power]", $machine->engine_power, FALSE, $readonly, "text");
							d2u_addon_backend_helper::form_input('d2u_machinery_machine_length', "form[length]", $machine->length, FALSE, $readonly, "number");
							d2u_addon_backend_helper::form_input('d2u_machinery_machine_width', "form[width]", $machine->width, FALSE, $readonly, "number");
							d2u_addon_backend_helper::form_input('d2u_machinery_machine_height', "form[height]", $machine->height, FALSE, $readonly, "number");
							d2u_addon_backend_helper::form_input('d2u_machinery_machine_depth', "form[depth]", $machine->depth, FALSE, $readonly, "number");
							d2u_addon_backend_helper::form_input('d2u_machinery_machine_weight', "form[weight]", $machine->weight, FALSE, $readonly, "text");
							d2u_addon_backend_helper::form_input('d2u_machinery_machine_weight_empty', "form[weight_empty]", $machine->weight_empty, FALSE, $readonly, "text");
							d2u_addon_backend_helper::form_input('d2u_machinery_machine_voltage_v', "form[operating_voltage_v]", $machine->operating_voltage_v, FALSE, $readonly, "text");
							d2u_addon_backend_helper::form_input('d2u_machinery_machine_voltage_hz', "form[operating_voltage_hz]", $machine->operating_voltage_hz, FALSE, $readonly, "text");
							d2u_addon_backend_helper::form_input('d2u_machinery_machine_voltage_a', "form[operating_voltage_a]", $machine->operating_voltage_a, FALSE, $readonly, "text");
						?>
					</div>
				</fieldset>
				<?php
					foreach(rex_clang::getAll() as $rex_clang) {
						$machine_lang = new Machine($entry_id, $rex_clang->getId());
						$required = $rex_clang->getId() == rex_config::get("d2u_machinery", "default_lang") ? TRUE : FALSE;
						
						$readonly_lang = TRUE;
						if(rex::getUser()->isAdmin() || (rex::getUser()->hasPerm('d2u_machinery[edit_lang]') && rex::getUser()->getComplexPerm('clang')->hasPerm($rex_clang->getId()))) {
							$readonly_lang = FALSE;
						}
				?>
					<fieldset>
						<legend><?php echo rex_i18n::msg('d2u_machinery_text_lang') .' "'. $rex_clang->getName() .'"'; ?></legend>
						<div class="panel-body-wrapper slide">
							<?php
								if($rex_clang->getId() != rex_config::get("d2u_machinery", "default_lang")) {
									$options_translations = array();
									$options_translations["yes"] = rex_i18n::msg('d2u_machinery_translation_needs_update');
									$options_translations["no"] = rex_i18n::msg('d2u_machinery_translation_is_uptodate');
									$options_translations["delete"] = rex_i18n::msg('d2u_machinery_translation_delete');
									d2u_addon_backend_helper::form_select('d2u_machinery_translation', 'form[lang]['. $rex_clang->getId() .'][translation_needs_update]', $options_translations, array($machine_lang->translation_needs_update), 1, FALSE, $readonly_lang);
								}
								else {
									print '<input type="hidden" name="form[lang]['. $rex_clang->getId() .'][translation_needs_update]" value="no">';
								}
								d2u_addon_backend_helper::form_input('d2u_machinery_machine_teaser', "form[lang][". $rex_clang->getId() ."][teaser]", $machine_lang->teaser, FALSE, $readonly_lang, "text");
								d2u_addon_backend_helper::form_textarea('d2u_machinery_machine_description', "form[lang][". $rex_clang->getId() ."][description]", $machine_lang->description, 5, FALSE, $readonly_lang, TRUE);
								d2u_addon_backend_helper::form_textarea('d2u_machinery_machine_description_tech', "form[lang][". $rex_clang->getId() ."][description_technical]", $machine_lang->description_technical, 5, FALSE, $readonly_lang, TRUE);
								d2u_addon_backend_helper::form_medialistfield('d2u_machinery_machine_pdfs', '1'. $rex_clang->getId(), $machine_lang->pdfs, $readonly_lang);
								d2u_addon_backend_helper::form_input('d2u_machinery_machine_connection_infos', "form[lang][". $rex_clang->getId() ."][connection_infos]", $machine_lang->connection_infos, FALSE, $readonly, "text");
								d2u_addon_backend_helper::form_input('d2u_machinery_machine_delivery_set_basic', "form[lang][". $rex_clang->getId() ."][delivery_set_basic]", $machine_lang->delivery_set_basic, FALSE, $readonly, "text");
								d2u_addon_backend_helper::form_input('d2u_machinery_machine_delivery_set_conversion', "form[lang][". $rex_clang->getId() ."][delivery_set_conversion]", $machine_lang->delivery_set_conversion, FALSE, $readonly, "text");
								d2u_addon_backend_helper::form_input('d2u_machinery_machine_delivery_set_full', "form[lang][". $rex_clang->getId() ."][delivery_set_full]", $machine_lang->delivery_set_full, FALSE, $readonly, "text");
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
						<button class="btn btn-delete" type="submit" name="btn_delete" formnovalidate="formnovalidate" data-confirm="<?php echo rex_i18n::msg('form_delete'); ?>?" value="1"><?php echo rex_i18n::msg('form_delete'); ?></button>
					</div>
				</div>
			</footer>
		</div>
	</form>
	<br>
	<?php
		print d2u_addon_backend_helper::getCSS('d2u_machinery');
		print d2u_addon_backend_helper::getJS();
}

if ($func == '') {
	$query = 'SELECT machine.machine_id, machine.name, machine.internal_name, category.name AS categoryname, online_status '
		. 'FROM '. rex::getTablePrefix() .'d2u_machinery_machines AS machine '
		. 'LEFT JOIN '. rex::getTablePrefix() .'d2u_machinery_categories_lang AS category '
			. 'ON machine.category_id = category.category_id AND category.clang_id = '. rex_config::get("d2u_machinery", "default_lang") .' '
		. 'ORDER BY machine.name ASC';
    $list = rex_list::factory($query);

    $list->addTableAttribute('class', 'table-striped table-hover');

    $tdIcon = '<i class="rex-icon rex-icon-module"></i>';
    $thIcon = '<a href="' . $list->getUrl(['func' => 'add']) . '" title="' . rex_i18n::msg('add') . '"><i class="rex-icon rex-icon-add-module"></i></a>';
    $list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
    $list->setColumnParams($thIcon, ['func' => 'edit', 'entry_id' => '###machine_id###']);

    $list->setColumnLabel('machine_id', rex_i18n::msg('id'));

    $list->setColumnLabel('name', rex_i18n::msg('d2u_machinery_machine_name'));
    $list->setColumnParams('name', ['func' => 'edit', 'entry_id' => '###machine_id###']);

	$list->setColumnLabel('internal_name', rex_i18n::msg('d2u_machinery_machine_internal_name'));

	$list->setColumnLabel('categoryname', rex_i18n::msg('d2u_machinery_name'));

    $list->addColumn(rex_i18n::msg('module_functions'), '<i class="rex-icon rex-icon-edit"></i> ' . rex_i18n::msg('system_update'));
    $list->setColumnLayout(rex_i18n::msg('module_functions'), ['<th class="rex-table-action" colspan="2">###VALUE###</th>', '<td class="rex-table-action">###VALUE###</td>']);
    $list->setColumnParams(rex_i18n::msg('module_functions'), ['func' => 'edit', 'entry_id' => '###machine_id###']);

    $list->addColumn(rex_i18n::msg('delete_module'), '<i class="rex-icon rex-icon-delete"></i> ' . rex_i18n::msg('delete'));
	$list->setColumnLayout(rex_i18n::msg('delete_module'), ['', '<td class="rex-table-action">###VALUE###</td>']);
    $list->setColumnParams(rex_i18n::msg('delete_module'), ['func' => 'delete', 'entry_id' => '###machine_id###']);
    $list->addLinkAttribute(rex_i18n::msg('delete_module'), 'data-confirm', rex_i18n::msg('d2u_machinery_confirm_delete'));

	$list->removeColumn('online_status');
    $list->addColumn(rex_i18n::msg('status_online'), '<a class="rex-###online_status###" href="' . rex_url::currentBackendPage(['func' => 'changestatus']) . '&entry_id=###machine_id###"><i class="rex-icon rex-icon-###online_status###"></i> ###online_status###</a>');
	$list->setColumnLayout(rex_i18n::msg('status_online'), ['', '<td class="rex-table-action">###VALUE###</td>']);

    $list->setNoRowsMessage(rex_i18n::msg('d2u_machinery_machine_no_machines_found'));

    $fragment = new rex_fragment();
    $fragment->setVar('title', rex_i18n::msg('d2u_machinery_meta_machines'), false);
    $fragment->setVar('content', $list->get(), false);
    echo $fragment->parse('core/page/section.php');
}