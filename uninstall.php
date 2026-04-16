<?php

$d2uMachineryAction = $d2uMachineryAction ?? null;
$d2uMachineryRequestedAction = $d2uMachineryRequestedAction ?? $d2uMachineryAction;
$d2uMachineryTriggeredByLegacyPlugin = $d2uMachineryTriggeredByLegacyPlugin ?? false;
$d2uMachineryCascadeDependencies = $d2uMachineryCascadeDependencies ?? false;
$d2uMachineryDeactivatedActions = null;

if (null !== $d2uMachineryRequestedAction) {
    if (!class_exists(\TobiasKrais\D2UMachinery\Extension::class)) {
        require_once __DIR__ .'/lib/Extension.php';
    }
    $d2uMachineryDeactivatedActions = $d2uMachineryCascadeDependencies
        ? \TobiasKrais\D2UMachinery\Extension::getDeactivationCascade($d2uMachineryRequestedAction)
        : [$d2uMachineryRequestedAction];
}

/**
 * @param array<int,string>|string|null $action
 */
if (!function_exists('d2u_machinery_should_uninstall')) {
    function d2u_machinery_should_uninstall(array|string|null $action, string $extension): bool
    {
        if (null === $action) {
            return true;
        }

        if (is_array($action)) {
            return in_array($extension, $action, true);
        }

        return $action === $extension;
    }
}

if (!function_exists('d2u_machinery_delete_url_profile_by_namespace')) {
    function d2u_machinery_delete_url_profile_by_namespace(string $namespace): void
    {
        if (!\rex_addon::get('url')->isAvailable()) {
            return;
        }

        foreach (\Url\Profile::getByNamespace($namespace) as $profile) {
            $profile->deleteUrls();
        }

        \rex_sql::factory()->setQuery('DELETE FROM '. \rex::getTablePrefix() ."url_generator_profile WHERE `namespace` = '". $namespace ."';");
    }
}

$sql = \rex_sql::factory();

if (null === $d2uMachineryAction) {
    // Delete views
    $sql->setQuery('DROP VIEW IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_url_machines');
    $sql->setQuery('DROP VIEW IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_url_machine_categories');

    // Delete url schemes
    if (\rex_addon::get('url')->isAvailable()) {
        d2u_machinery_delete_url_profile_by_namespace('machine_id');
        d2u_machinery_delete_url_profile_by_namespace('category_id');
    }

    // Delete language replacements
    if (!class_exists(\TobiasKrais\D2UMachinery\LangHelper::class)) {
        // Load class in case addon is deactivated
        require_once 'lib/LangHelper.php';
    }
    \TobiasKrais\D2UMachinery\LangHelper::factory()->uninstall();

    // Delete tables
    $sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_machines');
    $sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_machines_lang');
    $sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_categories');
    $sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_categories_lang');

    // Delete Media Manager media types
    $sql->setQuery('DELETE FROM '. \rex::getTablePrefix() ."media_manager_type WHERE name LIKE 'd2u_machinery%'");
    $sql->setQuery('DELETE FROM '. \rex::getTablePrefix() ."media_manager_type_effect WHERE createuser = 'd2u_machinery'");

    // Delete e-mail template
    $sql->setQuery('DELETE FROM '. \rex::getTablePrefix() ."yform_email_template WHERE name LIKE 'd2u_machinery%'");
}

// Plugin: contacts
if (d2u_machinery_should_uninstall($d2uMachineryAction, 'contacts')) {
    rex_sql_table::get(rex::getTable('d2u_machinery_machines'))
        ->removeColumn('contact_id')
        ->alter();
    $sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_machinery_contacts');
}

// Plugin: equipment
if (d2u_machinery_should_uninstall($d2uMachineryAction, 'equipment')) {
    $sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_equipments');
    $sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_equipments_lang');
    $sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_equipment_groups');
    $sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_equipment_groups_lang');
    \rex_sql_table::get(\rex::getTable('d2u_machinery_machines'))
        ->removeColumn('equipment_ids')
        ->ensure();
}

