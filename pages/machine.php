<?php

use TobiasKrais\D2UReferences\Reference;

$func = rex_request('func', 'string');
$entry_id = rex_request('entry_id', 'int');
$message = rex_get('message', 'string');

// Print message
if ('' !== $message) {
    echo rex_view::success(rex_i18n::msg($message));
}

// save settings
if (1 === (int) filter_input(INPUT_POST, 'btn_save') || 1 === (int) filter_input(INPUT_POST, 'btn_apply')) {
    $form = rex_post('form', 'array', []);

    // Media fields and links need special treatment
    $input_media = rex_post('REX_INPUT_MEDIA', 'array', []);
    $input_media_list = rex_post('REX_INPUT_MEDIALIST', 'array', []);
    $input_link = rex_post('REX_INPUT_LINK', 'array', []);
    $input_link_list = rex_post('REX_INPUT_LINKLIST', 'array', []);

    $success = true;
    $machine = false;
    $machine_id = $form['machine_id'];
    foreach (rex_clang::getAll() as $rex_clang) {
        if (!$machine instanceof Machine) {
            $machine = new Machine($machine_id, $rex_clang->getId());
            $machine->machine_id = $machine_id; // Ensure correct ID in case first language has no object
            $machine->priority = $form['priority'];
            $machine->name = $form['name'];
            $machine->product_number = $form['product_number'];
            $pics = preg_grep('/^\s*$/s', explode(',', $input_media_list[1]), PREG_GREP_INVERT);
            $machine->pics = is_array($pics) ? $pics : [];
            if (isset($form['category_id'])) {
                $machine->category = new Category($form['category_id'], $rex_clang->getId());
            }
            $machine->alternative_machine_ids = $form['alternative_machine_ids'] ?? [];
            $machine->online_status = array_key_exists('online_status', $form) ? 'online' : 'offline';
            $machine->article_id_service = (int) $input_link['article_id_service'];
            $machine->article_id_software = (int) $input_link['article_id_software'];
            $article_ids_references = preg_grep('/^\s*$/s', explode(',', $input_link_list[1]), PREG_GREP_INVERT);
            $machine->article_ids_references = is_array($article_ids_references) ? $article_ids_references : [];
            if (rex_addon::get('d2u_references')->isAvailable()) {
                $machine->reference_ids = $form['reference_ids'] ?? [];
            }
            $machine->engine_power = $form['engine_power'];
            $machine->engine_power_frequency_controlled = array_key_exists('engine_power_frequency_controlled', $form);
            $machine->length = $form['length'];
            $machine->width = $form['width'];
            $machine->height = $form['height'];
            $machine->depth = $form['depth'];
            $machine->weight = $form['weight'];
            $machine->operating_voltage_v = $form['operating_voltage_v'];
            $machine->operating_voltage_hz = $form['operating_voltage_hz'];
            $machine->operating_voltage_a = $form['operating_voltage_a'];

            if (rex_plugin::get('d2u_machinery', 'contacts')->isAvailable()) {
                $machine->contact = $form['contact_id'] ? new \D2U_Machinery\Contact($form['contact_id']) : false;
            }
            if (rex_plugin::get('d2u_machinery', 'equipment')->isAvailable()) {
                $machine->equipment_ids = $form['equipment_ids'] ?? [];
            }
            if (rex_plugin::get('d2u_machinery', 'industry_sectors')->isAvailable()) {
                $machine->industry_sector_ids = $form['industry_sector_ids'] ?? [];
            }
            if (rex_plugin::get('d2u_machinery', 'machine_agitator_extension')->isAvailable()) {
                $machine->agitator_type_id = 0 === (int) $form['agitator_type_id'] ? 0 : (int) ($form['agitator_type_id']);
                $machine->viscosity = 0 === (int) $form['viscosity'] ? 0 : (int) ($form['viscosity']);
            }
            if (rex_plugin::get('d2u_machinery', 'machine_certificates_extension')->isAvailable()) {
                $machine->certificate_ids = $form['certificate_ids'] ?? [];
            }
            if (rex_plugin::get('d2u_machinery', 'machine_construction_equipment_extension')->isAvailable()) {
                $machine->airless_hose_connection = $form['airless_hose_connection'];
                $machine->airless_hose_diameter = 0 === (int) $form['airless_hose_diameter'] ? 0 : $form['airless_hose_diameter'];
                $machine->airless_hose_length = 0 === (int) $form['airless_hose_length'] ? 0 : $form['airless_hose_length'];
                $machine->airless_nozzle_size = $form['airless_nozzle_size'];
                $machine->container_capacity = $form['container_capacity'];
                $machine->container_capacity_unit = $form['container_capacity_unit'];
                $machine->container_mixing_performance = $form['container_mixing_performance'];
                $machine->container_waterconnect_pressure = 0 === (int) $form['container_waterconnect_pressure'] ? 0 : $form['container_waterconnect_pressure'];
                $machine->container_waterconnect_diameter = $form['container_waterconnect_diameter'];
                $machine->container_weight_empty = 0 === (int) $form['container_weight_empty'] ? 0 : $form['container_weight_empty'];
                $machine->cutters_cutting_depth = $form['cutters_cutting_depth'];
                $machine->cutters_cutting_length = 0 === (int) $form['cutters_cutting_length'] ? 0 : $form['cutters_cutting_length'];
                $machine->cutters_rod_length = $form['cutters_rod_length'];
                $machine->floor_beam_power_on_concrete = 0 === (int) $form['floor_beam_power_on_concrete'] ? 0 : $form['floor_beam_power_on_concrete'];
                $machine->floor_dust_extraction_connection = 0 === (int) $form['floor_dust_extraction_connection'] ? 0 : $form['floor_dust_extraction_connection'];
                $machine->floor_feedrate = $form['floor_feedrate'];
                $machine->floor_filter_connection = $form['floor_filter_connection'];
                $machine->floor_rotations = $form['floor_rotations'];
                $machine->floor_working_pressure = $form['floor_working_pressure'];
                $machine->floor_working_width = 0 === (int) $form['floor_working_width'] ? 0 : $form['floor_working_width'];
                $machine->grinder_grinding_plate = 0 === (int) $form['grinder_grinding_plate'] ? 0 : $form['grinder_grinding_plate'];
                $machine->grinder_grinding_wheel = 0 === (int) $form['grinder_grinding_wheel'] ? 0 : $form['grinder_grinding_wheel'];
                $machine->grinder_rotational_frequency = $form['grinder_rotational_frequency'];
                $machine->grinder_sanding = $form['grinder_sanding'];
                $machine->grinder_vacuum_connection = 0 === (int) $form['grinder_vacuum_connection'] ? 0 : $form['grinder_vacuum_connection'];
                $machine->operating_pressure = $form['operating_pressure'];
                $pictures_delivery_set = preg_grep('/^\s*$/s', explode(',', $input_media_list[2]), PREG_GREP_INVERT);
                $machine->pictures_delivery_set = is_array($pictures_delivery_set) ? $pictures_delivery_set : [];
                $machine->pump_conveying_distance = 0 === (int) $form['pump_conveying_distance'] ? 0 : $form['pump_conveying_distance'];
                $machine->pump_filling = 0 === (int) $form['pump_filling'] ? 0 : $form['pump_filling'];
                $machine->pump_flow_volume = $form['pump_flow_volume'];
                $machine->pump_grain_size = $form['pump_grain_size'];
                $machine->pump_material_container = $form['pump_material_container'];
                $machine->pump_pressure_height = 0 === (int) $form['pump_pressure_height'] ? 0 : $form['pump_pressure_height'];
                $machine->waste_water_capacity = 0 === (int) $form['waste_water_capacity'] ? 0 : $form['waste_water_capacity'];
            }
            if (rex_plugin::get('d2u_machinery', 'service_options')->isAvailable()) {
                $machine->service_option_ids = $form['service_option_ids'] ?? [];
            }
            if (rex_plugin::get('d2u_machinery', 'machine_features_extension')->isAvailable()) {
                $machine->feature_ids = $form['feature_ids'] ?? [];
            }
            if (rex_plugin::get('d2u_machinery', 'machine_options_extension')->isAvailable()) {
                $machine->option_ids = $form['option_ids'] ?? [];
            }
            if (rex_plugin::get('d2u_machinery', 'machine_steel_processing_extension')->isAvailable()) {
                $process_ids = $form['process_ids'] ?? [];
                $machine->processes = [];
                foreach ($process_ids as $process_id) {
                    if ($process_id > 0) {
                        $machine->processes[$process_id] = new Process($process_id, (int) rex_config::get('d2u_helper', 'default_lang'));
                    }
                }
                $procedure_ids = $form['procedure_ids'] ?? [];
                $machine->procedures = [];
                foreach ($procedure_ids as $procedure_id) {
                    if ($procedure_id > 0) {
                        $machine->procedures[$procedure_id] = new Procedure($procedure_id, (int) rex_config::get('d2u_helper', 'default_lang'));
                    }
                }
                $material_ids = $form['material_ids'] ?? [];
                $machine->materials = [];
                foreach ($material_ids as $material_id) {
                    if ($material_id > 0) {
                        $machine->materials[$material_id] = new Material($material_id, (int) rex_config::get('d2u_helper', 'default_lang'));
                    }
                }
                $tool_ids = $form['tool_ids'] ?? [];
                $machine->tools = [];
                foreach ($tool_ids as $tool_id) {
                    if ($tool_id > 0) {
                        $machine->tools[$tool_id] = new Tool($tool_id, (int) rex_config::get('d2u_helper', 'default_lang'));
                    }
                }
                $machine->automation_supply_single_stroke = $form['automation_supply_single_stroke'];
                $machine->automation_supply_multi_stroke = $form['automation_supply_multi_stroke'];
                $machine->automation_feedrate = $form['automation_feedrate'];
                $machine->automation_feedrate_sawblade = $form['automation_feedrate_sawblade'];
                $machine->automation_rush_leader_flyback = $form['automation_rush_leader_flyback'];
                $automation_automationgrade_ids = $form['automation_automationgrade_ids'] ?? [];
                $machine->automation_automationgrades = [];
                foreach ($automation_automationgrade_ids as $automation_automationgrade_id) {
                    if ($automation_automationgrade_id > 0) {
                        $machine->automation_automationgrades[$automation_automationgrade_id] = new Automation($automation_automationgrade_id, (int) rex_config::get('d2u_helper', 'default_lang'));
                    }
                }
                $automation_supply_ids = $form['automation_supply_ids'] ?? [];
                $machine->automation_supply_ids = [];
                foreach ($automation_supply_ids as $automation_supply_id) {
                    if ($automation_supply_id > 0) {
                        $machine->automation_supply_ids[] = $automation_supply_id;
                    }
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
                $machine->drilling_unit_below = 0 === (int) $form['drilling_unit_below'] ? 0 : $form['drilling_unit_below'];
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
                $machine->bevel_angle = 0 === (int) $form['bevel_angle'] ? 0 : $form['bevel_angle'];
                $machine->punching_diameter = $form['punching_diameter'];
                $machine->punching_power = 0 === (int) $form['punching_power'] ? 0 : $form['punching_power'];
                $machine->punching_tools = $form['punching_tools'];
                $machine->shaving_unit_angle_steel_single_cut = 0 === (int) $form['shaving_unit_angle_steel_single_cut'] ? 0 : $form['shaving_unit_angle_steel_single_cut'];
                $profile_ids = $form['profile_ids'] ?? [];
                $machine->profiles = [];
                foreach ($profile_ids as $profile_id) {
                    if ($profile_id > 0) {
                        $machine->profiles[$profile_id] = new Profile($profile_id, (int) rex_config::get('d2u_helper', 'default_lang'));
                    }
                }
                $machine->carrier_width = $form['carrier_width'];
                $machine->carrier_height = $form['carrier_height'];
                $machine->carrier_weight = 0 === (int) $form['carrier_weight'] ? 0 : $form['carrier_weight'];
                $machine->flange_thickness = $form['flange_thickness'];
                $machine->web_thickness = $form['web_thickness'];
                $machine->component_length = $form['component_length'];
                $machine->component_weight = 0 === (int) $form['component_weight'] ? 0 : $form['component_weight'];
                $welding_process_ids = $form['welding_process_ids'] ?? [];
                $machine->weldings = [];
                foreach ($welding_process_ids as $welding_process_id) {
                    if ($welding_process_id > 0) {
                        $machine->weldings[$welding_process_id] = new Welding($welding_process_id, (int) rex_config::get('d2u_helper', 'default_lang'));
                    }
                }
                $machine->welding_thickness = 0 === (int) $form['welding_thickness'] ? 0 : $form['welding_thickness'];
                $machine->welding_wire_thickness = $form['welding_wire_thickness'];
                $machine->beam_continuous_opening = $form['beam_continuous_opening'];
                $machine->beam_turbines = 0 === (int) $form['beam_turbines'] ? 0 : $form['beam_turbines'];
                $machine->beam_turbine_power = $form['beam_turbine_power'];
                $machine->beam_color_guns = $form['beam_color_guns'];
            }
            if (rex_plugin::get('d2u_machinery', 'machine_usage_area_extension')->isAvailable()) {
                $machine->usage_area_ids = $form['usage_area_ids'] ?? [];
            }

            if (\rex_addon::get('d2u_videos')->isAvailable()) {
                $video_ids = $form['video_ids'] ?? [];
                $machine->videos = []; // Clear video array
                foreach ($video_ids as $video_id) {
                    if ($video_id > 0) {
                        $machine->videos[$video_id] = new \TobiasKrais\D2UVideos\Video($video_id, (int) rex_config::get('d2u_helper', 'default_lang'));
                    }
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
        $machine->benefits_long = $form['lang'][$rex_clang->getId()]['benefits_long'];
        $machine->benefits_short = $form['lang'][$rex_clang->getId()]['benefits_short'];
        $machine->leaflet = $input_media['1'. $rex_clang->getId()];
        $pdfs = preg_grep('/^\s*$/s', explode(',', $input_media_list['1'. $rex_clang->getId()]), PREG_GREP_INVERT);
        $machine->pdfs = is_array($pdfs) ? $pdfs : [];
        if (rex_plugin::get('d2u_machinery', 'machine_construction_equipment_extension')->isAvailable()) {
            $machine->container_connection_port = $form['lang'][$rex_clang->getId()]['container_connection_port'];
            $machine->container_conveying_wave = $form['lang'][$rex_clang->getId()]['container_conveying_wave'];
            $machine->description_technical = $form['lang'][$rex_clang->getId()]['description_technical'];
            $machine->delivery_set_basic = $form['lang'][$rex_clang->getId()]['delivery_set_basic'];
            $machine->delivery_set_conversion = $form['lang'][$rex_clang->getId()]['delivery_set_conversion'];
            $machine->delivery_set_full = $form['lang'][$rex_clang->getId()]['delivery_set_full'];
        }

        if ('delete' === $machine->translation_needs_update) {
            $machine->delete(false);
        } elseif ($machine->save()) {
            // remember id, for each database lang object needs same id
            $machine_id = $machine->machine_id;
        } else {
            $success = false;
        }
    }

    // message output
    $message = 'form_save_error';
    if ($success) {
        $message = 'form_saved';
    }

    // Redirect to make reload and thus double save impossible
    if (1 === (int) filter_input(INPUT_POST, 'btn_apply', FILTER_VALIDATE_INT) && $machine instanceof Machine) {
        header('Location: '. rex_url::currentBackendPage(['entry_id' => $machine->machine_id, 'func' => 'edit', 'message' => $message], false));
    } else {
        header('Location: '. rex_url::currentBackendPage(['message' => $message], false));
    }
    exit;
}
// Delete
if (1 === (int) filter_input(INPUT_POST, 'btn_delete', FILTER_VALIDATE_INT) || 'delete' === $func) {
    $machine_id = $entry_id;
    if (0 === $machine_id) {
        $form = rex_post('form', 'array', []);
        $machine_id = $form['machine_id'];
    }
    $machine = new Machine($machine_id, (int) rex_config::get('d2u_helper', 'default_lang'));
    $machine->machine_id = $machine_id; // Ensure correct ID in case language has no object

    // Check if object is used
    $referring_machines = $machine->getReferringMachines();
    $referring_used_machines = [];
    if (rex_plugin::get('d2u_machinery', 'used_machines')->isAvailable()) {
        $referring_used_machines = $machine->getReferringUsedMachines();
    }
    $referring_production_lines = [];
    if (rex_plugin::get('d2u_machinery', 'production_lines')->isAvailable()) {
        $referring_production_lines = $machine->getReferringProductionLines();
    }

    // If not used, delete
    if (0 === count($referring_machines) && 0 === count($referring_used_machines)) {
        $machine->delete();
    } else {
        $message = '<ul>';
        if (count($referring_machines) > 0) {
            foreach ($referring_machines as $referring_machine) {
                $message .= '<li><a href="index.php?page=d2u_machinery/machine&func=edit&entry_id='. $referring_machine->machine_id .'">'. $referring_machine->name.'</a></li>';
            }
        }
        if (count($referring_used_machines) > 0) {
            foreach ($referring_used_machines as $referring_used_machine) {
                $message .= '<li><a href="index.php?page=d2u_machinery/used_machines&func=edit&entry_id='. $referring_used_machine->used_machine_id .'">'. $referring_used_machine->name.'</a></li>';
            }
        }
        if (count($referring_production_lines) > 0) {
            foreach ($referring_production_lines as $referring_production_line) {
                $message .= '<li><a href="index.php?page=d2u_machinery/production_line&func=edit&entry_id='. $referring_production_line->production_line_id .'">'. $referring_production_line->name.'</a></li>';
            }
        }
        $message .= '</ul>';

        echo rex_view::error(rex_i18n::msg('d2u_helper_could_not_delete') . $message);
    }

    $func = '';
}
// Change online status of machine
elseif ('changestatus' === $func) {
    $machine = new Machine($entry_id, (int) rex_config::get('d2u_helper', 'default_lang'));
    $machine->machine_id = $entry_id; // Ensure correct ID in case language has no object
    $machine->changeStatus();

    header('Location: '. rex_url::currentBackendPage());
    exit;
}

// Eingabeformular
if ('edit' === $func || 'clone' === $func || 'add' === $func) {
    $machine = new Machine($entry_id, (int) rex_config::get('d2u_helper', 'default_lang'));
?>
	<form action="<?= rex_url::currentBackendPage() ?>" method="post">
		<div class="panel panel-edit">
			<header class="panel-heading"><div class="panel-title"><?= rex_i18n::msg('d2u_machinery_machine') ?></div></header>
			<div class="panel-body">
				<input type="hidden" name="form[machine_id]" value="<?= 'edit' === $func ? $entry_id : 0 ?>">
				<fieldset>
					<legend><?= rex_i18n::msg('d2u_helper_data_all_lang') ?></legend>
					<div class="panel-body-wrapper slide">
						<?php
                            $readonly = (\rex::getUser() instanceof rex_user && (\rex::getUser()->isAdmin() || \rex::getUser()->hasPerm('d2u_machinery[edit_data]'))) ? false : true;

                            \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_helper_name', 'form[name]', $machine->name, true, $readonly, 'text');
                            \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_machine_product_number', 'form[product_number]', $machine->product_number, false, $readonly, 'text');
                            \TobiasKrais\D2UHelper\BackendHelper::form_input('header_priority', 'form[priority]', (string) $machine->priority, true, $readonly, 'number');
                            \TobiasKrais\D2UHelper\BackendHelper::form_imagelistfield('d2u_helper_picture', 1, $machine->pics, $readonly);
                            if (rex_plugin::get('d2u_machinery', 'machine_construction_equipment_extension')->isAvailable()) {
                                \TobiasKrais\D2UHelper\BackendHelper::form_imagelistfield('d2u_machinery_construction_equipment_picture_delivery_set', 2, $machine->pictures_delivery_set, $readonly);
                            }
                            $options = [];
                            foreach (Category::getAll((int) rex_config::get('d2u_helper', 'default_lang')) as $category) {
                                if ('' !== $category->name) {
                                    $options[$category->category_id] = $category->name;
                                }
                            }                
                            \TobiasKrais\D2UHelper\BackendHelper::form_select('d2u_helper_category', 'form[category_id]', $options, $machine->category instanceof Category ? [$machine->category->category_id] : [], 1, false, $readonly);
                            if (rex_plugin::get('d2u_machinery', 'contacts')->isAvailable()) {
                                $options_contacts = [0 => rex_i18n::msg('d2u_machinery_contacts_settings_contact')];
                                foreach (\D2U_Machinery\Contact::getAll() as $contact) {
                                    if ('' !== $contact->name) {
                                        $options_contacts[$contact->contact_id] = $contact->name;
                                    }
                                }
                                \TobiasKrais\D2UHelper\BackendHelper::form_select('d2u_machinery_contacts_contact', 'form[contact_id]', $options_contacts, [$machine->contact instanceof \D2U_Machinery\Contact ? (string) $machine->contact->contact_id : ''], 1, false, $readonly);
                            }
                            $options_alt_machines = [];
                            foreach (Machine::getAll((int) rex_config::get('d2u_helper', 'default_lang')) as $alt_machine) {
                                if ($alt_machine->machine_id !== $machine->machine_id) {
                                    $options_alt_machines[$alt_machine->machine_id] = $alt_machine->name;
                                }
                            }
                            \TobiasKrais\D2UHelper\BackendHelper::form_select('d2u_machinery_machine_alternatives', 'form[alternative_machine_ids][]', $options_alt_machines, $machine->alternative_machine_ids, 10, true, $readonly);
                            \TobiasKrais\D2UHelper\BackendHelper::form_checkbox('d2u_helper_online_status', 'form[online_status]', 'online', 'online' === $machine->online_status, $readonly);
                            \TobiasKrais\D2UHelper\BackendHelper::form_linkfield('d2u_machinery_machine_software', 'article_id_software', $machine->article_id_software, (int) rex_config::get('d2u_helper', 'default_lang'), $readonly);
                            \TobiasKrais\D2UHelper\BackendHelper::form_linkfield('d2u_machinery_machine_service', 'article_id_service', $machine->article_id_service, (int) rex_config::get('d2u_helper', 'default_lang'), $readonly);
                            \TobiasKrais\D2UHelper\BackendHelper::form_linklistfield('d2u_machinery_machine_references', 1, $machine->article_ids_references, (int) rex_config::get('d2u_helper', 'default_lang'), $readonly);
                            if (\rex_addon::get('d2u_references')->isAvailable()) {
                                $options_references = [];
                                foreach (Reference::getAll((int) rex_config::get('d2u_helper', 'default_lang')) as $reference) {
                                    $options_references[$reference->reference_id] = $reference->name;
                                }
                                \TobiasKrais\D2UHelper\BackendHelper::form_select('d2u_references', 'form[reference_ids][]', $options_references, $machine->reference_ids, 10, true, $readonly);
                            }
                            if (\rex_addon::get('d2u_videos')->isAvailable()) {
                                $options = [];
                                foreach (TobiasKrais\D2UVideos\Video::getAll((int) rex_config::get('d2u_helper', 'default_lang')) as $video) {
                                    $options[$video->video_id] = $video->name;
                                }
                                \TobiasKrais\D2UHelper\BackendHelper::form_select('d2u_machinery_category_videos', 'form[video_ids][]', $options, array_keys($machine->videos), 10, true, $readonly);
                            }

                            \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_machine_engine_power', 'form[engine_power]', $machine->engine_power, false, $readonly, 'text');
                            \TobiasKrais\D2UHelper\BackendHelper::form_checkbox('d2u_machinery_machine_engine_power_frequency_controlled', 'form[engine_power_frequency_controlled]', 'true', $machine->engine_power_frequency_controlled, $readonly);
                            \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_machine_length', 'form[length]', $machine->length, false, $readonly, 'number');
                            \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_machine_width', 'form[width]', $machine->width, false, $readonly, 'number');
                            \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_machine_height', 'form[height]', $machine->height, false, $readonly, 'number');
                            \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_machine_depth', 'form[depth]', $machine->depth, false, $readonly, 'number');
                            \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_machine_weight', 'form[weight]', $machine->weight, false, $readonly, 'text');
                            \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_machine_voltage_v', 'form[operating_voltage_v]', $machine->operating_voltage_v, false, $readonly, 'text');
                            \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_machine_voltage_hz', 'form[operating_voltage_hz]', $machine->operating_voltage_hz, false, $readonly, 'text');
                            \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_machine_voltage_a', 'form[operating_voltage_a]', $machine->operating_voltage_a, false, $readonly, 'text');
                        ?>
					</div>
				</fieldset>
				<?php
                    if (rex_plugin::get('d2u_machinery', 'equipment')->isAvailable()) {
                        echo '<fieldset>';
                        echo '<legend><small><i class="rex-icon fa-plus"></i></small> '. rex_i18n::msg('d2u_machinery_equipments') .'</legend>';
                        echo '<div class="panel-body-wrapper slide">';
                        $options_equipments = [];
                        foreach (Equipment::getAll((int) rex_config::get('d2u_helper', 'default_lang')) as $equipments) {
                            $options_equipments[$equipments->equipment_id] = $equipments->name;
                        }
                        \TobiasKrais\D2UHelper\BackendHelper::form_select('d2u_machinery_equipments', 'form[equipment_ids][]', $options_equipments, $machine->equipment_ids, 10, true, $readonly);
                        echo '</div>';
                        echo '</fieldset>';
                    }
                    if (rex_plugin::get('d2u_machinery', 'industry_sectors')->isAvailable()) {
                        echo '<fieldset>';
                        echo '<legend><small><i class="rex-icon fa-industry"></i></small> '. rex_i18n::msg('d2u_machinery_industry_sectors') .'</legend>';
                        echo '<div class="panel-body-wrapper slide">';
                        $options_industry_sectors = [];
                        foreach (IndustrySector::getAll((int) rex_config::get('d2u_helper', 'default_lang')) as $industry_sector) {
                            $options_industry_sectors[$industry_sector->industry_sector_id] = $industry_sector->name;
                        }
                        \TobiasKrais\D2UHelper\BackendHelper::form_select('d2u_machinery_industry_sectors', 'form[industry_sector_ids][]', $options_industry_sectors, $machine->industry_sector_ids, 10, true, $readonly);
                        echo '</div>';
                        echo '</fieldset>';
                    }
                    if (rex_plugin::get('d2u_machinery', 'machine_agitator_extension')->isAvailable()) {
                        echo '<fieldset>';
                        echo '<legend><small><i class="rex-icon fa-spoon"></i></small> '. rex_i18n::msg('d2u_machinery_agitator_extension') .'</legend>';
                        echo '<div class="panel-body-wrapper slide">';
                        $options_agitator_types = [0 => rex_i18n::msg('d2u_machinery_no_selection')];
                        foreach (AgitatorType::getAll((int) rex_config::get('d2u_helper', 'default_lang')) as $agitator_type) {
                            $options_agitator_types[$agitator_type->agitator_type_id] = $agitator_type->name;
                        }
                        \TobiasKrais\D2UHelper\BackendHelper::form_select('d2u_machinery_agitator_type', 'form[agitator_type_id]', $options_agitator_types, [$machine->agitator_type_id], 1, false, $readonly);
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_agitators_viscosity', 'form[viscosity]', $machine->viscosity, false, $readonly, 'number');
                        echo '</div>';
                        echo '</fieldset>';
                    }
                    if (rex_plugin::get('d2u_machinery', 'machine_certificates_extension')->isAvailable()) {
                        echo '<fieldset>';
                        echo '<legend><small><i class="rex-icon fa-certificate"></i></small> '. rex_i18n::msg('d2u_machinery_certificates') .'</legend>';
                        echo '<div class="panel-body-wrapper slide">';
                        $options_certificates = [];
                        foreach (Certificate::getAll((int) rex_config::get('d2u_helper', 'default_lang')) as $certificate) {
                            $options_certificates[$certificate->certificate_id] = $certificate->name;
                        }
                        \TobiasKrais\D2UHelper\BackendHelper::form_select('d2u_machinery_certificates', 'form[certificate_ids][]', $options_certificates, $machine->certificate_ids, 10, true, $readonly);
                        echo '</div>';
                        echo '</fieldset>';
                    }
                    if (rex_plugin::get('d2u_machinery', 'machine_construction_equipment_extension')->isAvailable()) {
                        echo '<fieldset>';
                        echo '<legend><small><i class="rex-icon fa-shower"></i></small> '. rex_i18n::msg('d2u_machinery_construction_equipment') .' - '. rex_i18n::msg('d2u_machinery_construction_equipment_airless') .'</legend>';
                        echo '<div class="panel-body-wrapper slide">';
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_construction_equipment_airless_hose_connection', 'form[airless_hose_connection]', $machine->airless_hose_connection, false, $readonly, 'text');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_construction_equipment_airless_hose_diameter', 'form[airless_hose_diameter]', $machine->airless_hose_diameter, false, $readonly, 'number');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_construction_equipment_airless_hose_length', 'form[airless_hose_length]', $machine->airless_hose_length, false, $readonly, 'number');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_construction_equipment_airless_nozzle_size', 'form[airless_nozzle_size]', $machine->airless_nozzle_size, false, $readonly, 'text');
                        echo '</div>';
                        echo '</fieldset>';

                        echo '<fieldset>';
                        echo '<legend><small><i class="rex-icon fa-square"></i></small> '. rex_i18n::msg('d2u_machinery_construction_equipment') .' - '. rex_i18n::msg('d2u_machinery_construction_equipment_container') .'</legend>';
                        echo '<div class="panel-body-wrapper slide">';
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_construction_equipment_container_capacity', 'form[container_capacity]', $machine->container_capacity, false, $readonly, 'text');
                        $options_container_unit = [
                            'kg' => Sprog\Wildcard::get('d2u_machinery_unit_kg'),
                            'l' => Sprog\Wildcard::get('d2u_machinery_unit_l'),
                            'm3' => Sprog\Wildcard::get('d2u_machinery_unit_m3'),
                        ];
                        \TobiasKrais\D2UHelper\BackendHelper::form_select('d2u_machinery_construction_equipment_container_capacity_unit', 'form[container_capacity_unit]', $options_container_unit, [$machine->container_capacity_unit], 1, false, $readonly);
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_construction_equipment_container_mixing_performance', 'form[container_mixing_performance]', $machine->container_mixing_performance, false, $readonly, 'text');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_construction_equipment_container_waterconnect_pressure', 'form[container_waterconnect_pressure]', $machine->container_waterconnect_pressure, false, $readonly, 'number');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_construction_equipment_container_waterconnect_diameter', 'form[container_waterconnect_diameter]', $machine->container_waterconnect_diameter, false, $readonly, 'text');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_construction_equipment_container_weight_empty', 'form[container_weight_empty]', $machine->container_weight_empty, false, $readonly, 'number');
                        echo '</div>';
                        echo '</fieldset>';

                        echo '<fieldset>';
                        echo '<legend><small><i class="rex-icon fa-cut"></i></small> '. rex_i18n::msg('d2u_machinery_construction_equipment') .' - '. rex_i18n::msg('d2u_machinery_construction_equipment_cutters') .'</legend>';
                        echo '<div class="panel-body-wrapper slide">';
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_construction_equipment_cutters_cutting_depth', 'form[cutters_cutting_depth]', $machine->cutters_cutting_depth, false, $readonly, 'text');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_construction_equipment_cutters_cutting_length', 'form[cutters_cutting_length]', $machine->cutters_cutting_length, false, $readonly, 'number');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_construction_equipment_cutters_rod_length', 'form[cutters_rod_length]', $machine->cutters_rod_length, false, $readonly, 'text');
                        echo '</div>';
                        echo '</fieldset>';

                        echo '<fieldset>';
                        echo '<legend><small><i class="rex-icon fa-window-minimize"></i></small> '. rex_i18n::msg('d2u_machinery_construction_equipment') .' - '. rex_i18n::msg('d2u_machinery_construction_equipment_floor') .'</legend>';
                        echo '<div class="panel-body-wrapper slide">';
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_construction_equipment_floor_beam_power_on_concrete', 'form[floor_beam_power_on_concrete]', $machine->floor_beam_power_on_concrete, false, $readonly, 'number');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_construction_equipment_floor_dust_extraction_connection', 'form[floor_dust_extraction_connection]', $machine->floor_dust_extraction_connection, false, $readonly, 'number');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_construction_equipment_floor_feedrate', 'form[floor_feedrate]', $machine->floor_feedrate, false, $readonly, 'text');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_construction_equipment_floor_filter_connection', 'form[floor_filter_connection]', $machine->floor_filter_connection, false, $readonly, 'text');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_construction_equipment_floor_rotations', 'form[floor_rotations]', $machine->floor_rotations, false, $readonly, 'text');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_construction_equipment_floor_working_pressure', 'form[floor_working_pressure]', $machine->floor_working_pressure, false, $readonly, 'text');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_construction_equipment_floor_working_width', 'form[floor_working_width]', $machine->floor_working_width, false, $readonly, 'number');
                        echo '</div>';
                        echo '</fieldset>';

                        echo '<fieldset>';
                        echo '<legend><small><i class="rex-icon fa-ticket"></i></small> '. rex_i18n::msg('d2u_machinery_construction_equipment') .' - '. rex_i18n::msg('d2u_machinery_construction_equipment_grinder') .'</legend>';
                        echo '<div class="panel-body-wrapper slide">';
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_construction_equipment_grinder_grinding_plate', 'form[grinder_grinding_plate]', $machine->grinder_grinding_plate, false, $readonly, 'number');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_construction_equipment_grinder_grinding_wheel', 'form[grinder_grinding_wheel]', $machine->grinder_grinding_wheel, false, $readonly, 'number');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_construction_equipment_grinder_rotational_frequency', 'form[grinder_rotational_frequency]', $machine->grinder_rotational_frequency, false, $readonly, 'text');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_construction_equipment_grinder_sanding', 'form[grinder_sanding]', $machine->grinder_sanding, false, $readonly, 'text');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_construction_equipment_grinder_vacuum_connection', 'form[grinder_vacuum_connection]', $machine->grinder_vacuum_connection, false, $readonly, 'number');
                        echo '</div>';
                        echo '</fieldset>';

                        echo '<fieldset>';
                        echo '<legend><small><i class="rex-icon fa-arrow-up"></i></small> '. rex_i18n::msg('d2u_machinery_construction_equipment') .' - '. rex_i18n::msg('d2u_machinery_construction_equipment_pumps') .'</legend>';
                        echo '<div class="panel-body-wrapper slide">';
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_construction_equipment_operating_pressure', 'form[operating_pressure]', $machine->operating_pressure, false, $readonly, 'text');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_construction_equipment_pump_filling', 'form[pump_filling]', $machine->pump_filling, false, $readonly, 'number');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_construction_equipment_pump_grain_size', 'form[pump_grain_size]', $machine->pump_grain_size, false, $readonly, 'text');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_construction_equipment_pump_material_container', 'form[pump_material_container]', $machine->pump_material_container, false, $readonly, 'text');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_construction_equipment_pump_flow_volume', 'form[pump_flow_volume]', $machine->pump_flow_volume, false, $readonly, 'text');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_construction_equipment_pump_conveying_distance', 'form[pump_conveying_distance]', $machine->pump_conveying_distance, false, $readonly, 'number');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_construction_equipment_pump_pressure_height', 'form[pump_pressure_height]', $machine->pump_pressure_height, false, $readonly, 'number');
                        echo '</div>';
                        echo '</fieldset>';

                        echo '<fieldset>';
                        echo '<legend><small><i class="rex-icon fa-arrow-up"></i></small> '. rex_i18n::msg('d2u_machinery_construction_equipment') .' - '. rex_i18n::msg('d2u_machinery_construction_equipment_waste_water') .'</legend>';
                        echo '<div class="panel-body-wrapper slide">';
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_construction_equipment_waste_water_capacity', 'form[waste_water_capacity]', $machine->waste_water_capacity, false, $readonly, 'number');
                        echo '</div>';
                        echo '</fieldset>';
                    }
                    if (rex_plugin::get('d2u_machinery', 'service_options')->isAvailable()) {
                        echo '<fieldset>';
                        echo '<legend><small><i class="rex-icon fa-check-square"></i></small> '. rex_i18n::msg('d2u_machinery_service_options') .'</legend>';
                        echo '<div class="panel-body-wrapper slide">';
                        $options_services = [];
                        foreach (ServiceOption::getAll((int) rex_config::get('d2u_helper', 'default_lang'), false) as $service_options) {
                            $options_services[$service_options->service_option_id] = $service_options->name;
                        }
                        \TobiasKrais\D2UHelper\BackendHelper::form_select('d2u_machinery_service_options', 'form[service_option_ids][]', $options_services, $machine->service_option_ids, 10, true, $readonly);
                        echo '</div>';
                        echo '</fieldset>';
                    }
                    if (rex_plugin::get('d2u_machinery', 'machine_features_extension')->isAvailable()) {
                        echo '<fieldset>';
                        echo '<legend><small><i class="rex-icon fa-plug"></i></small> '. rex_i18n::msg('d2u_machinery_features') .'</legend>';
                        echo '<div class="panel-body-wrapper slide">';
                        $options_features = [];
                        foreach (Feature::getAll((int) rex_config::get('d2u_helper', 'default_lang'), false !== $machine->category ? $machine->category->category_id : 0) as $feature) {
                            $options_features[$feature->feature_id] = $feature->priority .' - '. $feature->name .' (ID: '. $feature->feature_id .')';
                        }
                        \TobiasKrais\D2UHelper\BackendHelper::form_select('d2u_machinery_features', 'form[feature_ids][]', $options_features, $machine->feature_ids, 10, true, $readonly);
                        echo '</div>';
                        echo '</fieldset>';
                    }
                    if (rex_plugin::get('d2u_machinery', 'machine_options_extension')->isAvailable()) {
                        echo '<fieldset>';
                        echo '<legend><small><i class="rex-icon fa-plug"></i></small> '. rex_i18n::msg('d2u_machinery_options') .'</legend>';
                        echo '<div class="panel-body-wrapper slide">';
                        $options_options = [];
                        foreach (Option::getAll((int) rex_config::get('d2u_helper', 'default_lang'), false !== $machine->category ? $machine->category->category_id : 0) as $option) {
                            $options_options[$option->option_id] = $option->priority .' - '. $option->name .' (ID: '. $option->option_id .')';
                        }
                        \TobiasKrais\D2UHelper\BackendHelper::form_select('d2u_machinery_options', 'form[option_ids][]', $options_options, $machine->option_ids, 10, true, $readonly);
                        echo '</div>';
                        echo '</fieldset>';
                    }
                    if (rex_plugin::get('d2u_machinery', 'machine_steel_processing_extension')->isAvailable()) {
                        echo '<fieldset>';
                        echo '<legend><small><i class="rex-icon fa-steam"></i></small> '. rex_i18n::msg('d2u_machinery_machine_steel_extension') .'</legend>';
                        echo '<div class="panel-body-wrapper slide">';
                        $options_processes = [];
                        foreach (Process::getAll((int) rex_config::get('d2u_helper', 'default_lang')) as $process) {
                            $options_processes[$process->process_id] = $process->name;
                        }
                        \TobiasKrais\D2UHelper\BackendHelper::form_select('d2u_machinery_steel_processes', 'form[process_ids][]', $options_processes, array_keys($machine->processes), 4, true, $readonly);
                        $options_procedures = [];
                        foreach (Procedure::getAll((int) rex_config::get('d2u_helper', 'default_lang')) as $procedure) {
                            $options_procedures[$procedure->procedure_id] = $procedure->name;
                        }
                        \TobiasKrais\D2UHelper\BackendHelper::form_select('d2u_machinery_steel_procedures', 'form[procedure_ids][]', $options_procedures, array_keys($machine->procedures), 4, true, $readonly);
                        $options_materials = [];
                        foreach (Material::getAll((int) rex_config::get('d2u_helper', 'default_lang')) as $material) {
                            $options_materials[$material->material_id] = $material->name;
                        }
                        \TobiasKrais\D2UHelper\BackendHelper::form_select('d2u_machinery_steel_material_class', 'form[material_ids][]', $options_materials, array_keys($machine->materials), 4, true, $readonly);
                        $options_tools = [];
                        foreach (Tool::getAll((int) rex_config::get('d2u_helper', 'default_lang')) as $tool) {
                            $options_tools[$tool->tool_id] = $tool->name;
                        }
                        \TobiasKrais\D2UHelper\BackendHelper::form_select('d2u_machinery_steel_tools', 'form[tool_ids][]', $options_tools, array_keys($machine->tools), 4, true, $readonly);
                        echo '</div>';
                        echo '</fieldset>';

                        echo '<fieldset>';
                        echo '<legend><small><i class="rex-icon fa-steam"></i></small> '. rex_i18n::msg('d2u_machinery_steel_automation') .'</legend>';
                        echo '<div class="panel-body-wrapper slide">';
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_steel_automation_supply_single_stroke', 'form[automation_supply_single_stroke]', $machine->automation_supply_single_stroke, false, $readonly, 'text');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_steel_automation_supply_multi_stroke', 'form[automation_supply_multi_stroke]', $machine->automation_supply_multi_stroke, false, $readonly, 'text');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_steel_automation_feedrate', 'form[automation_feedrate]', $machine->automation_feedrate, false, $readonly, 'text');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_steel_automation_rush_leader_flyback', 'form[automation_rush_leader_flyback]', $machine->automation_rush_leader_flyback, false, $readonly, 'text');
                        $options_automation = [];
                        foreach (Automation::getAll((int) rex_config::get('d2u_helper', 'default_lang')) as $automation) {
                            $options_automation[$automation->automation_id] = $automation->name .' (ID: '. $automation->automation_id .')';
                        }
                        \TobiasKrais\D2UHelper\BackendHelper::form_select('d2u_machinery_steel_automation_automationgrades', 'form[automation_automationgrade_ids][]', $options_automation, array_keys($machine->automation_automationgrades), 4, true, $readonly);
                        $options_supply = [];
                        foreach (Supply::getAll((int) rex_config::get('d2u_helper', 'default_lang')) as $supply) {
                            $options_supply[$supply->supply_id] = $supply->priority .' - '. $supply->name .' (ID: '. $supply->supply_id .')';
                        }
                        \TobiasKrais\D2UHelper\BackendHelper::form_select('d2u_machinery_steel_automation_supplys', 'form[automation_supply_ids][]', $options_supply, $machine->automation_supply_ids, 4, true, $readonly);
                        echo '</div>';
                        echo '</fieldset>';

                        echo '<fieldset>';
                        echo '<legend><small><i class="rex-icon fa-steam"></i></small> '. rex_i18n::msg('d2u_machinery_steel_sheet_processing') .'</legend>';
                        echo '<div class="panel-body-wrapper slide">';
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_steel_workspace', 'form[workspace]', $machine->workspace, false, $readonly, 'text');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_steel_workspace_min', 'form[workspace_min]', $machine->workspace_min, false, $readonly, 'text');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_steel_workspace_square', 'form[workspace_square]', $machine->workspace_square, false, $readonly, 'text');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_steel_workspace_flat', 'form[workspace_flat]', $machine->workspace_flat, false, $readonly, 'text');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_steel_workspace_plate', 'form[workspace_plate]', $machine->workspace_plate, false, $readonly, 'text');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_steel_workspace_profile', 'form[workspace_profile]', $machine->workspace_profile, false, $readonly, 'text');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_steel_workspace_angle_steel', 'form[workspace_angle_steel]', $machine->workspace_angle_steel, false, $readonly, 'text');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_steel_workspace_round', 'form[workspace_round]', $machine->workspace_round, false, $readonly, 'text');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_steel_sheet_width', 'form[sheet_width]', $machine->sheet_width, false, $readonly, 'text');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_steel_sheet_length', 'form[sheet_length]', $machine->sheet_length, false, $readonly, 'text');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_steel_sheet_thickness', 'form[sheet_thickness]', $machine->sheet_thickness, false, $readonly, 'text');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_steel_tool_changer_locations', 'form[tool_changer_locations]', $machine->tool_changer_locations, false, $readonly, 'text');
                        echo '</div>';
                        echo '</fieldset>';

                        echo '<fieldset>';
                        echo '<legend><small><i class="rex-icon fa-steam"></i></small> '. rex_i18n::msg('d2u_machinery_steel_drilling') .'</legend>';
                        echo '<div class="panel-body-wrapper slide">';
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_steel_drilling_unit_vertical', 'form[drilling_unit_vertical]', $machine->drilling_unit_vertical, false, $readonly, 'text');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_steel_drilling_unit_horizontal', 'form[drilling_unit_horizontal]', $machine->drilling_unit_horizontal, false, $readonly, 'text');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_steel_drilling_unit_below', 'form[drilling_unit_below]', $machine->drilling_unit_below, false, $readonly, 'number');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_steel_drilling_diameter', 'form[drilling_diameter]', $machine->drilling_diameter, false, $readonly, 'text');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_steel_drilling_tools_axis', 'form[drilling_tools_axis]', $machine->drilling_tools_axis, false, $readonly, 'text');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_steel_drilling_axis_drive_power', 'form[drilling_axis_drive_power]', $machine->drilling_axis_drive_power, false, $readonly, 'text');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_steel_drilling_rpm_speed', 'form[drilling_rpm_speed]', $machine->drilling_rpm_speed, false, $readonly, 'text');
                        echo '</div>';
                        echo '</fieldset>';

                        echo '<fieldset>';
                        echo '<legend><small><i class="rex-icon fa-steam"></i></small> '. rex_i18n::msg('d2u_machinery_steel_saw') .'</legend>';
                        echo '<div class="panel-body-wrapper slide">';
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_steel_saw_blade', 'form[saw_blade]', $machine->saw_blade, false, $readonly, 'text');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_steel_saw_band', 'form[saw_band]', $machine->saw_band, false, $readonly, 'text');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_steel_saw_band_tilt', 'form[saw_band_tilt]', $machine->saw_band_tilt, false, $readonly, 'text');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_steel_automation_feedrate_sawblade', 'form[automation_feedrate_sawblade]', $machine->automation_feedrate_sawblade, false, $readonly, 'text');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_steel_saw_cutting_speed', 'form[saw_cutting_speed]', $machine->saw_cutting_speed, false, $readonly, 'text');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_steel_saw_miter', 'form[saw_miter]', $machine->saw_miter, false, $readonly, 'text');
                        echo '</div>';
                        echo '</fieldset>';

                        echo '<fieldset>';
                        echo '<legend><small><i class="rex-icon fa-steam"></i></small> '. rex_i18n::msg('d2u_machinery_steel_punching') .'</legend>';
                        echo '<div class="panel-body-wrapper slide">';
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_steel_punching_bevel_angle', 'form[bevel_angle]', $machine->bevel_angle, false, $readonly, 'number');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_steel_punching_diameter', 'form[punching_diameter]', $machine->punching_diameter, false, $readonly, 'text');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_steel_punching_power', 'form[punching_power]', $machine->punching_power, false, $readonly, 'number');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_steel_punching_tools', 'form[punching_tools]', $machine->punching_tools, false, $readonly, 'text');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_steel_punching_shaving_unit_angle_steel_single_cut', 'form[shaving_unit_angle_steel_single_cut]', $machine->shaving_unit_angle_steel_single_cut, false, $readonly, 'number');
                        $options_profile = [];
                        foreach (Profile::getAll((int) rex_config::get('d2u_helper', 'default_lang')) as $profile) {
                            $options_profile[$profile->profile_id] = $profile->name;
                        }
                        \TobiasKrais\D2UHelper\BackendHelper::form_select('d2u_machinery_steel_punching_profiles', 'form[profile_ids][]', $options_profile, array_keys($machine->profiles), 4, true, $readonly);
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_steel_punching_carrier_width', 'form[carrier_width]', $machine->carrier_width, false, $readonly, 'text');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_steel_punching_carrier_height', 'form[carrier_height]', $machine->carrier_height, false, $readonly, 'text');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_steel_punching_carrier_weight', 'form[carrier_weight]', $machine->carrier_weight, false, $readonly, 'number');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_steel_punching_flange_thickness', 'form[flange_thickness]', $machine->flange_thickness, false, $readonly, 'text');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_steel_punching_web_thickness', 'form[web_thickness]', $machine->web_thickness, false, $readonly, 'text');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_steel_punching_component_length', 'form[component_length]', $machine->component_length, false, $readonly, 'text');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_steel_punching_component_weight', 'form[component_weight]', $machine->component_weight, false, $readonly, 'number');
                        $options_welding = [];
                        foreach (Welding::getAll((int) rex_config::get('d2u_helper', 'default_lang')) as $welding) {
                            $options_welding[$welding->welding_id] = $welding->name;
                        }
                        \TobiasKrais\D2UHelper\BackendHelper::form_select('d2u_machinery_steel_weldings', 'form[welding_process_ids][]', $options_welding, array_keys($machine->weldings), 4, true, $readonly);
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_steel_welding_thickness', 'form[welding_thickness]', $machine->welding_thickness, false, $readonly, 'number');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_steel_welding_wire_thickness', 'form[welding_wire_thickness]', $machine->welding_wire_thickness, false, $readonly, 'text');
                        echo '</div>';
                        echo '</fieldset>';

                        echo '<fieldset>';
                        echo '<legend><small><i class="rex-icon fa-steam"></i></small> '. rex_i18n::msg('d2u_machinery_steel_beam') .'</legend>';
                        echo '<div class="panel-body-wrapper slide">';
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_steel_beam_continuous_opening', 'form[beam_continuous_opening]', $machine->beam_continuous_opening, false, $readonly, 'text');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_steel_beam_turbines', 'form[beam_turbines]', $machine->beam_turbines, false, $readonly, 'number');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_steel_beam_turbine_power', 'form[beam_turbine_power]', $machine->beam_turbine_power, false, $readonly, 'text');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_steel_beam_color_guns', 'form[beam_color_guns]', $machine->beam_color_guns, false, $readonly, 'text');
                        echo '</div>';
                        echo '</fieldset>';
                    }
                    if (rex_plugin::get('d2u_machinery', 'machine_usage_area_extension')->isAvailable()) {
                        echo '<fieldset>';
                        echo '<legend><small><i class="rex-icon fa-codepen"></i></small> '. rex_i18n::msg('d2u_machinery_usage_areas') .'</legend>';
                        echo '<div class="panel-body-wrapper slide">';
                        $options_usage_areas = [];
                        foreach (UsageArea::getAll((int) rex_config::get('d2u_helper', 'default_lang'), $machine->category instanceof Category ? $machine->category->category_id : 0) as $usage_area) {
                            $options_usage_areas[$usage_area->usage_area_id] = $usage_area->name;
                        }
                        \TobiasKrais\D2UHelper\BackendHelper::form_select('d2u_machinery_usage_areas', 'form[usage_area_ids][]', $options_usage_areas, $machine->usage_area_ids, 10, true, $readonly);
                        echo '</div>';
                        echo '</fieldset>';
                    }

                    foreach (rex_clang::getAll() as $rex_clang) {
                        $machine_lang = new Machine($entry_id, $rex_clang->getId());
                        $required = $rex_clang->getId() === (int) (rex_config::get('d2u_helper', 'default_lang')) ? true : false;

                        $readonly_lang = true;
                        if (\rex::getUser() instanceof rex_user && (\rex::getUser()->isAdmin() || (\rex::getUser()->hasPerm('d2u_machinery[edit_lang]') && \rex::getUser()->getComplexPerm('clang') instanceof rex_clang_perm && \rex::getUser()->getComplexPerm('clang')->hasPerm($rex_clang->getId())))) {
                            $readonly_lang = false;
                        }
                ?>
					<fieldset>
						<legend><?= rex_i18n::msg('d2u_helper_text_lang') .' "'. $rex_clang->getName() .'"' ?></legend>
						<div class="panel-body-wrapper slide">
							<?php
                                if ($rex_clang->getId() !== (int) rex_config::get('d2u_helper', 'default_lang')) {
                                    $options_translations = [];
                                    $options_translations['yes'] = rex_i18n::msg('d2u_helper_translation_needs_update');
                                    $options_translations['no'] = rex_i18n::msg('d2u_helper_translation_is_uptodate');
                                    $options_translations['delete'] = rex_i18n::msg('d2u_helper_translation_delete');
                                    \TobiasKrais\D2UHelper\BackendHelper::form_select('d2u_helper_translation', 'form[lang]['. $rex_clang->getId() .'][translation_needs_update]', $options_translations, [$machine_lang->translation_needs_update], 1, false, $readonly_lang);
                                } else {
                                    echo '<input type="hidden" name="form[lang]['. $rex_clang->getId() .'][translation_needs_update]" value="no">';
                                }
                            ?>
							<script>
								// Hide on document load
								$(document).ready(function() {
									toggleClangDetailsView(<?= $rex_clang->getId() ?>);
								});

								// Hide on selection change
								$("select[name='form[lang][<?= $rex_clang->getId() ?>][translation_needs_update]']").on('change', function(e) {
									toggleClangDetailsView(<?= $rex_clang->getId() ?>);
								});
							</script>
							<div id="details_clang_<?= $rex_clang->getId() ?>">
								<?php
                                    \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_lang_name', 'form[lang]['. $rex_clang->getId() .'][lang_name]', $machine_lang->lang_name, false, $readonly_lang, 'text');
                                    \TobiasKrais\D2UHelper\BackendHelper::form_textarea('d2u_machinery_machine_teaser', 'form[lang]['. $rex_clang->getId() .'][teaser]', $machine_lang->teaser, 3, false, $readonly_lang, false);
                                    \TobiasKrais\D2UHelper\BackendHelper::form_textarea('d2u_helper_description', 'form[lang]['. $rex_clang->getId() .'][description]', $machine_lang->description, 5, false, $readonly_lang, true);
                                    \TobiasKrais\D2UHelper\BackendHelper::form_textarea('d2u_machinery_benefits_long', 'form[lang]['. $rex_clang->getId() .'][benefits_long]', $machine_lang->benefits_long, 5, false, $readonly_lang, true);
                                    \TobiasKrais\D2UHelper\BackendHelper::form_textarea('d2u_machinery_benefits_short', 'form[lang]['. $rex_clang->getId() .'][benefits_short]', $machine_lang->benefits_short, 5, false, $readonly_lang, true);
                                    \TobiasKrais\D2UHelper\BackendHelper::form_medialistfield('d2u_machinery_machine_pdfs', (int) ('1'. $rex_clang->getId()), $machine_lang->pdfs, $readonly_lang);
                                    \TobiasKrais\D2UHelper\BackendHelper::form_mediafield('d2u_machinery_machine_leaflet', '1'. $rex_clang->getId(), $machine_lang->leaflet, $readonly_lang);
                                    if (rex_plugin::get('d2u_machinery', 'machine_construction_equipment_extension')->isAvailable()) {
                                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_construction_equipment_container_connection_port', 'form[lang]['. $rex_clang->getId() .'][container_connection_port]', $machine_lang->container_connection_port, false, $readonly_lang, 'text');
                                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_construction_equipment_container_conveying_wave', 'form[lang]['. $rex_clang->getId() .'][container_conveying_wave]', $machine_lang->container_conveying_wave, false, $readonly_lang, 'text');
                                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_machinery_construction_equipment_description_technical', 'form[lang]['. $rex_clang->getId() .'][description_technical]', $machine_lang->description_technical, false, $readonly_lang, 'text');
                                        \TobiasKrais\D2UHelper\BackendHelper::form_textarea('d2u_machinery_construction_equipment_delivery_set_basic', 'form[lang]['. $rex_clang->getId() .'][delivery_set_basic]', $machine_lang->delivery_set_basic, 5, false, $readonly_lang, true);
                                        \TobiasKrais\D2UHelper\BackendHelper::form_textarea('d2u_machinery_construction_equipment_delivery_set_conversion', 'form[lang]['. $rex_clang->getId() .'][delivery_set_conversion]', $machine_lang->delivery_set_conversion, 5, false, $readonly_lang, true);
                                        \TobiasKrais\D2UHelper\BackendHelper::form_textarea('d2u_machinery_construction_equipment_delivery_set_full', 'form[lang]['. $rex_clang->getId() .'][delivery_set_full]', $machine_lang->delivery_set_full, 5, false, $readonly_lang, true);
                                    }
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
						<button class="btn btn-save rex-form-aligned" type="submit" name="btn_save" value="1"><?= rex_i18n::msg('form_save') ?></button>
						<button class="btn btn-apply" type="submit" name="btn_apply" value="1"><?= rex_i18n::msg('form_apply') ?></button>
						<button class="btn btn-abort" type="submit" name="btn_abort" formnovalidate="formnovalidate" value="1"><?= rex_i18n::msg('form_abort') ?></button>
						<?php
                            if (\rex::getUser() instanceof rex_user && (\rex::getUser()->isAdmin() || \rex::getUser()->hasPerm('d2u_machinery[edit_data]'))) {
                                echo '<button class="btn btn-delete" type="submit" name="btn_delete" formnovalidate="formnovalidate" data-confirm="'. rex_i18n::msg('form_delete') .'?" value="1">'. rex_i18n::msg('form_delete') .'</button>';
                            }
                        ?>
					</div>
				</div>
			</footer>
		</div>
	</form>
	<br>
	<?php
        echo \TobiasKrais\D2UHelper\BackendHelper::getCSS();
        echo \TobiasKrais\D2UHelper\BackendHelper::getJS();
}

