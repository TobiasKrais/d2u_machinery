<?php
$func = rex_request('func', 'string');
$entry_id = rex_request('entry_id', 'int');
$message = rex_get('message', 'string');

// Print message
if($message != "") {
	print rex_view::success(rex_i18n::msg($message));
}

// save settings
if (filter_input(INPUT_POST, "btn_save") == 1 || filter_input(INPUT_POST, "btn_apply") == 1) {
	$form = (array) rex_post('form', 'array', array());

	// Media fields and links need special treatment
	$input_media_list = (array) rex_post('REX_INPUT_MEDIALIST', 'array', array());
	
	$success = TRUE;
	$used_machine = FALSE;

	$used_machine_id = $form['used_machine_id'];
	foreach(rex_clang::getAll() as $rex_clang) {
		if($used_machine === FALSE) {
			$used_machine = new UsedMachine($used_machine_id, $rex_clang->getId());
			$used_machine->name = $form['name'];
			$used_machine->category = new Category($form['category_id'], $rex_clang->getId());
			$used_machine->offer_type = $form['offer_type'];
			$used_machine->availability = $form['availability'];
			$used_machine->product_number = $form['product_number'];
			$used_machine->manufacturer = $form['manufacturer'];
			$used_machine->year_built = $form['year_built'];
			$used_machine->price = $form['price'];
			$used_machine->currency_code = $form['currency_code'];
			$used_machine->vat = $form['vat'];
			$used_machine->online_status = array_key_exists('online_status', $form) ? "online" : "offline";
			$used_machine->pics = preg_grep('/^\s*$/s', explode(",", $input_media_list[1]), PREG_GREP_INVERT);
			$used_machine->machine = $form['machine_id'] > 0 ? new Machine($form['machine_id'], $rex_clang->getId()) : FALSE;
			$used_machine->location = $form['location'];
			$used_machine->external_url = $form['external_url'];
		}
		else {
			$used_machine->clang_id = $rex_clang->getId();
		}
		$used_machine->translation_needs_update = $form['lang'][$rex_clang->getId()]['translation_needs_update'];
		$used_machine->teaser = $form['lang'][$rex_clang->getId()]['teaser'];
		$used_machine->description = $form['lang'][$rex_clang->getId()]['description'];
		$used_machine->downloads = preg_grep('/^\s*$/s', explode(",", $input_media_list[$rex_clang->getId() + 100]), PREG_GREP_INVERT);
	
		if($used_machine->translation_needs_update == "delete") {
			$used_machine->delete(FALSE);
		}
		else if($used_machine->save() > 0){
			$success = FALSE;
		}
		else {
			// remember id, for each database lang object needs same id
			$used_machine_id = $used_machine->used_machine_id;
		}
	}

	// message output
	$message = 'form_save_error';
	if($success) {
		$message = 'form_saved';
	}

	// Redirect to make reload and thus double save impossible
	if(filter_input(INPUT_POST, "btn_apply") == 1 && $used_machine !== FALSE) {
		header("Location: ". rex_url::currentBackendPage(array("entry_id"=>$used_machine->used_machine_id, "func"=>'edit', "message"=>$message), FALSE));
	}
	else {
		header("Location: ". rex_url::currentBackendPage(array("message"=>$message), FALSE));
	}
	exit;
}
// Delete
else if ($func == 'delete') {
	$used_machine_id = $entry_id;
	if($used_machine_id == 0) {
		$form = (array) rex_post('form', 'array', array());
		$used_machine_id = $form['used_machine_id'];
	}
	$used_machine = new UsedMachine($used_machine_id, rex_config::get("d2u_machinery", "default_lang"));
	
	foreach(rex_clang::getAll() as $rex_clang) {
		if($used_machine === FALSE) {
			$used_machine = new UsedMachine($used_machine_id, $rex_clang->getId());
			// If object is not found in language, set id anyway to be able to delete
			$used_machine->used_machine_id = $used_machine_id;
		}
		else {
			$used_machine->clang_id = $rex_clang->getId();
		}
		$used_machine->delete();
	}
	
	$func = '';
}
// Change online status of machine
else if($func == 'changestatus') {
	$used_machine = new UsedMachine($entry_id, rex_config::get("d2u_machinery", "default_lang"));
	$used_machine->changeStatus();
	
	header("Location: ". rex_url::currentBackendPage());
	exit;
}