// Plugin: export
if (d2u_machinery_should_uninstall($d2uMachineryAction, 'export')) {
    $sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_export_machines');
    $sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_export_provider');
    \rex_sql_table::get(\rex::getTable('d2u_machinery_categories'))
        ->removeColumn('export_machinerypark_category_id')
        ->removeColumn('export_europemachinery_category_id')
        ->removeColumn('export_europemachinery_category_name')
        ->removeColumn('export_mascus_category_name')
        ->ensure();
    if (!class_exists(\TobiasKrais\D2UMachinery\ExportCronjob::class)) {
        require_once 'lib/ExportCronjob.php';
    }
    $export_cronjob = \TobiasKrais\D2UMachinery\ExportCronjob::factory();
    if ($export_cronjob->isInstalled()) {
        $export_cronjob->delete();
    }
}

// Plugin: industry sectors
if (d2u_machinery_should_uninstall($d2uMachineryAction, 'industry_sectors')) {
    $sql->setQuery('DROP VIEW IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_url_industry_sectors');
    if (\rex_addon::get('url')->isAvailable()) {
        d2u_machinery_delete_url_profile_by_namespace('industry_sector_id');
    }
    $sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_industry_sectors');
    $sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_industry_sectors_lang');
    \rex_sql_table::get(\rex::getTable('d2u_machinery_machines'))
        ->removeColumn('industry_sector_ids')
        ->ensure();
}

// Plugin: agitators
if (d2u_machinery_should_uninstall($d2uMachineryAction, 'machine_agitator_extension')) {
    $sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_agitator_types');
    $sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_agitator_types_lang');
    $sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_agitators');
    $sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_agitators_lang');
    \rex_sql_table::get(\rex::getTable('d2u_machinery_machines'))
        ->removeColumn('agitator_type_id')
        ->removeColumn('viscosity')
        ->ensure();
    \rex_sql_table::get(\rex::getTable('d2u_machinery_categories'))
        ->removeColumn('show_agitators')
        ->ensure();
}

// Plugin: certificates
if (d2u_machinery_should_uninstall($d2uMachineryAction, 'machine_certificates_extension')) {
    $sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_certificates');
    $sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_certificates_lang');
    \rex_sql_table::get(\rex::getTable('d2u_machinery_machines'))
        ->removeColumn('certificate_ids')
        ->ensure();
}

// Plugin: construction equipment
if (d2u_machinery_should_uninstall($d2uMachineryAction, 'machine_construction_equipment_extension')) {
    \rex_sql_table::get(\rex::getTable('d2u_machinery_machines'))
        ->removeColumn('airless_hose_connection')
        ->removeColumn('airless_hose_diameter')
        ->removeColumn('airless_hose_length')
        ->removeColumn('airless_nozzle_size')
        ->removeColumn('container_capacity')
        ->removeColumn('container_capacity_unit')
        ->removeColumn('container_mixing_performance')
        ->removeColumn('container_waterconnect_pressure')
        ->removeColumn('container_waterconnect_diameter')
        ->removeColumn('container_weight_empty')
        ->removeColumn('cutters_cutting_depth')
        ->removeColumn('cutters_cutting_length')
        ->removeColumn('cutters_rod_length')
        ->removeColumn('floor_beam_power_on_concrete')
        ->removeColumn('floor_dust_extraction_connection')
        ->removeColumn('floor_feedrate')
        ->removeColumn('floor_filter_connection')
        ->removeColumn('floor_rotations')
        ->removeColumn('floor_working_pressure')
        ->removeColumn('floor_working_width')
        ->removeColumn('grinder_grinding_plate')
        ->removeColumn('grinder_grinding_wheel')
        ->removeColumn('grinder_rotational_frequency')
        ->removeColumn('grinder_sanding')
        ->removeColumn('grinder_vacuum_connection')
        ->removeColumn('operating_pressure')
        ->removeColumn('picture_delivery_set')
        ->removeColumn('pump_conveying_distance')
        ->removeColumn('pump_filling')
        ->removeColumn('pump_flow_volume')
        ->removeColumn('pump_grain_size')
        ->removeColumn('pump_material_container')
        ->removeColumn('pump_pressure_height')
        ->removeColumn('waste_water_capacity')
        ->ensure();
    \rex_sql_table::get(\rex::getTable('d2u_machinery_machines_lang'))
        ->removeColumn('container_connection_port')
        ->removeColumn('container_conveying_wave')
        ->removeColumn('description_technical')
        ->removeColumn('delivery_set_basic')
        ->removeColumn('delivery_set_conversion')
        ->removeColumn('delivery_set_full')
        ->ensure();
    $sql->setQuery('DELETE FROM '. \rex::getTablePrefix() ."media_manager_type WHERE name LIKE 'd2u_machinery_construction_equipment%'");
    $sql->setQuery('DELETE FROM '. \rex::getTablePrefix() ."media_manager_type_effect WHERE createuser = 'd2u_machinery_construction_equipment'");
}

