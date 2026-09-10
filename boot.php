<?php

use TobiasKrais\D2UMachinery\Agitator;
use TobiasKrais\D2UMachinery\AgitatorType;
use TobiasKrais\D2UMachinery\Category;
use TobiasKrais\D2UMachinery\Certificate;
use TobiasKrais\D2UMachinery\Equipment;
use TobiasKrais\D2UMachinery\EquipmentGroup;
use TobiasKrais\D2UMachinery\Extension;
use TobiasKrais\D2UMachinery\Feature;
use TobiasKrais\D2UMachinery\IndustrySector;
use TobiasKrais\D2UMachinery\Machine;
use TobiasKrais\D2UMachinery\Option;
use TobiasKrais\D2UMachinery\ProductionLine;
use TobiasKrais\D2UMachinery\Provider;
use TobiasKrais\D2UMachinery\ServiceOption;
use TobiasKrais\D2UMachinery\Supply;
use TobiasKrais\D2UMachinery\UsageArea;
use TobiasKrais\D2UMachinery\UsedMachine;

if (\rex::isBackend() && is_object(\rex::getUser())) {
    Extension::ensureConfigInitialized();

    $page = $this->getProperty('page');
    if (is_array($page)) {
        $this->setProperty('page', Extension::removeInactivePagesFromNavigation($page));
    }

    rex_perm::register('d2u_machinery[]', rex_i18n::msg('d2u_machinery_rights_all'));
    rex_perm::register('d2u_machinery[machine]', rex_i18n::msg('d2u_machinery_rights_all') .': '. rex_i18n::msg('d2u_machinery_meta_machines'));
    rex_perm::register('d2u_machinery[category]', rex_i18n::msg('d2u_machinery_rights_all') .': '. rex_i18n::msg('d2u_helper_categories'));
    rex_perm::register('d2u_machinery[edit_lang]', rex_i18n::msg('d2u_machinery_rights_edit_lang'), rex_perm::OPTIONS);
    rex_perm::register('d2u_machinery[edit_data]', rex_i18n::msg('d2u_machinery_rights_edit_data'), rex_perm::OPTIONS);
    rex_perm::register('d2u_machinery[settings]', rex_i18n::msg('d2u_machinery_rights_settings'), rex_perm::OPTIONS);
    if (Extension::isActive('used_machines')) {
        rex_perm::register('d2u_machinery[used_machines]', rex_i18n::msg('d2u_machinery_used_machines_rights'));
    }
    if (Extension::isActive('export')) {
        rex_perm::register('d2u_machinery[export]', rex_i18n::msg('d2u_machinery_export_rights_export'), rex_perm::OPTIONS);
        rex_perm::register('d2u_machinery[export_provider]', rex_i18n::msg('d2u_machinery_export_rights_export_provider'));
    }
    Extension::hideInactiveBackendPages();

    rex_extension::register('D2U_HELPER_TRANSLATION_LIST', rex_d2u_machinery_translation_list(...));
    rex_extension::register('D2U_HELPER_TRANSLATE_OBJECT', rex_d2u_machinery_translate_object(...));
}

if (\rex::isBackend()) {
    rex_extension::register('ART_PRE_DELETED', rex_d2u_machinery_article_is_in_use(...));
    rex_extension::register('CLANG_DELETED', rex_d2u_machinery_clang_deleted(...));
    rex_extension::register('D2U_VIDEO_IN_USE', rex_d2u_machinery_video_is_in_use(...));
    rex_extension::register('MEDIA_IS_IN_USE', rex_d2u_machinery_media_is_in_use(...));

    if (Extension::isActive('contacts')) {
        rex_extension::register('MEDIA_IS_IN_USE', rex_d2u_machinery_contacts_media_is_in_use(...));
    }
    if (Extension::isActive('equipment')) {
        rex_extension::register('CLANG_DELETED', rex_d2u_machinery_equipment_clang_deleted(...));
        rex_extension::register('MEDIA_IS_IN_USE', rex_d2u_machinery_equipment_media_is_in_use(...));
    }
    if (Extension::isActive('export')) {
        rex_extension::register('CLANG_DELETED', rex_d2u_machinery_export_clang_deleted(...));
    }
    if (Extension::isActive('industry_sectors')) {
        rex_extension::register('CLANG_DELETED', rex_d2u_machinery_industry_sectors_clang_deleted(...));
        rex_extension::register('MEDIA_IS_IN_USE', rex_d2u_machinery_industry_sectors_media_is_in_use(...));
    }
    if (Extension::isActive('machine_agitator_extension')) {
        rex_extension::register('CLANG_DELETED', rex_d2u_machinery_agitators_clang_deleted(...));
        rex_extension::register('MEDIA_IS_IN_USE', rex_d2u_machinery_agitators_media_is_in_use(...));
    }
    if (Extension::isActive('machine_certificates_extension')) {
        rex_extension::register('CLANG_DELETED', rex_d2u_machinery_certificates_clang_deleted(...));
        rex_extension::register('MEDIA_IS_IN_USE', rex_d2u_machinery_certificates_media_is_in_use(...));
    }
    if (Extension::isActive('machine_features_extension')) {
        rex_extension::register('CLANG_DELETED', rex_d2u_machinery_features_clang_deleted(...));
        rex_extension::register('MEDIA_IS_IN_USE', rex_d2u_machinery_features_media_is_in_use(...));
    }
    if (Extension::isActive('machine_options_extension')) {
        rex_extension::register('CLANG_DELETED', rex_d2u_machinery_options_clang_deleted(...));
        rex_extension::register('MEDIA_IS_IN_USE', rex_d2u_machinery_options_media_is_in_use(...));
    }
    if (Extension::isActive('machine_steel_automation_extension')) {
        rex_extension::register('CLANG_DELETED', rex_d2u_machinery_supply_clang_deleted(...));
        rex_extension::register('MEDIA_IS_IN_USE', rex_d2u_machinery_supply_media_is_in_use(...));
    }
    if (Extension::isActive('machine_usage_area_extension')) {
        rex_extension::register('CLANG_DELETED', rex_d2u_machinery_usage_area_clang_deleted(...));
    }
    if (Extension::isActive('production_lines')) {
        rex_extension::register('CLANG_DELETED', rex_d2u_machinery_production_lines_clang_deleted(...));
        rex_extension::register('MEDIA_IS_IN_USE', rex_d2u_machinery_production_lines_media_is_in_use(...));
    }
    if (Extension::isActive('service_options')) {
        rex_extension::register('CLANG_DELETED', rex_d2u_machinery_service_options_clang_deleted(...));
        rex_extension::register('MEDIA_IS_IN_USE', rex_d2u_machinery_service_options_media_is_in_use(...));
    }
    if (Extension::isActive('used_machines')) {
        rex_extension::register('CLANG_DELETED', rex_d2u_machinery_used_machines_clang_deleted(...));
        rex_extension::register('MEDIA_IS_IN_USE', rex_d2u_machinery_used_machines_media_is_in_use(...));
        rex_extension::register('ART_PRE_DELETED', rex_d2u_machinery_used_machines_article_is_in_use(...));
    }
} else {
    rex_extension::register('D2U_HELPER_ALTERNATE_URLS', rex_d2u_machinery_alternate_urls(...));
    rex_extension::register('D2U_HELPER_BREADCRUMBS', rex_d2u_machinery_breadcrumbs(...));
    $d2u_video = rex_addon::get('d2u_videos');
    if ($d2u_video->isAvailable() && rex_version::compare($d2u_video->getVersion(), '1.1', '>=')) {
        rex_extension::register('YREWRITE_SITEMAP', rex_d2u_machinery_video_sitemap(...));
    }
}
// Call this extension point also in frontend
rex_extension::register('URL_PRE_SAVE', rex_d2u_machinery_url_shortener(...));

/**
 * Get alternate URLs.
 * @param rex_extension_point<array<string>> $ep Redaxo extension point
 * @return array<int,string> Addon url list
 */
function rex_d2u_machinery_alternate_urls(rex_extension_point $ep) {
    $params = $ep->getParams();
    $url_namespace = (string) $params['url_namespace'];
    $url_id = (int) $params['url_id'];

    $url_list = \TobiasKrais\D2UMachinery\FrontendHelper::getAlternateURLs($url_namespace, $url_id);
    if (count($url_list) === 0 && is_array($ep->getSubject())) {
        $url_list = $ep->getSubject();
    }

    return $url_list;
}

/**
 * Checks if article is used by this addon.
 * @param rex_extension_point<string> $ep Redaxo extension point
 * @throws rex_api_exception If article is used
 * @return string Warning message as array
 */
