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
			if(isset($form['category_id'])) {
				$machine->category = new Category($form['category_id'], $rex_clang->getId());
			}
			$machine->alternative_machine_ids = isset($form['alternative_machine_ids']) ? $form['alternative_machine_ids'] : [];
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
			if(rex_plugin::get("d2u_machinery", "machine_agitator_extension")->isAvailable()) {
				$machine->agitator_type_id = $form['agitator_type_id'] == '' ? 0 : $form['agitator_type_id'];
				$machine->viscosity = $form['viscosity'] == '' ? 0 : $form['viscosity'];
			}
			if(rex_plugin::get("d2u_machinery", "machine_certificates_extension")->isAvailable()) {
				$machine->certificate_ids = isset($form['certificate_ids']) ? $form['certificate_ids'] : [];
			}
			if(rex_plugin::get("d2u_machinery", "machine_features_extension")->isAvailable()) {
				$machine->feature_ids = isset($form['feature_ids']) ? $form['feature_ids'] : [];
			}
			if(rex_plugin::get("d2u_machinery", "machine_steel_processing_extension")->isAvailable()) {
				$process_ids = isset($form['process_ids']) ? $form['process_ids'] : [];
				foreach($process_ids as $process_id) {
					$machine->processes[$process_id] = new Process($process_id, rex_config::get("d2u_machinery", "default_lang"));
				}
				$procedure_ids = isset($form['procedure_ids']) ? $form['procedure_ids'] : [];
				foreach($procedure_ids as $procedure_id) {
					$machine->procedures[$procedure_id] = new Procedure($procedure_id, rex_config::get("d2u_machinery", "default_lang"));
				}
				$material_ids = isset($form['material_ids']) ? $form['material_ids'] : [];
				foreach($material_ids as $material_id) {
					$machine->materials[$material_id] = new Procedure($material_id, rex_config::get("d2u_machinery", "default_lang"));
				}
				$tool_ids = isset($form['tool_ids']) ? $form['tool_ids'] : [];
				foreach($tool_ids as $tool_id) {
					$machine->tools[$tool_id] = new Procedure($tool_id, rex_config::get("d2u_machinery", "default_lang"));
				}
				$machine->automation_supply_single_stroke = $form['automation_supply_single_stroke'];
				$machine->automation_supply_multi_stroke = $form['automation_supply_multi_stroke'];
				$machine->automation_feedrate = $form['automation_feedrate'];
				$machine->automation_rush_leader_flyback = $form['automation_rush_leader_flyback'];
				$automation_automationgrade_ids = isset($form['automation_automationgrade_ids']) ? $form['automation_automationgrade_ids'] : [];
				foreach($automation_automationgrade_ids as $automation_automationgrade_id) {
					$machine->automation_automationgrades[$automation_automationgrade_id] = new Automation($automation_automationgrade_id, rex_config::get("d2u_machinery", "default_lang"));
				}
				$automation_supply_ids = isset($form['automation_supply_ids']) ? $form['automation_supply_ids'] : [];
				foreach($automation_supply_ids as $automation_supply_id) {
					$machine->automation_supplys[$automation_supply_id] = new Supply($automation_supply_id, rex_config::get("d2u_machinery", "default_lang"));
				}
				$machine->workspace = $form['workspace'];
				$machine->workspace_square = $form['workspace_square'];
				$machine->workspace_flat = $form['workspace_flat'];
				$machine->workspace_plate = $form['workspace_plate'];
				$machine->workspace_profile = $form['workspace_profile'];
				$machine->workspace_angle_steel = $form['workspace_angle_steel'];
				$machine->workspace_round = $form['workspace_round'];
				$machine->workspace_min = $form['workspace_min'];
				$machine->sheet_width = $form['sheet_width'];
				$machine->sheet_length = $form['sheet_length'];
				$machine->sheet_thickness = $form['sheet_thickness'];
				$machine->tool_changer_locations = $form['tool_changer_locations'];
				$machine->drilling_unit_vertical = $form['drilling_unit_vertical'];
				$machine->drilling_unit_horizontal = $form['drilling_unit_horizontal'];
				$machine->drilling_diameter = $form['drilling_diameter'];
				$machine->drilling_tools_axis = $form['drilling_tools_axis'];
				$machine->drilling_axis_drive_power = $form['drilling_axis_drive_power'];
				$machine->drilling_rpm_speed = $form['drilling_rpm_speed'];
				$machine->saw_blade = $form['saw_blade'];
				$machine->saw_band = $form['saw_band'];
				$machine->saw_band_tilt = $form['saw_band_tilt'];
				$machine->saw_cutting_speed = $form['saw_cutting_speed'];
				$machine->saw_miter = $form['saw_miter'];
				$machine->bevel_angle = $form['bevel_angle'] == "" ? 0 : $form['bevel_angle'];
				$machine->punching_diameter = $form['punching_diameter'];
				$machine->punching_power = $form['punching_power'] == "" ? 0 : $form['punching_power'];
				$machine->punching_tools = $form['punching_tools'];
				$machine->shaving_unit_angle_steel_single_cut = $form['shaving_unit_angle_steel_single_cut'] == "" ? 0 : $form['shaving_unit_angle_steel_single_cut'];
				$profile_ids = isset($form['profile_ids']) ? $form['profile_ids'] : [];
				foreach($profile_ids as $profile_id) {
					$machine->profiles[$profile_id] = new Profile($profile_id, rex_config::get("d2u_machinery", "default_lang"));
				}
				$machine->carrier_width = $form['carrier_width'];
				$machine->carrier_height = $form['carrier_height'];
				$machine->carrier_weight = $form['carrier_weight'] == "" ? 0 : $form['carrier_weight'];
				$machine->flange_thickness = $form['flange_thickness'];
				$machine->web_thickness = $form['web_thickness'];
				$machine->component_length = $form['component_length'];
				$machine->component_weight = $form['component_weight'] == "" ? 0 : $form['component_weight'];
				$welding_process_ids = isset($form['welding_process_ids']) ? $form['welding_process_ids'] : [];
				foreach($welding_process_ids as $welding_process_id) {
					$machine->weldings[$welding_process_id] = new Welding($welding_process_id, rex_config::get("d2u_machinery", "default_lang"));
				}
				$machine->welding_thickness = $form['welding_thickness'] == "" ? 0 : $form['welding_thickness'];
				$machine->welding_wire_thickness = $form['welding_wire_thickness'];
				$machine->beam_continuous_opening = $form['beam_continuous_opening'];
				$machine->beam_turbines = $form['beam_turbines'] == "" ? 0 : $form['beam_turbines'];
				$machine->beam_turbine_power = $form['beam_turbine_power'];
				$machine->beam_color_guns = $form['beam_color_guns'];
			}
			if(rex_plugin::get("d2u_machinery", "machine_usage_area_extension")->isAvailable()) {
				$machine->usage_area_ids = $form['usage_area_ids'];
			}
			
			if(rex_addon::get("d2u_videos")->isAvailable()) {
				$video_ids = isset($form['video_ids']) ? $form['video_ids'] : [];
				$machine->videos = []; // Clear video array
				foreach($video_ids as $video_id) {
					$machine->videos[$video_id] = new Video($video_id, rex_config::get("d2u_machinery", "default_lang"));
				}
			}
		}
		else {
			$machine->clang_id = $rex_clang->getId();
		}
		$machine->translation_needs_update = $form['lang'][$rex_clang->getId()]['translation_needs_update'];
		$machine->lang_name = $form['lang'][$rex_clang->getId()]['lang_name'];
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
					<legend><?php echo rex_i18n::msg('d2u_helper_data_all_lang'); ?></legend>
					<div class="panel-body-wrapper slide">
						<?php
							$readonly = (rex::getUser()->isAdmin() || rex::getUser()->hasPerm('d2u_machinery[edit_tech_data]')) ? FALSE : TRUE;
							
							d2u_addon_backend_helper::form_input('d2u_machinery_name', "form[name]", $machine->name, TRUE, $readonly, "text");
							d2u_addon_backend_helper::form_input('d2u_machinery_internal_name', "form[internal_name]", $machine->internal_name, FALSE, $readonly, "text");
							d2u_addon_backend_helper::form_input('d2u_machinery_machine_product_number', "form[product_number]", $machine->product_number, FALSE, $readonly, "text");
							d2u_addon_backend_helper::form_input('header_priority', 'form[priority]', $machine->priority, TRUE, $readonly, 'number');
							d2u_addon_backend_helper::form_medialistfield('d2u_machinery_machine_pics', 1, $machine->pics, $readonly);
							$options = [];
							foreach(Category::getAll(rex_config::get("d2u_machinery", "default_lang")) as $category) {
								if($category->name != "") {
									$options[$category->category_id] = $category->name;
								}
							}
							d2u_addon_backend_helper::form_select('d2u_machinery_category', 'form[category_id]', $options, isset($machine->category->category_id) ? [$machine->category->category_id] : [], 1, FALSE, $readonly);
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
							if(rex_addon::get("d2u_videos")->isAvailable()) {
								$options = [];
								foreach(Video::getAll(rex_config::get("d2u_machinery", "default_lang")) as $video) {
									$options[$video->video_id] = $video->name;
								}
								d2u_addon_backend_helper::form_select('d2u_machinery_category_videos', 'form[video_ids][]', $options, array_keys($machine->videos), 10, TRUE, $readonly);
							}

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
					if(rex_plugin::get("d2u_machinery", "machine_steel_processing_extension")->isAvailable()) {
						print '<fieldset>';
						print '<legend><small><i class="rex-icon fa-steam"></i></small> '. rex_i18n::msg('d2u_machinery_machine_steel_extension') .'</legend>';
						print '<div class="panel-body-wrapper slide">';
						$options_processes = [];
						foreach (Process::getAll(rex_config::get("d2u_machinery", "default_lang")) as $process) {
							$options_processes[$process->process_id] = $process->name;
						}
						d2u_addon_backend_helper::form_select('d2u_machinery_steel_processes', 'form[process_ids][]', $options_processes, array_keys($machine->processes), 4, TRUE, $readonly);
						$options_procedures = [];
						foreach (Procedure::getAll(rex_config::get("d2u_machinery", "default_lang")) as $procedure) {
							$options_procedures[$procedure->procedure_id] = $procedure->name;
						}
						d2u_addon_backend_helper::form_select('d2u_machinery_steel_procedures', 'form[procedure_ids][]', $options_procedures, array_keys($machine->procedures), 4, TRUE, $readonly);
						$options_materials = [];
						foreach (Material::getAll(rex_config::get("d2u_machinery", "default_lang")) as $material) {
							$options_materials[$material->material_id] = $material->name;
						}
						d2u_addon_backend_helper::form_select('d2u_machinery_steel_material_class', 'form[material_ids][]', $options_materials, array_keys($machine->materials), 4, TRUE, $readonly);
						$options_tools = [];
						foreach (Tool::getAll(rex_config::get("d2u_machinery", "default_lang")) as $tool) {
							$options_tools[$tool->tool_id] = $tool->name;
						}
						d2u_addon_backend_helper::form_select('d2u_machinery_steel_tools', 'form[tool_ids][]', $options_tools, array_keys($machine->tools), 4, TRUE, $readonly);
						print '</div>';
						print '</fieldset>';
						
						print '<fieldset>';
						print '<legend><small><i class="rex-icon fa-steam"></i></small> '. rex_i18n::msg('d2u_machinery_steel_automation') .'</legend>';
						print '<div class="panel-body-wrapper slide">';
						d2u_addon_backend_helper::form_input('d2u_machinery_steel_automation_supply_single_stroke', 'form[automation_supply_single_stroke]', $machine->automation_supply_single_stroke, FALSE, $readonly, "text");
						d2u_addon_backend_helper::form_input('d2u_machinery_steel_automation_supply_multi_stroke', 'form[automation_supply_multi_stroke]', $machine->automation_supply_multi_stroke, FALSE, $readonly, "text");
						d2u_addon_backend_helper::form_input('d2u_machinery_steel_automation_feedrate', 'form[automation_feedrate]', $machine->automation_feedrate, FALSE, $readonly, "text");
						d2u_addon_backend_helper::form_input('d2u_machinery_steel_automation_rush_leader_flyback', 'form[automation_rush_leader_flyback]', $machine->automation_rush_leader_flyback, FALSE, $readonly, "text");
						$options_automation = [];
						foreach (Automation::getAll(rex_config::get("d2u_machinery", "default_lang")) as $automation) {
							$options_automation[$automation->automation_id] = $automation->name;
						}
						d2u_addon_backend_helper::form_select('d2u_machinery_steel_automation_automationgrades', 'form[automation_automationgrade_ids][]', $options_automation, array_keys($machine->automation_automationgrades), 4, TRUE, $readonly);
						$options_supply = [];
						foreach (Supply::getAll(rex_config::get("d2u_machinery", "default_lang")) as $supply) {
							$options_supply[$supply->supply_id] = $supply->name;
						}
						d2u_addon_backend_helper::form_select('d2u_machinery_steel_automation_supplys', 'form[automation_supply_ids][]', $options_supply, array_keys($machine->automation_supplys), 4, TRUE, $readonly);
						print '</div>';
						print '</fieldset>';

						print '<fieldset>';
						print '<legend><small><i class="rex-icon fa-steam"></i></small> '. rex_i18n::msg('d2u_machinery_steel_sheet_processing') .'</legend>';
						print '<div class="panel-body-wrapper slide">';
						d2u_addon_backend_helper::form_input('d2u_machinery_steel_workspace', 'form[workspace]', $machine->workspace, FALSE, $readonly, "text");
						d2u_addon_backend_helper::form_input('d2u_machinery_steel_workspace_min', 'form[workspace_min]', $machine->workspace_min, FALSE, $readonly, "text");
						d2u_addon_backend_helper::form_input('d2u_machinery_steel_workspace_square', 'form[workspace_square]', $machine->workspace_square, FALSE, $readonly, "text");
						d2u_addon_backend_helper::form_input('d2u_machinery_steel_workspace_flat', 'form[workspace_flat]', $machine->workspace_flat, FALSE, $readonly, "text");
						d2u_addon_backend_helper::form_input('d2u_machinery_steel_workspace_plate', 'form[workspace_plate]', $machine->workspace_plate, FALSE, $readonly, "text");
						d2u_addon_backend_helper::form_input('d2u_machinery_steel_workspace_profile', 'form[workspace_profile]', $machine->workspace_profile, FALSE, $readonly, "text");
						d2u_addon_backend_helper::form_input('d2u_machinery_steel_workspace_angle_steel', 'form[workspace_angle_steel]', $machine->workspace_angle_steel, FALSE, $readonly, "text");
						d2u_addon_backend_helper::form_input('d2u_machinery_steel_workspace_round', 'form[workspace_round]', $machine->workspace_round, FALSE, $readonly, "text");
						d2u_addon_backend_helper::form_input('d2u_machinery_steel_sheet_width', 'form[sheet_width]', $machine->sheet_width, FALSE, $readonly, "text");
						d2u_addon_backend_helper::form_input('d2u_machinery_steel_sheet_length', 'form[sheet_length]', $machine->sheet_length, FALSE, $readonly, "text");
						d2u_addon_backend_helper::form_input('d2u_machinery_steel_sheet_thickness', 'form[sheet_thickness]', $machine->sheet_thickness, FALSE, $readonly, "text");
						d2u_addon_backend_helper::form_input('d2u_machinery_steel_tool_changer_locations', 'form[tool_changer_locations]', $machine->tool_changer_locations, FALSE, $readonly, "text");
						print '</div>';
						print '</fieldset>';
						
						print '<fieldset>';
						print '<legend><small><i class="rex-icon fa-steam"></i></small> '. rex_i18n::msg('d2u_machinery_steel_drilling') .'</legend>';
						print '<div class="panel-body-wrapper slide">';
						d2u_addon_backend_helper::form_input('d2u_machinery_steel_drilling_unit_vertical', 'form[drilling_unit_vertical]', $machine->drilling_unit_vertical, FALSE, $readonly, "text");
						d2u_addon_backend_helper::form_input('d2u_machinery_steel_drilling_unit_horizontal', 'form[drilling_unit_horizontal]', $machine->drilling_unit_horizontal, FALSE, $readonly, "text");
						d2u_addon_backend_helper::form_input('d2u_machinery_steel_drilling_diameter', 'form[drilling_diameter]', $machine->drilling_diameter, FALSE, $readonly, "text");
						d2u_addon_backend_helper::form_input('d2u_machinery_steel_drilling_tools_axis', 'form[drilling_tools_axis]', $machine->drilling_tools_axis, FALSE, $readonly, "text");
						d2u_addon_backend_helper::form_input('d2u_machinery_steel_drilling_axis_drive_power', 'form[drilling_axis_drive_power]', $machine->drilling_axis_drive_power, FALSE, $readonly, "text");
						d2u_addon_backend_helper::form_input('d2u_machinery_steel_drilling_rpm_speed', 'form[drilling_rpm_speed]', $machine->drilling_rpm_speed, FALSE, $readonly, "text");
						print '</div>';
						print '</fieldset>';
						
						print '<fieldset>';
						print '<legend><small><i class="rex-icon fa-steam"></i></small> '. rex_i18n::msg('d2u_machinery_steel_saw') .'</legend>';
						print '<div class="panel-body-wrapper slide">';
						d2u_addon_backend_helper::form_input('d2u_machinery_steel_saw_blade', 'form[saw_blade]', $machine->saw_blade, FALSE, $readonly, "text");
						d2u_addon_backend_helper::form_input('d2u_machinery_steel_saw_band', 'form[saw_band]', $machine->saw_band, FALSE, $readonly, "text");
						d2u_addon_backend_helper::form_input('d2u_machinery_steel_saw_band_tilt', 'form[saw_band_tilt]', $machine->saw_band_tilt, FALSE, $readonly, "text");
						d2u_addon_backend_helper::form_input('d2u_machinery_steel_saw_cutting_speed', 'form[saw_cutting_speed]', $machine->saw_cutting_speed, FALSE, $readonly, "text");
						d2u_addon_backend_helper::form_input('d2u_machinery_steel_saw_miter', 'form[saw_miter]', $machine->saw_miter, FALSE, $readonly, "text");
						print '</div>';
						print '</fieldset>';
						
						print '<fieldset>';
						print '<legend><small><i class="rex-icon fa-steam"></i></small> '. rex_i18n::msg('d2u_machinery_steel_punching') .'</legend>';
						print '<div class="panel-body-wrapper slide">';
						d2u_addon_backend_helper::form_input('d2u_machinery_steel_punching_bevel_angle', 'form[bevel_angle]', $machine->bevel_angle, FALSE, $readonly, "number");
						d2u_addon_backend_helper::form_input('d2u_machinery_steel_punching_diameter', 'form[punching_diameter]', $machine->punching_diameter, FALSE, $readonly, "text");
						d2u_addon_backend_helper::form_input('d2u_machinery_steel_punching_power', 'form[punching_power]', $machine->punching_power, FALSE, $readonly, "number");
						d2u_addon_backend_helper::form_input('d2u_machinery_steel_punching_tools', 'form[punching_tools]', $machine->punching_tools, FALSE, $readonly, "text");
						d2u_addon_backend_helper::form_input('d2u_machinery_steel_punching_shaving_unit_angle_steel_single_cut', 'form[shaving_unit_angle_steel_single_cut]', $machine->shaving_unit_angle_steel_single_cut, FALSE, $readonly, "number");
						$options_profile = [];
						foreach (Profile::getAll(rex_config::get("d2u_machinery", "default_lang")) as $profile) {
							$options_profile[$profile->profile_id] = $profile->name;
						}
						d2u_addon_backend_helper::form_select('d2u_machinery_steel_punching_profiles', 'form[profile_ids][]', $options_profile, array_keys($machine->profiles), 4, TRUE, $readonly);
						d2u_addon_backend_helper::form_input('d2u_machinery_steel_punching_carrier_width', 'form[carrier_width]', $machine->carrier_width, FALSE, $readonly, "text");
						d2u_addon_backend_helper::form_input('d2u_machinery_steel_punching_carrier_height', 'form[carrier_height]', $machine->carrier_height, FALSE, $readonly, "text");
						d2u_addon_backend_helper::form_input('d2u_machinery_steel_punching_carrier_weight', 'form[carrier_weight]', $machine->carrier_weight, FALSE, $readonly, "number");
						d2u_addon_backend_helper::form_input('d2u_machinery_steel_punching_flange_thickness', 'form[flange_thickness]', $machine->flange_thickness, FALSE, $readonly, "text");
						d2u_addon_backend_helper::form_input('d2u_machinery_steel_punching_web_thickness', 'form[web_thickness]', $machine->web_thickness, FALSE, $readonly, "text");
						d2u_addon_backend_helper::form_input('d2u_machinery_steel_punching_component_length', 'form[component_length]', $machine->component_length, FALSE, $readonly, "text");
						d2u_addon_backend_helper::form_input('d2u_machinery_steel_punching_component_weight', 'form[component_weight]', $machine->component_weight, FALSE, $readonly, "number");
						$options_welding = [];
						foreach (Welding::getAll(rex_config::get("d2u_machinery", "default_lang")) as $welding) {
							$options_welding[$welding->welding_id] = $welding->name;
						}
						d2u_addon_backend_helper::form_select('d2u_machinery_steel_weldings', 'form[welding_process_ids][]', $options_welding, array_keys($machine->weldings), 4, TRUE, $readonly);
						d2u_addon_backend_helper::form_input('d2u_machinery_steel_welding_thickness', 'form[welding_thickness]', $machine->welding_thickness, FALSE, $readonly, "number");
						d2u_addon_backend_helper::form_input('d2u_machinery_steel_welding_wire_thickness', 'form[welding_wire_thickness]', $machine->welding_wire_thickness, FALSE, $readonly, "text");
						print '</div>';
						print '</fieldset>';
						
						print '<fieldset>';
						print '<legend><small><i class="rex-icon fa-steam"></i></small> '. rex_i18n::msg('d2u_machinery_steel_beam') .'</legend>';
						print '<div class="panel-body-wrapper slide">';
						d2u_addon_backend_helper::form_input('d2u_machinery_steel_beam_continuous_opening', 'form[beam_continuous_opening]', $machine->beam_continuous_opening, FALSE, $readonly, "text");
						d2u_addon_backend_helper::form_input('d2u_machinery_steel_beam_turbines', 'form[beam_turbines]', $machine->beam_turbines, FALSE, $readonly, "number");
						d2u_addon_backend_helper::form_input('d2u_machinery_steel_beam_turbine_power', 'form[beam_turbine_power]', $machine->beam_turbine_power, FALSE, $readonly, "text");
						d2u_addon_backend_helper::form_input('d2u_machinery_steel_beam_color_guns', 'form[beam_color_guns]', $machine->beam_color_guns, FALSE, $readonly, "text");
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
						<legend><?php echo rex_i18n::msg('d2u_helper_text_lang') .' "'. $rex_clang->getName() .'"'; ?></legend>
						<div class="panel-body-wrapper slide">
							<?php
								if($rex_clang->getId() != rex_config::get("d2u_machinery", "default_lang")) {
									$options_translations = [];
									$options_translations["yes"] = rex_i18n::msg('d2u_helper_translation_needs_update');
									$options_translations["no"] = rex_i18n::msg('d2u_helper_translation_is_uptodate');
									$options_translations["delete"] = rex_i18n::msg('d2u_helper_translation_delete');
									d2u_addon_backend_helper::form_select('d2u_helper_translation', 'form[lang]['. $rex_clang->getId() .'][translation_needs_update]', $options_translations, array($machine_lang->translation_needs_update), 1, FALSE, $readonly_lang);
								}
								else {
									print '<input type="hidden" name="form[lang]['. $rex_clang->getId() .'][translation_needs_update]" value="no">';
								}
								d2u_addon_backend_helper::form_input('d2u_machinery_lang_name', "form[lang][". $rex_clang->getId() ."][lang_name]", $machine_lang->lang_name, FALSE, $readonly_lang, "text");
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
		print d2u_addon_backend_helper::getCSS();
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

	$list->setColumnLabel('internal_name', rex_i18n::msg('d2u_machinery_internal_name'));

	$list->setColumnLabel('categoryname', rex_i18n::msg('d2u_machinery_name'));

	$list->setColumnLabel('priority', rex_i18n::msg('header_priority'));

    $list->addColumn(rex_i18n::msg('module_functions'), '<i class="rex-icon rex-icon-edit"></i> ' . rex_i18n::msg('system_update'));
    $list->setColumnLayout(rex_i18n::msg('module_functions'), ['<th class="rex-table-action" colspan="2">###VALUE###</th>', '<td class="rex-table-action">###VALUE###</td>']);
    $list->setColumnParams(rex_i18n::msg('module_functions'), ['func' => 'edit', 'entry_id' => '###machine_id###']);

 	$list->addColumn(rex_i18n::msg('d2u_helper_clone'), '<i class="rex-icon fa-copy"></i> ' . rex_i18n::msg('d2u_helper_clone'));
    $list->setColumnLayout(rex_i18n::msg('d2u_helper_clone'), ['', '<td class="rex-table-action">###VALUE###</td>']);
    $list->setColumnParams(rex_i18n::msg('d2u_helper_clone'), ['func' => 'clone', 'entry_id' => '###machine_id###']);

	$list->addColumn(rex_i18n::msg('delete_module'), '<i class="rex-icon rex-icon-delete"></i> ' . rex_i18n::msg('delete'));
	$list->setColumnLayout(rex_i18n::msg('delete_module'), ['', '<td class="rex-table-action">###VALUE###</td>']);
    $list->setColumnParams(rex_i18n::msg('delete_module'), ['func' => 'delete', 'entry_id' => '###machine_id###']);
    $list->addLinkAttribute(rex_i18n::msg('delete_module'), 'data-confirm', rex_i18n::msg('d2u_helper_confirm_delete'));

	$list->removeColumn('online_status');
    $list->addColumn(rex_i18n::msg('status_online'), '<a class="rex-###online_status###" href="' . rex_url::currentBackendPage(['func' => 'changestatus']) . '&entry_id=###machine_id###"><i class="rex-icon rex-icon-###online_status###"></i> ###online_status###</a>');
	$list->setColumnLayout(rex_i18n::msg('status_online'), ['', '<td class="rex-table-action">###VALUE###</td>']);

    $list->setNoRowsMessage(rex_i18n::msg('d2u_machinery_machine_no_machines_found'));

    $fragment = new rex_fragment();
    $fragment->setVar('title', rex_i18n::msg('d2u_machinery_meta_machines'), false);
    $fragment->setVar('content', $list->get(), false);
    echo $fragment->parse('core/page/section.php');
}