<?php

if (\rex::isBackend()) {
    rex_extension::register('CLANG_DELETED', 'rex_d2u_machinery_features_clang_deleted');
    rex_extension::register('MEDIA_IS_IN_USE', 'rex_d2u_machinery_features_media_is_in_use');
}

/**
 * Deletes language specific configurations and objects.
 * @param rex_extension_point<array<string>> $ep Redaxo extension point
 * @return array<string> Warning message as array
 */
function rex_d2u_machinery_features_clang_deleted(rex_extension_point $ep)
{
    $warning = $ep->getSubject();
    $params = $ep->getParams();
    $clang_id = $params['id'];

    // Delete
    $features = Feature::getAll($clang_id);
    foreach ($features as $feature) {
        $feature->delete(false);
    }

    // Delete language replacements
    d2u_machinery_machine_features_extension_lang_helper::factory()->uninstall($clang_id);

    return $warning;
}

/**
 * Checks if media is used by this plugin.
 * @param rex_extension_point<array<string>> $ep Redaxo extension point
 * @return array<string> Warning message as array
 */
function rex_d2u_machinery_features_media_is_in_use(rex_extension_point $ep)
{
    $warning = $ep->getSubject();
    $params = $ep->getParams();
    $filename = addslashes($params['filename']);

    // Features
    $sql = \rex_sql::factory();
    $sql->setQuery('SELECT lang.feature_id, name FROM `' . \rex::getTablePrefix() . 'd2u_machinery_features_lang` AS lang '
        .'LEFT JOIN `' . \rex::getTablePrefix() . 'd2u_machinery_features` AS features ON lang.feature_id = features.feature_id '
        .'WHERE pic = "'. $filename .'" AND clang_id = '. (int) rex_config::get('d2u_helper', 'default_lang'));

    // Prepare warnings
    // Features
    for ($i = 0; $i < $sql->getRows(); ++$i) {
        $message = '<a href="javascript:openPage(\'index.php?page=d2u_machinery/machine_features_extension&func=edit&entry_id='.
            $sql->getValue('feature_id') .'\')">'. rex_i18n::msg('d2u_machinery_rights_all') .' - '. rex_i18n::msg('d2u_machinery_features') .': '. $sql->getValue('name') .'</a>';
        if (!in_array($message, $warning, true)) {
            $warning[] = $message;
        }
        $sql->next();
    }

    return $warning;
}