function rex_d2u_machinery_article_is_in_use(rex_extension_point $ep)
{
    $warning = [];
    $params = $ep->getParams();
    $article_id = (int) $params['id'];

    // Machines
    $sql_machine = \rex_sql::factory();
    $sql_machine->setQuery('SELECT lang.machine_id, name FROM `' . \rex::getTablePrefix() . 'd2u_machinery_machines_lang` AS lang '
        .'LEFT JOIN `' . \rex::getTablePrefix() . 'd2u_machinery_machines` AS machines ON lang.machine_id = machines.machine_id '
        .'WHERE article_id_software = "'. $article_id .'" OR article_id_service = "'. $article_id .'" '.
            'OR article_id_service = "'. $article_id .'" OR article_ids_references LIKE "%,'. $article_id .',%" OR article_ids_references LIKE "%,'. $article_id .'" OR article_ids_references LIKE "'. $article_id .',%"'
        .'GROUP BY machine_id');

    // Prepare warnings
    // Machines
    for ($i = 0; $i < $sql_machine->getRows(); ++$i) {
        $message = '<a href="javascript:openPage(\'index.php?page=d2u_machinery/machine/machine&func=edit&entry_id='.
            $sql_machine->getValue('machine_id') .'\')">'. rex_i18n::msg('d2u_machinery_rights_all') .' - '. rex_i18n::msg('d2u_machinery_meta_machines') .': '. $sql_machine->getValue('name') .'</a>';
        if (!in_array($message, $warning, true)) {
            $warning[] = $message;
        }
        $sql_machine->next();
    }

    // Settings
    $addon = rex_addon::get('d2u_machinery');
    if ($addon->hasConfig('article_id') && $addon->getConfig('article_id') === $article_id) {
        $message = '<a href="index.php?page=d2u_machinery/settings">'.
             rex_i18n::msg('d2u_machinery_rights_all') .' - '. rex_i18n::msg('d2u_helper_settings') . '</a>';
        if (!in_array($message, $warning, true)) {
            $warning[] = $message;
        }
    }

    if (count($warning) > 0) {
        throw new rex_api_exception(rex_i18n::msg('d2u_helper_rex_article_cannot_delete').'<ul><li>'. implode('</li><li>', $warning) .'</li></ul>');
    }

    return '';

}

/**
 * Get breadcrumb part.
 * @param rex_extension_point<array<string>> $ep Redaxo extension point
 * @return array<int,string> HTML formatted breadcrumb elements
 */
function rex_d2u_machinery_breadcrumbs(rex_extension_point $ep) {
    $params = $ep->getParams();
    $url_namespace = (string) $params['url_namespace'];
    $url_id = (int) $params['url_id'];

    $breadcrumbs = \TobiasKrais\D2UMachinery\FrontendHelper::getBreadcrumbs($url_namespace, $url_id);
    if (count($breadcrumbs) === 0) {
        $breadcrumbs = $ep->getSubject();
    }

    return $breadcrumbs;
}

/**
 * Deletes language specific configurations and objects.
 * @param rex_extension_point<array<string>> $ep Redaxo extension point
 * @return array<string> Warning message as array
 */
function rex_d2u_machinery_clang_deleted(rex_extension_point $ep)
{
    $warning = $ep->getSubject();
    $params = $ep->getParams();
    $clang_id = $params['id'];

    // Delete
    $categories = Category::getAll($clang_id);
    foreach ($categories as $category) {
        $category->delete(false);
    }
    $machines = Machine::getAll($clang_id, false);
    foreach ($machines as $machine) {
        $machine->delete(false);
    }

    // Delete language settings
    if (rex_config::has('d2u_machinery', 'lang_replacement_'. $clang_id)) {
        rex_config::remove('d2u_machinery', 'lang_replacement_'. $clang_id);
    }
    // Delete language replacements
    \TobiasKrais\D2UMachinery\LangHelper::factory()->uninstall($clang_id);

    return $warning;
}

/**
 * Checks if media is used by this addon.
 * @param rex_extension_point<array<string>> $ep Redaxo extension point
 * @return array<string> Warning message as array
 */
function rex_d2u_machinery_media_is_in_use(rex_extension_point $ep)
{
    $warning = $ep->getSubject();
    $params = $ep->getParams();
    $filename = (string) $params['filename'];
    $filenameLike = '%' . str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $filename) . '%';

    // Machines
    $machineQueryParams = [
        ':filenamePdfs' => $filename,
        ':filenamePics' => $filename,
        ':filenameDescriptionLike' => $filenameLike,
        ':filenameBenefitsShortLike' => $filenameLike,
        ':filenameBenefitsLongLike' => $filenameLike,
        ':filenameLeaflet' => $filename,
    ];
    if (Extension::isActive('machine_construction_equipment_extension')) {
        $machineQueryParams[':filenamePicturesDeliverySet'] = $filename;
    }
    $sql_machine = \rex_sql::factory();
    $sql_machine->setQuery('SELECT lang.machine_id, name FROM `' . \rex::getTablePrefix() . 'd2u_machinery_machines_lang` AS lang '
        .'LEFT JOIN `' . \rex::getTablePrefix() . 'd2u_machinery_machines` AS machines ON lang.machine_id = machines.machine_id '
        .'WHERE FIND_IN_SET(:filenamePdfs, pdfs) OR FIND_IN_SET(:filenamePics, pics) OR description LIKE :filenameDescriptionLike OR benefits_short LIKE :filenameBenefitsShortLike OR benefits_long LIKE :filenameBenefitsLongLike OR leaflet = :filenameLeaflet'
        . (Extension::isActive('machine_construction_equipment_extension') ? ' OR FIND_IN_SET(:filenamePicturesDeliverySet, pictures_delivery_set)' : '')
        .' GROUP BY machine_id', $machineQueryParams);

    // Categories
    $categoryQueryParams = [
        ':filenamePic' => $filename,
        ':filenamePicUsage' => $filename,
        ':filenamePicLang' => $filename,
        ':filenamePdfs' => $filename,
        ':filenameDescriptionLike' => $filenameLike,
    ];
    $sql_categories = \rex_sql::factory();
    $sql_categories->setQuery('SELECT lang.category_id, name FROM `' . \rex::getTablePrefix() . 'd2u_machinery_categories_lang` AS lang '
        .'LEFT JOIN `' . \rex::getTablePrefix() . 'd2u_machinery_categories` AS categories ON lang.category_id = categories.category_id '
        .'WHERE pic = :filenamePic OR pic_usage = :filenamePicUsage OR pic_lang = :filenamePicLang OR FIND_IN_SET(:filenamePdfs, pdfs) OR description LIKE :filenameDescriptionLike', $categoryQueryParams);

    // Prepare warnings
    // Machines
    for ($i = 0; $i < $sql_machine->getRows(); ++$i) {
        $message = '<a href="javascript:openPage(\'index.php?page=d2u_machinery/machine/machine&func=edit&entry_id='.
            $sql_machine->getValue('machine_id') .'\')">'. rex_i18n::msg('d2u_machinery_rights_all') .' - '. rex_i18n::msg('d2u_machinery_meta_machines') .': '. $sql_machine->getValue('name') .'</a>';
        if (!in_array($message, $warning, true)) {
            $warning[] = $message;
        }
        $sql_machine->next();
    }

    // Categories
    for ($i = 0; $i < $sql_categories->getRows(); ++$i) {
        $message = '<a href="javascript:openPage(\'index.php?page=d2u_machinery/category&func=edit&entry_id='. $sql_categories->getValue('category_id') .'\')">'.
             rex_i18n::msg('d2u_machinery_rights_all') .' - '. rex_i18n::msg('d2u_helper_categories') .': '. $sql_categories->getValue('name') . '</a>';
        if (!in_array($message, $warning, true)) {
            $warning[] = $message;
        }
        $sql_categories->next();
    }

    // Settings
    $addon = rex_addon::get('d2u_machinery');
    if (($addon->hasConfig('consultation_pic') && $addon->getConfig('consultation_pic') === $filename)
        || ($addon->hasConfig('consultation_pics') && str_contains((string) $addon->getConfig('consultation_pics'), $filename))) {
        $message = '<a href="javascript:openPage(\'index.php?page=d2u_machinery/settings\')">'.
             rex_i18n::msg('d2u_machinery_rights_all') .' - '. rex_i18n::msg('d2u_helper_settings') . '</a>';
        if (!in_array($message, $warning, true)) {
            $warning[] = $message;
        }
    }

    return $warning;
}

/**
 * Checks if video is used by this addon.
 * @param rex_extension_point<array<string>> $ep Redaxo extension point
 * @return array<string> Warning message as array
 */
