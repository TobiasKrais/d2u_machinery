<?php

// Delete fields
\rex_sql_table::get(
    \rex::getTable('d2u_machinery_machines'))
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

\rex_sql_table::get(
    \rex::getTable('d2u_machinery_machines_lang'))
    ->removeColumn('container_connection_port')
    ->removeColumn('container_conveying_wave')
    ->removeColumn('description_technical')
    ->removeColumn('delivery_set_basic')
    ->removeColumn('delivery_set_conversion')
    ->removeColumn('delivery_set_full')
    ->ensure();

// Delete Media Manager media types
$sql = \rex_sql::factory();
$sql->setQuery('DELETE FROM '. \rex::getTablePrefix() ."media_manager_type WHERE name LIKE 'd2u_machinery_construction_equipment%'");
$sql->setQuery('DELETE FROM '. \rex::getTablePrefix() ."media_manager_type_effect WHERE createuser = 'd2u_machinery_construction_equipment'");

// Delete language replacements
if (!class_exists(d2u_machinery_machine_construction_equipment_extension_lang_helper::class)) {
    // Load class in case addon is deactivated
    require_once 'lib/d2u_machinery_machine_construction_equipment_extension_lang_helper.php';
}
d2u_machinery_machine_construction_equipment_extension_lang_helper::factory()->uninstall();
