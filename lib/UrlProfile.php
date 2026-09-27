<?php

namespace TobiasKrais\D2UMachinery;

use rex;
use rex_addon;
use rex_article;
use rex_clang;
use rex_config;
use rex_sql;
use rex_user;
use Url\Profile;

/**
 * Manages url addon generator profiles for d2u_machinery detail URLs.
 */
class UrlProfile
{
    /** URL profile namespace for industry sector detail pages. */
    public const INDUSTRY_SECTOR_NAMESPACE = 'industry_sector_id';

    /**
     * Deletes a url_generator profile and its generated URLs by namespace.
     */
    public static function deleteByNamespace(string $namespace): void
    {
        if (!rex_addon::get('url')->isAvailable()) {
            return;
        }

        foreach (Profile::getByNamespace($namespace) as $profile) {
            $profile->deleteUrls();
        }

        rex_sql::factory()->setQuery('DELETE FROM '. rex::getTablePrefix() .'url_generator_profile WHERE `namespace` = :namespace', [':namespace' => $namespace]);
    }

    /**
     * (Re)creates the industry sector url profile and its database view, then
     * rebuilds the URL cache. Reads article and language from config.
     * No-op when the url addon is missing.
     */
    public static function createIndustrySector(): void
    {
        if (!rex_addon::get('url')->isAvailable()) {
            return;
        }

        $prefix = rex::getTablePrefix();

        rex_sql::factory()->setQuery('CREATE OR REPLACE VIEW '. $prefix .'d2u_machinery_url_industry_sectors AS
    		SELECT lang.industry_sector_id, lang.clang_id, lang.name, lang.name AS seo_title, lang.teaser AS seo_description, industries.pic AS picture, lang.updatedate
    		FROM '. $prefix .'d2u_machinery_industry_sectors_lang AS lang
    		LEFT JOIN '. $prefix .'d2u_machinery_industry_sectors AS industries ON lang.industry_sector_id = industries.industry_sector_id
    		LEFT JOIN '. $prefix .'clang AS clang ON lang.clang_id = clang.id
    		WHERE clang.`status` = 1');

        $clang_id = 1 === count(rex_clang::getAllIds()) ? rex_clang::getStartId() : 0;
        $article_id = (int) rex_config::get('d2u_machinery', 'industry_sectors_article_id', rex_article::getSiteStartArticleId());
        $login = rex::getUser() instanceof rex_user ? rex::getUser()->getValue('login') : '';

        self::deleteByNamespace(self::INDUSTRY_SECTOR_NAMESPACE);

        rex_sql::factory()->setQuery('INSERT INTO '. rex::getTablePrefix() ."url_generator_profile (`namespace`, `article_id`, `clang_id`, `table_name`, `table_parameters`, `relation_1_table_name`, `relation_1_table_parameters`, `relation_2_table_name`, `relation_2_table_parameters`, `relation_3_table_name`, `relation_3_table_parameters`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
    		('industry_sector_id', "
            . $article_id .', '
            . $clang_id .', '
            . "'1_xxx_". rex::getTablePrefix() ."d2u_machinery_url_industry_sectors', "
            . "'{\"column_id\":\"industry_sector_id\",\"column_clang_id\":\"clang_id\",\"restriction_1_column\":\"\",\"restriction_1_comparison_operator\":\"=\",\"restriction_1_value\":\"\",\"restriction_2_logical_operator\":\"\",\"restriction_2_column\":\"\",\"restriction_2_comparison_operator\":\"=\",\"restriction_2_value\":\"\",\"restriction_3_logical_operator\":\"\",\"restriction_3_column\":\"\",\"restriction_3_comparison_operator\":\"=\",\"restriction_3_value\":\"\",\"column_segment_part_1\":\"name\",\"column_segment_part_2_separator\":\"\\/\",\"column_segment_part_2\":\"\",\"column_segment_part_3_separator\":\"\\/\",\"column_segment_part_3\":\"\",\"relation_1_column\":\"\",\"relation_1_position\":\"BEFORE\",\"relation_2_column\":\"\",\"relation_2_position\":\"BEFORE\",\"relation_3_column\":\"\",\"relation_3_position\":\"BEFORE\",\"append_user_paths\":\"\",\"append_structure_categories\":\"0\",\"column_seo_title\":\"seo_title\",\"column_seo_description\":\"seo_description\",\"column_seo_image\":\"picture\",\"sitemap_add\":\"1\",\"sitemap_frequency\":\"monthly\",\"sitemap_priority\":\"0.5\",\"column_sitemap_lastmod\":\"updatedate\"}', "
            . "'', '[]', '', '[]', '', '[]', CURRENT_TIMESTAMP, '". $login ."', CURRENT_TIMESTAMP, '". $login ."');");

        \TobiasKrais\D2UHelper\BackendHelper::generateUrlCache(self::INDUSTRY_SECTOR_NAMESPACE);
    }

    /**
     * Removes the industry sector url profile and its database view.
     * No-op when the url addon is missing.
     */
    public static function removeIndustrySector(): void
    {
        if (!rex_addon::get('url')->isAvailable()) {
            return;
        }

        self::deleteByNamespace(self::INDUSTRY_SECTOR_NAMESPACE);
        rex_sql::factory()->setQuery('DROP VIEW IF EXISTS '. rex::getTablePrefix() .'d2u_machinery_url_industry_sectors');
    }
}