function rex_d2u_machinery_video_is_in_use(rex_extension_point $ep): array
{
    $warning = $ep->getSubject();
    $params = $ep->getParams();
    $video_id = (int) $params['video_id'];
    $video_id_pipe = '%|'. $video_id .'|%';

    $sql_machine = \rex_sql::factory();
    $sql_machine->setQuery('SELECT lang.machine_id, name FROM `' . \rex::getTablePrefix() . 'd2u_machinery_machines_lang` AS lang '
        .'LEFT JOIN `' . \rex::getTablePrefix() . 'd2u_machinery_machines` AS machines ON lang.machine_id = machines.machine_id '
        .'WHERE machines.video_ids LIKE :video_id_pipe GROUP BY lang.machine_id', [':video_id_pipe' => $video_id_pipe]);
    $warning = rex_d2u_machinery_add_video_usage_warnings($warning, $sql_machine, 'machine_id', 'name', 'index.php?page=d2u_machinery/machine/machine&func=edit&entry_id=', rex_i18n::msg('d2u_machinery_meta_machines'));

    $sql_categories = \rex_sql::factory();
    $sql_categories->setQuery('SELECT lang.category_id, name FROM `' . \rex::getTablePrefix() . 'd2u_machinery_categories_lang` AS lang '
        .'LEFT JOIN `' . \rex::getTablePrefix() . 'd2u_machinery_categories` AS categories ON lang.category_id = categories.category_id '
        .'WHERE categories.video_ids LIKE :video_id_pipe GROUP BY lang.category_id', [':video_id_pipe' => $video_id_pipe]);
    $warning = rex_d2u_machinery_add_video_usage_warnings($warning, $sql_categories, 'category_id', 'name', 'index.php?page=d2u_machinery/category&func=edit&entry_id=', rex_i18n::msg('d2u_helper_categories'));

    if (Extension::isActive('machine_features_extension')) {
        $sql_features = \rex_sql::factory();
        $sql_features->setQuery('SELECT lang.feature_id, name FROM `' . \rex::getTablePrefix() . 'd2u_machinery_features_lang` AS lang '
            .'LEFT JOIN `' . \rex::getTablePrefix() . 'd2u_machinery_features` AS features ON lang.feature_id = features.feature_id '
            .'WHERE features.video_id = :video_id GROUP BY lang.feature_id', [':video_id' => $video_id]);
        $warning = rex_d2u_machinery_add_video_usage_warnings($warning, $sql_features, 'feature_id', 'name', 'index.php?page=d2u_machinery/machine/features&func=edit&entry_id=', rex_i18n::msg('d2u_machinery_features'));
    }

    if (Extension::isActive('machine_options_extension')) {
        $sql_options = \rex_sql::factory();
        $sql_options->setQuery('SELECT lang.option_id, name FROM `' . \rex::getTablePrefix() . 'd2u_machinery_options_lang` AS lang '
            .'LEFT JOIN `' . \rex::getTablePrefix() . 'd2u_machinery_options` AS options ON lang.option_id = options.option_id '
            .'WHERE options.video_id = :video_id GROUP BY lang.option_id', [':video_id' => $video_id]);
        $warning = rex_d2u_machinery_add_video_usage_warnings($warning, $sql_options, 'option_id', 'name', 'index.php?page=d2u_machinery/machine/options&func=edit&entry_id=', rex_i18n::msg('d2u_machinery_options'));
    }

    if (Extension::isActive('machine_steel_automation_extension')) {
        $sql_supplies = \rex_sql::factory();
        $sql_supplies->setQuery('SELECT lang.supply_id, name FROM `' . \rex::getTablePrefix() . 'd2u_machinery_steel_supply_lang` AS lang '
            .'LEFT JOIN `' . \rex::getTablePrefix() . 'd2u_machinery_steel_supply` AS supplies ON lang.supply_id = supplies.supply_id '
            .'WHERE supplies.video_id = :video_id GROUP BY lang.supply_id', [':video_id' => $video_id]);
        $warning = rex_d2u_machinery_add_video_usage_warnings($warning, $sql_supplies, 'supply_id', 'name', 'index.php?page=d2u_machinery/machine/supply&func=edit&entry_id=', rex_i18n::msg('d2u_machinery_steel_supply'));
    }

    if (Extension::isActive('production_lines')) {
        $sql_production_lines = \rex_sql::factory();
        $sql_production_lines->setQuery('SELECT lang.production_line_id, name FROM `' . \rex::getTablePrefix() . 'd2u_machinery_production_lines_lang` AS lang '
            .'LEFT JOIN `' . \rex::getTablePrefix() . 'd2u_machinery_production_lines` AS production_lines ON lang.production_line_id = production_lines.production_line_id '
            .'WHERE production_lines.video_ids LIKE :video_id_pipe GROUP BY lang.production_line_id', [':video_id_pipe' => $video_id_pipe]);
        $warning = rex_d2u_machinery_add_video_usage_warnings($warning, $sql_production_lines, 'production_line_id', 'name', 'index.php?page=d2u_machinery/production_lines/lines&func=edit&entry_id=', rex_i18n::msg('d2u_machinery_production_lines'));
    }

    if (Extension::isActive('used_machines')) {
        $sql_used_machines = \rex_sql::factory();
        $sql_used_machines->setQuery('SELECT used_machine_id, manufacturer, name FROM `' . \rex::getTablePrefix() . 'd2u_machinery_used_machines` '
            .'WHERE video_ids LIKE :video_id_pipe', [':video_id_pipe' => $video_id_pipe]);
        for ($i = 0; $i < $sql_used_machines->getRows(); ++$i) {
            $message = '<a href="javascript:openPage(\'index.php?page=d2u_machinery/used_machines/used_machines&func=edit&entry_id='.
                $sql_used_machines->getValue('used_machine_id') .'\')">'. rex_i18n::msg('d2u_machinery_rights_all') .' - '. rex_i18n::msg('d2u_machinery_used_machines') .': '. $sql_used_machines->getValue('manufacturer') .' '. $sql_used_machines->getValue('name') .'</a>';
            if (!in_array($message, $warning, true)) {
                $warning[] = $message;
            }
            $sql_used_machines->next();
        }
    }

    return $warning;
}

/**
 * Adds video usage warnings from SQL result.
 * @param array<string> $warning Warning messages
 * @return array<string> Warning messages
 */
function rex_d2u_machinery_add_video_usage_warnings(array $warning, rex_sql $sql, string $id_field, string $name_field, string $page_url, string $label): array
{
    $page_url = preg_replace('/^index\.php\?page=/', '', $page_url) ?? $page_url;

    for ($i = 0; $i < $sql->getRows(); ++$i) {
        $message = '<a href="?page='. rex_escape($page_url) . $sql->getValue($id_field) .'">'. rex_i18n::msg('d2u_machinery_rights_all') .' - '. $label .': '. $sql->getValue($name_field) .'</a>';
        if (!in_array($message, $warning, true)) {
            $warning[] = $message;
        }
        $sql->next();
    }

    return $warning;
}

/**
 * Checks if media is used by contacts.
 * @param rex_extension_point<array<string>> $ep Redaxo extension point
 * @return array<string> Warning message as array
 */
function rex_d2u_machinery_contacts_media_is_in_use(rex_extension_point $ep)
{
    $warning = $ep->getSubject();
    $params = $ep->getParams();
    $filename = (string) $params['filename'];

    $sql_contacts = rex_sql::factory();
    $sql_contacts->setQuery('SELECT contact_id, name FROM `' . rex::getTablePrefix() . 'd2u_machinery_contacts` '
        .'WHERE picture = :filename', [':filename' => $filename]);

    for ($i = 0; $i < $sql_contacts->getRows(); ++$i) {
        $message = '<a href="javascript:openPage(\'index.php?page=d2u_machinery/contacts&func=edit&entry_id='.
            $sql_contacts->getValue('contact_id') .'\')">'.
             rex_i18n::msg('d2u_machinery_meta_title') .' - '. rex_i18n::msg('d2u_machinery_contacts') .': '. $sql_contacts->getValue('name') . '</a>';
        if (!in_array($message, $warning, true)) {
            $warning[] = $message;
        }
        $sql_contacts->next();
    }

    return $warning;
}

/**
 * Deletes language specific equipment objects.
 * @param rex_extension_point<array<string>> $ep Redaxo extension point
 * @return array<string> Warning message as array
 */
function rex_d2u_machinery_equipment_clang_deleted(rex_extension_point $ep)
{
    $warning = $ep->getSubject();
    $params = $ep->getParams();
    $clang_id = $params['id'];

    $equipments = Equipment::getAll($clang_id);
    foreach ($equipments as $equipment) {
        $equipment->delete(false);
    }
    $equipment_groups = EquipmentGroup::getAll($clang_id);
    foreach ($equipment_groups as $equipment_group) {
        $equipment_group->delete(false);
    }

    return $warning;
}

/**
 * Checks if media is used by equipment groups.
 * @param rex_extension_point<array<string>> $ep Redaxo extension point
 * @return array<string> Warning message as array
 */
function rex_d2u_machinery_equipment_media_is_in_use(rex_extension_point $ep)
{
    $warning = $ep->getSubject();
    $params = $ep->getParams();
    $filename = (string) $params['filename'];

    $sql = \rex_sql::factory();
    $sql->setQuery('SELECT lang.group_id, name FROM `' . \rex::getTablePrefix() . 'd2u_machinery_equipment_groups_lang` AS lang '
        .'LEFT JOIN `' . \rex::getTablePrefix() . 'd2u_machinery_equipment_groups` AS equipment_groups ON lang.group_id = equipment_groups.group_id '
        .'WHERE picture = :filename AND clang_id = '. (int) rex_config::get('d2u_helper', 'default_lang'), [':filename' => $filename]);

    for ($i = 0; $i < $sql->getRows(); ++$i) {
        $message = '<a href="javascript:openPage(\'index.php?page=d2u_machinery/machine/equipment&equipment_subpage=equipment_group&func=edit&entry_id='.
            $sql->getValue('group_id') .'\')">'. rex_i18n::msg('d2u_machinery_rights_all') .' - '. rex_i18n::msg('d2u_machinery_equipment_groups') .': '. $sql->getValue('name') .'</a>';
        if (!in_array($message, $warning, true)) {
            $warning[] = $message;
        }
        $sql->next();
    }

    return $warning;
}

/**
 * Corrects export providers after language deletion.
 * @param rex_extension_point<array<string>> $ep Redaxo extension point
 * @return array<string> Warning message as array
 */
function rex_d2u_machinery_export_clang_deleted(rex_extension_point $ep)
{
    $warning = $ep->getSubject();
    $params = $ep->getParams();
    $clang_id = $params['id'];

    $providers = Provider::getAll();
    foreach ($providers as $provider) {
        if ($provider->clang_id === $clang_id) {
            $provider->clang_id = rex_clang::getStartId();
            $provider->save();
        }
    }

    return $warning;
}

