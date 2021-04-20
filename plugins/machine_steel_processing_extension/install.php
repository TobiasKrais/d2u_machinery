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
	priority int(10) default NULL,
	online_status varchar(10) collate utf8mb4_unicode_ci default 'online',
	pic varchar(255) collate utf8mb4_unicode_ci default NULL,
	video_id int(10) default NULL,
	PRIMARY KEY (supply_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;");
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". \rex::getTablePrefix() ."d2u_machinery_steel_supply_lang (
	supply_id int(10) NOT NULL,
	clang_id int(10) NOT NULL,
	name varchar(255) collate utf8mb4_unicode_ci default NULL,
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

// Insert frontend translations
if(class_exists('d2u_machinery_machine_steel_processing_extension_lang_helper')) {
	d2u_machinery_machine_steel_processing_extension_lang_helper::factory()->install();
}