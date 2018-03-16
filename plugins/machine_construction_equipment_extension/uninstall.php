<?php
$sql = rex_sql::factory();

// Delete fields
$sql->setQuery('ALTER TABLE ' . \rex::getTablePrefix() . 'd2u_machinery_machines '
	. 'DROP airless_hose_connection, '
	. 'DROP airless_hose_diameter, '
	. 'DROP airless_hose_length, '
	. 'DROP airless_nozzle_size, '
	. 'DROP container_capacity, '
	. 'DROP container_mixing_performance, '
	. 'DROP container_waterconnect_pressure, '
	. 'DROP container_waterconnect_diameter, '
	. 'DROP container_weight_empty, '
	. 'DROP cutters_cutting_depth, '
	. 'DROP cutters_cutting_length, '
	. 'DROP cutters_rod_length, '
	. 'DROP floor_beam_power_on_concrete, '
	. 'DROP floor_dust_extraction_connection, '
	. 'DROP floor_feedrate, '
	. 'DROP floor_filter_connection, '
	. 'DROP floor_rotations, '
	. 'DROP floor_working_pressure, '
	. 'DROP floor_working_width, '
	. 'DROP grinder_grinding_plate, '
	. 'DROP grinder_grinding_wheel, '
	. 'DROP grinder_rotational_frequency, '
	. 'DROP grinder_sanding, '
	. 'DROP grinder_vacuum_connection, '
	. 'DROP operating_pressure, '
	. 'DROP pump_conveying_distance_fluid, '
	. 'DROP pump_conveying_distance_mineral, '
	. 'DROP pump_conveying_distance_pasty, '
	. 'DROP pump_filling, '
	. 'DROP pump_flow_volume_fluid, '
	. 'DROP pump_flow_volume_mineral, '
	. 'DROP pump_flow_volume_pasty, '
	. 'DROP pump_grain_size, '
	. 'DROP pump_material_container, '
	. 'DROP pump_pressure_height_fluid, '
	. 'DROP pump_pressure_height_mineral, '
	. 'DROP pump_pressure_height_pasty, '
	. 'DROP waste_water_capacity;'
);

$sql->setQuery('ALTER TABLE ' . \rex::getTablePrefix() . 'd2u_machinery_machines_lang '
	. 'DROP container_connection_port, '
	. 'DROP container_conveying_wave, '
	. 'DROP description_technical, '
	. 'DROP delivery_set_basic, '
	. 'DROP delivery_set_conversion, '
	. 'DROP delivery_set_full;'
);

// Delete language replacements
d2u_machinery_machine_construction_equipment_extension_lang_helper::factory()->uninstall();