/**
 * Deletes language specific industry sectors.
 * @param rex_extension_point<array<string>> $ep Redaxo extension point
 * @return array<string> Warning message as array
 */
function rex_d2u_machinery_industry_sectors_clang_deleted(rex_extension_point $ep)
{
    $warning = $ep->getSubject();
    $params = $ep->getParams();
    $clang_id = $params['id'];

    $industry_sectors = IndustrySector::getAll($clang_id, false);
    foreach ($industry_sectors as $industry_sector) {
        $industry_sector->delete(false);
    }

    return $warning;
}

/**
 * Checks if media is used by industry sectors.
 * @param rex_extension_point<array<string>> $ep Redaxo extension point
 * @return array<string> Warning message as array
 */
function rex_d2u_machinery_industry_sectors_media_is_in_use(rex_extension_point $ep)
{
    $warning = $ep->getSubject();
    $params = $ep->getParams();
    $filename = (string) $params['filename'];

    $sql = \rex_sql::factory();
    $sql->setQuery('SELECT lang.industry_sector_id, name FROM `' . \rex::getTablePrefix() . 'd2u_machinery_industry_sectors_lang` AS lang '
        .'LEFT JOIN `' . \rex::getTablePrefix() . 'd2u_machinery_industry_sectors` AS sectors ON lang.industry_sector_id = sectors.industry_sector_id '
        .'WHERE (pic = :filename OR icon = :filename) AND clang_id = '. (int) rex_config::get('d2u_helper', 'default_lang'), [':filename' => $filename]);

    for ($i = 0; $i < $sql->getRows(); ++$i) {
        $message = '<a href="javascript:openPage(\'index.php?page=d2u_machinery/industry_sectors&func=edit&entry_id='.
            $sql->getValue('industry_sector_id') .'\')">'. rex_i18n::msg('d2u_machinery_rights_all') .' - '. rex_i18n::msg('d2u_machinery_industry_sectors') .': '. $sql->getValue('name') .'</a>';
        if (!in_array($message, $warning, true)) {
            $warning[] = $message;
        }
        $sql->next();
    }

    return $warning;
}

/**
 * Deletes language specific agitator objects.
 * @param rex_extension_point<array<string>> $ep Redaxo extension point
 * @return array<string> Warning message as array
 */
function rex_d2u_machinery_agitators_clang_deleted(rex_extension_point $ep)
{
    $warning = $ep->getSubject();
    $params = $ep->getParams();
    $clang_id = $params['id'];

    $agitators = Agitator::getAll($clang_id);
    foreach ($agitators as $agitator) {
        $agitator->delete(false);
    }
    $agitator_types = AgitatorType::getAll($clang_id);
    foreach ($agitator_types as $agitator_type) {
        $agitator_type->delete(false);
    }

    return $warning;
}

/**
 * Checks if media is used by agitators.
 * @param rex_extension_point<array<string>> $ep Redaxo extension point
 * @return array<string> Warning message as array
 */
function rex_d2u_machinery_agitators_media_is_in_use(rex_extension_point $ep)
{
    $warning = $ep->getSubject();
    $params = $ep->getParams();
    $filename = (string) $params['filename'];

    $sql_agitator_types = \rex_sql::factory();
    $sql_agitator_types->setQuery('SELECT lang.agitator_type_id, name FROM `' . \rex::getTablePrefix() . 'd2u_machinery_agitator_types_lang` AS lang '
        .'LEFT JOIN `' . \rex::getTablePrefix() . 'd2u_machinery_agitator_types` AS types ON lang.agitator_type_id = types.agitator_type_id '
        .'WHERE pic = :filename AND clang_id = '. (int) rex_config::get('d2u_helper', 'default_lang'), [':filename' => $filename]);

    $sql_agitators = \rex_sql::factory();
    $sql_agitators->setQuery('SELECT lang.agitator_id, name FROM `' . \rex::getTablePrefix() . 'd2u_machinery_agitators_lang` AS lang '
        .'LEFT JOIN `' . \rex::getTablePrefix() . 'd2u_machinery_agitators` AS types ON lang.agitator_id = types.agitator_id '
        .'WHERE pic = :filename AND clang_id = '. (int) rex_config::get('d2u_helper', 'default_lang'), [':filename' => $filename]);

    for ($i = 0; $i < $sql_agitator_types->getRows(); ++$i) {
        $message = '<a href="javascript:openPage(\'index.php?page=d2u_machinery/machine/agitators&agitator_subpage=agitator_type&func=edit&entry_id='.
            $sql_agitator_types->getValue('agitator_type_id') .'\')">'. rex_i18n::msg('d2u_machinery_rights_all') .' - '. rex_i18n::msg('d2u_machinery_agitator_types') .': '. $sql_agitator_types->getValue('name') .'</a>';
        if (!in_array($message, $warning, true)) {
            $warning[] = $message;
        }
        $sql_agitator_types->next();
    }
    for ($i = 0; $i < $sql_agitators->getRows(); ++$i) {
        $message = '<a href="javascript:openPage(\'index.php?page=d2u_machinery/machine/agitators&agitator_subpage=agitator&func=edit&entry_id='.
            $sql_agitators->getValue('agitator_id') .'\')">'. rex_i18n::msg('d2u_machinery_rights_all') .' - '. rex_i18n::msg('d2u_machinery_agitators') .': '. $sql_agitators->getValue('name') .'</a>';
        if (!in_array($message, $warning, true)) {
            $warning[] = $message;
        }
        $sql_agitators->next();
    }

    return $warning;
}

/**
 * Deletes language specific certificates.
 * @param rex_extension_point<array<string>> $ep Redaxo extension point
 * @return array<string> Warning message as array
 */
function rex_d2u_machinery_certificates_clang_deleted(rex_extension_point $ep)
{
    $warning = $ep->getSubject();
    $params = $ep->getParams();
    $clang_id = $params['id'];

    $certificates = Certificate::getAll($clang_id);
    foreach ($certificates as $certificate) {
        $certificate->delete(false);
    }

    return $warning;
}

/**
 * Checks if media is used by certificates.
 * @param rex_extension_point<array<string>> $ep Redaxo extension point
 * @return array<string> Warning message as array
 */
function rex_d2u_machinery_certificates_media_is_in_use(rex_extension_point $ep)
{
    $warning = $ep->getSubject();
    $params = $ep->getParams();
    $filename = (string) $params['filename'];

    $sql = \rex_sql::factory();
    $sql->setQuery('SELECT lang.certificate_id, name FROM `' . \rex::getTablePrefix() . 'd2u_machinery_certificates_lang` AS lang '
        .'LEFT JOIN `' . \rex::getTablePrefix() . 'd2u_machinery_certificates` AS certificates ON lang.certificate_id = certificates.certificate_id '
        .'WHERE pic = :filename AND clang_id = '. (int) rex_config::get('d2u_helper', 'default_lang'), [':filename' => $filename]);

    for ($i = 0; $i < $sql->getRows(); ++$i) {
        $message = '<a href="javascript:openPage(\'index.php?page=d2u_machinery/machine/certificates&func=edit&entry_id='.
            $sql->getValue('certificate_id') .'\')">'. rex_i18n::msg('d2u_machinery_rights_all') .' - '. rex_i18n::msg('d2u_machinery_certificates') .': '. $sql->getValue('name') .'</a>';
        if (!in_array($message, $warning, true)) {
            $warning[] = $message;
        }
        $sql->next();
    }

    return $warning;
}

/**
 * Deletes language specific features.
 * @param rex_extension_point<array<string>> $ep Redaxo extension point
 * @return array<string> Warning message as array
 */
function rex_d2u_machinery_features_clang_deleted(rex_extension_point $ep)
{
    $warning = $ep->getSubject();
    $params = $ep->getParams();
    $clang_id = $params['id'];

    $features = Feature::getAll($clang_id);
    foreach ($features as $feature) {
        $feature->delete(false);
    }

    return $warning;
}

/**
 * Checks if media is used by features.
 * @param rex_extension_point<array<string>> $ep Redaxo extension point
 * @return array<string> Warning message as array
 */
function rex_d2u_machinery_features_media_is_in_use(rex_extension_point $ep)
{
    $warning = $ep->getSubject();
    $params = $ep->getParams();
    $filename = (string) $params['filename'];

    $sql = \rex_sql::factory();
    $sql->setQuery('SELECT lang.feature_id, name FROM `' . \rex::getTablePrefix() . 'd2u_machinery_features_lang` AS lang '
        .'LEFT JOIN `' . \rex::getTablePrefix() . 'd2u_machinery_features` AS features ON lang.feature_id = features.feature_id '
        .'WHERE pic = :filename AND clang_id = '. (int) rex_config::get('d2u_helper', 'default_lang'), [':filename' => $filename]);

    for ($i = 0; $i < $sql->getRows(); ++$i) {
        $message = '<a href="javascript:openPage(\'index.php?page=d2u_machinery/machine/features&func=edit&entry_id='.
            $sql->getValue('feature_id') .'\')">'. rex_i18n::msg('d2u_machinery_rights_all') .' - '. rex_i18n::msg('d2u_machinery_features') .': '. $sql->getValue('name') .'</a>';
        if (!in_array($message, $warning, true)) {
            $warning[] = $message;
        }
        $sql->next();
    }

    return $warning;
}

