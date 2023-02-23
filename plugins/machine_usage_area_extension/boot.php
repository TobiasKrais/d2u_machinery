<?php

if (\rex::isBackend()) {
    rex_extension::register('CLANG_DELETED', 'rex_d2u_machinery_usage_area_clang_deleted');
}

/**
 * Deletes language specific configurations and objects.
 * @param rex_extension_point<array<string>> $ep Redaxo extension point
 * @return array<string> Warning message as array
 */
function rex_d2u_machinery_usage_area_clang_deleted(rex_extension_point $ep)
{
    $warning = $ep->getSubject();
    $params = $ep->getParams();
    $clang_id = $params['id'];

    // Delete
    $usage_areas = UsageArea::getAll($clang_id, 0);
    foreach ($usage_areas as $usage_area) {
        $usage_area->delete(false);
    }

    // Delete language replacements
    d2u_machinery_machine_usage_area_extension_lang_helper::factory()->uninstall($clang_id);

    return $warning;
}
