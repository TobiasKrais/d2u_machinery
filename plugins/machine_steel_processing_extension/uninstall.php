<?php

$sql = \rex_sql::factory();

// Delete columns in machine tables
\rex_sql_table::get(
    \rex::getTable('d2u_machinery_machines'))
    ->removeColumn('process_ids')
    ->removeColumn('procedure_ids')
    ->removeColumn('material_ids')
    ->removeColumn('tool_ids')
    ->removeColumn('automation_supply_single_stroke')
    ->removeColumn('automation_supply_multi_stroke')
    ->removeColumn('automation_feedrate')
    ->removeColumn('automation_rush_leader_flyback')
    ->removeColumn('automation_automationgrade_ids')
    ->removeColumn('automation_supply_ids')
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

// Delete tables
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
$sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_steel_supply');
$sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_steel_supply_lang');
$sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_steel_tool');
$sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_steel_tool_lang');
$sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_steel_welding');
$sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_steel_welding_lang');

// Delete language replacements
if (!class_exists('d2u_machinery_machine_steel_processing_extension_lang_helper')) {
    // Load class in case addon is deactivated
    require_once 'lib/d2u_machinery_machine_steel_processing_extension_lang_helper.php';
}
d2u_machinery_machine_steel_processing_extension_lang_helper::factory()->uninstall();