/**
 * Deletes language specific options.
 * @param rex_extension_point<array<string>> $ep Redaxo extension point
 * @return array<string> Warning message as array
 */
function rex_d2u_machinery_options_clang_deleted(rex_extension_point $ep)
{
    $warning = $ep->getSubject();
    $params = $ep->getParams();
    $clang_id = $params['id'];

    $options = Option::getAll($clang_id);
    foreach ($options as $option) {
        $option->delete(false);
    }

    return $warning;
}

/**
 * Checks if media is used by options.
 * @param rex_extension_point<array<string>> $ep Redaxo extension point
 * @return array<string> Warning message as array
 */
function rex_d2u_machinery_options_media_is_in_use(rex_extension_point $ep)
{
    $warning = $ep->getSubject();
    $params = $ep->getParams();
    $filename = (string) $params['filename'];

    $sql = \rex_sql::factory();
    $sql->setQuery('SELECT lang.option_id, name FROM `' . \rex::getTablePrefix() . 'd2u_machinery_options_lang` AS lang '
        .'LEFT JOIN `' . \rex::getTablePrefix() . 'd2u_machinery_options` AS options ON lang.option_id = options.option_id '
        .'WHERE pic = :filename AND clang_id = '. (int) rex_config::get('d2u_helper', 'default_lang'), [':filename' => $filename]);

    for ($i = 0; $i < $sql->getRows(); ++$i) {
        $message = '<a href="javascript:openPage(\'index.php?page=d2u_machinery/machine/options&func=edit&entry_id='.
            $sql->getValue('option_id') .'\')">'. rex_i18n::msg('d2u_machinery_rights_all') .' - '. rex_i18n::msg('d2u_machinery_options') .': '. $sql->getValue('name') .'</a>';
        if (!in_array($message, $warning, true)) {
            $warning[] = $message;
        }
        $sql->next();
    }

    return $warning;
}

/**
 * Deletes language specific supply objects.
 * @param rex_extension_point<array<string>> $ep Redaxo extension point
 * @return array<string> Warning message as array
 */
function rex_d2u_machinery_supply_clang_deleted(rex_extension_point $ep)
{
    $warning = $ep->getSubject();
    $params = $ep->getParams();
    $clang_id = $params['id'];

    $supplies = Supply::getAll($clang_id);
    foreach ($supplies as $supply) {
        $supply->delete(false);
    }

    return $warning;
}

/**
 * Checks if media is used by supply objects.
 * @param rex_extension_point<array<string>> $ep Redaxo extension point
 * @return array<string> Warning message as array
 */
function rex_d2u_machinery_supply_media_is_in_use(rex_extension_point $ep)
{
    $warning = $ep->getSubject();
    $params = $ep->getParams();
    $filename = (string) $params['filename'];

    $sql_machine = \rex_sql::factory();
    $sql_machine->setQuery('SELECT lang.supply_id, name FROM `' . \rex::getTablePrefix() . 'd2u_machinery_steel_supply_lang` AS lang '
        .'LEFT JOIN `' . \rex::getTablePrefix() . 'd2u_machinery_steel_supply` AS supplies ON lang.supply_id = supplies.supply_id '
        .'WHERE pic = :filename '
        .'GROUP BY supply_id', [':filename' => $filename]);

    for ($i = 0; $i < $sql_machine->getRows(); ++$i) {
        $message = '<a href="javascript:openPage(\'index.php?page=d2u_machinery/machine/supply&func=edit&entry_id='.
            $sql_machine->getValue('supply_id') .'\')">'.rex_i18n::msg('d2u_machinery_meta_title') .' '. rex_i18n::msg('d2u_machinery_supply_extension') .' - '. rex_i18n::msg('d2u_machinery_steel_supply') .': '. $sql_machine->getValue('name') .'</a>';
        if (!in_array($message, $warning, true)) {
            $warning[] = $message;
        }
        $sql_machine->next();
    }

    return $warning;
}

/**
 * Deletes language specific usage areas.
 * @param rex_extension_point<array<string>> $ep Redaxo extension point
 * @return array<string> Warning message as array
 */
function rex_d2u_machinery_usage_area_clang_deleted(rex_extension_point $ep)
{
    $params = $ep->getParams();
    $clang_id = $params['id'];
    $warning = [];

    $usage_areas = UsageArea::getAll($clang_id, 0);
    foreach ($usage_areas as $usage_area) {
        $usage_area->delete(false);
    }

    return $warning;
}

/**
 * Deletes language specific production lines.
 * @param rex_extension_point<array<string>> $ep Redaxo extension point
 * @return array<string> Warning message as array
 */
function rex_d2u_machinery_production_lines_clang_deleted(rex_extension_point $ep)
{
    $warning = $ep->getSubject();
    $params = $ep->getParams();
    $clang_id = $params['id'];

    $production_lines = ProductionLine::getAll($clang_id, false);
    foreach ($production_lines as $production_line) {
        $production_line->delete(false);
    }

    return $warning;
}

/**
 * Checks if media is used by production lines.
 * @param rex_extension_point<array<string>> $ep Redaxo extension point
 * @return array<string> Warning message as array
 */
function rex_d2u_machinery_production_lines_media_is_in_use(rex_extension_point $ep)
{
    $warning = $ep->getSubject();
    $params = $ep->getParams();
    $filename = (string) $params['filename'];
    $filenameLike = '%' . str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $filename) . '%';

    $sql = \rex_sql::factory();
    $sql->setQuery('SELECT lang.production_line_id, name FROM `' . \rex::getTablePrefix() . 'd2u_machinery_production_lines_lang` AS lang '
        .'LEFT JOIN `' . \rex::getTablePrefix() . 'd2u_machinery_production_lines` AS `lines` ON lang.production_line_id = `lines`.production_line_id '
        .'WHERE FIND_IN_SET(:filename, pictures) OR FIND_IN_SET(:filename, pictures) OR link_picture = :filename OR description_long LIKE :filenameLike OR description_short LIKE :filenameLike '
        .'GROUP BY production_line_id', [':filename' => $filename, ':filenameLike' => $filenameLike]);

    for ($i = 0; $i < $sql->getRows(); ++$i) {
        $message = '<a href="javascript:openPage(\'index.php?page=d2u_machinery/production_lines&func=edit&entry_id='.
            $sql->getValue('production_line_id') .'\')">'. rex_i18n::msg('d2u_machinery_rights_all') .' - '. rex_i18n::msg('d2u_machinery_production_lines') .': '. $sql->getValue('name') .'</a>';
        if (!in_array($message, $warning, true)) {
            $warning[] = $message;
        }
        $sql->next();
    }

    return $warning;
}

/**
 * Deletes language specific service options.
 * @param rex_extension_point<array<string>> $ep Redaxo extension point
 * @return array<string> Warning message as array
 */
function rex_d2u_machinery_service_options_clang_deleted(rex_extension_point $ep)
{
    $warning = $ep->getSubject();
    $params = $ep->getParams();
    $clang_id = $params['id'];

    $service_options = ServiceOption::getAll($clang_id);
    foreach ($service_options as $service_option) {
        $service_option->delete(false);
    }

    return $warning;
}

/**
 * Checks if media is used by service options.
 * @param rex_extension_point<array<string>> $ep Redaxo extension point
 * @return array<string> Warning message as array
 */
function rex_d2u_machinery_service_options_media_is_in_use(rex_extension_point $ep)
{
    $warning = $ep->getSubject();
    $params = $ep->getParams();
    $filename = (string) $params['filename'];

    $sql = \rex_sql::factory();
    $sql->setQuery('SELECT lang.service_option_id, name FROM `' . \rex::getTablePrefix() . 'd2u_machinery_service_options_lang` AS lang '
        .'LEFT JOIN `' . \rex::getTablePrefix() . 'd2u_machinery_service_options` AS service_option ON lang.service_option_id = service_option.service_option_id '
        .'WHERE picture = :filename AND clang_id = '. (int) rex_config::get('d2u_helper', 'default_lang'), [':filename' => $filename]);

    for ($i = 0; $i < $sql->getRows(); ++$i) {
        $message = '<a href="javascript:openPage(\'index.php?page=d2u_machinery/machine/service_options&func=edit&entry_id='.
            $sql->getValue('service_option_id') .'\')">'. rex_i18n::msg('d2u_machinery_rights_all') .' - '. rex_i18n::msg('d2u_machinery_service_option') .': '. $sql->getValue('name') .'</a>';
        if (!in_array($message, $warning, true)) {
            $warning[] = $message;
        }
        $sql->next();
    }

    return $warning;
}

/**
 * Checks if articles are used by used machine settings.
 * @param rex_extension_point<string> $ep Redaxo extension point
 * @throws rex_api_exception If article is used
 * @return array<string> Warning message as array
 */
function rex_d2u_machinery_used_machines_article_is_in_use(rex_extension_point $ep)
{
    $warning = [];
    $params = $ep->getParams();
    $article_id = $params['id'];

    $addon = rex_addon::get('d2u_machinery');
    if ($addon->hasConfig('used_machine_article_id_rent') && (int) $addon->getConfig('used_machine_article_id_rent') === $article_id ||
            $addon->hasConfig('used_machine_article_id_sale') && (int) $addon->getConfig('used_machine_article_id_sale') === $article_id) {
        $warning[] = '<a href="javascript:openPage(\'index.php?page=d2u_machinery/settings\')">'.
             rex_i18n::msg('d2u_machinery_used_machines') .' - '. rex_i18n::msg('d2u_helper_settings') . '</a>';
    }

    if (count($warning) > 0) {
        throw new rex_api_exception(rex_i18n::msg('d2u_helper_rex_article_cannot_delete') .'<ul><li>'. implode('</li><li>', $warning) .'</li></ul>');
    }

    return [];
}

