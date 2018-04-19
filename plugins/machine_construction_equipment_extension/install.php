<?php
$sql = rex_sql::factory();
// Extend machine table
$sql->setQuery("SHOW COLUMNS FROM ". \rex::getTablePrefix() ."d2u_machinery_machines LIKE 'airless_hose_connection';");
if($sql->getRows() == 0) {
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_machinery_machines "
		."ADD airless_hose_connection VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, "
		."ADD airless_hose_diameter INT(5) NOT NULL DEFAULT 0, "
		."ADD airless_hose_length INT(5) NOT NULL DEFAULT 0, "
		."ADD airless_nozzle_size VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, "
		."ADD container_capacity VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, "
		."ADD container_mixing_performance VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, "
		."ADD container_waterconnect_pressure INT(5) NOT NULL DEFAULT 0, "
		."ADD container_waterconnect_diameter VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, "
		."ADD container_weight_empty VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, "
		."ADD cutters_cutting_depth VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, "
		."ADD cutters_cutting_length VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, "
		."ADD cutters_rod_length VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, "
		."ADD floor_beam_power_on_concrete VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, "
		."ADD floor_dust_extraction_connection VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, "
		."ADD floor_feedrate VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 0, "
		."ADD floor_filter_connection VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, "
		."ADD floor_rotations VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, "
		."ADD floor_working_pressure VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, "
		."ADD floor_working_width INT(5) NOT NULL DEFAULT 0, "
		."ADD grinder_grinding_plate INT(5) NOT NULL DEFAULT 0, "
		."ADD grinder_grinding_wheel INT(5) NOT NULL DEFAULT 0, "
		."ADD grinder_rotational_frequency VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, "
		."ADD grinder_sanding VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, "
		."ADD grinder_vacuum_connection INT(5) NOT NULL DEFAULT 0, "
		."ADD operating_pressure VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, "
		."ADD picture_delivery_set VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, "
		."ADD pump_conveying_distance_fluid INT(5) NULL DEFAULT NULL, "
		."ADD pump_conveying_distance_mineral INT(5) NULL DEFAULT NULL, "
		."ADD pump_conveying_distance_pasty INT(5) NULL DEFAULT NULL, "
		."ADD pump_filling VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, "
		."ADD pump_flow_volume_fluid VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, "
		."ADD pump_flow_volume_mineral VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, "
		."ADD pump_flow_volume_pasty VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, "
		."ADD pump_grain_size VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, "
		."ADD pump_material_container VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, "
		."ADD pump_pressure_height_fluid INT(5) NOT NULL DEFAULT 0, "
		."ADD pump_pressure_height_mineral INT(5) NOT NULL DEFAULT 0, "
		."ADD pump_pressure_height_pasty INT(5) NOT NULL DEFAULT 0, "
		."ADD waste_water_capacity VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL "
	);
}

$sql->setQuery("SHOW COLUMNS FROM ". \rex::getTablePrefix() ."d2u_machinery_machines_lang LIKE 'container_connection_port';");
if($sql->getRows() == 0) {
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_machinery_machines_lang "
		."ADD container_connection_port VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, "
		."ADD container_conveying_wave VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, "
		."ADD description_technical VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `description`, "
		."ADD delivery_set_basic TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, "
		."ADD delivery_set_conversion TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, "
		."ADD delivery_set_full TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL "
	);
}

// Insert frontend translations
if(class_exists(d2u_machinery_machine_construction_equipment_extension_lang_helper)) {
	d2u_machinery_machine_construction_equipment_extension_lang_helper::factory()->install();
}