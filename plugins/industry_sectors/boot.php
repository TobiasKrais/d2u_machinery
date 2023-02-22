<?php

if (\rex::isBackend()) {
    rex_extension::register('CLANG_DELETED', 'rex_d2u_machinery_industry_sectors_clang_deleted');
    rex_extension::register('MEDIA_IS_IN_USE', 'rex_d2u_machinery_industry_sectors_media_is_in_use');
}

/**
 * Deletes language specific configurations and objects.
 * @param rex_extension_point<string> $ep Redaxo extension point
 * @return array<string> Warning message as array
 */
function rex_d2u_machinery_industry_sectors_clang_deleted(rex_extension_point $ep)
{
    /** @var array<string> $warning */
    $warning = $ep->getSubject();
    $params = $ep->getParams();
    $clang_id = $params['id'];

    // Delete
    $industry_sectors = IndustrySector::getAll($clang_id, false);
    foreach ($industry_sectors as $industry_sector) {
        $industry_sector->delete(false);
    }

    // Delete language replacements
    d2u_machinery_industry_sectors_lang_helper::factory()->uninstall($clang_id);

    return $warning;
}

/**
 * Checks if media is used by this plugin.
 * @param rex_extension_point<string> $ep Redaxo extension point
 * @return array<string> Warning message as array
 */
function rex_d2u_machinery_industry_sectors_media_is_in_use(rex_extension_point $ep)
{
    /** @var array<string> $warning */
    $warning = $ep->getSubject();
    $params = $ep->getParams();
    $filename = addslashes($params['filename']);

    // Industry sectors
    $sql = \rex_sql::factory();
    $sql->setQuery('SELECT lang.industry_sector_id, name FROM `' . \rex::getTablePrefix() . 'd2u_machinery_industry_sectors_lang` AS lang '
        .'LEFT JOIN `' . \rex::getTablePrefix() . 'd2u_machinery_industry_sectors` AS sectors ON lang.industry_sector_id = sectors.industry_sector_id '
        .'WHERE (pic = "'. $filename .'" OR icon = "'. $filename .'") AND clang_id = '. (int) rex_config::get('d2u_helper', 'default_lang'));

    // Prepare warnings
    // Industry sectors
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