if ('' === $func) {
    $query = 'SELECT machine.machine_id, machine.name, category.name AS categoryname, online_status, priority '
        . 'FROM '. \rex::getTablePrefix() .'d2u_machinery_machines AS machine '
        . 'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_categories_lang AS category '
            . 'ON machine.category_id = category.category_id AND category.clang_id = '. (int) rex_config::get('d2u_helper', 'default_lang') .' ';
    if ('priority' === rex_config::get('d2u_machinery', 'default_machine_sort')) {
        $query .= 'ORDER BY priority ASC';
    } else {
        $query .= 'ORDER BY machine.name ASC';
    }
    $list = rex_list::factory($query, 1000);

    $list->addTableAttribute('class', 'table-striped table-hover');

    $tdIcon = '<i class="rex-icon rex-icon-module"></i>';
    $thIcon = '';
    if (\rex::getUser() instanceof rex_user && (\rex::getUser()->isAdmin() || \rex::getUser()->hasPerm('d2u_machinery[edit_data]'))) {
        $thIcon = '<a href="' . $list->getUrl(['func' => 'add']) . '" title="' . rex_i18n::msg('add') . '"><i class="rex-icon rex-icon-add-module"></i></a>';
    }
    $list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
    $list->setColumnParams($thIcon, ['func' => 'edit', 'entry_id' => '###machine_id###']);

    $list->setColumnLabel('machine_id', rex_i18n::msg('id'));

    $list->setColumnLabel('name', rex_i18n::msg('d2u_helper_name'));
    $list->setColumnParams('name', ['func' => 'edit', 'entry_id' => '###machine_id###']);

    $list->setColumnLabel('categoryname', rex_i18n::msg('d2u_helper_category'));

    $list->setColumnLabel('priority', rex_i18n::msg('header_priority'));

    $list->addColumn(rex_i18n::msg('module_functions'), '<i class="rex-icon rex-icon-edit"></i> ' . rex_i18n::msg('edit'));
    $list->setColumnLayout(rex_i18n::msg('module_functions'), ['<th class="rex-table-action" colspan="2">###VALUE###</th>', '<td class="rex-table-action">###VALUE###</td>']);
    $list->setColumnParams(rex_i18n::msg('module_functions'), ['func' => 'edit', 'entry_id' => '###machine_id###']);

    $list->removeColumn('online_status');
    if (\rex::getUser() instanceof rex_user && (\rex::getUser()->isAdmin() || \rex::getUser()->hasPerm('d2u_machinery[edit_data]'))) {
        $list->addColumn(rex_i18n::msg('status_online'), '<a class="rex-###online_status###" href="' . rex_url::currentBackendPage(['func' => 'changestatus']) . '&entry_id=###machine_id###"><i class="rex-icon rex-icon-###online_status###"></i> ###online_status###</a>');
        $list->setColumnLayout(rex_i18n::msg('status_online'), ['', '<td class="rex-table-action">###VALUE###</td>']);

        $list->addColumn(rex_i18n::msg('d2u_helper_clone'), '<i class="rex-icon fa-copy"></i> ' . rex_i18n::msg('d2u_helper_clone'));
        $list->setColumnLayout(rex_i18n::msg('d2u_helper_clone'), ['', '<td class="rex-table-action">###VALUE###</td>']);
        $list->setColumnParams(rex_i18n::msg('d2u_helper_clone'), ['func' => 'clone', 'entry_id' => '###machine_id###']);

        $list->addColumn(rex_i18n::msg('delete_module'), '<i class="rex-icon rex-icon-delete"></i> ' . rex_i18n::msg('delete'));
        $list->setColumnLayout(rex_i18n::msg('delete_module'), ['', '<td class="rex-table-action">###VALUE###</td>']);
        $list->setColumnParams(rex_i18n::msg('delete_module'), ['func' => 'delete', 'entry_id' => '###machine_id###']);
        $list->addLinkAttribute(rex_i18n::msg('delete_module'), 'data-confirm', rex_i18n::msg('d2u_helper_confirm_delete'));
    }

    $list->setNoRowsMessage(rex_i18n::msg('d2u_machinery_machine_no_machines_found'));

    $fragment = new rex_fragment();
    $fragment->setVar('title', rex_i18n::msg('d2u_machinery_meta_machines'), false);
    $fragment->setVar('content', $list->get(), false);
    echo $fragment->parse('core/page/section.php');
}