/**
 * Deletes language specific used machines.
 * @param rex_extension_point<array<string>> $ep Redaxo extension point
 * @return array<string> Warning message as array
 */
function rex_d2u_machinery_used_machines_clang_deleted(rex_extension_point $ep)
{
    $warning = $ep->getSubject();
    $params = $ep->getParams();
    $clang_id = $params['id'];

    $used_machines = UsedMachine::getAll($clang_id);
    foreach ($used_machines as $used_machine) {
        $used_machine->delete(false);
    }

    return $warning;
}

/**
 * Checks if media is used by used machines.
 * @param rex_extension_point<array<string>> $ep Redaxo extension point
 * @return array<string> Warning message as array
 */
function rex_d2u_machinery_used_machines_media_is_in_use(rex_extension_point $ep)
{
    $warning = $ep->getSubject();
    $params = $ep->getParams();
    $filename = (string) $params['filename'];

    $sql = \rex_sql::factory();
    $sql->setQuery('SELECT lang.used_machine_id, manufacturer, name FROM `' . \rex::getTablePrefix() . 'd2u_machinery_used_machines_lang` AS lang '
        .'LEFT JOIN `' . \rex::getTablePrefix() . 'd2u_machinery_used_machines` AS used_machines ON lang.used_machine_id = used_machines.used_machine_id '
        .'WHERE FIND_IN_SET(:filename, pics) AND clang_id = '. (int) \rex_config::get('d2u_helper', 'default_lang', \rex_clang::getStartId()), [':filename' => $filename]);

    for ($i = 0; $i < $sql->getRows(); ++$i) {
        $message = '<a href="javascript:openPage(\'index.php?page=d2u_machinery/used_machines/used_machines&func=edit&entry_id='.
            $sql->getValue('used_machine_id') .'\')">'. rex_i18n::msg('d2u_machinery_used_machines') .' - '. rex_i18n::msg('d2u_machinery_used_machines') .': '. $sql->getValue('manufacturer') .' '. $sql->getValue('name') .'</a>';
        if (!in_array($message, $warning, true)) {
            $warning[] = $message;
        }
        $sql->next();
    }

    return $warning;
}

/**
 * Translate a single d2u_machinery object with AI (D2U_HELPER_TRANSLATE_OBJECT).
 * @param rex_extension_point<array<string,mixed>> $ep Redaxo extension point
 * @return array<string,mixed> Result array with success, name and message
 */
function rex_d2u_machinery_translate_object(rex_extension_point $ep) {
    $params = $ep->getParams();
    if ('d2u_machinery' !== ($params['addon'] ?? '')) {
        return $ep->getSubject();
    }

    $type = (string) ($params['type'] ?? '');
    $id = (int) ($params['id'] ?? 0);
    $source_clang_id = (int) ($params['source_clang_id'] ?? 0);
    $target_clang_id = (int) ($params['target_clang_id'] ?? 0);

    // These constructors only return a row when a translation for that clang
    // exists. For missing translations load the source and retarget it.
    $object = null;
    $name = '';
    switch ($type) {
        case 'category':
            $category = new Category($id, $target_clang_id);
            if ($category->category_id <= 0) {
                $category = new Category($id, $source_clang_id);
                $category->clang_id = $target_clang_id;
            }
            $object = $category->category_id > 0 ? $category : null;
            $name = null !== $object ? $category->name : '';
            break;
        case 'machine':
            $machine = new Machine($id, $target_clang_id);
            if ($machine->machine_id <= 0) {
                $machine = new Machine($id, $source_clang_id);
                $machine->clang_id = $target_clang_id;
            }
            $object = $machine->machine_id > 0 ? $machine : null;
            $name = null !== $object ? $machine->name : '';
            break;
        case 'equipment':
            $o = new Equipment($id, $target_clang_id);
            if ($o->equipment_id <= 0) { $o = new Equipment($id, $source_clang_id); $o->clang_id = $target_clang_id; }
            $object = $o->equipment_id > 0 ? $o : null;
            $name = null !== $object ? $o->name : '';
            break;
        case 'equipment_group':
            $o = new EquipmentGroup($id, $target_clang_id);
            if ($o->group_id <= 0) { $o = new EquipmentGroup($id, $source_clang_id); $o->clang_id = $target_clang_id; }
            $object = $o->group_id > 0 ? $o : null;
            $name = null !== $object ? $o->name : '';
            break;
        case 'industry_sector':
            $o = new IndustrySector($id, $target_clang_id);
            if ($o->industry_sector_id <= 0) { $o = new IndustrySector($id, $source_clang_id); $o->clang_id = $target_clang_id; }
            $object = $o->industry_sector_id > 0 ? $o : null;
            $name = null !== $object ? $o->name : '';
            break;
        case 'certificate':
            $o = new Certificate($id, $target_clang_id);
            if ($o->certificate_id <= 0) { $o = new Certificate($id, $source_clang_id); $o->clang_id = $target_clang_id; }
            $object = $o->certificate_id > 0 ? $o : null;
            $name = null !== $object ? $o->name : '';
            break;
        case 'feature':
            $o = new Feature($id, $target_clang_id);
            if ($o->feature_id <= 0) { $o = new Feature($id, $source_clang_id); $o->clang_id = $target_clang_id; }
            $object = $o->feature_id > 0 ? $o : null;
            $name = null !== $object ? $o->name : '';
            break;
        case 'option':
            $o = new Option($id, $target_clang_id);
            if ($o->option_id <= 0) { $o = new Option($id, $source_clang_id); $o->clang_id = $target_clang_id; }
            $object = $o->option_id > 0 ? $o : null;
            $name = null !== $object ? $o->name : '';
            break;
        case 'supply':
            $o = new Supply($id, $target_clang_id);
            if ($o->supply_id <= 0) { $o = new Supply($id, $source_clang_id); $o->clang_id = $target_clang_id; }
            $object = $o->supply_id > 0 ? $o : null;
            $name = null !== $object ? $o->name : '';
            break;
        case 'usage_area':
            $o = new UsageArea($id, $target_clang_id);
            if ($o->usage_area_id <= 0) { $o = new UsageArea($id, $source_clang_id); $o->clang_id = $target_clang_id; }
            $object = $o->usage_area_id > 0 ? $o : null;
            $name = null !== $object ? $o->name : '';
            break;
        case 'service_option':
            $o = new ServiceOption($id, $target_clang_id);
            if ($o->service_option_id <= 0) { $o = new ServiceOption($id, $source_clang_id); $o->clang_id = $target_clang_id; }
            $object = $o->service_option_id > 0 ? $o : null;
            $name = null !== $object ? $o->name : '';
            break;
        case 'production_line':
            $o = new ProductionLine($id, $target_clang_id);
            if ($o->production_line_id <= 0) { $o = new ProductionLine($id, $source_clang_id); $o->clang_id = $target_clang_id; }
            $object = $o->production_line_id > 0 ? $o : null;
            $name = null !== $object ? $o->name : '';
            break;
        case 'used_machine':
            $o = new UsedMachine($id, $target_clang_id);
            if ($o->used_machine_id <= 0) { $o = new UsedMachine($id, $source_clang_id); $o->clang_id = $target_clang_id; }
            $object = $o->used_machine_id > 0 ? $o : null;
            $name = null !== $object ? $o->name : '';
            break;
        default:
            return $ep->getSubject();
    }

    if (!$object instanceof \TobiasKrais\D2UHelper\ITranslateable) {
        return ['success' => false, 'name' => '', 'message' => rex_i18n::msg('d2u_helper_translations_ai_error')];
    }

    $success = $object->translateFrom($source_clang_id);

    return [
        'success' => $success,
        'name' => $name,
        'message' => $success ? '' : rex_i18n::msg('d2u_helper_translations_ai_error'),
    ];
}

/**
 * Addon translation list.
 * @param rex_extension_point<array<string>> $ep Redaxo extension point
 * @return array<array<string,array<int,array<string,string>>|string>|string> Addon translation list
 */
