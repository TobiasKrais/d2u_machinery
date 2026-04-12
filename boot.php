<?php

use TobiasKrais\D2UMachinery\Extension;

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
}

if (\rex::isBackend()) {
    rex_extension::register('ART_PRE_DELETED', rex_d2u_machinery_article_is_in_use(...));
    rex_extension::register('CLANG_DELETED', rex_d2u_machinery_clang_deleted(...));
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
    if (Extension::isActive('machine_steel_processing_extension')) {
        rex_extension::register('CLANG_DELETED', rex_d2u_machinery_steel_processing_clang_deleted(...));
        rex_extension::register('MEDIA_IS_IN_USE', rex_d2u_machinery_steel_processing_media_is_in_use(...));
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
    d2u_machinery_lang_helper::factory()->uninstall($clang_id);

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
    $filename = addslashes($params['filename']);

    // Machines
    $sql_machine = \rex_sql::factory();
    $sql_machine->setQuery('SELECT lang.machine_id, name FROM `' . \rex::getTablePrefix() . 'd2u_machinery_machines_lang` AS lang '
        .'LEFT JOIN `' . \rex::getTablePrefix() . 'd2u_machinery_machines` AS machines ON lang.machine_id = machines.machine_id '
        .'WHERE FIND_IN_SET("'. $filename .'", pdfs) OR FIND_IN_SET("'. $filename .'", pics) OR description LIKE "%'. $filename .'%" OR benefits_short LIKE "%'. $filename .'%" OR benefits_long LIKE "%'. $filename .'%" OR leaflet = "'. $filename .'"'
        . (Extension::isActive('machine_construction_equipment_extension') ? 'OR FIND_IN_SET("'. $filename .'", pictures_delivery_set) ' : '')
        .'GROUP BY machine_id');

    // Categories
    $sql_categories = \rex_sql::factory();
    $sql_categories->setQuery('SELECT lang.category_id, name FROM `' . \rex::getTablePrefix() . 'd2u_machinery_categories_lang` AS lang '
        .'LEFT JOIN `' . \rex::getTablePrefix() . 'd2u_machinery_categories` AS categories ON lang.category_id = categories.category_id '
        .'WHERE pic = "'. $filename .'" OR pic_usage = "'. $filename .'" OR pic_lang = "'. $filename .'" OR FIND_IN_SET("'. $filename .'", pdfs) OR description LIKE "%'. $filename .'%"');

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
 * Checks if media is used by contacts.
 * @param rex_extension_point<array<string>> $ep Redaxo extension point
 * @return array<string> Warning message as array
 */
function rex_d2u_machinery_contacts_media_is_in_use(rex_extension_point $ep)
{
    $warning = $ep->getSubject();
    $params = $ep->getParams();
    $filename = addslashes($params['filename']);

    $sql_contacts = rex_sql::factory();
    $sql_contacts->setQuery('SELECT contact_id, name FROM `' . rex::getTablePrefix() . 'd2u_machinery_contacts` '
        .'WHERE picture = "'. $filename .'"');

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
    $filename = addslashes($params['filename']);

    $sql = \rex_sql::factory();
    $sql->setQuery('SELECT lang.group_id, name FROM `' . \rex::getTablePrefix() . 'd2u_machinery_equipment_groups_lang` AS lang '
        .'LEFT JOIN `' . \rex::getTablePrefix() . 'd2u_machinery_equipment_groups` AS equipment_groups ON lang.group_id = equipment_groups.group_id '
        .'WHERE picture = "'. $filename .'" AND clang_id = '. (int) rex_config::get('d2u_helper', 'default_lang'));

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
    $filename = addslashes($params['filename']);

    $sql = \rex_sql::factory();
    $sql->setQuery('SELECT lang.industry_sector_id, name FROM `' . \rex::getTablePrefix() . 'd2u_machinery_industry_sectors_lang` AS lang '
        .'LEFT JOIN `' . \rex::getTablePrefix() . 'd2u_machinery_industry_sectors` AS sectors ON lang.industry_sector_id = sectors.industry_sector_id '
        .'WHERE (pic = "'. $filename .'" OR icon = "'. $filename .'") AND clang_id = '. (int) rex_config::get('d2u_helper', 'default_lang'));

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
    $filename = addslashes($params['filename']);

    $sql_agitator_types = \rex_sql::factory();
    $sql_agitator_types->setQuery('SELECT lang.agitator_type_id, name FROM `' . \rex::getTablePrefix() . 'd2u_machinery_agitator_types_lang` AS lang '
        .'LEFT JOIN `' . \rex::getTablePrefix() . 'd2u_machinery_agitator_types` AS types ON lang.agitator_type_id = types.agitator_type_id '
        .'WHERE pic = "'. $filename .'" AND clang_id = '. (int) rex_config::get('d2u_helper', 'default_lang'));

    $sql_agitators = \rex_sql::factory();
    $sql_agitators->setQuery('SELECT lang.agitator_id, name FROM `' . \rex::getTablePrefix() . 'd2u_machinery_agitators_lang` AS lang '
        .'LEFT JOIN `' . \rex::getTablePrefix() . 'd2u_machinery_agitators` AS types ON lang.agitator_id = types.agitator_id '
        .'WHERE pic = "'. $filename .'" AND clang_id = '. (int) rex_config::get('d2u_helper', 'default_lang'));

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
    $filename = addslashes($params['filename']);

    $sql = \rex_sql::factory();
    $sql->setQuery('SELECT lang.certificate_id, name FROM `' . \rex::getTablePrefix() . 'd2u_machinery_certificates_lang` AS lang '
        .'LEFT JOIN `' . \rex::getTablePrefix() . 'd2u_machinery_certificates` AS certificates ON lang.certificate_id = certificates.certificate_id '
        .'WHERE pic = "'. $filename .'" AND clang_id = '. (int) rex_config::get('d2u_helper', 'default_lang'));

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
    $filename = addslashes($params['filename']);

    $sql = \rex_sql::factory();
    $sql->setQuery('SELECT lang.feature_id, name FROM `' . \rex::getTablePrefix() . 'd2u_machinery_features_lang` AS lang '
        .'LEFT JOIN `' . \rex::getTablePrefix() . 'd2u_machinery_features` AS features ON lang.feature_id = features.feature_id '
        .'WHERE pic = "'. $filename .'" AND clang_id = '. (int) rex_config::get('d2u_helper', 'default_lang'));

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
    $filename = addslashes($params['filename']);

    $sql = \rex_sql::factory();
    $sql->setQuery('SELECT lang.option_id, name FROM `' . \rex::getTablePrefix() . 'd2u_machinery_options_lang` AS lang '
        .'LEFT JOIN `' . \rex::getTablePrefix() . 'd2u_machinery_options` AS options ON lang.option_id = options.option_id '
        .'WHERE pic = "'. $filename .'" AND clang_id = '. (int) rex_config::get('d2u_helper', 'default_lang'));

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
 * Deletes language specific steel-processing objects.
 * @param rex_extension_point<array<string>> $ep Redaxo extension point
 * @return array<string> Warning message as array
 */
function rex_d2u_machinery_steel_processing_clang_deleted(rex_extension_point $ep)
{
    $warning = $ep->getSubject();
    $params = $ep->getParams();
    $clang_id = $params['id'];

    $automations = Automation::getAll($clang_id);
    foreach ($automations as $automation) {
        $automation->delete(false);
    }
    $materials = Material::getAll($clang_id);
    foreach ($materials as $material) {
        $material->delete(false);
    }
    $procedures = Procedure::getAll($clang_id);
    foreach ($procedures as $procedure) {
        $procedure->delete(false);
    }
    $processes = Process::getAll($clang_id);
    foreach ($processes as $process) {
        $process->delete(false);
    }
    $profiles = Profile::getAll($clang_id);
    foreach ($profiles as $profile) {
        $profile->delete(false);
    }
    $supplies = Supply::getAll($clang_id);
    foreach ($supplies as $supply) {
        $supply->delete(false);
    }
    $tools = Tool::getAll($clang_id);
    foreach ($tools as $tool) {
        $tool->delete(false);
    }
    $weldings = Welding::getAll($clang_id);
    foreach ($weldings as $welding) {
        $welding->delete(false);
    }

    return $warning;
}

/**
 * Checks if media is used by steel processing objects.
 * @param rex_extension_point<array<string>> $ep Redaxo extension point
 * @return array<string> Warning message as array
 */
function rex_d2u_machinery_steel_processing_media_is_in_use(rex_extension_point $ep)
{
    $warning = $ep->getSubject();
    $params = $ep->getParams();
    $filename = addslashes($params['filename']);

    $sql_machine = \rex_sql::factory();
    $sql_machine->setQuery('SELECT lang.supply_id, name FROM `' . \rex::getTablePrefix() . 'd2u_machinery_steel_supply_lang` AS lang '
        .'LEFT JOIN `' . \rex::getTablePrefix() . 'd2u_machinery_steel_supply` AS supplies ON lang.supply_id = supplies.supply_id '
        .'WHERE pic = "'. $filename .'" '
        .'GROUP BY supply_id');

    for ($i = 0; $i < $sql_machine->getRows(); ++$i) {
        $message = '<a href="javascript:openPage(\'index.php?page=d2u_machinery/machine/steel_processing&steel_processing_subpage=supply&func=edit&entry_id='.
            $sql_machine->getValue('supply_id') .'\')">'.rex_i18n::msg('d2u_machinery_meta_title') .' '. rex_i18n::msg('d2u_machinery_machine_steel_extension') .' - '. rex_i18n::msg('d2u_machinery_steel_supply') .': '. $sql_machine->getValue('name') .'</a>';
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
    $filename = addslashes($params['filename']);

    $sql = \rex_sql::factory();
    $sql->setQuery('SELECT lang.production_line_id, name FROM `' . \rex::getTablePrefix() . 'd2u_machinery_production_lines_lang` AS lang '
        .'LEFT JOIN `' . \rex::getTablePrefix() . 'd2u_machinery_production_lines` AS `lines` ON lang.production_line_id = `lines`.production_line_id '
        .'WHERE FIND_IN_SET("'. $filename .'", pictures) OR FIND_IN_SET("'. $filename .'", pictures) OR link_picture = "'. $filename .'" OR description_long LIKE "%'. $filename .'%" OR description_short LIKE "%'. $filename .'%"'
        .'GROUP BY production_line_id');

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
    $filename = addslashes($params['filename']);

    $sql = \rex_sql::factory();
    $sql->setQuery('SELECT lang.service_option_id, name FROM `' . \rex::getTablePrefix() . 'd2u_machinery_service_options_lang` AS lang '
        .'LEFT JOIN `' . \rex::getTablePrefix() . 'd2u_machinery_service_options` AS service_option ON lang.service_option_id = service_option.service_option_id '
        .'WHERE picture = "'. $filename .'" AND clang_id = '. (int) rex_config::get('d2u_helper', 'default_lang'));

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
    $filename = addslashes($params['filename']);

    $sql = \rex_sql::factory();
    $sql->setQuery('SELECT lang.used_machine_id, manufacturer, name FROM `' . \rex::getTablePrefix() . 'd2u_machinery_used_machines_lang` AS lang '
        .'LEFT JOIN `' . \rex::getTablePrefix() . 'd2u_machinery_used_machines` AS used_machines ON lang.used_machine_id = used_machines.used_machine_id '
        .'WHERE FIND_IN_SET("'. $filename .'", pics) AND clang_id = '. \rex_config::get('d2u_helper', 'default_lang', \rex_clang::getStartId()));

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
            $html_categories .= '<li><a href="'. rex_url::backendPage('d2u_machinery/category', ['entry_id' => $category->category_id, 'func' => 'edit']) .'">'. $category->name .'</a></li>';
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
            $html_machines .= '<li><a href"'. rex_url::backendPage('d2u_machinery/machine', ['entry_id' => $machine->machine_id, 'func' => 'edit']) .'">'. $machine->name .'</a></li>';
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
                $html_equipments .= '<li><a href="'. rex_url::backendPage('d2u_machinery/machine/equipment', ['equipment_subpage' => 'equipment', 'entry_id' => $equipment->equipment_id, 'func' => 'edit']) .'">'. $equipment->name .'</a></li>';
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
                $html_equipment_groups .= '<li><a href="'. rex_url::backendPage('d2u_machinery/machine/equipment', ['equipment_subpage' => 'equipment_group', 'entry_id' => $equipment_group->group_id, 'func' => 'edit']) .'">'. $equipment_group->name .'</a></li>';
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
                $html_industry_sectors .= '<li><a href="'. rex_url::backendPage('d2u_machinery/industry_sectors', ['entry_id' => $industry_sector->industry_sector_id, 'func' => 'edit']) .'">'. $industry_sector->name .'</a></li>';
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
                $html_certificates .= '<li><a href="'. rex_url::backendPage('d2u_machinery/machine_certificates_extension', ['entry_id' => $certificate->certificate_id, 'func' => 'edit']) .'">'. $certificate->name .'</a></li>';
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
                $html_features .= '<li><a href="'. rex_url::backendPage('d2u_machinery/machine_features_extension', ['entry_id' => $feature->feature_id, 'func' => 'edit']) .'">'. $feature->name .'</a></li>';
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
                $html_options .= '<li><a href="'. rex_url::backendPage('d2u_machinery/machine/options', ['entry_id' => $option->option_id, 'func' => 'edit']) .'">'. $option->name .'</a></li>';
            }
            $html_options .= '</ul>';

            $list_entry['pages'][] = [
                'title' => rex_i18n::msg('d2u_machinery_options'),
                'icon' => 'rex_icon fa-plug',
                'html' => $html_options
            ];
        }
    }

    if (Extension::isActive('machine_steel_processing_extension')) {
        $automations = Automation::getTranslationHelperObjects($target_clang_id, $filter_type);
        if (count($automations) > 0) {
            $html_automations = '<ul>';
            foreach ($automations as $automation) {
                if ('' === $automation->name) {
                    $automation = new Automation($automation->automation_id, $source_clang_id);
                }
                $html_automations .= '<li><a href="'. rex_url::backendPage('d2u_machinery/machine/automation', ['entry_id' => $automation->automation_id, 'func' => 'edit']) .'">'. $automation->name .'</a></li>';
            }
            $html_automations .= '</ul>';
            $list_entry['pages'][] = [
                'title' => rex_i18n::msg('d2u_machinery_machine_steel_extension') .' - '. rex_i18n::msg('d2u_machinery_steel_automation_degrees'),
                'icon' => 'rex-icon fa-exchange',
                'html' => $html_automations
            ];
        }
        $materials = Material::getTranslationHelperObjects($target_clang_id, $filter_type);
        if (count($materials) > 0) {
            $html_materials = '<ul>';
            foreach ($materials as $material) {
                if ('' === $material->name) {
                    $material = new Material($material->material_id, $source_clang_id);
                }
                $html_materials .= '<li><a href="'. rex_url::backendPage('d2u_machinery/machine/steel_processing', ['steel_processing_subpage' => 'material', 'entry_id' => $material->material_id, 'func' => 'edit']) .'">'. $material->name .'</a></li>';
            }
            $html_materials .= '</ul>';
            $list_entry['pages'][] = [
                'title' => rex_i18n::msg('d2u_machinery_machine_steel_extension') .' - '. rex_i18n::msg('d2u_machinery_steel_material_class'),
                'icon' => 'rex-icon fa-flask',
                'html' => $html_materials
            ];
        }
        $procedures = Procedure::getTranslationHelperObjects($target_clang_id, $filter_type);
        if (count($procedures) > 0) {
            $html_procedures = '<ul>';
            foreach ($procedures as $procedure) {
                if ('' === $procedure->name) {
                    $procedure = new Procedure($procedure->procedure_id, $source_clang_id);
                }
                $html_procedures .= '<li><a href="'. rex_url::backendPage('d2u_machinery/machine/steel_processing', ['steel_processing_subpage' => 'procedure', 'entry_id' => $procedure->procedure_id, 'func' => 'edit']) .'">'. $procedure->name .'</a></li>';
            }
            $html_procedures .= '</ul>';
            $list_entry['pages'][] = [
                'title' => rex_i18n::msg('d2u_machinery_machine_steel_extension') .' - '. rex_i18n::msg('d2u_machinery_steel_procedures'),
                'icon' => 'rex-icon fa-tasks',
                'html' => $html_procedures
            ];
        }
        $processes = Process::getTranslationHelperObjects($target_clang_id, $filter_type);
        if (count($processes) > 0) {
            $html_processes = '<ul>';
            foreach ($processes as $process) {
                if ('' === $process->name) {
                    $process = new Process($process->process_id, $source_clang_id);
                }
                $html_processes .= '<li><a href="'. rex_url::backendPage('d2u_machinery/machine/steel_processing', ['steel_processing_subpage' => 'process', 'entry_id' => $process->process_id, 'func' => 'edit']) .'">'. $process->name .'</a></li>';
            }
            $html_processes .= '</ul>';
            $list_entry['pages'][] = [
                'title' => rex_i18n::msg('d2u_machinery_machine_steel_extension') .' - '. rex_i18n::msg('d2u_machinery_steel_processes'),
                'icon' => 'rex-icon fa-sort-numeric-asc',
                'html' => $html_processes
            ];
        }
        $profiles = Profile::getTranslationHelperObjects($target_clang_id, $filter_type);
        if (count($profiles) > 0) {
            $html_profiles = '<ul>';
            foreach ($profiles as $profile) {
                if ('' === $profile->name) {
                    $profile = new Profile($profile->profile_id, $source_clang_id);
                }
                $html_profiles .= '<li><a href="'. rex_url::backendPage('d2u_machinery/machine/steel_processing', ['steel_processing_subpage' => 'profile', 'entry_id' => $profile->profile_id, 'func' => 'edit']) .'">'. $profile->name .'</a></li>';
            }
            $html_profiles .= '</ul>';
            $list_entry['pages'][] = [
                'title' => rex_i18n::msg('d2u_machinery_machine_steel_extension') .' - '. rex_i18n::msg('d2u_machinery_steel_profiles'),
                'icon' => 'rex-icon fa-i-cursor',
                'html' => $html_profiles
            ];
        }
        $supplies = Supply::getTranslationHelperObjects($target_clang_id, $filter_type);
        if (count($supplies) > 0) {
            $html_supplies = '<ul>';
            foreach ($supplies as $supply) {
                if ('' === $supply->name) {
                    $supply = new Supply($supply->supply_id, $source_clang_id);
                }
                $html_supplies .= '<li><a href="'. rex_url::backendPage('d2u_machinery/machine/steel_processing', ['steel_processing_subpage' => 'supply', 'entry_id' => $supply->supply_id, 'func' => 'edit']) .'">'. $supply->name .'</a></li>';
            }
            $html_supplies .= '</ul>';
            $list_entry['pages'][] = [
                'title' => rex_i18n::msg('d2u_machinery_machine_steel_extension') .' - '. rex_i18n::msg('d2u_machinery_steel_supply'),
                'icon' => 'rex-icon fa-stack-overflow',
                'html' => $html_supplies
            ];
        }
        $tools = Tool::getTranslationHelperObjects($target_clang_id, $filter_type);
        if (count($tools) > 0) {
            $html_tools = '<ul>';
            foreach ($tools as $tool) {
                if ('' === $tool->name) {
                    $tool = new Tool($tool->tool_id, $source_clang_id);
                }
                $html_tools .= '<li><a href="'. rex_url::backendPage('d2u_machinery/machine/steel_processing', ['steel_processing_subpage' => 'tool', 'entry_id' => $tool->tool_id, 'func' => 'edit']) .'">'. $tool->name .'</a></li>';
            }
            $html_tools .= '</ul>';
            $list_entry['pages'][] = [
                'title' => rex_i18n::msg('d2u_machinery_machine_steel_extension') .' - '. rex_i18n::msg('d2u_machinery_steel_tools'),
                'icon' => 'rex-icon fa-magnet',
                'html' => $html_tools
            ];
        }
        $weldings = Welding::getTranslationHelperObjects($target_clang_id, $filter_type);
        if (count($weldings) > 0) {
            $html_weldings = '<ul>';
            foreach ($weldings as $welding) {
                if ('' === $welding->name) {
                    $welding = new Welding($welding->welding_id, $source_clang_id);
                }
                $html_weldings .= '<li><a href="'. rex_url::backendPage('d2u_machinery/machine/steel_processing', ['steel_processing_subpage' => 'welding', 'entry_id' => $welding->welding_id, 'func' => 'edit']) .'">'. $welding->name .'</a></li>';
            }
            $html_weldings .= '</ul>';
            $list_entry['pages'][] = [
                'title' => rex_i18n::msg('d2u_machinery_machine_steel_extension') .' - '. rex_i18n::msg('d2u_machinery_steel_welding'),
                'icon' => 'rex-icon fa-magic',
                'html' => $html_weldings
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
                $html_usage_areas .= '<li><a href="'. rex_url::backendPage('d2u_machinery/machine/usage_areas', ['entry_id' => $usage_area->usage_area_id, 'func' => 'edit']) .'">'. $usage_area->name .'</a></li>';
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
                $html_production_lines .= '<li><a href="'. rex_url::backendPage('d2u_machinery/production_lines', ['entry_id' => $production_line->production_line_id, 'func' => 'edit']) .'">'. $production_line->name .'</a></li>';
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
                $html_service_options .= '<li><a href="'. rex_url::backendPage('d2u_machinery/machine/service_options', ['entry_id' => $service_option->service_option_id, 'func' => 'edit']) .'">'. $service_option->name .'</a></li>';
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
                $html_used_machines .= '<li><a href="'. rex_url::backendPage('d2u_machinery/used_machines', ['entry_id' => $used_machine->used_machine_id, 'func' => 'edit']) .'">'. $used_machine->name .'</a></li>';
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
            $query = 'DELETE FROM '. \rex::getTablePrefix() .'yrewrite_forward '
                ."WHERE `url` = '". trim(str_replace($domain->getName(), '/', $url->__toString()), '/') ."'";
            $result = \rex_sql::factory();
            $result->setQuery($query);

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
                $query = 'SELECT id FROM '. \rex::getTablePrefix() .'yrewrite_forward '
                    ."WHERE extern = '". str_replace('///', '', $domain->getUrl() . str_replace($domain->getName(), '', urldecode($new_url->__toString()))) ."' " /** @phpstan-ignore-line */
                    . "OR url = '". trim(str_replace($domain->getName(), '/', urldecode($url->__toString())), '/') ."'";
                $result = \rex_sql::factory();
                $result->setQuery($query);

                // Add only if not already existing
                if (0 === $result->getRows() && $domain->getId() > 0) {
                    $query_forward = 'INSERT INTO `'. \rex::getTablePrefix() .'yrewrite_forward` (`domain_id`, `status`, `url`, `type`, `article_id`, `clang`, `extern`, `movetype`, `expiry_date`) '
                        .'VALUES ('. $domain->getId() .", 1, '". trim(str_replace($domain->getName(), '/', urldecode($url->__toString())), '/') ."', 'extern', ". $article_id .', '. $clang_id .", '". str_replace('///', '', $domain->getUrl() . str_replace($domain->getName(), '', urldecode($new_url->__toString()))) ."', '301', '0000-00-00');"; /** @phpstan-ignore-line */
                    $result_forward = \rex_sql::factory();
                    $result_forward->setQuery($query_forward);

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
