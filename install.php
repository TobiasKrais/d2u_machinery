<?php

use TobiasKrais\D2UMachinery\Extension;

if (!class_exists(Extension::class)) {
    require_once __DIR__ . DIRECTORY_SEPARATOR .'lib'. DIRECTORY_SEPARATOR .'Extension.php';
}

$d2uMachineryAction = $d2uMachineryAction ?? null;
$d2uMachineryRequestedAction = $d2uMachineryRequestedAction ?? $d2uMachineryAction;
$d2uMachineryCascadeDependencies = $d2uMachineryCascadeDependencies ?? false;
$d2uMachineryActivatedActions = null;
$sql = \rex_sql::factory();

if (null !== $d2uMachineryRequestedAction) {
    $d2uMachineryActivatedActions = $d2uMachineryCascadeDependencies
        ? Extension::getActivationCascade($d2uMachineryRequestedAction)
        : [$d2uMachineryRequestedAction];
}

if (!function_exists('d2u_machinery_should_install')) {
    function d2u_machinery_should_install(array|string|null $action, string $extension): bool
    {
        if (null === $action) {
            return true;
        }

        if (is_array($action)) {
            return in_array($extension, $action, true);
        }

        return $action === $extension;
    }
}

if (!function_exists('d2u_machinery_delete_url_profile_by_namespace')) {
    function d2u_machinery_delete_url_profile_by_namespace(string $namespace): void
    {
        if (!\rex_addon::get('url')->isAvailable()) {
            return;
        }

        foreach (\Url\Profile::getByNamespace($namespace) as $profile) {
            $profile->deleteUrls();
        }

        \rex_sql::factory()->setQuery('DELETE FROM '. \rex::getTablePrefix() ."url_generator_profile WHERE `namespace` = '". $namespace ."';");
    }
}

if (!function_exists('d2u_machinery_cleanup_inactive_url_profiles')) {
    /**
     * @param array<string,bool> $activeExtensions
     */
    function d2u_machinery_cleanup_inactive_url_profiles(array $activeExtensions): void
    {
        if (!\rex_addon::get('url')->isAvailable()) {
            return;
        }

        $namespacesByExtension = [
            'industry_sectors' => ['industry_sector_id'],
            'production_lines' => ['production_line_id'],
            'used_machines' => [
                'used_rent_machine_id',
                'used_rent_category_id',
                'used_sale_machine_id',
                'used_sale_category_id',
            ],
        ];

        foreach ($namespacesByExtension as $extension => $namespaces) {
            if (($activeExtensions[$extension] ?? false) === true) {
                continue;
            }

            foreach ($namespaces as $namespace) {
                d2u_machinery_delete_url_profile_by_namespace($namespace);
            }
        }
    }
}

$d2uMachineryEffectiveExtensionStates = Extension::getStates();
if (is_array($d2uMachineryActivatedActions)) {
    foreach ($d2uMachineryActivatedActions as $extensionKey) {
        $d2uMachineryEffectiveExtensionStates[$extensionKey] = true;
    }
}
d2u_machinery_cleanup_inactive_url_profiles($d2uMachineryEffectiveExtensionStates);

