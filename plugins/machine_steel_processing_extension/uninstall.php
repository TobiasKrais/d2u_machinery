<?php
$sql = rex_sql::factory();

// Delete columns in machine tables
$sql->setQuery("ALTER TABLE ". rex::getTablePrefix() ."d2u_machinery_machines "
		. "DROP process_ids, "
		. "DROP procedure_ids, "
		. "DROP material_ids, "
		. "DROP tool_ids, "
		. "DROP automation_supply_single_stroke, "
		. "DROP automation_supply_multi_stroke, "
		. "DROP automation_feedrate, "
		. "DROP automation_rush_leader_flyback, "
		. "DROP automation_automationgrade_ids, "
		. "DROP automation_supply_ids, "
		. "DROP automation_video_id, "
		. "DROP workspace, "
		. "DROP workspace_square, "
		. "DROP workspace_flat, "
		. "DROP workspace_plate, "
		. "DROP workspace_profile, "
		. "DROP workspace_angle_steel, "
		. "DROP workspace_round, "
		. "DROP workspace_min, "
		. "DROP sheet_width, "
		. "DROP sheet_length, "
		. "DROP sheet_thickness, "
		. "DROP tool_changer_locations, "
		. "DROP drilling_unit_vertical, "
		. "DROP drilling_unit_horizontal, "
		. "DROP drilling_diameter, "
		. "DROP drilling_tools_axis, "
		. "DROP drilling_axis_drive_power, "
		. "DROP drilling_rpm_speed, "
		. "DROP saw_blade, "
		. "DROP saw_band, "
		. "DROP saw_band_tilt, "
		. "DROP saw_cutting_speed, "
		. "DROP saw_miter, "
		. "DROP bevel_angle, "
		. "DROP punching_diameter, "
		. "DROP punching_tools, "
		. "DROP shaving_unit_angle_steel_single_cut, "
		. "DROP profile_area_ids, "
		. "DROP carrier_width, "
		. "DROP carrier_height, "
		. "DROP carrier_weight, "
		. "DROP flange_thickness, "
		. "DROP web_thickness, "
		. "DROP component_length, "
		. "DROP component_weight, "
		. "DROP welding_process_ids, "
		. "DROP welding_thickness, "
		. "DROP welding_wire_thickness, "
		. "DROP beam_continuous_opening, "
		. "DROP beam_turbines, "
		. "DROP beam_turbine_power, "
		. "DROP beam_color_guns"
	);

// Delete columns in catgory tables
$sql->setQuery('ALTER TABLE ' . rex::getTablePrefix() . 'd2u_machinery_categories_lang
	DROP steel_processing_saw_cutting_range_title,
	DROP steel_processing_saw_cutting_range_file;');

// Delete tables
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_machinery_steel_automation');
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_machinery_steel_automation_lang');
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_machinery_steel_material');
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_machinery_steel_material_lang');
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_machinery_steel_process');
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_machinery_steel_process_lang');
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_machinery_steel_procedure');
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_machinery_steel_procedure_lang');
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_machinery_steel_profile');
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_machinery_steel_profile_lang');
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_machinery_steel_supply');
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_machinery_steel_supply_lang');
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_machinery_steel_tool');
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_machinery_steel_tool_lang');
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_machinery_steel_welding');
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_machinery_steel_welding_lang');