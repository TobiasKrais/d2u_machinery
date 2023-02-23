<?php

if (\rex::isBackend()) {
    rex_extension::register('CLANG_DELETED', 'rex_d2u_machinery_options_clang_deleted');
    rex_extension::register('MEDIA_IS_IN_USE', 'rex_d2u_machinery_options_media_is_in_use');
}

/**
 * Deletes language specific configurations and objects.
 * @param rex_extension_point<array<string>> $ep Redaxo extension point
 * @return array<string> Warning message as array
 */
function rex_d2u_machinery_options_clang_deleted(rex_extension_point $ep)
{
    $warning = $ep->getSubject();
    $params = $ep->getParams();
    $clang_id = $params['id'];

    // Delete
    $options = Option::getAll($clang_id);
    foreach ($options as $option) {
        $option->delete(false);
    }

    // Delete language replacements
    d2u_machinery_machine_options_extension_lang_helper::factory()->uninstall($clang_id);

    return $warning;
}

/**
 * Checks if media is used by this plugin.
 * @param rex_extension_point<array<string>> $ep Redaxo extension point
 * @return array<string> Warning message as array
 */
function rex_d2u_machinery_options_media_is_in_use(rex_extension_point $ep)
{
    $warning = $ep->getSubject();
    $params = $ep->getParams();
    $filename = addslashes($params['filename']);

    // Options
    $sql = \rex_sql::factory();
    $sql->setQuery('SELECT lang.option_id, name FROM `' . \rex::getTablePrefix() . 'd2u_machinery_options_lang` AS lang '
        .'LEFT JOIN `' . \rex::getTablePrefix() . 'd2u_machinery_options` AS options ON lang.option_id = options.option_id '
        .'WHERE pic = "'. $filename .'" AND clang_id = '. (int) rex_config::get('d2u_helper', 'default_lang'));

    // Prepare warnings
    // Options
    for ($i = 0; $i < $sql->getRows(); ++$i) {
        $message = '<a href="javascript:openPage(\'index.php?page=d2u_machinery/machine_options_extension&func=edit&entry_id='.
            $sql->getValue('option_id') .'\')">'. rex_i18n::msg('d2u_machinery_rights_all') .' - '. rex_i18n::msg('d2u_machinery_options') .': '. $sql->getValue('name') .'</a>';
        if (!in_array($message, $warning, true)) {
            $warning[] = $message;
        }
        $sql->next();
    }

    return $warning;
}
