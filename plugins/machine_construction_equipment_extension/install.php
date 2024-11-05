<?php

// Extend machine table
\rex_sql_table::get(\rex::getTable('d2u_machinery_machines'))
    ->ensureColumn(new \rex_sql_column('airless_hose_connection', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('airless_hose_diameter', 'INT(5)', false))
    ->ensureColumn(new \rex_sql_column('airless_hose_length', 'INT(5)', false))
    ->ensureColumn(new \rex_sql_column('airless_nozzle_size', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('container_capacity', 'VARCHAR(50)'))
    ->ensureColumn(new \rex_sql_column('container_capacity_unit', 'VARCHAR(5)'))
    ->ensureColumn(new \rex_sql_column('container_mixing_performance', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('container_waterconnect_pressure', 'INT(5)', false))
    ->ensureColumn(new \rex_sql_column('container_waterconnect_diameter', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('container_weight_empty', 'INT(5)', false))
    ->ensureColumn(new \rex_sql_column('cutters_cutting_depth', 'VARCHAR(50)'))
    ->ensureColumn(new \rex_sql_column('cutters_cutting_length', 'INT(5)', false))
    ->ensureColumn(new \rex_sql_column('cutters_rod_length', 'VARCHAR(50)'))
    ->ensureColumn(new \rex_sql_column('floor_beam_power_on_concrete', 'INT(5)', false))
    ->ensureColumn(new \rex_sql_column('floor_dust_extraction_connection', 'INT(5)', false))
    ->ensureColumn(new \rex_sql_column('floor_feedrate', 'VARCHAR(50)'))
    ->ensureColumn(new \rex_sql_column('floor_filter_connection', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('floor_rotations', 'VARCHAR(50)'))
    ->ensureColumn(new \rex_sql_column('floor_working_pressure', 'VARCHAR(50)'))
    ->ensureColumn(new \rex_sql_column('floor_working_width', 'INT(5)', false))
    ->ensureColumn(new \rex_sql_column('grinder_grinding_plate', 'INT(5)', false))
    ->ensureColumn(new \rex_sql_column('grinder_grinding_wheel', 'INT(5)', false))
    ->ensureColumn(new \rex_sql_column('grinder_rotational_frequency', 'VARCHAR(50)'))
    ->ensureColumn(new \rex_sql_column('grinder_sanding', 'VARCHAR(50)'))
    ->ensureColumn(new \rex_sql_column('grinder_vacuum_connection', 'INT(5)', false))
    ->ensureColumn(new \rex_sql_column('operating_pressure', 'VARCHAR(50)'))
    ->ensureColumn(new \rex_sql_column('pictures_delivery_set', 'TEXT'))
    ->ensureColumn(new \rex_sql_column('pump_conveying_distance', 'INT(5)', false))
    ->ensureColumn(new \rex_sql_column('pump_filling', 'INT(10)', false))
    ->ensureColumn(new \rex_sql_column('pump_flow_volume', 'VARCHAR(50)'))
    ->ensureColumn(new \rex_sql_column('pump_grain_size', 'VARCHAR(50)'))
    ->ensureColumn(new \rex_sql_column('pump_material_container', 'VARCHAR(50)'))
    ->ensureColumn(new \rex_sql_column('pump_pressure_height', 'INT(5)', false))
    ->ensureColumn(new \rex_sql_column('waste_water_capacity', 'INT(5)', false))
    ->alter();

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
    \rex::getTable('d2u_machinery_machines_lang'))
    ->ensureColumn(new \rex_sql_column('container_connection_port', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('container_conveying_wave', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('description_technical', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('delivery_set_basic', 'TEXT'))
    ->ensureColumn(new \rex_sql_column('delivery_set_conversion', 'TEXT'))
    ->ensureColumn(new \rex_sql_column('delivery_set_full', 'TEXT'))
    ->alter();

// Media Manager media types
$sql = \rex_sql::factory();
$sql->setQuery('SELECT * FROM '. \rex::getTablePrefix() ."media_manager_type WHERE name = 'd2u_machinery_construction_equipment_delivery_set_slider'");
if (0 === $sql->getRows()) {
    $sql->setQuery('INSERT INTO '. \rex::getTablePrefix() ."media_manager_type (`status`, `name`, `description`) VALUES
		(0, 'd2u_machinery_construction_equipment_delivery_set_slider', 'D2U Machinery Addon - Baustellenausrüstung Plugin: Bilder slider Lieferumfang');");
    $last_id_d2u_machinery_list_tile = $sql->getLastId();
    $sql->setQuery('INSERT INTO '. \rex::getTablePrefix() .'media_manager_type_effect (`type_id`, `effect`, `parameters`, `priority`, `createdate`, `createuser`) VALUES
		('. $last_id_d2u_machinery_list_tile .", 'resize', '{\"rex_effect_convert2img\":{\"rex_effect_convert2img_convert_to\":\"jpg\",\"rex_effect_convert2img_density\":\"100\"},\"rex_effect_crop\":{\"rex_effect_crop_width\":\"\",\"rex_effect_crop_height\":\"\",\"rex_effect_crop_offset_width\":\"\",\"rex_effect_crop_offset_height\":\"\",\"rex_effect_crop_hpos\":\"left\",\"rex_effect_crop_vpos\":\"top\"},\"rex_effect_filter_blur\":{\"rex_effect_filter_blur_repeats\":\"\",\"rex_effect_filter_blur_type\":\"\",\"rex_effect_filter_blur_smoothit\":\"\"},\"rex_effect_filter_colorize\":{\"rex_effect_filter_colorize_filter_r\":\"\",\"rex_effect_filter_colorize_filter_g\":\"\",\"rex_effect_filter_colorize_filter_b\":\"\"},\"rex_effect_filter_sharpen\":{\"rex_effect_filter_sharpen_amount\":\"\",\"rex_effect_filter_sharpen_radius\":\"\",\"rex_effect_filter_sharpen_threshold\":\"\"},\"rex_effect_flip\":{\"rex_effect_flip_flip\":\"X\"},\"rex_effect_header\":{\"rex_effect_header_download\":\"open_media\",\"rex_effect_header_cache\":\"no_cache\"},\"rex_effect_insert_image\":{\"rex_effect_insert_image_brandimage\":\"\",\"rex_effect_insert_image_hpos\":\"left\",\"rex_effect_insert_image_vpos\":\"top\",\"rex_effect_insert_image_padding_x\":\"\",\"rex_effect_insert_image_padding_y\":\"\"},\"rex_effect_mediapath\":{\"rex_effect_mediapath_mediapath\":\"\"},\"rex_effect_mirror\":{\"rex_effect_mirror_height\":\"\",\"rex_effect_mirror_set_transparent\":\"colored\",\"rex_effect_mirror_bg_r\":\"\",\"rex_effect_mirror_bg_g\":\"\",\"rex_effect_mirror_bg_b\":\"\"},\"rex_effect_resize\":{\"rex_effect_resize_width\":\"768\",\"rex_effect_resize_height\":\"768\",\"rex_effect_resize_style\":\"minimum\",\"rex_effect_resize_allow_enlarge\":\"enlarge\"},\"rex_effect_rounded_corners\":{\"rex_effect_rounded_corners_topleft\":\"\",\"rex_effect_rounded_corners_topright\":\"\",\"rex_effect_rounded_corners_bottomleft\":\"\",\"rex_effect_rounded_corners_bottomright\":\"\"},\"rex_effect_workspace\":{\"rex_effect_workspace_width\":\"\",\"rex_effect_workspace_height\":\"\",\"rex_effect_workspace_hpos\":\"left\",\"rex_effect_workspace_vpos\":\"top\",\"rex_effect_workspace_set_transparent\":\"colored\",\"rex_effect_workspace_bg_r\":\"\",\"rex_effect_workspace_bg_g\":\"\",\"rex_effect_workspace_bg_b\":\"\"}}', 1, CURRENT_TIMESTAMP, 'd2u_machinery'),
		(". $last_id_d2u_machinery_list_tile .", 'workspace', '{\"rex_effect_convert2img\":{\"rex_effect_convert2img_convert_to\":\"jpg\",\"rex_effect_convert2img_density\":\"100\"},\"rex_effect_crop\":{\"rex_effect_crop_width\":\"\",\"rex_effect_crop_height\":\"\",\"rex_effect_crop_offset_width\":\"\",\"rex_effect_crop_offset_height\":\"\",\"rex_effect_crop_hpos\":\"left\",\"rex_effect_crop_vpos\":\"top\"},\"rex_effect_filter_blur\":{\"rex_effect_filter_blur_repeats\":\"\",\"rex_effect_filter_blur_type\":\"\",\"rex_effect_filter_blur_smoothit\":\"\"},\"rex_effect_filter_colorize\":{\"rex_effect_filter_colorize_filter_r\":\"\",\"rex_effect_filter_colorize_filter_g\":\"\",\"rex_effect_filter_colorize_filter_b\":\"\"},\"rex_effect_filter_sharpen\":{\"rex_effect_filter_sharpen_amount\":\"\",\"rex_effect_filter_sharpen_radius\":\"\",\"rex_effect_filter_sharpen_threshold\":\"\"},\"rex_effect_flip\":{\"rex_effect_flip_flip\":\"X\"},\"rex_effect_header\":{\"rex_effect_header_download\":\"open_media\",\"rex_effect_header_cache\":\"no_cache\"},\"rex_effect_insert_image\":{\"rex_effect_insert_image_brandimage\":\"\",\"rex_effect_insert_image_hpos\":\"left\",\"rex_effect_insert_image_vpos\":\"top\",\"rex_effect_insert_image_padding_x\":\"\",\"rex_effect_insert_image_padding_y\":\"\"},\"rex_effect_mediapath\":{\"rex_effect_mediapath_mediapath\":\"\"},\"rex_effect_mirror\":{\"rex_effect_mirror_height\":\"\",\"rex_effect_mirror_set_transparent\":\"colored\",\"rex_effect_mirror_bg_r\":\"\",\"rex_effect_mirror_bg_g\":\"\",\"rex_effect_mirror_bg_b\":\"\"},\"rex_effect_resize\":{\"rex_effect_resize_width\":\"\",\"rex_effect_resize_height\":\"\",\"rex_effect_resize_style\":\"maximum\",\"rex_effect_resize_allow_enlarge\":\"enlarge\"},\"rex_effect_rounded_corners\":{\"rex_effect_rounded_corners_topleft\":\"\",\"rex_effect_rounded_corners_topright\":\"\",\"rex_effect_rounded_corners_bottomleft\":\"\",\"rex_effect_rounded_corners_bottomright\":\"\"},\"rex_effect_workspace\":{\"rex_effect_workspace_width\":\"768\",\"rex_effect_workspace_height\":\"768\",\"rex_effect_workspace_hpos\":\"center\",\"rex_effect_workspace_vpos\":\"middle\",\"rex_effect_workspace_set_transparent\":\"transparent\",\"rex_effect_workspace_bg_r\":\"255\",\"rex_effect_workspace_bg_g\":\"255\",\"rex_effect_workspace_bg_b\":\"255\"}}', 2, CURRENT_TIMESTAMP, 'd2u_machinery');");
}

// Insert frontend translations
if (!class_exists(d2u_machinery_machine_construction_equipment_extension_lang_helper::class)) {
    // Load class in case addon is deactivated
    require_once 'lib/d2u_machinery_machine_construction_equipment_extension_lang_helper.php';
}
if (class_exists(d2u_machinery_machine_construction_equipment_extension_lang_helper::class)) {
    d2u_machinery_machine_construction_equipment_extension_lang_helper::factory()->install();
}
