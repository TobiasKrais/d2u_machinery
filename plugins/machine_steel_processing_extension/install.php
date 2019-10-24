<?php
$sql = \rex_sql::factory();

// Create tables: automation
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". \rex::getTablePrefix() ."d2u_machinery_steel_automation (
	automation_id int(10) unsigned NOT NULL auto_increment,
	internal_name varchar(255) collate utf8mb4_unicode_ci default NULL,
	PRIMARY KEY (automation_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;");
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". \rex::getTablePrefix() ."d2u_machinery_steel_automation_lang (
	automation_id int(10) NOT NULL,
	clang_id int(10) NOT NULL,
	name varchar(255) collate utf8mb4_unicode_ci default NULL,
	translation_needs_update varchar(7) collate utf8mb4_unicode_ci default NULL,
	PRIMARY KEY (automation_id, clang_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;");
// Create tables: material classes
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". \rex::getTablePrefix() ."d2u_machinery_steel_material (
	material_id int(10) unsigned NOT NULL auto_increment,
	internal_name varchar(255) collate utf8mb4_unicode_ci default NULL,
	PRIMARY KEY (material_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;");
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". \rex::getTablePrefix() ."d2u_machinery_steel_material_lang (
	material_id int(10) NOT NULL,
	clang_id int(10) NOT NULL,
	name varchar(255) collate utf8mb4_unicode_ci default NULL,
	translation_needs_update varchar(7) collate utf8mb4_unicode_ci default NULL,
	PRIMARY KEY (material_id, clang_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;");
// Create tables: processes
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". \rex::getTablePrefix() ."d2u_machinery_steel_process (
	process_id int(10) unsigned NOT NULL auto_increment,
	internal_name varchar(255) collate utf8mb4_unicode_ci default NULL,
	PRIMARY KEY (process_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;");
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". \rex::getTablePrefix() ."d2u_machinery_steel_process_lang (
	process_id int(10) NOT NULL,
	clang_id int(10) NOT NULL,
	name varchar(255) collate utf8mb4_unicode_ci default NULL,
	translation_needs_update varchar(7) collate utf8mb4_unicode_ci default NULL,
	PRIMARY KEY (process_id, clang_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;");
// Create tables: procedures
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". \rex::getTablePrefix() ."d2u_machinery_steel_procedure (
	procedure_id int(10) unsigned NOT NULL auto_increment,
	internal_name varchar(255) collate utf8mb4_unicode_ci default NULL,
	PRIMARY KEY (procedure_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;");
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". \rex::getTablePrefix() ."d2u_machinery_steel_procedure_lang (
	procedure_id int(10) NOT NULL,
	clang_id int(10) NOT NULL,
	name varchar(255) collate utf8mb4_unicode_ci default NULL,
	translation_needs_update varchar(7) collate utf8mb4_unicode_ci default NULL,
	PRIMARY KEY (procedure_id, clang_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;");
// Create tables: profile areas
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". \rex::getTablePrefix() ."d2u_machinery_steel_profile (
	profile_id int(10) unsigned NOT NULL auto_increment,
	internal_name varchar(255) collate utf8mb4_unicode_ci default NULL,
	PRIMARY KEY (profile_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;");
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". \rex::getTablePrefix() ."d2u_machinery_steel_profile_lang (
	profile_id int(10) NOT NULL,
	clang_id int(10) NOT NULL,
	name varchar(255) collate utf8mb4_unicode_ci default NULL,
	translation_needs_update varchar(7) collate utf8mb4_unicode_ci default NULL,
	PRIMARY KEY (profile_id, clang_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;");
// Create tables: supply chain
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". \rex::getTablePrefix() ."d2u_machinery_steel_supply (
	supply_id int(10) unsigned NOT NULL auto_increment,
	online_status varchar(10) collate utf8mb4_unicode_ci default 'online',
	pic varchar(255) collate utf8mb4_unicode_ci default NULL,
	video_id int(10) default NULL,
	PRIMARY KEY (supply_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;");
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". \rex::getTablePrefix() ."d2u_machinery_steel_supply_lang (
	supply_id int(10) NOT NULL,
	clang_id int(10) NOT NULL,
	name varchar(255) collate utf8mb4_unicode_ci default NULL,
	title varchar(255) collate utf8mb4_unicode_ci default NULL,
	description text collate utf8mb4_unicode_ci default NULL,
	translation_needs_update varchar(7) collate utf8mb4_unicode_ci default NULL,
	PRIMARY KEY (supply_id, clang_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;");
// Create tables: tools
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". \rex::getTablePrefix() ."d2u_machinery_steel_tool (
	tool_id int(10) unsigned NOT NULL auto_increment,
	internal_name varchar(255) collate utf8mb4_unicode_ci default NULL,
	PRIMARY KEY (tool_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;");
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". \rex::getTablePrefix() ."d2u_machinery_steel_tool_lang (
	tool_id int(10) NOT NULL,
	clang_id int(10) NOT NULL,
	name varchar(255) collate utf8mb4_unicode_ci default NULL,
	translation_needs_update varchar(7) collate utf8mb4_unicode_ci default NULL,
	PRIMARY KEY (tool_id, clang_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;");
// Create tables: welding process
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". \rex::getTablePrefix() ."d2u_machinery_steel_welding (
	welding_id int(10) unsigned NOT NULL auto_increment,
	internal_name varchar(255) collate utf8mb4_unicode_ci default NULL,
	PRIMARY KEY (welding_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;");
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". \rex::getTablePrefix() ."d2u_machinery_steel_welding_lang (
	welding_id int(10) NOT NULL,
	clang_id int(10) NOT NULL,
	name varchar(255) collate utf8mb4_unicode_ci default NULL,
	translation_needs_update varchar(7) collate utf8mb4_unicode_ci default NULL,
	PRIMARY KEY (welding_id, clang_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;");

// Extend machine table
$sql->setQuery("SHOW COLUMNS FROM ". \rex::getTablePrefix() ."d2u_machinery_machines LIKE 'process_ids';");
if($sql->getRows() == 0) {
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_machinery_machines "
		. "ADD process_ids TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER 	operating_voltage_a, "
		. "ADD procedure_ids TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER process_ids, "
		. "ADD material_ids TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER procedure_ids, "
		. "ADD tool_ids TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER material_ids, "
		. "ADD automation_supply_single_stroke VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER tool_ids, "
		. "ADD automation_supply_multi_stroke VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER automation_supply_single_stroke, "
		. "ADD automation_feedrate VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER automation_supply_multi_stroke, "
		. "ADD automation_rush_leader_flyback VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER automation_feedrate, "
		. "ADD automation_automationgrade_ids TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER automation_rush_leader_flyback, "
		. "ADD automation_supply_ids TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER automation_automationgrade_ids, "
		. "ADD workspace VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER automation_supply_ids, "
		. "ADD workspace_square VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER workspace, "
		. "ADD workspace_flat VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER workspace_square, "
		. "ADD workspace_plate VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER workspace_flat, "
		. "ADD workspace_profile VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER workspace_plate, "
		. "ADD workspace_angle_steel VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER workspace_profile, "
		. "ADD workspace_round VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER workspace_angle_steel, "
		. "ADD workspace_min VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER workspace_round, "
		. "ADD sheet_width VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER workspace_min, "
		. "ADD sheet_length VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER sheet_width, "
		. "ADD sheet_thickness VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER sheet_length, "
		. "ADD tool_changer_locations VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER sheet_thickness, "
		. "ADD drilling_unit_vertical VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER tool_changer_locations, "
		. "ADD drilling_unit_horizontal VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER drilling_unit_vertical, "
		. "ADD drilling_diameter VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER drilling_unit_horizontal, "
		. "ADD drilling_tools_axis VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER drilling_diameter, "
		. "ADD drilling_axis_drive_power VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER drilling_tools_axis, "
		. "ADD drilling_rpm_speed VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER drilling_axis_drive_power, "
		. "ADD saw_blade VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER drilling_rpm_speed, "
		. "ADD saw_band VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER saw_blade, "
		. "ADD saw_band_tilt VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER saw_band, "
		. "ADD saw_cutting_speed VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER saw_band_tilt, "
		. "ADD saw_miter VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER saw_cutting_speed, "
		. "ADD bevel_angle int(10) DEFAULT NULL AFTER saw_miter, "
		. "ADD punching_diameter VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER bevel_angle, "
		. "ADD punching_power INT(5) NULL DEFAULT NULL AFTER punching_diameter, "
		. "ADD punching_tools VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER punching_power, "
		. "ADD shaving_unit_angle_steel_single_cut int(10) DEFAULT NULL AFTER punching_tools, "
		. "ADD profile_ids TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER shaving_unit_angle_steel_single_cut, "
		. "ADD carrier_width VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER profile_ids, "
		. "ADD carrier_height VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER carrier_width, "
		. "ADD carrier_weight int(10) DEFAULT NULL AFTER carrier_height, "
		. "ADD flange_thickness VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER carrier_weight, "
		. "ADD web_thickness VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER flange_thickness, "
		. "ADD component_length VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER web_thickness, "
		. "ADD component_weight int(10) DEFAULT NULL AFTER component_length, "
		. "ADD welding_process_ids TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER component_weight, "
		. "ADD welding_thickness int(10) DEFAULT NULL AFTER welding_process_ids, "
		. "ADD welding_wire_thickness VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER welding_thickness, "
		. "ADD beam_continuous_opening VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER welding_wire_thickness, "
		. "ADD beam_turbines int(3) DEFAULT NULL AFTER beam_continuous_opening, "
		. "ADD beam_turbine_power VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER beam_turbines, "
		. "ADD beam_color_guns VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER beam_turbine_power"		
	);
}

// Extend category table
$sql->setQuery("SHOW COLUMNS FROM ". \rex::getTablePrefix() ."d2u_machinery_categories_lang LIKE 'steel_processing_saw_cutting_range_file';");
if($sql->getRows() == 0) {
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_machinery_categories_lang "
		. "ADD steel_processing_saw_cutting_range_file VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER pdfs;");
}
$sql->setQuery("SHOW COLUMNS FROM ". \rex::getTablePrefix() ."d2u_machinery_categories_lang LIKE 'steel_processing_saw_cutting_range_title';");
if($sql->getRows() == 0) {
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_machinery_categories_lang "
		. "ADD steel_processing_saw_cutting_range_title VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER steel_processing_saw_cutting_range_file;");
}

// Insert frontend translations
if(class_exists('d2u_machinery_machine_steel_processing_extension_lang_helper')) {
	d2u_machinery_machine_steel_processing_extension_lang_helper::factory()->install();
}