// Plugin: features
if (d2u_machinery_should_uninstall($d2uMachineryAction, 'machine_features_extension')) {
    $sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_features');
    $sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_features_lang');
    \rex_sql_table::get(\rex::getTable('d2u_machinery_machines'))
        ->removeColumn('feature_ids')
        ->ensure();
}

// Plugin: options
if (d2u_machinery_should_uninstall($d2uMachineryAction, 'machine_options_extension')) {
    $sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_options');
    $sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_options_lang');
    \rex_sql_table::get(\rex::getTable('d2u_machinery_machines'))
        ->removeColumn('option_ids')
        ->ensure();
}

// Plugin: steel processing
if (d2u_machinery_should_uninstall($d2uMachineryAction, 'machine_steel_processing_extension')) {
    \rex_sql_table::get(\rex::getTable('d2u_machinery_machines'))
        ->removeColumn('process_ids')
        ->removeColumn('procedure_ids')
        ->removeColumn('material_ids')
        ->removeColumn('tool_ids')
        ->removeColumn('automation_automationgrade_ids')
        ->removeColumn('workspace')
        ->removeColumn('workspace_square')
        ->removeColumn('workspace_flat')
        ->removeColumn('workspace_plate')
        ->removeColumn('workspace_profile')
        ->removeColumn('workspace_angle_steel')
        ->removeColumn('workspace_round')
        ->removeColumn('workspace_min')
        ->removeColumn('sheet_width')
        ->removeColumn('sheet_length')
        ->removeColumn('sheet_thickness')
        ->removeColumn('tool_changer_locations')
        ->removeColumn('drilling_unit_vertical')
        ->removeColumn('drilling_unit_horizontal')
        ->removeColumn('drilling_diameter')
        ->removeColumn('drilling_tools_axis')
        ->removeColumn('drilling_axis_drive_power')
        ->removeColumn('drilling_rpm_speed')
        ->removeColumn('saw_blade')
        ->removeColumn('saw_band')
        ->removeColumn('saw_band_tilt')
        ->removeColumn('saw_cutting_speed')
        ->removeColumn('saw_miter')
        ->removeColumn('bevel_angle')
        ->removeColumn('punching_diameter')
        ->removeColumn('punching_tools')
        ->removeColumn('shaving_unit_angle_steel_single_cut')
        ->removeColumn('profile_area_ids')
        ->removeColumn('carrier_width')
        ->removeColumn('carrier_height')
        ->removeColumn('carrier_weight')
        ->removeColumn('flange_thickness')
        ->removeColumn('web_thickness')
        ->removeColumn('component_length')
        ->removeColumn('component_weight')
        ->removeColumn('welding_process_ids')
        ->removeColumn('welding_thickness')
        ->removeColumn('welding_wire_thickness')
        ->removeColumn('beam_continuous_opening')
        ->removeColumn('beam_turbines')
        ->removeColumn('beam_turbine_power')
        ->removeColumn('beam_color_guns')
        ->ensure();
    $sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_steel_automation');
    $sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_steel_automation_lang');
    $sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_steel_material');
    $sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_steel_material_lang');
    $sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_steel_process');
    $sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_steel_process_lang');
    $sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_steel_procedure');
    $sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_steel_procedure_lang');
    $sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_steel_profile');
    $sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_steel_profile_lang');
    $sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_steel_tool');
    $sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_steel_tool_lang');
    $sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_steel_welding');
    $sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_steel_welding_lang');
}