if (null === $d2uMachineryAction) {

    \rex_sql_table::get(\rex::getTable('d2u_machinery_machines'))
        ->ensureColumn(new rex_sql_column('machine_id', 'INT(11) unsigned', false, null, 'auto_increment'))
        ->setPrimaryKey('machine_id')
        ->ensureColumn(new \rex_sql_column('priority', 'INT(11)', true))
        ->ensureColumn(new \rex_sql_column('name', 'VARCHAR(255)'))
        ->ensureColumn(new \rex_sql_column('pics', 'TEXT', true))
        ->ensureColumn(new \rex_sql_column('category_id', 'INT(11)', true))
        ->ensureColumn(new \rex_sql_column('alternative_machine_ids', 'TEXT', true))
        ->ensureColumn(new \rex_sql_column('additional_machine_ids', 'TEXT', true))
        ->ensureColumn(new \rex_sql_column('product_number', 'VARCHAR(50)', true))
        ->ensureColumn(new \rex_sql_column('article_id_software', 'INT(11)', true))
        ->ensureColumn(new \rex_sql_column('article_id_service', 'INT(11)', true))
        ->ensureColumn(new \rex_sql_column('article_ids_references', 'VARCHAR(255)', true))
        ->ensureColumn(new \rex_sql_column('reference_ids', 'TEXT', true))
        ->ensureColumn(new \rex_sql_column('online_status', 'VARCHAR(10)', true))
        ->ensureColumn(new \rex_sql_column('engine_power', 'VARCHAR(50)', true))
        ->ensureColumn(new \rex_sql_column('engine_power_frequency_controlled', 'VARCHAR(10)', true))
        ->ensureColumn(new \rex_sql_column('length', 'INT(10)', true))
        ->ensureColumn(new \rex_sql_column('width', 'INT(10)', true))
        ->ensureColumn(new \rex_sql_column('height', 'INT(10)', true))
        ->ensureColumn(new \rex_sql_column('depth', 'INT(10)', true))
        ->ensureColumn(new \rex_sql_column('weight', 'VARCHAR(50)', true))
        ->ensureColumn(new \rex_sql_column('operating_voltage_v', 'VARCHAR(10)', true))
        ->ensureColumn(new \rex_sql_column('operating_voltage_hz', 'VARCHAR(10)', true))
        ->ensureColumn(new \rex_sql_column('operating_voltage_a', 'VARCHAR(10)', true))
        ->ensureColumn(new \rex_sql_column('video_ids', 'VARCHAR(255)', true))
        ->ensure();
    \rex_sql_table::get(
        \rex::getTable('d2u_machinery_machines'))
        ->removeColumn('internal_name')
        ->ensure();
    \rex_sql_table::get(\rex::getTable('d2u_machinery_machines_lang'))
        ->ensureColumn(new rex_sql_column('machine_id', 'INT(11)', false))
        ->ensureColumn(new \rex_sql_column('clang_id', 'INT(11)', false))
        ->setPrimaryKey(['machine_id', 'clang_id'])
        ->ensureColumn(new \rex_sql_column('lang_name', 'VARCHAR(255)'))
        ->ensureColumn(new \rex_sql_column('teaser', 'VARCHAR(255)', true))
        ->ensureColumn(new \rex_sql_column('description', 'TEXT', true))
        ->ensureColumn(new \rex_sql_column('benefits_short', 'TEXT', true))
        ->ensureColumn(new \rex_sql_column('benefits_long', 'TEXT', true))
        ->ensureColumn(new \rex_sql_column('leaflet', 'VARCHAR(255)', true))
        ->ensureColumn(new \rex_sql_column('pdfs', 'TEXT', true))
        ->ensureColumn(new \rex_sql_column('translation_needs_update', 'VARCHAR(7)', true))
        ->ensureColumn(new \rex_sql_column('updatedate', 'DATETIME', true))
        ->ensureColumn(new \rex_sql_column('updateuser', 'VARCHAR(255)', true))
        ->ensure();

    \rex_sql_table::get(\rex::getTable('d2u_machinery_categories'))
        ->ensureColumn(new rex_sql_column('category_id', 'INT(11) unsigned', false, null, 'auto_increment'))
        ->setPrimaryKey('category_id')
        ->ensureColumn(new \rex_sql_column('priority', 'INT(11)', true))
        ->ensureColumn(new \rex_sql_column('parent_category_id', 'INT(11)', true))
        ->ensureColumn(new \rex_sql_column('pic', 'VARCHAR(255)', true))
        ->ensureColumn(new \rex_sql_column('pic_usage', 'VARCHAR(255)', true))
        ->ensureColumn(new \rex_sql_column('video_ids', 'TEXT', true))
        ->ensureColumn(new \rex_sql_column('reference_ids', 'TEXT', true))
        ->ensure();
    \rex_sql_table::get(\rex::getTable('d2u_machinery_categories_lang'))
        ->ensureColumn(new rex_sql_column('category_id', 'INT(11)', false))
        ->ensureColumn(new \rex_sql_column('clang_id', 'INT(11)', false))
        ->setPrimaryKey(['category_id', 'clang_id'])
        ->ensureColumn(new \rex_sql_column('name', 'VARCHAR(255)'))
        ->ensureColumn(new \rex_sql_column('teaser', 'VARCHAR(255)', true))
        ->ensureColumn(new \rex_sql_column('description', 'TEXT', true))
        ->ensureColumn(new \rex_sql_column('usage_area', 'VARCHAR(255)', true))
        ->ensureColumn(new \rex_sql_column('pic_lang', 'VARCHAR(255)', true))
        ->ensureColumn(new \rex_sql_column('pdfs', 'TEXT', true))
        ->ensureColumn(new \rex_sql_column('translation_needs_update', 'VARCHAR(7)', true))
        ->ensureColumn(new \rex_sql_column('updatedate', 'DATETIME', true))
        ->ensureColumn(new \rex_sql_column('updateuser', 'VARCHAR(255)', true))
        ->ensure();

    // Create views for url addon
    $sql->setQuery('CREATE OR REPLACE VIEW '. \rex::getTablePrefix() .'d2u_machinery_url_machines AS
    	SELECT lang.machine_id, lang.clang_id, IF(lang.lang_name IS NULL or lang.lang_name = "", machines.name, lang.lang_name) as name, CONCAT(IF(lang.lang_name IS NULL or lang.lang_name = "", machines.name, lang.lang_name), " - ", categories.name) AS seo_title, lang.teaser AS seo_description, SUBSTRING_INDEX(machines.pics, ",", 1) as picture, machines.category_id, lang.updatedate
    	FROM '. \rex::getTablePrefix() .'d2u_machinery_machines_lang AS lang
    	LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_machines AS machines ON lang.machine_id = machines.machine_id
    	LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_categories_lang AS categories ON machines.category_id = categories.category_id AND lang.clang_id = categories.clang_id
    	LEFT JOIN '. \rex::getTablePrefix() .'clang AS clang ON lang.clang_id = clang.id
    	WHERE clang.`status` = 1 AND machines.online_status = "online"');
    $sql->setQuery('CREATE OR REPLACE VIEW '. \rex::getTablePrefix() .'d2u_machinery_url_machine_categories AS
    	SELECT machines.category_id, categories_lang.clang_id, CONCAT_WS(" - ", parent_categories.name, categories_lang.name) AS name, CONCAT_WS(" - ", categories_lang.name, parent_categories.name) AS seo_title, categories_lang.teaser AS seo_description, IF(categories_lang.pic_lang IS NULL or categories_lang.pic_lang = "", categories.pic, categories_lang.pic_lang) as picture, categories_lang.updatedate
    	FROM '. \rex::getTablePrefix() .'d2u_machinery_machines_lang AS lang
    	LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_machines AS machines ON lang.machine_id = machines.machine_id
    	LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_categories_lang AS categories_lang ON machines.category_id = categories_lang.category_id AND lang.clang_id = categories_lang.clang_id
    	LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_categories AS categories ON categories_lang.category_id = categories.category_id
    	LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_categories_lang AS parent_categories ON categories.parent_category_id = parent_categories.category_id AND lang.clang_id = parent_categories.clang_id
    	LEFT JOIN '. \rex::getTablePrefix() .'clang AS clang ON lang.clang_id = clang.id
    	WHERE clang.`status` = 1 AND machines.online_status = "online"
    	GROUP BY category_id, clang_id, name, seo_title, seo_description, updatedate');

    // Insert url schemes
    if (\rex_addon::get('url')->isAvailable()) {
        $clang_id = 1 === count(rex_clang::getAllIds()) ? rex_clang::getStartId() : 0;
        $article_id = rex_config::get('d2u_machinery', 'article_id', rex_article::getSiteStartArticleId());

        // Insert url schemes Version 2.x
        d2u_machinery_delete_url_profile_by_namespace('machine_id');
        $sql->setQuery('INSERT INTO '. \rex::getTablePrefix() ."url_generator_profile (`namespace`, `article_id`, `clang_id`, `table_name`, `table_parameters`, `relation_1_table_name`, `relation_1_table_parameters`, `relation_2_table_name`, `relation_2_table_parameters`, `relation_3_table_name`, `relation_3_table_parameters`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
    		('machine_id', "
            . $article_id .', '
            . $clang_id .', '
            . "'1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_machines', "
            . "'{\"column_id\":\"machine_id\",\"column_clang_id\":\"clang_id\",\"restriction_1_column\":\"\",\"restriction_1_comparison_operator\":\"=\",\"restriction_1_value\":\"\",\"restriction_2_logical_operator\":\"\",\"restriction_2_column\":\"\",\"restriction_2_comparison_operator\":\"=\",\"restriction_2_value\":\"\",\"restriction_3_logical_operator\":\"\",\"restriction_3_column\":\"\",\"restriction_3_comparison_operator\":\"=\",\"restriction_3_value\":\"\",\"column_segment_part_1\":\"name\",\"column_segment_part_2_separator\":\"\\/\",\"column_segment_part_2\":\"\",\"column_segment_part_3_separator\":\"\\/\",\"column_segment_part_3\":\"\",\"relation_1_column\":\"category_id\",\"relation_1_position\":\"BEFORE\",\"relation_2_column\":\"\",\"relation_2_position\":\"BEFORE\",\"relation_3_column\":\"\",\"relation_3_position\":\"BEFORE\",\"append_user_paths\":\"\",\"append_structure_categories\":\"0\",\"column_seo_title\":\"seo_title\",\"column_seo_description\":\"seo_description\",\"column_seo_image\":\"picture\",\"sitemap_add\":\"1\",\"sitemap_frequency\":\"weekly\",\"sitemap_priority\":\"1.0\",\"column_sitemap_lastmod\":\"updatedate\"}', "
            . "'relation_1_xxx_1_xxx_". rex::getTablePrefix() ."d2u_machinery_categories_lang', "
            . "'{\"column_id\":\"category_id\",\"column_clang_id\":\"clang_id\",\"column_segment_part_1\":\"name\",\"column_segment_part_2_separator\":\"\\/\",\"column_segment_part_2\":\"\",\"column_segment_part_3_separator\":\"\\/\",\"column_segment_part_3\":\"\"}', "
            . "'', '[]', '', '[]', CURRENT_TIMESTAMP, '". (rex::getUser() instanceof rex_user ? rex::getUser()->getValue('login') : '') ."', CURRENT_TIMESTAMP, '". (rex::getUser() instanceof rex_user ? rex::getUser()->getValue('login') : '') ."');");
        d2u_machinery_delete_url_profile_by_namespace('category_id');
        $sql->setQuery('INSERT INTO '. \rex::getTablePrefix() ."url_generator_profile (`namespace`, `article_id`, `clang_id`, `table_name`, `table_parameters`, `relation_1_table_name`, `relation_1_table_parameters`, `relation_2_table_name`, `relation_2_table_parameters`, `relation_3_table_name`, `relation_3_table_parameters`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
    		('category_id', "
            . $article_id .', '
            . $clang_id .', '
            . "'1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_machine_categories', "
            . "'{\"column_id\":\"category_id\",\"column_clang_id\":\"clang_id\",\"restriction_1_column\":\"\",\"restriction_1_comparison_operator\":\"=\",\"restriction_1_value\":\"\",\"restriction_2_logical_operator\":\"\",\"restriction_2_column\":\"\",\"restriction_2_comparison_operator\":\"=\",\"restriction_2_value\":\"\",\"restriction_3_logical_operator\":\"\",\"restriction_3_column\":\"\",\"restriction_3_comparison_operator\":\"=\",\"restriction_3_value\":\"\",\"column_segment_part_1\":\"name\",\"column_segment_part_2_separator\":\"\\/\",\"column_segment_part_2\":\"\",\"column_segment_part_3_separator\":\"\\/\",\"column_segment_part_3\":\"\",\"relation_1_column\":\"\",\"relation_1_position\":\"BEFORE\",\"relation_2_column\":\"\",\"relation_2_position\":\"BEFORE\",\"relation_3_column\":\"\",\"relation_3_position\":\"BEFORE\",\"append_user_paths\":\"\",\"append_structure_categories\":\"0\",\"column_seo_title\":\"seo_title\",\"column_seo_description\":\"seo_description\",\"column_seo_image\":\"picture\",\"sitemap_add\":\"1\",\"sitemap_frequency\":\"monthly\",\"sitemap_priority\":\"0.7\",\"column_sitemap_lastmod\":\"updatedate\"}', "
            . "'', '[]', '', '[]', '', '[]', CURRENT_TIMESTAMP, '". (rex::getUser() instanceof rex_user ? rex::getUser()->getValue('login') : '') ."', CURRENT_TIMESTAMP, '". (rex::getUser() instanceof rex_user ? rex::getUser()->getValue('login') : '') ."');");

        \TobiasKrais\D2UHelper\BackendHelper::generateUrlCache('machine_id');
        \TobiasKrais\D2UHelper\BackendHelper::generateUrlCache('category_id');
    }

    // Insert frontend translations
    if (!class_exists(\TobiasKrais\D2UMachinery\LangHelper::class)) {
        require_once __DIR__ . DIRECTORY_SEPARATOR .'lib'. DIRECTORY_SEPARATOR .'LangHelper.php';
    }
    \TobiasKrais\D2UMachinery\LangHelper::factory()->install();

    // Media Manager media types
    $sql->setQuery('SELECT * FROM '. \rex::getTablePrefix() ."media_manager_type WHERE name = 'd2u_machinery_list_tile'");
    if (0 === $sql->getRows()) {
        $sql->setQuery('INSERT INTO '. \rex::getTablePrefix() ."media_manager_type (`status`, `name`, `description`) VALUES
    		(0, 'd2u_machinery_list_tile', 'D2U Machinery Addon list tile pictures');");
        $last_id_d2u_machinery_list_tile = $sql->getLastId();
        $sql->setQuery('INSERT INTO '. \rex::getTablePrefix() .'media_manager_type_effect (`type_id`, `effect`, `parameters`, `priority`, `createdate`, `createuser`) VALUES
    		('. $last_id_d2u_machinery_list_tile .", 'resize', '{\"rex_effect_convert2img\":{\"rex_effect_convert2img_convert_to\":\"jpg\",\"rex_effect_convert2img_density\":\"100\"},\"rex_effect_crop\":{\"rex_effect_crop_width\":\"\",\"rex_effect_crop_height\":\"\",\"rex_effect_crop_offset_width\":\"\",\"rex_effect_crop_offset_height\":\"\",\"rex_effect_crop_hpos\":\"left\",\"rex_effect_crop_vpos\":\"top\"},\"rex_effect_filter_blur\":{\"rex_effect_filter_blur_repeats\":\"\",\"rex_effect_filter_blur_type\":\"\",\"rex_effect_filter_blur_smoothit\":\"\"},\"rex_effect_filter_colorize\":{\"rex_effect_filter_colorize_filter_r\":\"\",\"rex_effect_filter_colorize_filter_g\":\"\",\"rex_effect_filter_colorize_filter_b\":\"\"},\"rex_effect_filter_sharpen\":{\"rex_effect_filter_sharpen_amount\":\"\",\"rex_effect_filter_sharpen_radius\":\"\",\"rex_effect_filter_sharpen_threshold\":\"\"},\"rex_effect_flip\":{\"rex_effect_flip_flip\":\"X\"},\"rex_effect_header\":{\"rex_effect_header_download\":\"open_media\",\"rex_effect_header_cache\":\"no_cache\"},\"rex_effect_insert_image\":{\"rex_effect_insert_image_brandimage\":\"\",\"rex_effect_insert_image_hpos\":\"left\",\"rex_effect_insert_image_vpos\":\"top\",\"rex_effect_insert_image_padding_x\":\"\",\"rex_effect_insert_image_padding_y\":\"\"},\"rex_effect_mediapath\":{\"rex_effect_mediapath_mediapath\":\"\"},\"rex_effect_mirror\":{\"rex_effect_mirror_height\":\"\",\"rex_effect_mirror_set_transparent\":\"colored\",\"rex_effect_mirror_bg_r\":\"\",\"rex_effect_mirror_bg_g\":\"\",\"rex_effect_mirror_bg_b\":\"\"},\"rex_effect_resize\":{\"rex_effect_resize_width\":\"768\",\"rex_effect_resize_height\":\"768\",\"rex_effect_resize_style\":\"minimum\",\"rex_effect_resize_allow_enlarge\":\"enlarge\"},\"rex_effect_rounded_corners\":{\"rex_effect_rounded_corners_topleft\":\"\",\"rex_effect_rounded_corners_topright\":\"\",\"rex_effect_rounded_corners_bottomleft\":\"\",\"rex_effect_rounded_corners_bottomright\":\"\"},\"rex_effect_workspace\":{\"rex_effect_workspace_width\":\"\",\"rex_effect_workspace_height\":\"\",\"rex_effect_workspace_hpos\":\"left\",\"rex_effect_workspace_vpos\":\"top\",\"rex_effect_workspace_set_transparent\":\"colored\",\"rex_effect_workspace_bg_r\":\"\",\"rex_effect_workspace_bg_g\":\"\",\"rex_effect_workspace_bg_b\":\"\"}}', 1, CURRENT_TIMESTAMP, 'd2u_machinery'),
    		(". $last_id_d2u_machinery_list_tile .", 'workspace', '{\"rex_effect_convert2img\":{\"rex_effect_convert2img_convert_to\":\"jpg\",\"rex_effect_convert2img_density\":\"100\"},\"rex_effect_crop\":{\"rex_effect_crop_width\":\"\",\"rex_effect_crop_height\":\"\",\"rex_effect_crop_offset_width\":\"\",\"rex_effect_crop_offset_height\":\"\",\"rex_effect_crop_hpos\":\"left\",\"rex_effect_crop_vpos\":\"top\"},\"rex_effect_filter_blur\":{\"rex_effect_filter_blur_repeats\":\"\",\"rex_effect_filter_blur_type\":\"\",\"rex_effect_filter_blur_smoothit\":\"\"},\"rex_effect_filter_colorize\":{\"rex_effect_filter_colorize_filter_r\":\"\",\"rex_effect_filter_colorize_filter_g\":\"\",\"rex_effect_filter_colorize_filter_b\":\"\"},\"rex_effect_filter_sharpen\":{\"rex_effect_filter_sharpen_amount\":\"\",\"rex_effect_filter_sharpen_radius\":\"\",\"rex_effect_filter_sharpen_threshold\":\"\"},\"rex_effect_flip\":{\"rex_effect_flip_flip\":\"X\"},\"rex_effect_header\":{\"rex_effect_header_download\":\"open_media\",\"rex_effect_header_cache\":\"no_cache\"},\"rex_effect_insert_image\":{\"rex_effect_insert_image_brandimage\":\"\",\"rex_effect_insert_image_hpos\":\"left\",\"rex_effect_insert_image_vpos\":\"top\",\"rex_effect_insert_image_padding_x\":\"\",\"rex_effect_insert_image_padding_y\":\"\"},\"rex_effect_mediapath\":{\"rex_effect_mediapath_mediapath\":\"\"},\"rex_effect_mirror\":{\"rex_effect_mirror_height\":\"\",\"rex_effect_mirror_set_transparent\":\"colored\",\"rex_effect_mirror_bg_r\":\"\",\"rex_effect_mirror_bg_g\":\"\",\"rex_effect_mirror_bg_b\":\"\"},\"rex_effect_resize\":{\"rex_effect_resize_width\":\"\",\"rex_effect_resize_height\":\"\",\"rex_effect_resize_style\":\"maximum\",\"rex_effect_resize_allow_enlarge\":\"enlarge\"},\"rex_effect_rounded_corners\":{\"rex_effect_rounded_corners_topleft\":\"\",\"rex_effect_rounded_corners_topright\":\"\",\"rex_effect_rounded_corners_bottomleft\":\"\",\"rex_effect_rounded_corners_bottomright\":\"\"},\"rex_effect_workspace\":{\"rex_effect_workspace_width\":\"768\",\"rex_effect_workspace_height\":\"768\",\"rex_effect_workspace_hpos\":\"center\",\"rex_effect_workspace_vpos\":\"middle\",\"rex_effect_workspace_set_transparent\":\"colored\",\"rex_effect_workspace_bg_r\":\"255\",\"rex_effect_workspace_bg_g\":\"255\",\"rex_effect_workspace_bg_b\":\"255\"}}', 2, CURRENT_TIMESTAMP, 'd2u_machinery');");
    }
    $sql->setQuery('SELECT * FROM '. \rex::getTablePrefix() ."media_manager_type WHERE name = 'd2u_machinery_features'");
    if (0 === $sql->getRows()) {
        $sql->setQuery('INSERT INTO '. \rex::getTablePrefix() ."media_manager_type (`status`, `name`, `description`) VALUES
    		(0, 'd2u_machinery_features', 'D2U Machinery Addon Feature, Zubehör, ... Bilder');");
        $last_id_d2u_machinery_list_tile = $sql->getLastId();
        $sql->setQuery('INSERT INTO '. \rex::getTablePrefix() .'media_manager_type_effect (`type_id`, `effect`, `parameters`, `priority`, `createdate`, `createuser`) VALUES
    		('. $last_id_d2u_machinery_list_tile .", 'resize', '{\"rex_effect_convert2img\":{\"rex_effect_convert2img_convert_to\":\"jpg\",\"rex_effect_convert2img_density\":\"100\"},\"rex_effect_crop\":{\"rex_effect_crop_width\":\"\",\"rex_effect_crop_height\":\"\",\"rex_effect_crop_offset_width\":\"\",\"rex_effect_crop_offset_height\":\"\",\"rex_effect_crop_hpos\":\"left\",\"rex_effect_crop_vpos\":\"top\"},\"rex_effect_filter_blur\":{\"rex_effect_filter_blur_repeats\":\"\",\"rex_effect_filter_blur_type\":\"\",\"rex_effect_filter_blur_smoothit\":\"\"},\"rex_effect_filter_colorize\":{\"rex_effect_filter_colorize_filter_r\":\"\",\"rex_effect_filter_colorize_filter_g\":\"\",\"rex_effect_filter_colorize_filter_b\":\"\"},\"rex_effect_filter_sharpen\":{\"rex_effect_filter_sharpen_amount\":\"\",\"rex_effect_filter_sharpen_radius\":\"\",\"rex_effect_filter_sharpen_threshold\":\"\"},\"rex_effect_flip\":{\"rex_effect_flip_flip\":\"X\"},\"rex_effect_header\":{\"rex_effect_header_download\":\"open_media\",\"rex_effect_header_cache\":\"no_cache\"},\"rex_effect_insert_image\":{\"rex_effect_insert_image_brandimage\":\"\",\"rex_effect_insert_image_hpos\":\"left\",\"rex_effect_insert_image_vpos\":\"top\",\"rex_effect_insert_image_padding_x\":\"\",\"rex_effect_insert_image_padding_y\":\"\"},\"rex_effect_mediapath\":{\"rex_effect_mediapath_mediapath\":\"\"},\"rex_effect_mirror\":{\"rex_effect_mirror_height\":\"\",\"rex_effect_mirror_set_transparent\":\"colored\",\"rex_effect_mirror_bg_r\":\"\",\"rex_effect_mirror_bg_g\":\"\",\"rex_effect_mirror_bg_b\":\"\"},\"rex_effect_resize\":{\"rex_effect_resize_width\":\"768\",\"rex_effect_resize_height\":\"768\",\"rex_effect_resize_style\":\"minimum\",\"rex_effect_resize_allow_enlarge\":\"enlarge\"},\"rex_effect_rounded_corners\":{\"rex_effect_rounded_corners_topleft\":\"\",\"rex_effect_rounded_corners_topright\":\"\",\"rex_effect_rounded_corners_bottomleft\":\"\",\"rex_effect_rounded_corners_bottomright\":\"\"},\"rex_effect_workspace\":{\"rex_effect_workspace_width\":\"\",\"rex_effect_workspace_height\":\"\",\"rex_effect_workspace_hpos\":\"left\",\"rex_effect_workspace_vpos\":\"top\",\"rex_effect_workspace_set_transparent\":\"colored\",\"rex_effect_workspace_bg_r\":\"\",\"rex_effect_workspace_bg_g\":\"\",\"rex_effect_workspace_bg_b\":\"\"}}', 1, CURRENT_TIMESTAMP, 'd2u_machinery');");
    }

    // YForm e-mail-template
    $sql->setQuery('SELECT * FROM '. \rex::getTablePrefix() ."yform_email_template WHERE name = 'd2u_machinery_machine_request'");
    if (0 === $sql->getRows()) {
        $sql->setQuery('INSERT INTO '. \rex::getTablePrefix() ."yform_email_template (`name`, `mail_from`, `mail_from_name`, `mail_reply_to`, `mail_reply_to_name`, `subject`, `body`, `body_html`, `attachments`) VALUES
    		('d2u_machinery_machine_request', '', '', 'REX_YFORM_DATA[field=\"email\"]', 'REX_YFORM_DATA[field=\"vorname\"] REX_YFORM_DATA[field=\"name\"]', 'Maschinenanfrage', 'Maschinenanfrage von Internetseite:\r\nMaschine: REX_YFORM_DATA[field=\"machine_name\"]\r\n\r\nEs fragt an:\r\nVorname: REX_YFORM_DATA[field=\"vorname\"]\r\nName: REX_YFORM_DATA[field=\"name\"]\r\nFirma: REX_YFORM_DATA[field=\"company\"]\r\nAnschrift: REX_YFORM_DATA[field=\"address\"]\r\nPLZ/Ort: REX_YFORM_DATA[field=\"zip\"] REX_YFORM_DATA[field=\"city\"]\r\nLand: REX_YFORM_DATA[field=\"country\"]\r\nTelefon: REX_YFORM_DATA[field=\"phone\"]\r\nEmail: REX_YFORM_DATA[field=\"email\"]\r\nBitte um Rückruf: <?php print REX_YFORM_DATA[field=\"please_call\"] == 1 ? \"Ja\" : \"Nein\"; ?>\r\n\r\nNachricht: REX_YFORM_DATA[field=\"message\"]\r\n', '', '')");
    } elseif (rex_version::compare($this->getVersion(), '1.3.1', '<')) { /** @phpstan-ignore-line */
        $sql->setQuery('UPDATE '. \rex::getTablePrefix() ."yform_email_template SET `mail_from` = '', `mail_from_name` = '', `mail_reply_to` = 'REX_YFORM_DATA[field=\"email\"]', `mail_reply_to_name` = 'REX_YFORM_DATA[field=\"vorname\"] REX_YFORM_DATA[field=\"name\"]' WHERE name = 'd2u_machinery_machine_request';");
    }

    // Permission rename
    $sql->setQuery('SELECT * FROM '. \rex::getTablePrefix() ."user_role WHERE INSTR(perms, 'd2u_machinery[edit_tech_data]') > 0");
    if (0 === $sql->getRows()) {
        $sql->setQuery('UPDATE '. \rex::getTablePrefix() ."user_role SET perms = REPLACE(perms, 'd2u_machinery[edit_tech_data]', 'd2u_machinery[edit_data]') WHERE INSTR(perms, 'd2u_machinery[edit_tech_data]') > 0;");
    }

    // Standard settings
    if (!$this->hasConfig()) { /** @phpstan-ignore-line */
        $this->setConfig('article_id', rex_article::getSiteStartArticleId()); /** @phpstan-ignore-line */
    }
    Extension::migrateLegacyStates();
    // Remove default lang setting
    if (!$this->hasConfig()) { /** @phpstan-ignore-line */
        $this->removeConfig('default_lang'); /** @phpstan-ignore-line */
    }
    // Remove unused config: show usage areas in navi (never implemented)
    if ($this->hasConfig('show_categories_usage_area')) { /** @phpstan-ignore-line */
        $this->removeConfig('show_categories_usage_area'); /** @phpstan-ignore-line */
    }

    // Rename config (config was splittet in 1.2.2)
    if ('show' == $this->getConfig('show_usage_areas', 'hide')) { /** @phpstan-ignore-line */
        $this->setConfig('show_categories_usage_areas', 'show'); /** @phpstan-ignore-line */
        $this->removeConfig('show_usage_areas'); /** @phpstan-ignore-line */
    }

    // Update modules
    if (!class_exists(\TobiasKrais\D2UMachinery\Module::class)) {
        require_once __DIR__ . DIRECTORY_SEPARATOR .'lib'. DIRECTORY_SEPARATOR .'Module.php';
    }
    if (!class_exists(\TobiasKrais\D2UHelper\ModuleManager::class)) {
        require_once rex_path::addon('d2u_helper', 'lib/ModuleManager.php');
    }
    $d2u_module_manager = new \TobiasKrais\D2UHelper\ModuleManager(\TobiasKrais\D2UMachinery\Module::getModules(), '', 'd2u_machinery');
    $d2u_module_manager->autoupdate();

    $d2uMachineryAction = array_keys(array_filter(Extension::getStates()));

}

if (null !== $d2uMachineryRequestedAction) {
    $activatedExtensions = is_array($d2uMachineryActivatedActions) ? $d2uMachineryActivatedActions : [$d2uMachineryRequestedAction];
    foreach ($activatedExtensions as $extensionKey) {
        \rex_config::set('d2u_machinery', Extension::getConfigKey($extensionKey), Extension::STATE_ACTIVE);
    }
}

// Extension: contacts
if (d2u_machinery_should_install($d2uMachineryAction, 'contacts')) {
    \rex_sql_table::get(\rex::getTable('d2u_machinery_contacts'))
        ->ensureColumn(new rex_sql_column('contact_id', 'int(10) unsigned', false, null, 'auto_increment'))
        ->setPrimaryKey('contact_id')
        ->ensureColumn(new \rex_sql_column('name', 'VARCHAR(191)', true))
        ->ensureColumn(new \rex_sql_column('picture', 'VARCHAR(191)', true))
        ->ensureColumn(new \rex_sql_column('phone', 'VARCHAR(50)', true))
        ->ensureColumn(new \rex_sql_column('email', 'VARCHAR(50)', true))
        ->ensure();

    \rex_sql_table::get(rex::getTable('d2u_machinery_machines'))
        ->ensureColumn(new \rex_sql_column('contact_id', 'int(10)', true, null))
        ->alter();

}

// Extension: equipment
if (d2u_machinery_should_install($d2uMachineryAction, 'equipment')) {
    \rex_sql_table::get(\rex::getTable('d2u_machinery_equipments'))
        ->ensureColumn(new rex_sql_column('equipment_id', 'INT(11) unsigned', false, null, 'auto_increment'))
        ->setPrimaryKey('equipment_id')
        ->ensureColumn(new \rex_sql_column('article_number', 'VARCHAR(255)'))
        ->ensureColumn(new \rex_sql_column('group_id', 'INT(11)', true))
        ->ensureColumn(new \rex_sql_column('online_status', 'VARCHAR(10)', true))
        ->ensure();
    \rex_sql_table::get(\rex::getTable('d2u_machinery_equipments_lang'))
        ->ensureColumn(new rex_sql_column('equipment_id', 'INT(11)', false))
        ->ensureColumn(new \rex_sql_column('clang_id', 'INT(11)', false))
        ->setPrimaryKey(['equipment_id', 'clang_id'])
        ->ensureColumn(new \rex_sql_column('name', 'VARCHAR(255)'))
        ->ensureColumn(new \rex_sql_column('translation_needs_update', 'VARCHAR(7)', true))
        ->ensure();

    \rex_sql_table::get(\rex::getTable('d2u_machinery_equipment_groups'))
        ->ensureColumn(new rex_sql_column('group_id', 'INT(11) unsigned', false, null, 'auto_increment'))
        ->setPrimaryKey('group_id')
        ->ensureColumn(new \rex_sql_column('picture', 'VARCHAR(255)'))
        ->ensureColumn(new \rex_sql_column('priority', 'INT(11)', true))
        ->ensure();
    \rex_sql_table::get(\rex::getTable('d2u_machinery_equipment_groups_lang'))
        ->ensureColumn(new rex_sql_column('group_id', 'INT(11)', false))
        ->ensureColumn(new \rex_sql_column('clang_id', 'INT(11)', false))
        ->setPrimaryKey(['group_id', 'clang_id'])
        ->ensureColumn(new \rex_sql_column('name', 'VARCHAR(255)'))
        ->ensureColumn(new \rex_sql_column('description', 'TEXT'))
        ->ensureColumn(new \rex_sql_column('translation_needs_update', 'VARCHAR(7)', true))
        ->ensure();

    \rex_sql_table::get(\rex::getTable('d2u_machinery_machines'))
        ->ensureColumn(new \rex_sql_column('equipment_ids', 'TEXT'))
        ->alter();

}

// Extension: export
if (d2u_machinery_should_install($d2uMachineryAction, 'export')) {
    \rex_sql_table::get(\rex::getTable('d2u_machinery_export_provider'))
        ->ensureColumn(new rex_sql_column('provider_id', 'INT(11) unsigned', false, null, 'auto_increment'))
        ->setPrimaryKey('provider_id')
        ->ensureColumn(new \rex_sql_column('name', 'VARCHAR(50)'))
        ->ensureColumn(new \rex_sql_column('type', 'VARCHAR(50)', true))
        ->ensureColumn(new \rex_sql_column('clang_id', 'INT(11)', true))
        ->ensureColumn(new \rex_sql_column('company_name', 'VARCHAR(100)', true))
        ->ensureColumn(new \rex_sql_column('company_email', 'VARCHAR(191)', true))
        ->ensureColumn(new \rex_sql_column('customer_number', 'VARCHAR(50)', true))
        ->ensureColumn(new \rex_sql_column('media_manager_type', 'VARCHAR(191)', true))
        ->ensureColumn(new \rex_sql_column('online_status', 'VARCHAR(10)', true))
        ->ensureColumn(new \rex_sql_column('ftp_server', 'VARCHAR(100)', true))
        ->ensureColumn(new \rex_sql_column('ftp_username', 'VARCHAR(50)', true))
        ->ensureColumn(new \rex_sql_column('ftp_password', 'VARCHAR(50)', true))
        ->ensureColumn(new \rex_sql_column('ftp_filename', 'VARCHAR(50)', true))
        ->ensureColumn(new \rex_sql_column('social_app_id', 'VARCHAR(191)', true))
        ->ensureColumn(new \rex_sql_column('social_app_secret', 'VARCHAR(191)', true))
        ->ensureColumn(new \rex_sql_column('social_access_token', 'TEXT', true))
        ->ensureColumn(new \rex_sql_column('social_access_token_valid_until', 'INT(11)', true))
        ->ensureColumn(new \rex_sql_column('social_refresh_token', 'TEXT', true))
        ->ensureColumn(new \rex_sql_column('social_refresh_token_valid_until', 'INT(11)', true))
        ->ensureColumn(new \rex_sql_column('linkedin_type', 'VARCHAR(10)', true))
        ->ensureColumn(new \rex_sql_column('linkedin_id', 'VARCHAR(40)', true))
        ->ensure();

    \rex_sql_table::get(\rex::getTable('d2u_machinery_export_provider'))
        ->removeColumn('facebook_email')
        ->removeColumn('facebook_pageid')
        ->removeColumn('linkedin_email')
        ->removeColumn('linkedin_groupid')
        ->removeColumn('social_oauth_token')
        ->removeColumn('social_oauth_token_secret')
        ->removeColumn('social_oauth_token_valid_until')
        ->removeColumn('twitter_id')
        ->ensure();

    \rex_sql_table::get(\rex::getTable('d2u_machinery_export_machines'))
        ->ensureColumn(new rex_sql_column('used_machine_id', 'INT(11)', false))
        ->ensureColumn(new \rex_sql_column('provider_id', 'INT(11)', false))
        ->setPrimaryKey(['used_machine_id', 'provider_id'])
        ->ensureColumn(new \rex_sql_column('export_action', 'VARCHAR(10)'))
        ->ensureColumn(new \rex_sql_column('provider_import_id', 'VARCHAR(255)', true))
        ->ensureColumn(new \rex_sql_column('export_timestamp', 'DATETIME', true))
        ->ensure();

    \rex_sql_table::get(\rex::getTable('d2u_machinery_categories'))
        ->ensureColumn(new \rex_sql_column('export_machinerypark_category_id', 'INT(10)'))
        ->ensureColumn(new \rex_sql_column('export_europemachinery_category_id', 'INT(10)'))
        ->ensureColumn(new \rex_sql_column('export_europemachinery_category_name', 'VARCHAR(255)'))
        ->ensureColumn(new \rex_sql_column('export_mascus_category_name', 'VARCHAR(255)'))
        ->alter();

}

// Extension: industry sectors
if (d2u_machinery_should_install($d2uMachineryAction, 'industry_sectors')) {
    \rex_sql_table::get(\rex::getTable('d2u_machinery_industry_sectors'))
        ->ensureColumn(new rex_sql_column('industry_sector_id', 'INT(11) unsigned', false, null, 'auto_increment'))
        ->setPrimaryKey('industry_sector_id')
        ->ensureColumn(new \rex_sql_column('online_status', 'VARCHAR(7)', true))
        ->ensureColumn(new \rex_sql_column('pic', 'VARCHAR(255)', true))
        ->ensureColumn(new \rex_sql_column('icon', 'VARCHAR(255)', true))
        ->ensure();
    \rex_sql_table::get(\rex::getTable('d2u_machinery_industry_sectors_lang'))
        ->ensureColumn(new rex_sql_column('industry_sector_id', 'INT(11)', false))
        ->ensureColumn(new \rex_sql_column('clang_id', 'INT(11)', false))
        ->setPrimaryKey(['industry_sector_id', 'clang_id'])
        ->ensureColumn(new \rex_sql_column('name', 'VARCHAR(255)'))
        ->ensureColumn(new \rex_sql_column('teaser', 'VARCHAR(255)', true))
        ->ensureColumn(new \rex_sql_column('description', 'TEXT', true))
        ->ensureColumn(new \rex_sql_column('translation_needs_update', 'VARCHAR(7)', true))
        ->ensureColumn(new \rex_sql_column('updatedate', 'DATETIME', true))
        ->ensureColumn(new \rex_sql_column('updateuser', 'VARCHAR(255)', true))
        ->ensure();

    \rex_sql_table::get(\rex::getTable('d2u_machinery_machines'))
        ->ensureColumn(new \rex_sql_column('industry_sector_ids', 'TEXT'))
        ->alter();

    $sql->setQuery('CREATE OR REPLACE VIEW '. \rex::getTablePrefix() .'d2u_machinery_url_industry_sectors AS
    	SELECT lang.industry_sector_id, lang.clang_id, lang.name, lang.name AS seo_title, lang.teaser AS seo_description, industries.pic AS picture, lang.updatedate
    	FROM '. \rex::getTablePrefix() .'d2u_machinery_industry_sectors_lang AS lang
    	LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_industry_sectors AS industries ON lang.industry_sector_id = industries.industry_sector_id
    	LEFT JOIN '. \rex::getTablePrefix() .'clang AS clang ON lang.clang_id = clang.id
    	WHERE clang.`status` = 1');

    if (\rex_addon::get('url')->isAvailable()) {
        $clang_id = 1 === count(rex_clang::getAllIds()) ? rex_clang::getStartId() : 0;
        $article_id = rex_config::get('d2u_machinery', 'industry_sectors_article_id', rex_article::getSiteStartArticleId());
        d2u_machinery_delete_url_profile_by_namespace('industry_sector_id');
        $sql->setQuery('INSERT INTO '. \rex::getTablePrefix() ."url_generator_profile (`namespace`, `article_id`, `clang_id`, `table_name`, `table_parameters`, `relation_1_table_name`, `relation_1_table_parameters`, `relation_2_table_name`, `relation_2_table_parameters`, `relation_3_table_name`, `relation_3_table_parameters`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
    		('industry_sector_id', "
            . $article_id .', '
            . $clang_id .', '
            . "'1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_industry_sectors', "
            . "'{\"column_id\":\"industry_sector_id\",\"column_clang_id\":\"clang_id\",\"restriction_1_column\":\"\",\"restriction_1_comparison_operator\":\"=\",\"restriction_1_value\":\"\",\"restriction_2_logical_operator\":\"\",\"restriction_2_column\":\"\",\"restriction_2_comparison_operator\":\"=\",\"restriction_2_value\":\"\",\"restriction_3_logical_operator\":\"\",\"restriction_3_column\":\"\",\"restriction_3_comparison_operator\":\"=\",\"restriction_3_value\":\"\",\"column_segment_part_1\":\"name\",\"column_segment_part_2_separator\":\"\\/\",\"column_segment_part_2\":\"\",\"column_segment_part_3_separator\":\"\\/\",\"column_segment_part_3\":\"\",\"relation_1_column\":\"\",\"relation_1_position\":\"BEFORE\",\"relation_2_column\":\"\",\"relation_2_position\":\"BEFORE\",\"relation_3_column\":\"\",\"relation_3_position\":\"BEFORE\",\"append_user_paths\":\"\",\"append_structure_categories\":\"0\",\"column_seo_title\":\"seo_title\",\"column_seo_description\":\"seo_description\",\"column_seo_image\":\"picture\",\"sitemap_add\":\"1\",\"sitemap_frequency\":\"monthly\",\"sitemap_priority\":\"0.5\",\"column_sitemap_lastmod\":\"updatedate\"}', "
            . "'', '[]', '', '[]', '', '[]', CURRENT_TIMESTAMP, '". (rex::getUser() instanceof rex_user ? rex::getUser()->getValue('login') : '') ."', CURRENT_TIMESTAMP, '". (rex::getUser() instanceof rex_user ? rex::getUser()->getValue('login') : '') ."');");
        \TobiasKrais\D2UHelper\BackendHelper::generateUrlCache('industry_sector_id');
    }
    if (!rex_config::has('d2u_machinery', 'industry_sectors_article_id')) {
        rex_config::set('d2u_machinery', 'industry_sectors_article_id', rex_config::get('d2u_machinery', 'article_id'));
    }

}

// Extension: agitators
if (d2u_machinery_should_install($d2uMachineryAction, 'machine_agitator_extension')) {
    \rex_sql_table::get(\rex::getTable('d2u_machinery_agitator_types'))
        ->ensureColumn(new rex_sql_column('agitator_type_id', 'INT(11) unsigned', false, null, 'auto_increment'))
        ->setPrimaryKey('agitator_type_id')
        ->ensureColumn(new \rex_sql_column('agitator_ids', 'TEXT', true))
        ->ensureColumn(new \rex_sql_column('pic', 'VARCHAR(255)', true))
        ->ensure();
    \rex_sql_table::get(\rex::getTable('d2u_machinery_agitator_types_lang'))
        ->ensureColumn(new rex_sql_column('agitator_type_id', 'INT(11)', false))
        ->ensureColumn(new \rex_sql_column('clang_id', 'INT(11)', false))
        ->setPrimaryKey(['agitator_type_id', 'clang_id'])
        ->ensureColumn(new \rex_sql_column('name', 'VARCHAR(255)'))
        ->ensureColumn(new \rex_sql_column('translation_needs_update', 'VARCHAR(7)', true))
        ->ensure();
    \rex_sql_table::get(\rex::getTable('d2u_machinery_agitators'))
        ->ensureColumn(new rex_sql_column('agitator_id', 'INT(11) unsigned', false, null, 'auto_increment'))
        ->setPrimaryKey('agitator_id')
        ->ensureColumn(new \rex_sql_column('pic', 'VARCHAR(255)', true))
        ->ensure();
    \rex_sql_table::get(\rex::getTable('d2u_machinery_agitators_lang'))
        ->ensureColumn(new rex_sql_column('agitator_id', 'INT(11)', false))
        ->ensureColumn(new \rex_sql_column('clang_id', 'INT(11)', false))
        ->setPrimaryKey(['agitator_id', 'clang_id'])
        ->ensureColumn(new \rex_sql_column('name', 'VARCHAR(255)'))
        ->ensureColumn(new \rex_sql_column('description', 'TEXT'))
        ->ensureColumn(new \rex_sql_column('translation_needs_update', 'VARCHAR(7)', true))
        ->ensure();
    \rex_sql_table::get(\rex::getTable('d2u_machinery_machines'))
        ->ensureColumn(new \rex_sql_column('agitator_type_id', 'INT(10)'))
        ->ensureColumn(new \rex_sql_column('viscosity', 'INT(10)'))
        ->alter();
    \rex_sql_table::get(\rex::getTable('d2u_machinery_categories'))
        ->ensureColumn(new \rex_sql_column('show_agitators', 'VARCHAR(4)'))
        ->alter();

}

// Extension: certificates
if (d2u_machinery_should_install($d2uMachineryAction, 'machine_certificates_extension')) {
    \rex_sql_table::get(\rex::getTable('d2u_machinery_certificates'))
        ->ensureColumn(new rex_sql_column('certificate_id', 'INT(11) unsigned', false, null, 'auto_increment'))
        ->setPrimaryKey('certificate_id')
        ->ensureColumn(new \rex_sql_column('pic', 'VARCHAR(255)', true))
        ->ensure();
    \rex_sql_table::get(\rex::getTable('d2u_machinery_certificates_lang'))
        ->ensureColumn(new rex_sql_column('certificate_id', 'INT(11)', false))
        ->ensureColumn(new \rex_sql_column('clang_id', 'INT(11)', false))
        ->setPrimaryKey(['certificate_id', 'clang_id'])
        ->ensureColumn(new \rex_sql_column('name', 'VARCHAR(255)'))
        ->ensureColumn(new \rex_sql_column('description', 'TEXT'))
        ->ensureColumn(new \rex_sql_column('translation_needs_update', 'VARCHAR(7)', true))
        ->ensure();
    \rex_sql_table::get(\rex::getTable('d2u_machinery_machines'))
        ->ensureColumn(new \rex_sql_column('certificate_ids', 'TEXT'))
        ->alter();

}

// Extension: construction equipment
if (d2u_machinery_should_install($d2uMachineryAction, 'machine_construction_equipment_extension')) {
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
    \rex_sql_table::get(\rex::getTable('d2u_machinery_machines'))
        ->removeColumn('pump_conveying_distance_mineral')
        ->removeColumn('pump_conveying_distance_pasty')
        ->removeColumn('pump_flow_volume_mineral')
        ->removeColumn('pump_flow_volume_pasty')
        ->removeColumn('pump_pressure_height_mineral')
        ->removeColumn('pump_pressure_height_pasty')
        ->alter();
    \rex_sql_table::get(\rex::getTable('d2u_machinery_machines_lang'))
        ->ensureColumn(new \rex_sql_column('container_connection_port', 'VARCHAR(255)'))
        ->ensureColumn(new \rex_sql_column('container_conveying_wave', 'VARCHAR(255)'))
        ->ensureColumn(new \rex_sql_column('description_technical', 'VARCHAR(255)'))
        ->ensureColumn(new \rex_sql_column('delivery_set_basic', 'TEXT'))
        ->ensureColumn(new \rex_sql_column('delivery_set_conversion', 'TEXT'))
        ->ensureColumn(new \rex_sql_column('delivery_set_full', 'TEXT'))
        ->alter();

    $sql->setQuery('SELECT * FROM '. \rex::getTablePrefix() ."media_manager_type WHERE name = 'd2u_machinery_construction_equipment_delivery_set_slider'");
    if (0 === $sql->getRows()) {
        $sql->setQuery('INSERT INTO '. \rex::getTablePrefix() ."media_manager_type (`status`, `name`, `description`) VALUES
    		(0, 'd2u_machinery_construction_equipment_delivery_set_slider', 'D2U Machinery Addon - Baustellenausrüstung Plugin: Bilder slider Lieferumfang');");
        $last_id_d2u_machinery_list_tile = $sql->getLastId();
        $sql->setQuery('INSERT INTO '. \rex::getTablePrefix() .'media_manager_type_effect (`type_id`, `effect`, `parameters`, `priority`, `createdate`, `createuser`) VALUES
    		('. $last_id_d2u_machinery_list_tile .", 'resize', '{\"rex_effect_convert2img\":{\"rex_effect_convert2img_convert_to\":\"jpg\",\"rex_effect_convert2img_density\":\"100\"},\"rex_effect_crop\":{\"rex_effect_crop_width\":\"\",\"rex_effect_crop_height\":\"\",\"rex_effect_crop_offset_width\":\"\",\"rex_effect_crop_offset_height\":\"\",\"rex_effect_crop_hpos\":\"left\",\"rex_effect_crop_vpos\":\"top\"},\"rex_effect_filter_blur\":{\"rex_effect_filter_blur_repeats\":\"\",\"rex_effect_filter_blur_type\":\"\",\"rex_effect_filter_blur_smoothit\":\"\"},\"rex_effect_filter_colorize\":{\"rex_effect_filter_colorize_filter_r\":\"\",\"rex_effect_filter_colorize_filter_g\":\"\",\"rex_effect_filter_colorize_filter_b\":\"\"},\"rex_effect_filter_sharpen\":{\"rex_effect_filter_sharpen_amount\":\"\",\"rex_effect_filter_sharpen_radius\":\"\",\"rex_effect_filter_sharpen_threshold\":\"\"},\"rex_effect_flip\":{\"rex_effect_flip_flip\":\"X\"},\"rex_effect_header\":{\"rex_effect_header_download\":\"open_media\",\"rex_effect_header_cache\":\"no_cache\"},\"rex_effect_insert_image\":{\"rex_effect_insert_image_brandimage\":\"\",\"rex_effect_insert_image_hpos\":\"left\",\"rex_effect_insert_image_vpos\":\"top\",\"rex_effect_insert_image_padding_x\":\"\",\"rex_effect_insert_image_padding_y\":\"\"},\"rex_effect_mediapath\":{\"rex_effect_mediapath_mediapath\":\"\"},\"rex_effect_mirror\":{\"rex_effect_mirror_height\":\"\",\"rex_effect_mirror_set_transparent\":\"colored\",\"rex_effect_mirror_bg_r\":\"\",\"rex_effect_mirror_bg_g\":\"\",\"rex_effect_mirror_bg_b\":\"\"},\"rex_effect_resize\":{\"rex_effect_resize_width\":\"768\",\"rex_effect_resize_height\":\"768\",\"rex_effect_resize_style\":\"minimum\",\"rex_effect_resize_allow_enlarge\":\"enlarge\"},\"rex_effect_rounded_corners\":{\"rex_effect_rounded_corners_topleft\":\"\",\"rex_effect_rounded_corners_topright\":\"\",\"rex_effect_rounded_corners_bottomleft\":\"\",\"rex_effect_rounded_corners_bottomright\":\"\"},\"rex_effect_workspace\":{\"rex_effect_workspace_width\":\"\",\"rex_effect_workspace_height\":\"\",\"rex_effect_workspace_hpos\":\"left\",\"rex_effect_workspace_vpos\":\"top\",\"rex_effect_workspace_set_transparent\":\"colored\",\"rex_effect_workspace_bg_r\":\"\",\"rex_effect_workspace_bg_g\":\"\",\"rex_effect_workspace_bg_b\":\"\"}}', 1, CURRENT_TIMESTAMP, 'd2u_machinery'),
    		(". $last_id_d2u_machinery_list_tile .", 'workspace', '{\"rex_effect_convert2img\":{\"rex_effect_convert2img_convert_to\":\"jpg\",\"rex_effect_convert2img_density\":\"100\"},\"rex_effect_crop\":{\"rex_effect_crop_width\":\"\",\"rex_effect_crop_height\":\"\",\"rex_effect_crop_offset_width\":\"\",\"rex_effect_crop_offset_height\":\"\",\"rex_effect_crop_hpos\":\"left\",\"rex_effect_crop_vpos\":\"top\"},\"rex_effect_filter_blur\":{\"rex_effect_filter_blur_repeats\":\"\",\"rex_effect_filter_blur_type\":\"\",\"rex_effect_filter_blur_smoothit\":\"\"},\"rex_effect_filter_colorize\":{\"rex_effect_filter_colorize_filter_r\":\"\",\"rex_effect_filter_colorize_filter_g\":\"\",\"rex_effect_filter_colorize_filter_b\":\"\"},\"rex_effect_filter_sharpen\":{\"rex_effect_filter_sharpen_amount\":\"\",\"rex_effect_filter_sharpen_radius\":\"\",\"rex_effect_filter_sharpen_threshold\":\"\"},\"rex_effect_flip\":{\"rex_effect_flip_flip\":\"X\"},\"rex_effect_header\":{\"rex_effect_header_download\":\"open_media\",\"rex_effect_header_cache\":\"no_cache\"},\"rex_effect_insert_image\":{\"rex_effect_insert_image_brandimage\":\"\",\"rex_effect_insert_image_hpos\":\"left\",\"rex_effect_insert_image_vpos\":\"top\",\"rex_effect_insert_image_padding_x\":\"\",\"rex_effect_insert_image_padding_y\":\"\"},\"rex_effect_mediapath\":{\"rex_effect_mediapath_mediapath\":\"\"},\"rex_effect_mirror\":{\"rex_effect_mirror_height\":\"\",\"rex_effect_mirror_set_transparent\":\"colored\",\"rex_effect_mirror_bg_r\":\"\",\"rex_effect_mirror_bg_g\":\"\",\"rex_effect_mirror_bg_b\":\"\"},\"rex_effect_resize\":{\"rex_effect_resize_width\":\"\",\"rex_effect_resize_height\":\"\",\"rex_effect_resize_style\":\"maximum\",\"rex_effect_resize_allow_enlarge\":\"enlarge\"},\"rex_effect_rounded_corners\":{\"rex_effect_rounded_corners_topleft\":\"\",\"rex_effect_rounded_corners_topright\":\"\",\"rex_effect_rounded_corners_bottomleft\":\"\",\"rex_effect_rounded_corners_bottomright\":\"\"},\"rex_effect_workspace\":{\"rex_effect_workspace_width\":\"768\",\"rex_effect_workspace_height\":\"768\",\"rex_effect_workspace_hpos\":\"center\",\"rex_effect_workspace_vpos\":\"middle\",\"rex_effect_workspace_set_transparent\":\"transparent\",\"rex_effect_workspace_bg_r\":\"255\",\"rex_effect_workspace_bg_g\":\"255\",\"rex_effect_workspace_bg_b\":\"255\"}}', 2, CURRENT_TIMESTAMP, 'd2u_machinery');");
    }

}

// Extension: features
if (d2u_machinery_should_install($d2uMachineryAction, 'machine_features_extension')) {
    \rex_sql_table::get(\rex::getTable('d2u_machinery_features'))
        ->ensureColumn(new rex_sql_column('feature_id', 'INT(11) unsigned', false, null, 'auto_increment'))
        ->setPrimaryKey('feature_id')
        ->ensureColumn(new \rex_sql_column('priority', 'INT(11)', true))
        ->ensureColumn(new \rex_sql_column('category_ids', 'VARCHAR(255)', true))
        ->ensureColumn(new \rex_sql_column('pic', 'VARCHAR(100)', true))
        ->ensureColumn(new \rex_sql_column('video_id', 'INT(11)', true))
        ->ensure();
    \rex_sql_table::get(\rex::getTable('d2u_machinery_features_lang'))
        ->ensureColumn(new rex_sql_column('feature_id', 'INT(11)'))
        ->ensureColumn(new \rex_sql_column('clang_id', 'INT(11)', false))
        ->setPrimaryKey(['feature_id', 'clang_id'])
        ->ensureColumn(new \rex_sql_column('name', 'VARCHAR(255)'))
        ->ensureColumn(new \rex_sql_column('description', 'TEXT', true))
        ->ensureColumn(new \rex_sql_column('translation_needs_update', 'VARCHAR(7)'))
        ->ensureColumn(new \rex_sql_column('updatedate', 'DATETIME'))
        ->ensureColumn(new \rex_sql_column('updateuser', 'VARCHAR(255)'))
        ->ensure();
    \rex_sql_table::get(\rex::getTable('d2u_machinery_features_lang'))
        ->removeColumn('title')
        ->ensure();
    \rex_sql_table::get(\rex::getTable('d2u_machinery_machines'))
        ->ensureColumn(new \rex_sql_column('feature_ids', 'TEXT'))
        ->alter();

}

// Extension: options
if (d2u_machinery_should_install($d2uMachineryAction, 'machine_options_extension')) {
    \rex_sql_table::get(\rex::getTable('d2u_machinery_options'))
        ->ensureColumn(new rex_sql_column('option_id', 'INT(11) unsigned', false, null, 'auto_increment'))
        ->setPrimaryKey('option_id')
        ->ensureColumn(new \rex_sql_column('priority', 'INT(11)', true))
        ->ensureColumn(new \rex_sql_column('category_ids', 'VARCHAR(255)', true))
        ->ensureColumn(new \rex_sql_column('pic', 'VARCHAR(100)', true))
        ->ensureColumn(new \rex_sql_column('video_id', 'INT(11)', true))
        ->ensure();
    \rex_sql_table::get(\rex::getTable('d2u_machinery_options_lang'))
        ->ensureColumn(new rex_sql_column('option_id', 'INT(11)'))
        ->ensureColumn(new \rex_sql_column('clang_id', 'INT(11)', false))
        ->setPrimaryKey(['option_id', 'clang_id'])
        ->ensureColumn(new \rex_sql_column('name', 'VARCHAR(255)'))
        ->ensureColumn(new \rex_sql_column('description', 'TEXT', true))
        ->ensureColumn(new \rex_sql_column('translation_needs_update', 'VARCHAR(7)'))
        ->ensureColumn(new \rex_sql_column('updatedate', 'DATETIME'))
        ->ensureColumn(new \rex_sql_column('updateuser', 'VARCHAR(255)'))
        ->ensure();
    \rex_sql_table::get(\rex::getTable('d2u_machinery_machines'))
        ->ensureColumn(new \rex_sql_column('option_ids', 'TEXT'))
        ->alter();

}

// Extension: steel processing
if (d2u_machinery_should_install($d2uMachineryAction, 'machine_steel_processing_extension')) {
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
    \rex_sql_table::get(\rex::getTable('d2u_machinery_machines'))
        ->ensureColumn(new \rex_sql_column('process_ids', 'TEXT'))
        ->ensureColumn(new \rex_sql_column('procedure_ids', 'TEXT'))
        ->ensureColumn(new \rex_sql_column('material_ids', 'TEXT'))
        ->ensureColumn(new \rex_sql_column('tool_ids', 'TEXT'))
        ->ensureColumn(new \rex_sql_column('automation_automationgrade_ids', 'TEXT'))
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
    \rex_sql_table::get(\rex::getTable('d2u_machinery_categories_lang'))
        ->removeColumn('steel_processing_saw_cutting_range_file')
        ->removeColumn('steel_processing_saw_cutting_range_title')
        ->alter();

}

// Extension: steel automation
if (d2u_machinery_should_install($d2uMachineryAction, 'machine_steel_automation_extension')) {
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
    \rex_sql_table::get(\rex::getTable('d2u_machinery_steel_supply_lang'))
        ->removeColumn('title')
        ->alter();
    \rex_sql_table::get(\rex::getTable('d2u_machinery_machines'))
        ->ensureColumn(new \rex_sql_column('automation_supply_single_stroke', 'VARCHAR(25)'))
        ->ensureColumn(new \rex_sql_column('automation_supply_multi_stroke', 'VARCHAR(25)'))
        ->ensureColumn(new \rex_sql_column('automation_feedrate', 'VARCHAR(25)'))
        ->ensureColumn(new \rex_sql_column('automation_feedrate_sawblade', 'VARCHAR(25)'))
        ->ensureColumn(new \rex_sql_column('automation_rush_leader_flyback', 'VARCHAR(25)'))
        ->ensureColumn(new \rex_sql_column('automation_supply_ids', 'TEXT'))
        ->alter();
    $sql->setQuery('SHOW TABLES LIKE "'. \rex::getTable('d2u_machinery_production_lines') .'"');
    if ($sql->getRows() > 0) {
        \rex_sql_table::get(\rex::getTable('d2u_machinery_production_lines'))
                ->ensureColumn(new \rex_sql_column('automation_supply_ids', 'TEXT'))
                ->alter();
    }

}

// Extension: usage areas
if (d2u_machinery_should_install($d2uMachineryAction, 'machine_usage_area_extension')) {
    \rex_sql_table::get(\rex::getTable('d2u_machinery_usage_areas'))
        ->ensureColumn(new rex_sql_column('usage_area_id', 'INT(11) unsigned', false, null, 'auto_increment'))
        ->setPrimaryKey('usage_area_id')
        ->ensureColumn(new \rex_sql_column('priority', 'INT(11)', true))
        ->ensureColumn(new \rex_sql_column('category_ids', 'VARCHAR(255)', true))
        ->ensure();
    \rex_sql_table::get(\rex::getTable('d2u_machinery_usage_areas_lang'))
        ->ensureColumn(new rex_sql_column('usage_area_id', 'INT(11)', false))
        ->ensureColumn(new \rex_sql_column('clang_id', 'INT(11)', false))
        ->setPrimaryKey(['usage_area_id', 'clang_id'])
        ->ensureColumn(new \rex_sql_column('name', 'VARCHAR(255)'))
        ->ensureColumn(new \rex_sql_column('translation_needs_update', 'VARCHAR(7)', true))
        ->ensure();
    \rex_sql_table::get(\rex::getTable('d2u_machinery_machines'))
        ->ensureColumn(new \rex_sql_column('usage_area_ids', 'TEXT'))
        ->alter();

}

// Extension: production lines
if (d2u_machinery_should_install($d2uMachineryAction, 'production_lines')) {
    \rex_sql_table::get(\rex::getTable('d2u_machinery_production_lines'))
        ->ensureColumn(new rex_sql_column('production_line_id', 'int(10) unsigned', false, null, 'auto_increment'))
        ->setPrimaryKey('production_line_id')
        ->ensureColumn(new \rex_sql_column('complementary_machine_ids', 'VARCHAR(255)', true))
        ->ensureColumn(new \rex_sql_column('industry_sector_ids', 'VARCHAR(255)', true))
        ->ensureColumn(new \rex_sql_column('line_code', 'VARCHAR(50)', true))
        ->ensureColumn(new \rex_sql_column('machine_ids', 'VARCHAR(255)', true))
        ->ensureColumn(new \rex_sql_column('pictures', 'TEXT', true))
        ->ensureColumn(new \rex_sql_column('link_picture', 'TEXT', true))
        ->ensureColumn(new \rex_sql_column('usp_ids', 'VARCHAR(255)', true))
        ->ensureColumn(new \rex_sql_column('reference_ids', 'TEXT', true))
        ->ensureColumn(new \rex_sql_column('video_ids', 'VARCHAR(255)', true))
        ->ensureColumn(new \rex_sql_column('online_status', 'VARCHAR(10)'))
        ->ensure();
    \rex_sql_table::get(\rex::getTable('d2u_machinery_production_lines_lang'))
        ->ensureColumn(new rex_sql_column('production_line_id', 'int(10) unsigned', false, null, 'auto_increment'))
        ->ensureColumn(new \rex_sql_column('clang_id', 'INT(11)', false))
        ->setPrimaryKey(['production_line_id', 'clang_id'])
        ->ensureColumn(new \rex_sql_column('name', 'VARCHAR(255)'))
        ->ensureColumn(new \rex_sql_column('teaser', 'VARCHAR(255)'))
        ->ensureColumn(new \rex_sql_column('description_long', 'TEXT', true))
        ->ensureColumn(new \rex_sql_column('description_short', 'TEXT', true))
        ->ensureColumn(new \rex_sql_column('translation_needs_update', 'VARCHAR(7)'))
        ->ensureColumn(new \rex_sql_column('updatedate', 'DATETIME'))
        ->ensureColumn(new \rex_sql_column('updateuser', 'VARCHAR(255)'))
        ->ensure();
    \rex_sql_table::get(\rex::getTable('d2u_machinery_production_lines_usps'))
        ->ensureColumn(new rex_sql_column('usp_id', 'int(10) unsigned', false, null, 'auto_increment'))
        ->setPrimaryKey('usp_id')
        ->ensureColumn(new \rex_sql_column('picture', 'VARCHAR(100)', true))
        ->ensure();
    \rex_sql_table::get(\rex::getTable('d2u_machinery_production_lines_usps_lang'))
        ->ensureColumn(new rex_sql_column('usp_id', 'int(10) unsigned', false, null, 'auto_increment'))
        ->ensureColumn(new \rex_sql_column('clang_id', 'INT(11)', false))
        ->setPrimaryKey(['usp_id', 'clang_id'])
        ->ensureColumn(new \rex_sql_column('name', 'VARCHAR(255)'))
        ->ensureColumn(new \rex_sql_column('teaser', 'VARCHAR(255)'))
        ->ensureColumn(new \rex_sql_column('translation_needs_update', 'VARCHAR(7)'))
        ->ensureColumn(new \rex_sql_column('updatedate', 'DATETIME'))
        ->ensureColumn(new \rex_sql_column('updateuser', 'VARCHAR(255)'))
        ->ensure();

    $sql->setQuery('CREATE OR REPLACE VIEW '. \rex::getTablePrefix() .'d2u_machinery_url_production_lines AS
    	SELECT lang.production_line_id, lang.clang_id, lang.name, lang.name AS seo_title, lang.teaser AS seo_description, SUBSTRING_INDEX(production_lines.pictures, ",", 1) as picture, lang.updatedate
    	FROM '. \rex::getTablePrefix() .'d2u_machinery_production_lines_lang AS lang
    	LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_production_lines AS production_lines ON lang.production_line_id = production_lines.production_line_id
    	LEFT JOIN '. \rex::getTablePrefix() .'clang AS clang ON lang.clang_id = clang.id
    	WHERE clang.`status` = 1 AND production_lines.online_status = "online"');

    if (\rex_addon::get('url')->isAvailable()) {
        $clang_id = 1 === count(rex_clang::getAllIds()) ? rex_clang::getStartId() : 0;
        $article_id = rex_config::get('d2u_machinery', 'production_lines_article_id', 0) > 0 ? rex_config::get('d2u_machinery', 'production_lines_article_id') : rex_article::getSiteStartArticleId();
        d2u_machinery_delete_url_profile_by_namespace('production_line_id');
        $sql->setQuery('INSERT INTO '. \rex::getTablePrefix() ."url_generator_profile (`namespace`, `article_id`, `clang_id`, `table_name`, `table_parameters`, `relation_1_table_name`, `relation_1_table_parameters`, `relation_2_table_name`, `relation_2_table_parameters`, `relation_3_table_name`, `relation_3_table_parameters`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
    		('production_line_id', "
            . $article_id .', '
            . $clang_id .', '
            . "'1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_production_lines', "
            . "'{\"column_id\":\"production_line_id\",\"column_clang_id\":\"clang_id\",\"restriction_1_column\":\"\",\"restriction_1_comparison_operator\":\"=\",\"restriction_1_value\":\"\",\"restriction_2_logical_operator\":\"\",\"restriction_2_column\":\"\",\"restriction_2_comparison_operator\":\"=\",\"restriction_2_value\":\"\",\"restriction_3_logical_operator\":\"\",\"restriction_3_column\":\"\",\"restriction_3_comparison_operator\":\"=\",\"restriction_3_value\":\"\",\"column_segment_part_1\":\"name\",\"column_segment_part_2_separator\":\"\\/\",\"column_segment_part_2\":\"\",\"column_segment_part_3_separator\":\"\\/\",\"column_segment_part_3\":\"\",\"relation_1_column\":\"\",\"relation_1_position\":\"BEFORE\",\"relation_2_column\":\"\",\"relation_2_position\":\"BEFORE\",\"relation_3_column\":\"\",\"relation_3_position\":\"BEFORE\",\"append_user_paths\":\"\",\"append_structure_categories\":\"0\",\"column_seo_title\":\"seo_title\",\"column_seo_description\":\"seo_description\",\"column_seo_image\":\"picture\",\"sitemap_add\":\"1\",\"sitemap_frequency\":\"monthly\",\"sitemap_priority\":\"0.7\",\"column_sitemap_lastmod\":\"updatedate\"}', "
            . "'', '[]', '', '[]', '', '[]', CURRENT_TIMESTAMP, '". (rex::getUser() instanceof rex_user ? rex::getUser()->getValue('login') : '') ."', CURRENT_TIMESTAMP, '". (rex::getUser() instanceof rex_user ? rex::getUser()->getValue('login') : '') ."');");
        \TobiasKrais\D2UHelper\BackendHelper::generateUrlCache('production_line_id');
    }

}

// Extension: service options
if (d2u_machinery_should_install($d2uMachineryAction, 'service_options')) {
    \rex_sql_table::get(\rex::getTable('d2u_machinery_service_options'))
        ->ensureColumn(new rex_sql_column('service_option_id', 'int(10) unsigned', false, null, 'auto_increment'))
        ->setPrimaryKey('service_option_id')
        ->ensureColumn(new \rex_sql_column('online_status', 'VARCHAR(10)', true))
        ->ensureColumn(new \rex_sql_column('picture', 'VARCHAR(255)', true))
        ->ensure();
    \rex_sql_table::get(\rex::getTable('d2u_machinery_service_options_lang'))
        ->ensureColumn(new rex_sql_column('service_option_id', 'int(10) unsigned', false, null, 'auto_increment'))
        ->ensureColumn(new \rex_sql_column('clang_id', 'INT(11)', false))
        ->setPrimaryKey(['service_option_id', 'clang_id'])
        ->ensureColumn(new \rex_sql_column('name', 'VARCHAR(255)'))
        ->ensureColumn(new \rex_sql_column('description', 'TEXT'))
        ->ensureColumn(new \rex_sql_column('translation_needs_update', 'VARCHAR(7)'))
        ->ensureColumn(new \rex_sql_column('updatedate', 'DATETIME'))
        ->ensureColumn(new \rex_sql_column('updateuser', 'VARCHAR(255)'))
        ->ensure();
    \rex_sql_table::get(\rex::getTable('d2u_machinery_machines'))
        ->ensureColumn(new \rex_sql_column('service_option_ids', 'TEXT'))
        ->alter();

}

// Extension: used machines
if (d2u_machinery_should_install($d2uMachineryAction, 'used_machines')) {
    $d2u_machinery = rex_addon::get('d2u_machinery');
    if (!$d2u_machinery->hasConfig('used_machine_article_id_rent')) {
        $d2u_machinery->setConfig('used_machine_article_id_rent', rex_article::getSiteStartArticleId());
    }
    if (!$d2u_machinery->hasConfig('used_machine_article_id_sale')) {
        $d2u_machinery->setConfig('used_machine_article_id_sale', rex_article::getSiteStartArticleId());
    }

    \rex_sql_table::get(\rex::getTable('d2u_machinery_used_machines'))
        ->ensureColumn(new rex_sql_column('used_machine_id', 'INT(10) unsigned', false, null, 'auto_increment'))
        ->setPrimaryKey('used_machine_id')
        ->ensureColumn(new \rex_sql_column('manufacturer', 'VARCHAR(191)', true))
        ->ensureColumn(new \rex_sql_column('name', 'VARCHAR(191)', true))
        ->ensureColumn(new \rex_sql_column('category_id', 'INT(10)', true))
        ->ensureColumn(new \rex_sql_column('contact_id', 'INT(10)', true))
        ->ensureColumn(new \rex_sql_column('availability', 'VARCHAR(10)', true))
        ->ensureColumn(new \rex_sql_column('product_number', 'VARCHAR(191)', true))
        ->ensureColumn(new \rex_sql_column('offer_type', 'VARCHAR(10)', true))
        ->ensureColumn(new \rex_sql_column('year_built', 'INT(4)', true))
        ->ensureColumn(new \rex_sql_column('price', 'DECIMAL(10,2)', true))
        ->ensureColumn(new \rex_sql_column('currency_code', 'VARCHAR(3)', true))
        ->ensureColumn(new \rex_sql_column('vat', 'INT(2)', true))
        ->ensureColumn(new \rex_sql_column('online_status', 'VARCHAR(10)', true))
        ->ensureColumn(new \rex_sql_column('pics', 'TEXT', true))
        ->ensureColumn(new \rex_sql_column('machine_id', 'INT(10)', true))
        ->ensureColumn(new \rex_sql_column('location', 'VARCHAR(191)', true))
        ->ensureColumn(new \rex_sql_column('external_url', 'VARCHAR(191)', true))
        ->ensureColumn(new \rex_sql_column('video_ids', 'VARCHAR(191)', true))
        ->ensure();
    \rex_sql_table::get(\rex::getTable('d2u_machinery_used_machines_lang'))
        ->ensureColumn(new rex_sql_column('used_machine_id', 'INT(10) unsigned', false, null, 'auto_increment'))
        ->ensureColumn(new \rex_sql_column('clang_id', 'INT(11)', false))
        ->setPrimaryKey(['used_machine_id', 'clang_id'])
        ->ensureColumn(new \rex_sql_column('teaser', 'TEXT', true))
        ->ensureColumn(new \rex_sql_column('description', 'TEXT', true))
        ->ensureColumn(new \rex_sql_column('downloads', 'TEXT', true))
        ->ensureColumn(new \rex_sql_column('translation_needs_update', 'VARCHAR(7)', true))
        ->ensureColumn(new \rex_sql_column('updatedate', 'DATETIME', true))
        ->ensureColumn(new \rex_sql_column('updateuser', 'VARCHAR(50)', true))
        ->ensure();

    $sql->setQuery('CREATE OR REPLACE VIEW '. \rex::getTablePrefix() .'d2u_machinery_url_used_machines_rent AS
    	SELECT lang.used_machine_id, lang.clang_id, CONCAT(machines.manufacturer, " ", machines.name) AS name, CONCAT(machines.manufacturer, " ", machines.name, " - ", categories.name) AS seo_title, lang.teaser AS seo_description, SUBSTRING_INDEX(machines.pics, ",", 1) as picture, machines.category_id, lang.updatedate
    	FROM '. \rex::getTablePrefix() .'d2u_machinery_used_machines_lang AS lang
    	LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_used_machines AS machines ON lang.used_machine_id = machines.used_machine_id
    	LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_categories_lang AS categories ON machines.category_id = categories.category_id AND lang.clang_id = categories.clang_id
    	LEFT JOIN '. \rex::getTablePrefix() .'clang AS clang ON lang.clang_id = clang.id
    	WHERE clang.`status` = 1 AND machines.online_status = "online" AND machines.offer_type = "rent"
    	GROUP BY used_machine_id, clang_id, name, seo_title, seo_description, picture, category_id, updatedate;');
    $sql->setQuery('CREATE OR REPLACE VIEW '. \rex::getTablePrefix() .'d2u_machinery_url_used_machine_categories_rent AS
    	SELECT machines.category_id, categories_lang.clang_id, CONCAT_WS(" - ", parent_categories.name, categories_lang.name) AS name, CONCAT_WS(" - ", categories_lang.name, parent_categories.name) AS seo_title, categories_lang.teaser AS seo_description,IF(categories_lang.pic_lang IS NULL or categories_lang.pic_lang = "", categories.pic, categories_lang.pic_lang) as picture, categories_lang.updatedate
    	FROM '. \rex::getTablePrefix() .'d2u_machinery_used_machines_lang AS lang
    	LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_used_machines AS machines ON lang.used_machine_id = machines.used_machine_id
    	LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_categories_lang AS categories_lang ON machines.category_id = categories_lang.category_id AND lang.clang_id = categories_lang.clang_id
    	LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_categories AS categories ON categories_lang.category_id = categories.category_id
    	LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_categories_lang AS parent_categories ON categories.parent_category_id = parent_categories.category_id AND lang.clang_id = parent_categories.clang_id
    	LEFT JOIN '. \rex::getTablePrefix() .'clang AS clang ON lang.clang_id = clang.id
    	WHERE clang.`status` = 1 AND machines.online_status = "online" AND machines.offer_type = "rent"
    	GROUP BY category_id, clang_id, name, seo_title, seo_description, picture, updatedate;');
    $sql->setQuery('CREATE OR REPLACE VIEW '. \rex::getTablePrefix() .'d2u_machinery_url_used_machines_sale AS
    	SELECT lang.used_machine_id, lang.clang_id, CONCAT(machines.manufacturer, " ", machines.name) AS name, CONCAT(machines.manufacturer, " ", machines.name, " - ", categories.name) AS seo_title, lang.teaser AS seo_description, SUBSTRING_INDEX(machines.pics, ",", 1) as picture, machines.category_id, lang.updatedate
    	FROM '. \rex::getTablePrefix() .'d2u_machinery_used_machines_lang AS lang
    	LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_used_machines AS machines ON lang.used_machine_id = machines.used_machine_id
    	LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_categories_lang AS categories ON machines.category_id = categories.category_id AND lang.clang_id = categories.clang_id
    	LEFT JOIN '. \rex::getTablePrefix() .'clang AS clang ON lang.clang_id = clang.id
    	WHERE clang.`status` = 1 AND machines.online_status = "online" AND machines.offer_type = "sale"
    	GROUP BY used_machine_id, clang_id, name, seo_title, seo_description, picture, category_id, updatedate;');
    $sql->setQuery('CREATE OR REPLACE VIEW '. \rex::getTablePrefix() .'d2u_machinery_url_used_machine_categories_sale AS
    	SELECT machines.category_id, categories_lang.clang_id, CONCAT_WS(" - ", parent_categories.name, categories_lang.name) AS name, CONCAT_WS(" - ", categories_lang.name, parent_categories.name) AS seo_title, categories_lang.teaser AS seo_description,IF(categories_lang.pic_lang IS NULL or categories_lang.pic_lang = "", categories.pic, categories_lang.pic_lang) as picture, categories_lang.updatedate
    	FROM '. \rex::getTablePrefix() .'d2u_machinery_used_machines_lang AS lang
    	LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_used_machines AS machines ON lang.used_machine_id = machines.used_machine_id
    	LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_categories_lang AS categories_lang ON machines.category_id = categories_lang.category_id AND lang.clang_id = categories_lang.clang_id
    	LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_categories AS categories ON categories_lang.category_id = categories.category_id
    	LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_categories_lang AS parent_categories ON categories.parent_category_id = parent_categories.category_id AND lang.clang_id = parent_categories.clang_id
    	LEFT JOIN '. \rex::getTablePrefix() .'clang AS clang ON lang.clang_id = clang.id
    	WHERE clang.`status` = 1 AND machines.online_status = "online" AND machines.offer_type = "sale"
    	GROUP BY category_id, clang_id, name, seo_title, seo_description, picture, updatedate;');

    if (\rex_addon::get('url')->isAvailable()) {
        $clang_id = 1 === count(rex_clang::getAllIds()) ? rex_clang::getStartId() : 0;
        $article_id_rent = rex_config::get('d2u_machinery', 'used_machine_article_id_rent', rex_article::getSiteStartArticleId());
        $article_id_sale = rex_config::get('d2u_machinery', 'used_machine_article_id_sale', rex_article::getSiteStartArticleId());
        d2u_machinery_delete_url_profile_by_namespace('used_rent_machine_id');
        $sql->setQuery('INSERT INTO '. \rex::getTablePrefix() ."url_generator_profile (`namespace`, `article_id`, `clang_id`, `table_name`, `table_parameters`, `relation_1_table_name`, `relation_1_table_parameters`, `relation_2_table_name`, `relation_2_table_parameters`, `relation_3_table_name`, `relation_3_table_parameters`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
    		('used_rent_machine_id', "
            . $article_id_rent .', '
            . $clang_id .', '
            . "'1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines_rent', "
            . "'{\"column_id\":\"used_machine_id\",\"column_clang_id\":\"clang_id\",\"restriction_1_column\":\"\",\"restriction_1_comparison_operator\":\"=\",\"restriction_1_value\":\"\",\"restriction_2_logical_operator\":\"\",\"restriction_2_column\":\"\",\"restriction_2_comparison_operator\":\"=\",\"restriction_2_value\":\"\",\"restriction_3_logical_operator\":\"\",\"restriction_3_column\":\"\",\"restriction_3_comparison_operator\":\"=\",\"restriction_3_value\":\"\",\"column_segment_part_1\":\"name\",\"column_segment_part_2_separator\":\"\\-\",\"column_segment_part_2\":\"used_machine_id\",\"column_segment_part_3_separator\":\"\\/\",\"column_segment_part_3\":\"\",\"relation_1_column\":\"category_id\",\"relation_1_position\":\"BEFORE\",\"relation_2_column\":\"\",\"relation_2_position\":\"BEFORE\",\"relation_3_column\":\"\",\"relation_3_position\":\"BEFORE\",\"append_user_paths\":\"\",\"append_structure_categories\":\"0\",\"column_seo_title\":\"seo_title\",\"column_seo_description\":\"seo_description\",\"column_seo_image\":\"picture\",\"sitemap_add\":\"1\",\"sitemap_frequency\":\"weekly\",\"sitemap_priority\":\"1.0\",\"column_sitemap_lastmod\":\"updatedate\"}', "
            . "'relation_1_xxx_1_xxx_". rex::getTablePrefix() ."d2u_machinery_categories_lang', "
            . "'{\"column_id\":\"category_id\",\"column_clang_id\":\"clang_id\",\"column_segment_part_1\":\"name\",\"column_segment_part_2_separator\":\"\\/\",\"column_segment_part_2\":\"\",\"column_segment_part_3_separator\":\"\\/\",\"column_segment_part_3\":\"\"}', "
            . "'', '[]', '', '[]', CURRENT_TIMESTAMP, '". (rex::getUser() instanceof rex_user ? rex::getUser()->getValue('login') : '') ."', CURRENT_TIMESTAMP, '". (rex::getUser() instanceof rex_user ? rex::getUser()->getValue('login') : '') ."');");
        d2u_machinery_delete_url_profile_by_namespace('used_rent_category_id');
        $sql->setQuery('INSERT INTO '. \rex::getTablePrefix() ."url_generator_profile (`namespace`, `article_id`, `clang_id`, `table_name`, `table_parameters`, `relation_1_table_name`, `relation_1_table_parameters`, `relation_2_table_name`, `relation_2_table_parameters`, `relation_3_table_name`, `relation_3_table_parameters`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
    		('used_rent_category_id', "
            . $article_id_rent .', '
            . $clang_id .', '
            . "'1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_rent', "
            . "'{\"column_id\":\"category_id\",\"column_clang_id\":\"clang_id\",\"restriction_1_column\":\"\",\"restriction_1_comparison_operator\":\"=\",\"restriction_1_value\":\"\",\"restriction_2_logical_operator\":\"\",\"restriction_2_column\":\"\",\"restriction_2_comparison_operator\":\"=\",\"restriction_2_value\":\"\",\"restriction_3_logical_operator\":\"\",\"restriction_3_column\":\"\",\"restriction_3_comparison_operator\":\"=\",\"restriction_3_value\":\"\",\"column_segment_part_1\":\"name\",\"column_segment_part_2_separator\":\"\\/\",\"column_segment_part_2\":\"\",\"column_segment_part_3_separator\":\"\\/\",\"column_segment_part_3\":\"\",\"relation_1_column\":\"\",\"relation_1_position\":\"BEFORE\",\"relation_2_column\":\"\",\"relation_2_position\":\"BEFORE\",\"relation_3_column\":\"\",\"relation_3_position\":\"BEFORE\",\"append_user_paths\":\"\",\"append_structure_categories\":\"0\",\"column_seo_title\":\"seo_title\",\"column_seo_description\":\"seo_description\",\"column_seo_image\":\"picture\",\"sitemap_add\":\"1\",\"sitemap_frequency\":\"weekly\",\"sitemap_priority\":\"0.7\",\"column_sitemap_lastmod\":\"updatedate\"}', "
            . "'', '[]', '', '[]', '', '[]', CURRENT_TIMESTAMP, '". (rex::getUser() instanceof rex_user ? rex::getUser()->getValue('login') : '') ."', CURRENT_TIMESTAMP, '". (rex::getUser() instanceof rex_user ? rex::getUser()->getValue('login') : '') ."');");
        d2u_machinery_delete_url_profile_by_namespace('used_sale_machine_id');
        $sql->setQuery('INSERT INTO '. \rex::getTablePrefix() ."url_generator_profile (`namespace`, `article_id`, `clang_id`, `table_name`, `table_parameters`, `relation_1_table_name`, `relation_1_table_parameters`, `relation_2_table_name`, `relation_2_table_parameters`, `relation_3_table_name`, `relation_3_table_parameters`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
    		('used_sale_machine_id', "
            . $article_id_sale .', '
            . $clang_id .', '
            . "'1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machines_sale', "
            . "'{\"column_id\":\"used_machine_id\",\"column_clang_id\":\"clang_id\",\"restriction_1_column\":\"\",\"restriction_1_comparison_operator\":\"=\",\"restriction_1_value\":\"\",\"restriction_2_logical_operator\":\"\",\"restriction_2_column\":\"\",\"restriction_2_comparison_operator\":\"=\",\"restriction_2_value\":\"\",\"restriction_3_logical_operator\":\"\",\"restriction_3_column\":\"\",\"restriction_3_comparison_operator\":\"=\",\"restriction_3_value\":\"\",\"column_segment_part_1\":\"name\",\"column_segment_part_2_separator\":\"\\-\",\"column_segment_part_2\":\"used_machine_id\",\"column_segment_part_3_separator\":\"\\/\",\"column_segment_part_3\":\"\",\"relation_1_column\":\"category_id\",\"relation_1_position\":\"BEFORE\",\"relation_2_column\":\"\",\"relation_2_position\":\"BEFORE\",\"relation_3_column\":\"\",\"relation_3_position\":\"BEFORE\",\"append_user_paths\":\"\",\"append_structure_categories\":\"0\",\"column_seo_title\":\"seo_title\",\"column_seo_description\":\"seo_description\",\"column_seo_image\":\"picture\",\"sitemap_add\":\"1\",\"sitemap_frequency\":\"weekly\",\"sitemap_priority\":\"1.0\",\"column_sitemap_lastmod\":\"updatedate\"}', "
            . "'relation_1_xxx_1_xxx_". rex::getTablePrefix() ."d2u_machinery_categories_lang', "
            . "'{\"column_id\":\"category_id\",\"column_clang_id\":\"clang_id\",\"column_segment_part_1\":\"name\",\"column_segment_part_2_separator\":\"\\/\",\"column_segment_part_2\":\"\",\"column_segment_part_3_separator\":\"\\/\",\"column_segment_part_3\":\"\"}', "
            . "'', '[]', '', '[]', CURRENT_TIMESTAMP, '". (rex::getUser() instanceof rex_user ? rex::getUser()->getValue('login') : '') ."', CURRENT_TIMESTAMP, '". (rex::getUser() instanceof rex_user ? rex::getUser()->getValue('login') : '') ."');");
        d2u_machinery_delete_url_profile_by_namespace('used_sale_category_id');
        $sql->setQuery('INSERT INTO '. \rex::getTablePrefix() ."url_generator_profile (`namespace`, `article_id`, `clang_id`, `table_name`, `table_parameters`, `relation_1_table_name`, `relation_1_table_parameters`, `relation_2_table_name`, `relation_2_table_parameters`, `relation_3_table_name`, `relation_3_table_parameters`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
    		('used_sale_category_id', "
            . $article_id_sale .', '
            . $clang_id .', '
            . "'1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sale', "
            . "'{\"column_id\":\"category_id\",\"column_clang_id\":\"clang_id\",\"restriction_1_column\":\"\",\"restriction_1_comparison_operator\":\"=\",\"restriction_1_value\":\"\",\"restriction_2_logical_operator\":\"\",\"restriction_2_column\":\"\",\"restriction_2_comparison_operator\":\"=\",\"restriction_2_value\":\"\",\"restriction_3_logical_operator\":\"\",\"restriction_3_column\":\"\",\"restriction_3_comparison_operator\":\"=\",\"restriction_3_value\":\"\",\"column_segment_part_1\":\"name\",\"column_segment_part_2_separator\":\"\\/\",\"column_segment_part_2\":\"\",\"column_segment_part_3_separator\":\"\\/\",\"column_segment_part_3\":\"\",\"relation_1_column\":\"\",\"relation_1_position\":\"BEFORE\",\"relation_2_column\":\"\",\"relation_2_position\":\"BEFORE\",\"relation_3_column\":\"\",\"relation_3_position\":\"BEFORE\",\"append_user_paths\":\"\",\"append_structure_categories\":\"0\",\"column_seo_title\":\"seo_title\",\"column_seo_description\":\"seo_description\",\"column_seo_image\":\"picture\",\"sitemap_add\":\"1\",\"sitemap_frequency\":\"always\",\"sitemap_priority\":\"0.7\",\"column_sitemap_lastmod\":\"updatedate\"}', "
            . "'', '[]', '', '[]', '', '[]', CURRENT_TIMESTAMP, '". (rex::getUser() instanceof rex_user ? rex::getUser()->getValue('login') : '') ."', CURRENT_TIMESTAMP, '". (rex::getUser() instanceof rex_user ? rex::getUser()->getValue('login') : '') ."');");
        \TobiasKrais\D2UHelper\BackendHelper::generateUrlCache('used_rent_machine_id');
        \TobiasKrais\D2UHelper\BackendHelper::generateUrlCache('used_rent_category_id');
        \TobiasKrais\D2UHelper\BackendHelper::generateUrlCache('used_sale_machine_id');
        \TobiasKrais\D2UHelper\BackendHelper::generateUrlCache('used_sale_category_id');
    }
}