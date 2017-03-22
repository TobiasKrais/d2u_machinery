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
	$input_link = (array) rex_post('REX_INPUT_LINK', 'array', array());
	$input_link_list = (array) rex_post('REX_INPUT_LINKLIST', 'array', array());

	$success = TRUE;
	$machine = FALSE;
	$machine_id = $form['machine_id'];
	foreach(rex_clang::getAll() as $rex_clang) {
		if($machine === FALSE) {
			$machine = new Machine($machine_id, $rex_clang->getId());
			$machine->priority = $form['priority'];
			$machine->name = $form['name'];
			$machine->internal_name = $form['internal_name'];
			$machine->product_number = $form['product_number'];
			$machine->pics = preg_grep('/^\s*$/s', explode(",", $input_media_list[1]), PREG_GREP_INVERT);
			$machine->category = new Category($form['category_id'], $rex_clang->getId());
			$machine->alternative_machine_ids = $form['alternative_machine_ids'];
			$machine->online_status = array_key_exists('online_status', $form) ? "online" : "offline";
			$machine->article_id_service = $input_link['article_id_service'];
			$machine->article_id_software = $input_link['article_id_software'];
			$machine->article_ids_references = preg_grep('/^\s*$/s', explode(",", $input_link_list[1]), PREG_GREP_INVERT);
			$machine->engine_power = $form['engine_power'];
			$machine->length = $form['length'];
			$machine->width = $form['width'];
			$machine->height = $form['height'];
			$machine->depth = $form['depth'];
			$machine->weight = $form['weight'];
			$machine->operating_voltage_v = $form['operating_voltage_v'];
			$machine->operating_voltage_hz = $form['operating_voltage_hz'];
			$machine->operating_voltage_a = $form['operating_voltage_a'];
		
			if(rex_plugin::get("d2u_machinery", "industry_sectors")->isAvailable()) {
				$machine->industry_sector_ids = $form['industry_sector_ids'];
			}
			if(rex_plugin::get("d2u_machinery", "machine_certificates_extension")->isAvailable()) {
				$machine->certificate_ids = $form['certificate_ids'];
			}
			if(rex_plugin::get("d2u_machinery", "machine_features_extension")->isAvailable()) {
				$machine->feature_ids = $form['feature_ids'];
			}
			if(rex_plugin::get("d2u_machinery", "machine_agitator_extension")->isAvailable()) {
				$machine->agitator_type_id = $form['agitator_type_id'] == '' ? 0 : $form['agitator_type_id'];
				$machine->viscosity = $form['viscosity'] == '' ? 0 : $form['viscosity'];
			}
			if(rex_plugin::get("d2u_machinery", "machine_usage_area_extension")->isAvailable()) {
				$machine->usage_area_ids = $form['usage_area_ids'];
			}
		}
		else {
			$machine->clang_id = $rex_clang->getId();
		}
		$machine->translation_needs_update = $form['lang'][$rex_clang->getId()]['translation_needs_update'];
		$machine->teaser = $form['lang'][$rex_clang->getId()]['teaser'];
		$machine->description = $form['lang'][$rex_clang->getId()]['description'];
		$machine->pdfs = preg_grep('/^\s*$/s', explode(",", $input_media_list['1'. $rex_clang->getId()]), PREG_GREP_INVERT);
		
		if($machine->translation_needs_update == "delete") {
			$machine->delete(FALSE);
		}
		else if($machine->save() > 0){
			$success = FALSE;
		}
		else {
			// remember id, for each database lang object needs same id
			$machine_id = $machine->machine_id;
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
	$machine = new Machine($machine_id, rex_config::get("d2u_machinery", "default_lang"));
	
	// Check if object is used
	$reffering_machines = $machine->getRefferingMachines();
	$reffering_used_machines = [];
	if(rex_plugin::get("d2u_machinery", "used_machines")->isAvailable()) {
		$reffering_used_machines = $machine->getRefferingUsedMachines();
	}
	
	// If not used, delete
	if(count($reffering_machines) == 0 && count($reffering_used_machines) == 0) {
		foreach(rex_clang::getAll() as $rex_clang) {
			if($machine === FALSE) {
				$machine = new Machine($machine_id, $rex_clang->getId());
				// If object is not found in language, set id anyway to be able to delete
				$machine->machine_id = $machine_id;
			}
			else {
				$machine->clang_id = $rex_clang->getId();
			}
			$machine->delete();
		}
	}
	else {
		$message = '<ul>';
		if(count($reffering_machines) > 0) {
			foreach($reffering_machines as $reffering_machine) {
				$message .= '<li><a href="index.php?page=d2u_machinery/machine&func=edit&entry_id='. $reffering_machine->machine_id .'">'. $reffering_machine->name.'</a></li>';
			}
		}
		if(count($reffering_used_machines) > 0) {
			foreach($reffering_used_machines as $reffering_used_machine) {
				$message .= '<li><a href="index.php?page=d2u_machinery/used_machine&func=edit&entry_id='. $reffering_used_machine->used_machine_id .'">'. $reffering_used_machine->name.'</a></li>';
			}
		}
		$message .= '</ul>';

		print rex_view::error(rex_i18n::msg('d2u_machinery_could_not_delete') . $message);
	}
	
	$func = '';
}
// Change online status of machine
else if($func == 'changestatus') {
	$machine = new Machine($entry_id, rex_config::get("d2u_machinery", "default_lang"));
	$machine->changeStatus();
	
	header("Location: ". rex_url::currentBackendPage());
	exit;
}

// Eingabeformular
if ($func == 'edit' || $func == 'clone' || $func == 'add') {
	$machine = new Machine($entry_id, rex_config::get("d2u_machinery", "default_lang"));
?>
	<form action="<?php print rex_url::currentBackendPage(); ?>" method="post">
		<div class="panel panel-edit">
			<header class="panel-heading"><div class="panel-title"><?php print rex_i18n::msg('d2u_machinery_machine'); ?></div></header>
			<div class="panel-body">
				<input type="hidden" name="form[machine_id]" value="<?php echo ($func == 'edit' ? $entry_id : 0); ?>">
				<fieldset>
					<legend><?php echo rex_i18n::msg('d2u_machinery_data_all_lang'); ?></legend>
					<div class="panel-body-wrapper slide">
						<?php
							$readonly = (rex::getUser()->isAdmin() || rex::getUser()->hasPerm('d2u_machinery[edit_tech_data]')) ? FALSE : TRUE;
							
							d2u_addon_backend_helper::form_input('d2u_machinery_name', "form[name]", $machine->name, $required, $readonly_lang, "text");
							d2u_addon_backend_helper::form_input('d2u_machinery_machine_internal_name', "form[internal_name]", $machine->internal_name, $required, $readonly_lang, "text");
							d2u_addon_backend_helper::form_input('d2u_machinery_machine_product_number', "form[product_number]", $machine->product_number, $required, $readonly_lang, "text");
							d2u_addon_backend_helper::form_input('header_priority', 'form[priority]', $machine->priority, TRUE, $readonly, 'number');
							d2u_addon_backend_helper::form_medialistfield('d2u_machinery_machine_pics', 1, $machine->pics, $readonly_lang);
							$options = [];
							foreach(Category::getAll(rex_config::get("d2u_machinery", "default_lang")) as $category) {
								if($category->name != "") {
									$options[$category->category_id] = $category->name;
								}
							}
							d2u_addon_backend_helper::form_select('d2u_machinery_category', 'form[category_id]', $options, array($machine->category->category_id), 1, FALSE, $readonly);
							$options_alt_machines = [];
							foreach(Machine::getAll(rex_config::get("d2u_machinery", "default_lang")) as $alt_machine) {
								if($alt_machine->machine_id != $machine->machine_id) {
									$options_alt_machines[$alt_machine->machine_id] = $alt_machine->name;
								}
							}
							d2u_addon_backend_helper::form_select('d2u_machinery_machine_alternatives', 'form[alternative_machine_ids][]', $options_alt_machines, $machine->alternative_machine_ids, 10, TRUE, $readonly);
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
							d2u_addon_backend_helper::form_input('d2u_machinery_machine_voltage_v', "form[operating_voltage_v]", $machine->operating_voltage_v, FALSE, $readonly, "text");
							d2u_addon_backend_helper::form_input('d2u_machinery_machine_voltage_hz', "form[operating_voltage_hz]", $machine->operating_voltage_hz, FALSE, $readonly, "text");
							d2u_addon_backend_helper::form_input('d2u_machinery_machine_voltage_a', "form[operating_voltage_a]", $machine->operating_voltage_a, FALSE, $readonly, "text");
						?>
					</div>
				</fieldset>
				<?php
					if(rex_plugin::get("d2u_machinery", "industry_sectors")->isAvailable()) {
						print '<fieldset>';
						print '<legend><small><i class="rex-icon fa-industry"></i></small> '. rex_i18n::msg('d2u_machinery_industry_sectors') .'</legend>';
						print '<div class="panel-body-wrapper slide">';
						$options_industry_sectors = [];
						foreach (IndustrySector::getAll(rex_config::get("d2u_machinery", "default_lang")) as $industry_sector) {
							$options_industry_sectors[$industry_sector->industry_sector_id] = $industry_sector->name;
						}
						d2u_addon_backend_helper::form_select('d2u_machinery_industry_sectors', 'form[industry_sector_ids][]', $options_industry_sectors, $machine->industry_sector_ids, 10, TRUE, $readonly);
						print '</div>';
						print '</fieldset>';
					}
					if(rex_plugin::get("d2u_machinery", "machine_agitator_extension")->isAvailable()) {
						print '<fieldset>';
						print '<legend><small><i class="rex-icon fa-spoon"></i></small> '. rex_i18n::msg('d2u_machinery_agitator_extension') .'</legend>';
						print '<div class="panel-body-wrapper slide">';
						$options_agitator_types = array(0=>rex_i18n::msg('d2u_machinery_no_selection'));
						foreach (AgitatorType::getAll(rex_config::get("d2u_machinery", "default_lang")) as $agitator_type) {
							$options_agitator_types[$agitator_type->agitator_type_id] = $agitator_type->name;
						}
						d2u_addon_backend_helper::form_select('d2u_machinery_agitator_type', 'form[agitator_type_id]', $options_agitator_types, array($machine->agitator_type_id), 1, FALSE, $readonly);
						d2u_addon_backend_helper::form_input('d2u_machinery_agitators_viscosity', "form[viscosity]", $machine->viscosity, FALSE, $readonly, "number");
						print '</div>';
						print '</fieldset>';
					}
					if(rex_plugin::get("d2u_machinery", "machine_certificates_extension")->isAvailable()) {
						print '<fieldset>';
						print '<legend><small><i class="rex-icon fa-certificate"></i></small> '. rex_i18n::msg('d2u_machinery_certificates') .'</legend>';
						print '<div class="panel-body-wrapper slide">';
						$options_certificates = [];
						foreach (Certificate::getAll(rex_config::get("d2u_machinery", "default_lang"), $machine->certificate_ids) as $certificate) {
							$options_certificates[$certificate->certificate_id] = $certificate->name;
						}
						d2u_addon_backend_helper::form_select('d2u_machinery_certificates', 'form[certificate_ids][]', $options_certificates, $machine->certificate_ids, 10, TRUE, $readonly);
						print '</div>';
						print '</fieldset>';
					}
					if(rex_plugin::get("d2u_machinery", "machine_features_extension")->isAvailable()) {
						print '<fieldset>';
						print '<legend><small><i class="rex-icon fa-plug"></i></small> '. rex_i18n::msg('d2u_machinery_features') .'</legend>';
						print '<div class="panel-body-wrapper slide">';
						$options_features = [];
						foreach (Feature::getAll(rex_config::get("d2u_machinery", "default_lang"), $machine->category->category_id) as $feature) {
							$options_features[$feature->feature_id] = $feature->title;
						}
						d2u_addon_backend_helper::form_select('d2u_machinery_features', 'form[feature_ids][]', $options_features, $machine->feature_ids, 10, TRUE, $readonly);
						print '</div>';
						print '</fieldset>';
					}
					if(rex_plugin::get("d2u_machinery", "machine_usage_area_extension")->isAvailable()) {
						print '<fieldset>';
						print '<legend><small><i class="rex-icon fa-codepen"></i></small> '. rex_i18n::msg('d2u_machinery_usage_areas') .'</legend>';
						print '<div class="panel-body-wrapper slide">';
						$options_usage_areas = [];
						foreach (UsageArea::getAll(rex_config::get("d2u_machinery", "default_lang"), $machine->category->category_id) as $usage_area) {
							$options_usage_areas[$usage_area->usage_area_id] = $usage_area->name;
						}
						d2u_addon_backend_helper::form_select('d2u_machinery_usage_areas', 'form[usage_area_ids][]', $options_usage_areas, $machine->usage_area_ids, 10, TRUE, $readonly);
						print '</div>';
						print '</fieldset>';
					}

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
									$options_translations = [];
									$options_translations["yes"] = rex_i18n::msg('d2u_machinery_translation_needs_update');
									$options_translations["no"] = rex_i18n::msg('d2u_machinery_translation_is_uptodate');
									$options_translations["delete"] = rex_i18n::msg('d2u_machinery_translation_delete');
									d2u_addon_backend_helper::form_select('d2u_machinery_translation', 'form[lang]['. $rex_clang->getId() .'][translation_needs_update]', $options_translations, array($machine_lang->translation_needs_update), 1, FALSE, $readonly_lang);
								}
								else {
									print '<input type="hidden" name="form[lang]['. $rex_clang->getId() .'][translation_needs_update]" value="no">';
								}
								d2u_addon_backend_helper::form_textarea('d2u_machinery_machine_teaser', "form[lang][". $rex_clang->getId() ."][teaser]", $machine_lang->teaser, 3, FALSE, $readonly_lang, FALSE);
								d2u_addon_backend_helper::form_textarea('d2u_machinery_machine_description', "form[lang][". $rex_clang->getId() ."][description]", $machine_lang->description, 5, FALSE, $readonly_lang, TRUE);
								d2u_addon_backend_helper::form_medialistfield('d2u_machinery_machine_pdfs', '1'. $rex_clang->getId(), $machine_lang->pdfs, $readonly_lang);
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
	$query = 'SELECT machine.machine_id, machine.name, machine.internal_name, category.name AS categoryname, online_status, priority '
		. 'FROM '. rex::getTablePrefix() .'d2u_machinery_machines AS machine '
		. 'LEFT JOIN '. rex::getTablePrefix() .'d2u_machinery_categories_lang AS category '
			. 'ON machine.category_id = category.category_id AND category.clang_id = '. rex_config::get("d2u_machinery", "default_lang") .' ';
	if($this->getConfig('default_machine_sort') == 'priority') {
		$query .= 'ORDER BY priority ASC';
	}
	else {
		$query .= 'ORDER BY machine.name ASC';
	}
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

	$list->setColumnLabel('priority', rex_i18n::msg('header_priority'));

    $list->addColumn(rex_i18n::msg('module_functions'), '<i class="rex-icon rex-icon-edit"></i> ' . rex_i18n::msg('system_update'));
    $list->setColumnLayout(rex_i18n::msg('module_functions'), ['<th class="rex-table-action" colspan="2">###VALUE###</th>', '<td class="rex-table-action">###VALUE###</td>']);
    $list->setColumnParams(rex_i18n::msg('module_functions'), ['func' => 'edit', 'entry_id' => '###machine_id###']);

 	$list->addColumn(rex_i18n::msg('d2u_machinery_clone'), '<i class="rex-icon fa-copy"></i> ' . rex_i18n::msg('d2u_machinery_clone'));
    $list->setColumnLayout(rex_i18n::msg('d2u_machinery_clone'), ['', '<td class="rex-table-action">###VALUE###</td>']);
    $list->setColumnParams(rex_i18n::msg('d2u_machinery_clone'), ['func' => 'clone', 'entry_id' => '###machine_id###']);

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