// Plugin: steel automation
if (d2u_machinery_should_uninstall($d2uMachineryAction, 'machine_steel_automation_extension')) {
    \rex_sql_table::get(\rex::getTable('d2u_machinery_machines'))
        ->removeColumn('automation_supply_single_stroke')
        ->removeColumn('automation_supply_multi_stroke')
        ->removeColumn('automation_feedrate')
        ->removeColumn('automation_feedrate_sawblade')
        ->removeColumn('automation_rush_leader_flyback')
        ->removeColumn('automation_supply_ids')
        ->ensure();
    $sql->setQuery('SHOW TABLES LIKE "'. \rex::getTable('d2u_machinery_production_lines') .'"');
    if ($sql->getRows() > 0) {
        \rex_sql_table::get(\rex::getTable('d2u_machinery_production_lines'))
            ->removeColumn('automation_supply_ids')
            ->ensure();
    }
    $sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_steel_supply');
    $sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_steel_supply_lang');
}

// Plugin: usage areas
if (d2u_machinery_should_uninstall($d2uMachineryAction, 'machine_usage_area_extension')) {
    $sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_usage_areas');
    $sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_usage_areas_lang');
    \rex_sql_table::get(\rex::getTable('d2u_machinery_machines'))
        ->removeColumn('usage_area_ids')
        ->ensure();
}

// Plugin: production lines
if (d2u_machinery_should_uninstall($d2uMachineryAction, 'production_lines')) {
    $sql->setQuery('DROP VIEW IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_url_production_lines');
    if (\rex_addon::get('url')->isAvailable()) {
        d2u_machinery_delete_url_profile_by_namespace('production_line_id');
    }
    $sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_production_lines');
    $sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_production_lines_lang');
    $sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_production_lines_usps');
    $sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_production_lines_usps_lang');
}

// Plugin: service options
if (d2u_machinery_should_uninstall($d2uMachineryAction, 'service_options')) {
    $sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_service_options');
    $sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_service_options_lang');
    \rex_sql_table::get(\rex::getTable('d2u_machinery_machines'))
        ->removeColumn('service_option_ids')
        ->ensure();
}

// Plugin: used machines
if (d2u_machinery_should_uninstall($d2uMachineryAction, 'used_machines')) {
    $sql->setQuery('DROP VIEW IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_url_used_machines_rent');
    $sql->setQuery('DROP VIEW IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_url_used_machine_categories_rent');
    $sql->setQuery('DROP VIEW IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_url_used_machines_sale');
    $sql->setQuery('DROP VIEW IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_url_used_machine_categories_sale');
    if (\rex_addon::get('url')->isAvailable()) {
        d2u_machinery_delete_url_profile_by_namespace('used_rent_machine_id');
        d2u_machinery_delete_url_profile_by_namespace('used_rent_category_id');
        d2u_machinery_delete_url_profile_by_namespace('used_sale_machine_id');
        d2u_machinery_delete_url_profile_by_namespace('used_sale_category_id');
    }
    $sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_used_machines');
    $sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_used_machines_lang');
}

if (null !== $d2uMachineryRequestedAction) {
    if (!class_exists(\TobiasKrais\D2UMachinery\Extension::class)) {
        require_once __DIR__ .'/lib/Extension.php';
    }

    $deactivatedExtensions = is_array($d2uMachineryDeactivatedActions) ? $d2uMachineryDeactivatedActions : [$d2uMachineryRequestedAction];
    foreach ($deactivatedExtensions as $extensionKey) {
        \rex_config::set(
            'd2u_machinery',
            \TobiasKrais\D2UMachinery\Extension::getConfigKey($extensionKey),
            \TobiasKrais\D2UMachinery\Extension::STATE_INACTIVE
        );
    }

    if ($d2uMachineryTriggeredByLegacyPlugin) {
        foreach ($deactivatedExtensions as $extensionKey) {
            if ($extensionKey === $d2uMachineryRequestedAction) {
                continue;
            }

            \TobiasKrais\D2UMachinery\Extension::uninstallLegacyPlugin($extensionKey);
        }
    }
}