// Eingabeformular
if ($func == 'edit' || $func == 'clone' || $func == 'add') {
	$used_machine = new UsedMachine($entry_id, rex_config::get("d2u_machinery", "default_lang"));
?>
	<form action="<?php print rex_url::currentBackendPage(); ?>" method="post">
		<div class="panel panel-edit">
			<header class="panel-heading"><div class="panel-title"><?php print rex_i18n::msg('d2u_machinery_used_machines_machine'); ?></div></header>
			<div class="panel-body">
				<input type="hidden" name="form[used_machine_id]" value="<?php echo ($func == 'edit' ? $entry_id : 0); ?>">
				<fieldset>
					<legend><?php echo rex_i18n::msg('d2u_helper_data_all_lang'); ?></legend>
					<div class="panel-body-wrapper slide">
						<?php
							$readonly = (rex::getUser()->isAdmin() || rex::getUser()->hasPerm('d2u_machinery[edit_tech_data]')) ? FALSE : TRUE;
							
							d2u_addon_backend_helper::form_input('d2u_machinery_used_machines_manufacturer', "form[manufacturer]", $used_machine->manufacturer, TRUE, $readonly, "text");
							d2u_addon_backend_helper::form_input('d2u_machinery_name', "form[name]", $used_machine->name, TRUE, $readonly, "text");
							$options = array();
							foreach(Category::getAll(rex_config::get("d2u_machinery", "default_lang")) as $category) {
								if($category->name != "") {
									$options[$category->category_id] = $category->name;
								}
							}
							d2u_addon_backend_helper::form_select('d2u_machinery_category', 'form[category_id]', $options, array($used_machine->category->category_id), 1, FALSE, $readonly);
							$options_offer_type = ["sale" => rex_i18n::msg('d2u_machinery_used_machines_offer_type_sale'),
								"rent" => rex_i18n::msg('d2u_machinery_used_machines_offer_type_rent')];
							d2u_addon_backend_helper::form_select('d2u_machinery_used_machines_offer_type', 'form[offer_type]', $options_offer_type, array($used_machine->offer_type), 1, FALSE, $readonly);
							d2u_addon_backend_helper::form_input('d2u_machinery_used_machines_availability', "form[availability]", $used_machine->availability, FALSE, $readonly, "date");
							d2u_addon_backend_helper::form_input('d2u_machinery_used_machines_product_number', "form[product_number]", $used_machine->product_number, FALSE, $readonly, "text");
							d2u_addon_backend_helper::form_input('d2u_machinery_used_machines_year_built', "form[year_built]", $used_machine->year_built, FALSE, $readonly, "number");
							d2u_addon_backend_helper::form_input('d2u_machinery_used_machines_price', "form[price]", $used_machine->price, FALSE, $readonly, "number");
							d2u_addon_backend_helper::form_input('d2u_machinery_used_machines_currency_code', "form[currency_code]", $used_machine->currency_code, FALSE, $readonly, "text");
							d2u_addon_backend_helper::form_input('d2u_machinery_used_machines_vat', "form[vat]", $used_machine->vat, FALSE, $readonly, "number");
							d2u_addon_backend_helper::form_checkbox('d2u_machinery_online_status', 'form[online_status]', 'online', $used_machine->online_status == "online", $readonly);
							d2u_addon_backend_helper::form_medialistfield('d2u_machinery_machine_pics', 1, $used_machine->pics, $readonly);
							$options_machines = array(0 => rex_i18n::msg('d2u_machinery_no_selection'));
							foreach(Machine::getAll(rex_config::get("d2u_machinery", "default_lang")) as $machine) {
								$options_machines[$machine->machine_id] = $machine->name;
							}
							d2u_addon_backend_helper::form_select('d2u_machinery_used_machines_linked_machine', 'form[machine_id]', $options_machines, ($used_machine->machine === FALSE ? [] : [$used_machine->machine->machine_id]), 1, FALSE, $readonly);
							d2u_addon_backend_helper::form_input('d2u_machinery_used_machines_location', "form[location]", $used_machine->location, FALSE, $readonly, "text");
							d2u_addon_backend_helper::form_input('d2u_machinery_used_machines_external_url', "form[external_url]", $used_machine->external_url, FALSE, $readonly, "text");
						?>
					</div>
				</fieldset>
				<?php
					foreach(rex_clang::getAll() as $rex_clang) {
						$used_machine_lang = new UsedMachine($entry_id, $rex_clang->getId());
						$required = $rex_clang->getId() == rex_config::get("d2u_machinery", "default_lang") ? TRUE : FALSE;
						
						$readonly_lang = TRUE;
						if(rex::getUser()->isAdmin() || (rex::getUser()->hasPerm('d2u_machinery[edit_lang]') && rex::getUser()->getComplexPerm('clang')->hasPerm($rex_clang->getId()))) {
							$readonly_lang = FALSE;
						}
				?>
					<fieldset>
						<legend><?php echo rex_i18n::msg('d2u_helper_text_lang') .' "'. $rex_clang->getName() .'"'; ?></legend>
						<div class="panel-body-wrapper slide">
							<?php
								if($rex_clang->getId() != rex_config::get("d2u_machinery", "default_lang")) {
									$options_translations = array();
									$options_translations["yes"] = rex_i18n::msg('d2u_helper_translation_needs_update');
									$options_translations["no"] = rex_i18n::msg('d2u_helper_translation_is_uptodate');
									$options_translations["delete"] = rex_i18n::msg('d2u_helper_translation_delete');
									d2u_addon_backend_helper::form_select('d2u_helper_translation', 'form[lang]['. $rex_clang->getId() .'][translation_needs_update]', $options_translations, array($used_machine_lang->translation_needs_update), 1, FALSE, $readonly_lang);
								}
								else {
									print '<input type="hidden" name="form[lang]['. $rex_clang->getId() .'][translation_needs_update]" value="no">';
								}
								d2u_addon_backend_helper::form_input('d2u_machinery_machine_teaser', "form[lang][". $rex_clang->getId() ."][teaser]", $used_machine_lang->teaser, FALSE, $readonly_lang, "text");
								d2u_addon_backend_helper::form_textarea('d2u_machinery_machine_description', "form[lang][". $rex_clang->getId() ."][description]", $used_machine_lang->description, 5, FALSE, $readonly_lang, TRUE);
								d2u_addon_backend_helper::form_medialistfield('d2u_machinery_used_machines_downloads', $rex_clang->getId() + 100, $used_machine_lang->downloads, $readonly_lang);
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
		print d2u_addon_backend_helper::getCSS();
		print d2u_addon_backend_helper::getJS();
}

if ($func == '') {
    $list = rex_list::factory('SELECT used_machine_id, manufacturer, machines.name AS machinename, year_built, categories.name AS categoryname, offer_type, online_status '
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

    $list->setColumnLabel('manufacturer', rex_i18n::msg('d2u_machinery_used_machines_manufacturer'));
    $list->setColumnParams('manufacturer', ['func' => 'edit', 'entry_id' => '###used_machine_id###']);

    $list->setColumnLabel('machinename', rex_i18n::msg('d2u_machinery_used_machines_name'));
    $list->setColumnParams('machinename', ['func' => 'edit', 'entry_id' => '###used_machine_id###']);

    $list->setColumnLabel('year_built', rex_i18n::msg('d2u_machinery_used_machines_year_built'));

	$list->setColumnLabel('categoryname', rex_i18n::msg('d2u_machinery_used_machines_category'));

	$list->setColumnLabel('offer_type', rex_i18n::msg('d2u_machinery_used_machines_offer_type'));

    $list->addColumn(rex_i18n::msg('module_functions'), '<i class="rex-icon rex-icon-edit"></i> ' . rex_i18n::msg('edit'));
    $list->setColumnLayout(rex_i18n::msg('module_functions'), ['<th class="rex-table-action" colspan="2">###VALUE###</th>', '<td class="rex-table-action">###VALUE###</td>']);
    $list->setColumnParams(rex_i18n::msg('module_functions'), ['func' => 'edit', 'entry_id' => '###used_machine_id###']);

 	$list->addColumn(rex_i18n::msg('d2u_helper_clone'), '<i class="rex-icon fa-copy"></i> ' . rex_i18n::msg('d2u_helper_clone'));
    $list->setColumnLayout(rex_i18n::msg('d2u_helper_clone'), ['', '<td class="rex-table-action">###VALUE###</td>']);
    $list->setColumnParams(rex_i18n::msg('d2u_helper_clone'), ['func' => 'clone', 'entry_id' => '###used_machine_id###']);

    $list->addColumn(rex_i18n::msg('delete_module'), '<i class="rex-icon rex-icon-delete"></i> ' . rex_i18n::msg('delete'));
    $list->setColumnLayout(rex_i18n::msg('delete_module'), ['', '<td class="rex-table-action">###VALUE###</td>']);
    $list->setColumnParams(rex_i18n::msg('delete_module'), ['func' => 'delete', 'entry_id' => '###used_machine_id###']);
    $list->addLinkAttribute(rex_i18n::msg('delete_module'), 'data-confirm', rex_i18n::msg('confirm_delete_module'));

	$list->removeColumn('online_status');
    $list->addColumn(rex_i18n::msg('status_online'), '<a class="rex-###online_status###" href="' . rex_url::currentBackendPage(['func' => 'changestatus']) . '&entry_id=###used_machine_id###"><i class="rex-icon rex-icon-###online_status###"></i> ###online_status###</a>');
	$list->setColumnLayout(rex_i18n::msg('status_online'), ['', '<td class="rex-table-action">###VALUE###</td>']);

    $list->setNoRowsMessage(rex_i18n::msg('d2u_machinery_used_machines_no_used_machines_found'));

    $fragment = new rex_fragment();
    $fragment->setVar('title', rex_i18n::msg('d2u_machinery_used_machines'), false);
    $fragment->setVar('content', $list->get(), false);
    echo $fragment->parse('core/page/section.php');
}