function rex_d2u_machinery_translation_list(rex_extension_point $ep) {
    $params = $ep->getParams();
    $source_clang_id = (int) $params['source_clang_id'];
    $target_clang_id = (int) $params['target_clang_id'];
    $filter_type = (string) $params['filter_type'];

    $list = $ep->getSubject();
    $list_entry = [
        'addon_name' => rex_i18n::msg('d2u_machinery_meta_title'),
        'pages' => []
    ];

    $categories = Category::getTranslationHelperObjects($target_clang_id, $filter_type);
    if (count($categories) > 0) {
        $html_categories = '<ul>';
        foreach ($categories as $category) {
            if ('' === $category->name) {
                $category = new Category($category->category_id, $source_clang_id);
            }
            $html_categories .= \TobiasKrais\D2UHelper\BackendHelper::getTranslationItem('d2u_machinery', 'category', $category->category_id, $category->name, rex_url::backendPage('d2u_machinery/category', ['entry_id' => $category->category_id, 'func' => 'edit']));
        }
        $html_categories .= '</ul>';
        
        $list_entry['pages'][] = [
            'title' => rex_i18n::msg('d2u_helper_categories'),
            'icon' => 'rex-icon-open-category',
            'html' => $html_categories
        ];
    }

    $machines = Machine::getTranslationHelperObjects($target_clang_id, $filter_type);
    if (count($machines) > 0) {
        $html_machines = '<ul>';
        foreach ($machines as $machine) {
            if ('' === $machine->name) {
                $machine = new Machine($machine->machine_id, $source_clang_id);
            }
            $html_machines .= \TobiasKrais\D2UHelper\BackendHelper::getTranslationItem('d2u_machinery', 'machine', $machine->machine_id, $machine->name, rex_url::backendPage('d2u_machinery/machine', ['entry_id' => $machine->machine_id, 'func' => 'edit']));
        }
        $html_machines .= '</ul>';

        $list_entry['pages'][] = [
            'title' => rex_i18n::msg('d2u_machinery_meta_machines'),
            'icon' => 'rex-icon-module',
            'html' => $html_machines
        ];
    }

    if (Extension::isActive('equipment')) {
        $equipments = Equipment::getTranslationHelperObjects($target_clang_id, $filter_type);
        if (count($equipments) > 0) {
            $html_equipments = '<ul>';
            foreach ($equipments as $equipment) {
                if ('' === $equipment->name) {
                    $equipment = new Equipment($equipment->equipment_id, $source_clang_id);
                }
                $html_equipments .= \TobiasKrais\D2UHelper\BackendHelper::getTranslationItem('d2u_machinery', 'equipment', $equipment->equipment_id, $equipment->name, rex_url::backendPage('d2u_machinery/machine/equipment', ['equipment_subpage' => 'equipment', 'entry_id' => $equipment->equipment_id, 'func' => 'edit']));
            }
            $html_equipments .= '</ul>';

            $list_entry['pages'][] = [
                'title' => rex_i18n::msg('d2u_machinery_equipments'),
                'icon' => 'rex_icon fa-plug',
                'html' => $html_equipments
            ];
        }

        $equipment_groups = EquipmentGroup::getTranslationHelperObjects($target_clang_id, $filter_type);
        if (count($equipment_groups) > 0) {
            $html_equipment_groups = '<ul>';
            foreach ($equipment_groups as $equipment_group) {
                if ('' === $equipment_group->name) {
                    $equipment_group = new EquipmentGroup($equipment_group->group_id, $source_clang_id);
                }
                $html_equipment_groups .= \TobiasKrais\D2UHelper\BackendHelper::getTranslationItem('d2u_machinery', 'equipment_group', $equipment_group->group_id, $equipment_group->name, rex_url::backendPage('d2u_machinery/machine/equipment', ['equipment_subpage' => 'equipment_group', 'entry_id' => $equipment_group->group_id, 'func' => 'edit']));
            }
            $html_equipment_groups .= '</ul>';

            $list_entry['pages'][] = [
                'title' => rex_i18n::msg('d2u_machinery_equipment_groups'),
                'icon' => 'rex_icon fa-plug',
                'html' => $html_equipment_groups
            ];
        }
    }

    if (Extension::isActive('industry_sectors')) {
        $industry_sectors = IndustrySector::getTranslationHelperObjects($target_clang_id, $filter_type);
        if (count($industry_sectors) > 0) {
            $html_industry_sectors = '<ul>';
            foreach ($industry_sectors as $industry_sector) {
                if ('' === $industry_sector->name) {
                    $industry_sector = new IndustrySector($industry_sector->industry_sector_id, $source_clang_id);
                }
                $html_industry_sectors .= \TobiasKrais\D2UHelper\BackendHelper::getTranslationItem('d2u_machinery', 'industry_sector', $industry_sector->industry_sector_id, $industry_sector->name, rex_url::backendPage('d2u_machinery/industry_sectors', ['entry_id' => $industry_sector->industry_sector_id, 'func' => 'edit']));
            }

            $list_entry['pages'][] = [
                'title' => rex_i18n::msg('d2u_machinery_industry_sectors'),
                'icon' => 'rex-icon fa-industry',
                'html' => $html_industry_sectors
            ];
        }
    }
    if (Extension::isActive('machine_certificates_extension')) {
        $certificates = Certificate::getTranslationHelperObjects($target_clang_id, $filter_type);
        if (count($certificates) > 0) {
            $html_certificates = '<ul>';
            foreach ($certificates as $certificate) {
                if ('' === $certificate->name) {
                    $certificate = new Certificate($certificate->certificate_id, $source_clang_id);
                }
                $html_certificates .= \TobiasKrais\D2UHelper\BackendHelper::getTranslationItem('d2u_machinery', 'certificate', $certificate->certificate_id, $certificate->name, rex_url::backendPage('d2u_machinery/machine_certificates_extension', ['entry_id' => $certificate->certificate_id, 'func' => 'edit']));
            }
            $html_certificates .= '</ul>';

            $list_entry['pages'][] = [
                'title' => rex_i18n::msg('d2u_machinery_certificates'),
                'icon' => 'rex-icon fa-certificate',
                'html' => $html_certificates
            ];
        }
    }

    if (Extension::isActive('machine_features_extension')) {
        $features = Feature::getTranslationHelperObjects($target_clang_id, $filter_type);
        if (count($features) > 0) {
            $html_features = '<ul>';
            foreach ($features as $feature) {
                if ('' === $feature->name) {
                    $feature = new Feature($feature->feature_id, $source_clang_id);
                }
                $html_features .= \TobiasKrais\D2UHelper\BackendHelper::getTranslationItem('d2u_machinery', 'feature', $feature->feature_id, $feature->name, rex_url::backendPage('d2u_machinery/machine_features_extension', ['entry_id' => $feature->feature_id, 'func' => 'edit']));
            }
            $html_features .= '</ul>';

            $list_entry['pages'][] = [
                'title' => rex_i18n::msg('d2u_machinery_features'),
                'icon' => 'rex_icon fa-plug',
                'html' => $html_features
            ];
        }
    }

    if (Extension::isActive('machine_options_extension')) {
        $options = Option::getTranslationHelperObjects($target_clang_id, $filter_type);
        if (count($options) > 0) {
            $html_options = '<ul>';
            foreach ($options as $option) {
                if ('' === $option->name) {
                    $option = new Option($option->option_id, $source_clang_id);
                }
                $html_options .= \TobiasKrais\D2UHelper\BackendHelper::getTranslationItem('d2u_machinery', 'option', $option->option_id, $option->name, rex_url::backendPage('d2u_machinery/machine/options', ['entry_id' => $option->option_id, 'func' => 'edit']));
            }
            $html_options .= '</ul>';

            $list_entry['pages'][] = [
                'title' => rex_i18n::msg('d2u_machinery_options'),
                'icon' => 'rex_icon fa-plug',
                'html' => $html_options
            ];
        }
    }

    if (Extension::isActive('machine_steel_automation_extension')) {
        $supplies = Supply::getTranslationHelperObjects($target_clang_id, $filter_type);
        if (count($supplies) > 0) {
            $html_supplies = '<ul>';
            foreach ($supplies as $supply) {
                if ('' === $supply->name) {
                    $supply = new Supply($supply->supply_id, $source_clang_id);
                }
                $html_supplies .= \TobiasKrais\D2UHelper\BackendHelper::getTranslationItem('d2u_machinery', 'supply', $supply->supply_id, $supply->name, rex_url::backendPage('d2u_machinery/machine/supply', ['entry_id' => $supply->supply_id, 'func' => 'edit']));
            }
            $html_supplies .= '</ul>';
            $list_entry['pages'][] = [
                'title' => rex_i18n::msg('d2u_machinery_supply_extension') .' - '. rex_i18n::msg('d2u_machinery_steel_supply'),
                'icon' => 'rex-icon fa-stack-overflow',
                'html' => $html_supplies
            ];
        }
    }
  
    if (Extension::isActive('machine_usage_area_extension')) {
        $usage_areas = UsageArea::getTranslationHelperObjects($target_clang_id, $filter_type);
        if (count($usage_areas) > 0) {
            $html_usage_areas = '<ul>';
            foreach ($usage_areas as $usage_area) {
                if ('' === $usage_area->name) {
                    $usage_area = new UsageArea($usage_area->usage_area_id, $source_clang_id);
                }
                $html_usage_areas .= \TobiasKrais\D2UHelper\BackendHelper::getTranslationItem('d2u_machinery', 'usage_area', $usage_area->usage_area_id, $usage_area->name, rex_url::backendPage('d2u_machinery/machine/usage_areas', ['entry_id' => $usage_area->usage_area_id, 'func' => 'edit']));
            }
            $html_usage_areas .= '</ul>';
            $list_entry['pages'][] = [
                'title' => rex_i18n::msg('d2u_machinery_usage_areas'),
                'icon' => 'rex-icon fa-codepen',
                'html' => $html_usage_areas
            ];
        }
    }
    if (Extension::isActive('production_lines')) {
        $production_lines = ProductionLine::getTranslationHelperObjects($target_clang_id, $filter_type);
        if (count($production_lines) > 0) {
            $html_production_lines = '<ul>';
            foreach ($production_lines as $production_line) {
                if ('' === $production_line->name) {
                    $production_line = new ProductionLine($production_line->production_line_id, $source_clang_id);
                }
                $html_production_lines .= \TobiasKrais\D2UHelper\BackendHelper::getTranslationItem('d2u_machinery', 'production_line', $production_line->production_line_id, $production_line->name, rex_url::backendPage('d2u_machinery/production_lines', ['entry_id' => $production_line->production_line_id, 'func' => 'edit']));
            }
            $html_production_lines .= '</ul>';
            $list_entry['pages'][] = [
                'title' => rex_i18n::msg('d2u_machinery_production_lines'),
                'icon' => 'rex-icon fa-arrows-h',
                'html' => $html_production_lines
            ];
        }
    }
    if (Extension::isActive('service_options')) {
        $service_options = ServiceOption::getTranslationHelperObjects($target_clang_id, $filter_type);
        if (count($service_options) > 0) {
            $html_service_options = '<ul>';
            foreach ($service_options as $service_option) {
                if ('' === $service_option->name) {
                    $service_option = new ServiceOption($service_option->service_option_id, $source_clang_id);
                }
                $html_service_options .= \TobiasKrais\D2UHelper\BackendHelper::getTranslationItem('d2u_machinery', 'service_option', $service_option->service_option_id, $service_option->name, rex_url::backendPage('d2u_machinery/machine/service_options', ['entry_id' => $service_option->service_option_id, 'func' => 'edit']));
            }
            $html_service_options .= '</ul>';
            $list_entry['pages'][] = [
                'title' => rex_i18n::msg('d2u_machinery_service_options'),
                'icon' => 'rex-icon fa-plug',
                'html' => $html_service_options
            ];
        }
    }
    if (Extension::isActive('used_machines')) {
        $used_machines = UsedMachine::getTranslationHelperObjects($target_clang_id, $filter_type);
        if (count($used_machines) > 0) {
            $html_used_machines = '<ul>';
            foreach ($used_machines as $used_machine) {
                if ('' === $used_machine->name) {
                    $used_machine = new UsedMachine($used_machine->used_machine_id, $source_clang_id);
                }
                $html_used_machines .= \TobiasKrais\D2UHelper\BackendHelper::getTranslationItem('d2u_machinery', 'used_machine', $used_machine->used_machine_id, $used_machine->name, rex_url::backendPage('d2u_machinery/used_machines', ['entry_id' => $used_machine->used_machine_id, 'func' => 'edit']));
            }
            $html_used_machines .= '</ul>';
            $list_entry['pages'][] = [
                'title' => rex_i18n::msg('d2u_machinery_used_machines'),
                'icon' => 'rex-icon fa-truck',
                'html' => $html_used_machines
            ];
        }
    }

    $list[] = $list_entry;

    return $list;
}

