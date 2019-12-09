<?php
// Update language replacements
if(!class_exists('d2u_machinery_machine_construction_equipment_extension_lang_helper')) {
	// Load class in case addon is deactivated
	require_once 'lib/d2u_machinery_machine_construction_equipment_extension_lang_helper.php';
}
d2u_machinery_machine_construction_equipment_extension_lang_helper::factory()->install();

\rex_sql_table::get(
    \rex::getTable('d2u_machinery_machines'))
    ->removeColumn('pump_conveying_distance_mineral')
    ->removeColumn('pump_conveying_distance_pasty')
    ->removeColumn('pump_flow_volume_mineral')
    ->removeColumn('pump_flow_volume_pasty')
    ->removeColumn('pump_pressure_height_mineral')
    ->removeColumn('pump_pressure_height_pasty')
    ->alter();

\rex_sql_table::get(
    \rex::getTable('d2u_machinery_machines'))
    ->ensureColumn(new \rex_sql_column('container_weight_empty', 'INT(5)', FALSE, 0))
    ->ensureColumn(new \rex_sql_column('cutters_cutting_length', 'INT(5)', FALSE, 0))
    ->ensureColumn(new \rex_sql_column('floor_beam_power_on_concrete', 'INT(5)', FALSE, 0))
    ->ensureColumn(new \rex_sql_column('floor_dust_extraction_connection', 'INT(5)', FALSE, 0))
    ->ensureColumn(new \rex_sql_column('pump_filling', 'INT(10)', FALSE, 0))
    ->ensureColumn(new \rex_sql_column('waste_water_capacity', 'INT(5)', FALSE, 0))
    ->alter();