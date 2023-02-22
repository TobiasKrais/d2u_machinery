<?php

\rex_sql_table::get(\rex::getTable('d2u_machinery_steel_automation'))
    ->ensureColumn(new rex_sql_column('automation_id', 'INT(11) unsigned', false, null, 'auto_increment'))
    ->setPrimaryKey('automation_id')
    ->ensureColumn(new \rex_sql_column('internal_name', 'VARCHAR(255)', true))
    ->ensure();
\rex_sql_table::get(\rex::getTable('d2u_machinery_steel_automation_lang'))
    ->ensureColumn(new rex_sql_column('automation_id', 'INT(11)'))
    ->ensureColumn(new \rex_sql_column('clang_id', 'INT(11)', false))
    ->setPrimaryKey(['automation_id', 'clang_id'])
    ->ensureColumn(new \rex_sql_column('name', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('translation_needs_update', 'VARCHAR(7)'))
    ->ensure();

\rex_sql_table::get(\rex::getTable('d2u_machinery_steel_material'))
    ->ensureColumn(new rex_sql_column('material_id', 'INT(11) unsigned', false, null, 'auto_increment'))
    ->setPrimaryKey('material_id')
    ->ensureColumn(new \rex_sql_column('internal_name', 'VARCHAR(255)', true))
    ->ensure();
\rex_sql_table::get(\rex::getTable('d2u_machinery_steel_material_lang'))
    ->ensureColumn(new rex_sql_column('material_id', 'INT(11)'))
    ->ensureColumn(new \rex_sql_column('clang_id', 'INT(11)', false))
    ->setPrimaryKey(['material_id', 'clang_id'])
    ->ensureColumn(new \rex_sql_column('name', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('translation_needs_update', 'VARCHAR(7)'))
    ->ensure();

\rex_sql_table::get(\rex::getTable('d2u_machinery_steel_process'))
    ->ensureColumn(new rex_sql_column('process_id', 'INT(11) unsigned', false, null, 'auto_increment'))
    ->setPrimaryKey('process_id')
    ->ensureColumn(new \rex_sql_column('internal_name', 'VARCHAR(255)', true))
    ->ensure();
\rex_sql_table::get(\rex::getTable('d2u_machinery_steel_process_lang'))
    ->ensureColumn(new rex_sql_column('process_id', 'INT(11)'))
    ->ensureColumn(new \rex_sql_column('clang_id', 'INT(11)', false))
    ->setPrimaryKey(['process_id', 'clang_id'])
    ->ensureColumn(new \rex_sql_column('name', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('translation_needs_update', 'VARCHAR(7)'))
    ->ensure();

\rex_sql_table::get(\rex::getTable('d2u_machinery_steel_procedure'))
    ->ensureColumn(new rex_sql_column('procedure_id', 'INT(11) unsigned', false, null, 'auto_increment'))
    ->setPrimaryKey('procedure_id')
    ->ensureColumn(new \rex_sql_column('internal_name', 'VARCHAR(255)', true))
    ->ensure();
\rex_sql_table::get(\rex::getTable('d2u_machinery_steel_procedure_lang'))
    ->ensureColumn(new rex_sql_column('procedure_id', 'INT(11)'))
    ->ensureColumn(new \rex_sql_column('clang_id', 'INT(11)', false))
    ->setPrimaryKey(['procedure_id', 'clang_id'])
    ->ensureColumn(new \rex_sql_column('name', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('translation_needs_update', 'VARCHAR(7)'))
    ->ensure();

\rex_sql_table::get(\rex::getTable('d2u_machinery_steel_profile'))
    ->ensureColumn(new rex_sql_column('profile_id', 'INT(11) unsigned', false, null, 'auto_increment'))
    ->setPrimaryKey('profile_id')
    ->ensureColumn(new \rex_sql_column('internal_name', 'VARCHAR(255)', true))
    ->ensure();
\rex_sql_table::get(\rex::getTable('d2u_machinery_steel_profile_lang'))
    ->ensureColumn(new rex_sql_column('profile_id', 'INT(11)'))
    ->ensureColumn(new \rex_sql_column('clang_id', 'INT(11)', false))
    ->setPrimaryKey(['profile_id', 'clang_id'])
    ->ensureColumn(new \rex_sql_column('name', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('translation_needs_update', 'VARCHAR(7)'))
    ->ensure();

\rex_sql_table::get(\rex::getTable('d2u_machinery_steel_supply'))
    ->ensureColumn(new rex_sql_column('supply_id', 'INT(11) unsigned', false, null, 'auto_increment'))
    ->setPrimaryKey('supply_id')
    ->ensureColumn(new \rex_sql_column('priority', 'INT(10)', true))
    ->ensureColumn(new \rex_sql_column('online_status', 'VARCHAR(10)', true))
    ->ensureColumn(new \rex_sql_column('pic', 'VARCHAR(255)', true))
    ->ensureColumn(new \rex_sql_column('video_id', 'INT(11)', true))
    ->ensure();
\rex_sql_table::get(\rex::getTable('d2u_machinery_steel_supply_lang'))
    ->ensureColumn(new rex_sql_column('supply_id', 'INT(11)'))
    ->ensureColumn(new \rex_sql_column('clang_id', 'INT(11)', false))
    ->setPrimaryKey(['supply_id', 'clang_id'])
    ->ensureColumn(new \rex_sql_column('name', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('description', 'TEXT'))
    ->ensureColumn(new \rex_sql_column('translation_needs_update', 'VARCHAR(7)'))
    ->ensure();
\rex_sql_table::get(
    \rex::getTable('d2u_machinery_steel_supply_lang'))
    ->removeColumn('title')
    ->alter();

\rex_sql_table::get(\rex::getTable('d2u_machinery_steel_tool'))
    ->ensureColumn(new rex_sql_column('tool_id', 'INT(11) unsigned', false, null, 'auto_increment'))
    ->setPrimaryKey('tool_id')
    ->ensureColumn(new \rex_sql_column('internal_name', 'VARCHAR(255)', true))
    ->ensure();
\rex_sql_table::get(\rex::getTable('d2u_machinery_steel_tool_lang'))
    ->ensureColumn(new rex_sql_column('tool_id', 'INT(11)'))
    ->ensureColumn(new \rex_sql_column('clang_id', 'INT(11)', false))
    ->setPrimaryKey(['tool_id', 'clang_id'])
    ->ensureColumn(new \rex_sql_column('name', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('translation_needs_update', 'VARCHAR(7)'))
    ->ensure();

\rex_sql_table::get(\rex::getTable('d2u_machinery_steel_welding'))
    ->ensureColumn(new rex_sql_column('welding_id', 'INT(11) unsigned', false, null, 'auto_increment'))
    ->setPrimaryKey('welding_id')
    ->ensureColumn(new \rex_sql_column('internal_name', 'VARCHAR(255)', true))
    ->ensure();
\rex_sql_table::get(\rex::getTable('d2u_machinery_steel_welding_lang'))
    ->ensureColumn(new rex_sql_column('welding_id', 'INT(11)'))
    ->ensureColumn(new \rex_sql_column('clang_id', 'INT(11)', false))
    ->setPrimaryKey(['welding_id', 'clang_id'])
    ->ensureColumn(new \rex_sql_column('name', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('translation_needs_update', 'VARCHAR(7)'))
    ->ensure();

// Extend machine table
\rex_sql_table::get(
    \rex::getTable('d2u_machinery_machines'))
    ->ensureColumn(new \rex_sql_column('process_ids', 'TEXT'))
    ->ensureColumn(new \rex_sql_column('procedure_ids', 'TEXT'))
    ->ensureColumn(new \rex_sql_column('material_ids', 'TEXT'))
    ->ensureColumn(new \rex_sql_column('tool_ids', 'TEXT'))
    ->ensureColumn(new \rex_sql_column('automation_supply_single_stroke', 'VARCHAR(25)'))
    ->ensureColumn(new \rex_sql_column('automation_supply_multi_stroke', 'VARCHAR(25)'))
    ->ensureColumn(new \rex_sql_column('automation_feedrate', 'VARCHAR(25)'))
    ->ensureColumn(new \rex_sql_column('automation_feedrate_sawblade', 'VARCHAR(25)'))
    ->ensureColumn(new \rex_sql_column('automation_rush_leader_flyback', 'VARCHAR(25)'))
    ->ensureColumn(new \rex_sql_column('automation_automationgrade_ids', 'TEXT'))
    ->ensureColumn(new \rex_sql_column('automation_supply_ids', 'TEXT'))
    ->ensureColumn(new \rex_sql_column('workspace', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('workspace_square', 'VARCHAR(50)'))
    ->ensureColumn(new \rex_sql_column('workspace_flat', 'VARCHAR(50)'))
    ->ensureColumn(new \rex_sql_column('workspace_plate', 'VARCHAR(50)'))
    ->ensureColumn(new \rex_sql_column('workspace_profile', 'VARCHAR(50)'))
    ->ensureColumn(new \rex_sql_column('workspace_angle_steel', 'VARCHAR(50)'))
    ->ensureColumn(new \rex_sql_column('workspace_round', 'VARCHAR(50)'))
    ->ensureColumn(new \rex_sql_column('workspace_min', 'VARCHAR(50)'))
    ->ensureColumn(new \rex_sql_column('sheet_width', 'VARCHAR(50)'))
    ->ensureColumn(new \rex_sql_column('sheet_length', 'VARCHAR(50)'))
    ->ensureColumn(new \rex_sql_column('sheet_thickness', 'VARCHAR(50)'))
    ->ensureColumn(new \rex_sql_column('tool_changer_locations', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('drilling_unit_below', 'INT(10)'))
    ->ensureColumn(new \rex_sql_column('drilling_unit_vertical', 'VARCHAR(50)'))
    ->ensureColumn(new \rex_sql_column('drilling_unit_horizontal', 'VARCHAR(50)'))
    ->ensureColumn(new \rex_sql_column('drilling_diameter', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('drilling_tools_axis', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('drilling_axis_drive_power', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('drilling_rpm_speed', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('saw_blade', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('saw_band', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('saw_band_tilt', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('saw_cutting_speed', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('saw_miter', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('bevel_angle', 'INT(10)'))
    ->ensureColumn(new \rex_sql_column('punching_diameter', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('punching_power', 'INT(5)'))
    ->ensureColumn(new \rex_sql_column('punching_tools', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('shaving_unit_angle_steel_single_cut', 'INT(10)'))
    ->ensureColumn(new \rex_sql_column('profile_ids', 'TEXT'))
    ->ensureColumn(new \rex_sql_column('carrier_width', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('carrier_height', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('carrier_weight', 'INT(10)'))
    ->ensureColumn(new \rex_sql_column('flange_thickness', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('web_thickness', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('component_length', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('component_weight', 'INT(10)'))
    ->ensureColumn(new \rex_sql_column('welding_process_ids', 'TEXT'))
    ->ensureColumn(new \rex_sql_column('welding_thickness', 'INT(10)'))
    ->ensureColumn(new \rex_sql_column('welding_wire_thickness', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('beam_continuous_opening', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('beam_turbines', 'INT(3)'))
    ->ensureColumn(new \rex_sql_column('beam_turbine_power', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('beam_color_guns', 'VARCHAR(255)'))
    ->alter();

\rex_sql_table::get(
    \rex::getTable('d2u_machinery_categories_lang'))
    ->removeColumn('steel_processing_saw_cutting_range_file')
    ->removeColumn('steel_processing_saw_cutting_range_title')
    ->alter();

// Insert frontend translations
if (!class_exists('d2u_machinery_machine_steel_processing_extension_lang_helper')) {
    // Load class in case addon is deactivated
    require_once 'lib/d2u_machinery_machine_steel_processing_extension_lang_helper.php';
}
if (class_exists('d2u_machinery_machine_steel_processing_extension_lang_helper')) {
    d2u_machinery_machine_steel_processing_extension_lang_helper::factory()->install();
}