/**
 * Shortens URL by removing article or category name.
 * @param rex_extension_point<string> $ep Redaxo extension point
 * @return \Url\Url New URL
 */
function rex_d2u_machinery_url_shortener(rex_extension_point $ep)
{
    $params = $ep->getParams();
    $url = $params['object'];
    $article_id = (int) $params['article_id'];
    $clang_id = (int) $params['clang_id'];

    // Only shorten URLs that are not start article and articles of this addon
    if ($article_id !== rex_yrewrite::getDomainByArticleId($article_id, $clang_id)->getStartId() &&
            ($article_id === rex_config::get('d2u_machinery', 'article_id'))
    ) {
        $domain = rex_yrewrite::getDomainByArticleId($article_id);

        // First: delete forwarder, if exists - there should be no forwarder to an existing URL
        if ('false' === rex_config::get('d2u_machinery', 'short_urls_forward', 'false')) {
            $forwardUrl = trim(str_replace($domain->getName(), '/', $url->__toString()), '/');
            $query = 'DELETE FROM '. \rex::getTablePrefix() .'yrewrite_forward WHERE `url` = :url';
            $result = \rex_sql::factory();
            $result->setQuery($query, [':url' => $forwardUrl]);

            // Don't forget to regenerate YRewrite path file this way
            // rex_yrewrite_forward::init();
            // rex_yrewrite_forward::generatePathFile();
            // This cannot be done here, because method would be called to often
        }

        // Second: make URL shorter
        if ('true' === rex_config::get('d2u_machinery', 'short_urls', 'false')) {
            $article_url = rex_getUrl($article_id, $clang_id);
            $start_article_url = rex_getUrl(rex_yrewrite::getDomainByArticleId($article_id, $clang_id)->getStartId(), $clang_id);
            $article_url_without_lang_slug = '';
            if (strlen($start_article_url) <= 1 && null !== rex_clang::get($clang_id)) {
                $article_url_without_lang_slug = str_replace('/'. strtolower(rex_clang::get($clang_id)->getCode()) .'/', '/', $article_url);
            } else {
                $article_url_without_lang_slug = str_replace($start_article_url, '/', $article_url);
            }

            // In case $url is urlencoded, encode your url, too
            $article_url_without_lang_slug_split = explode('/', $article_url_without_lang_slug);
            for ($i = 0; $i < count($article_url_without_lang_slug_split); ++$i) {
                $article_url_without_lang_slug_split[$i] = urlencode($article_url_without_lang_slug_split[$i]);
            }
            $article_url_without_lang_slug_split_encoded = implode('/', $article_url_without_lang_slug_split);

            // Replace
            $new_url = new \Url\Url(str_replace($article_url_without_lang_slug_split_encoded, '/', $url->__toString()));

            // Add forwarders
            if ('true' === rex_config::get('d2u_machinery', 'short_urls_forward', 'false')) {
                $externUrl = str_replace('///', '', $domain->getUrl() . str_replace($domain->getName(), '', urldecode($new_url->__toString()))); /** @phpstan-ignore-line */
                $forwardUrl = trim(str_replace($domain->getName(), '/', urldecode($url->__toString())), '/');
                $query = 'SELECT id FROM '. \rex::getTablePrefix() .'yrewrite_forward '
                    .'WHERE extern = :extern OR url = :url';
                $result = \rex_sql::factory();
                $result->setQuery($query, [':extern' => $externUrl, ':url' => $forwardUrl]);

                // Add only if not already existing
                if (0 === $result->getRows() && $domain->getId() > 0) {
                    $query_forward = 'INSERT INTO `'. \rex::getTablePrefix() .'yrewrite_forward` (`domain_id`, `status`, `url`, `type`, `article_id`, `clang`, `extern`, `movetype`, `expiry_date`) '
                        .'VALUES (:domain_id, 1, :url, \'extern\', :article_id, :clang, :extern, \'301\', \'0000-00-00\')';
                    $result_forward = \rex_sql::factory();
                    $result_forward->setQuery($query_forward, [
                        ':domain_id' => $domain->getId(),
                        ':url' => $forwardUrl,
                        ':article_id' => $article_id,
                        ':clang' => $clang_id,
                        ':extern' => $externUrl,
                    ]);

                    // Don't forget to regenerate YRewrite path file this way
                    // rex_yrewrite_forward::init();
                    // rex_yrewrite_forward::generatePathFile();
                    // This cannot be done here, because method would be called to often
                }
            }

            return $new_url;
        }
    }

    return $url;
}

/**
 * Adds videos to sitemap.
 * @param rex_extension_point<array<string>> $ep Redaxo extension point
 * @return array<string> updated sitemap entries
 */
function rex_d2u_machinery_video_sitemap(rex_extension_point $ep)
{
    $sitemap_entries = $ep->getSubject();

    foreach (rex_clang::getAllIds(true) as $clang_id) {
        $machines = Machine::getAll($clang_id, true);

        foreach ($machines as $machine) {
            $video_entry = '';
            // Get sitemap entry for videos
            foreach ($machine->videos as $video) {
                $video_entry .= $video->getSitemapEntry();
            }
            // insert into sitemap
            foreach ($sitemap_entries as $sitemap_key => $sitemap_entry) {
                if (str_contains($sitemap_entry, $machine->getUrl() .'</loc>')) {
                    $sitemap_entries[$sitemap_key] = str_replace('</url>', $video_entry .'</url>', $sitemap_entry);
                }
            }
        }

        $categories = Category::getAll($clang_id);

        foreach ($categories as $category) {
            $video_entry = '';
            // Get sitemap entry for videos
            foreach ($category->videos as $video) {
                $video_entry .= $video->getSitemapEntry();
            }
            // insert into sitemap
            foreach ($sitemap_entries as $sitemap_key => $sitemap_entry) {
                if (str_contains($sitemap_entry, $category->getUrl() .'</loc>')) {
                    $sitemap_entries[$sitemap_key] = str_replace('</url>', $video_entry .'</url>', $sitemap_entry);
                }
            }
        }

        if (Extension::isActive('used_machines')) {
            $used_machines = UsedMachine::getAll($clang_id, true);

            foreach ($used_machines as $used_machine) {
                $video_entry = '';
                foreach ($used_machine->videos as $video) {
                    $video_entry .= $video->getSitemapEntry();
                }
                foreach ($sitemap_entries as $sitemap_key => $sitemap_entry) {
                    if (str_contains($sitemap_entry, $used_machine->getUrl() .'</loc>')) {
                        $sitemap_entries[$sitemap_key] = str_replace('</url>', $video_entry .'</url>', $sitemap_entry);
                    }
                }
            }
        }
    }

    return $sitemap_entries;
}
