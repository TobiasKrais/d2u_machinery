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

    /**
     * (Re)creates the used machines (rent) url profiles and their database
     * views (machine detail + category), then rebuilds the URL cache.
     * No-op when the url addon is missing.
     */
    public static function createUsedMachinesRent(): void
    {
        if (!rex_addon::get('url')->isAvailable()) {
            return;
        }

        $prefix = rex::getTablePrefix();

        rex_sql::factory()->setQuery('CREATE OR REPLACE VIEW '. $prefix .'d2u_machinery_url_used_machines_rent AS
    		SELECT lang.used_machine_id, lang.clang_id, CONCAT(machines.manufacturer, " ", machines.name) AS name, CONCAT(machines.manufacturer, " ", machines.name, " - ", categories.name) AS seo_title, lang.teaser AS seo_description, SUBSTRING_INDEX(machines.pics, ",", 1) as picture, machines.category_id, lang.updatedate
    		FROM '. $prefix .'d2u_machinery_used_machines_lang AS lang
    		LEFT JOIN '. $prefix .'d2u_machinery_used_machines AS machines ON lang.used_machine_id = machines.used_machine_id
    		LEFT JOIN '. $prefix .'d2u_machinery_categories_lang AS categories ON machines.category_id = categories.category_id AND lang.clang_id = categories.clang_id
    		LEFT JOIN '. $prefix .'clang AS clang ON lang.clang_id = clang.id
    		WHERE clang.`status` = 1 AND machines.online_status = "online" AND machines.offer_type = "rent"
    		GROUP BY used_machine_id, clang_id, name, seo_title, seo_description, picture, category_id, updatedate;');
        rex_sql::factory()->setQuery('CREATE OR REPLACE VIEW '. $prefix .'d2u_machinery_url_used_machine_categories_rent AS
    		SELECT machines.category_id, categories_lang.clang_id, CONCAT_WS(" - ", parent_categories.name, categories_lang.name) AS name, CONCAT_WS(" - ", categories_lang.name, parent_categories.name) AS seo_title, categories_lang.teaser AS seo_description,IF(categories_lang.pic_lang IS NULL or categories_lang.pic_lang = "", categories.pic, categories_lang.pic_lang) as picture, categories_lang.updatedate
    		FROM '. $prefix .'d2u_machinery_used_machines_lang AS lang
    		LEFT JOIN '. $prefix .'d2u_machinery_used_machines AS machines ON lang.used_machine_id = machines.used_machine_id
    		LEFT JOIN '. $prefix .'d2u_machinery_categories_lang AS categories_lang ON machines.category_id = categories_lang.category_id AND lang.clang_id = categories_lang.clang_id
    		LEFT JOIN '. $prefix .'d2u_machinery_categories AS categories ON categories_lang.category_id = categories.category_id
    		LEFT JOIN '. $prefix .'d2u_machinery_categories_lang AS parent_categories ON categories.parent_category_id = parent_categories.category_id AND lang.clang_id = parent_categories.clang_id
    		LEFT JOIN '. $prefix .'clang AS clang ON lang.clang_id = clang.id
    		WHERE clang.`status` = 1 AND machines.online_status = "online" AND machines.offer_type = "rent"
    		GROUP BY category_id, clang_id, name, seo_title, seo_description, picture, updatedate;');

        $clang_id = 1 === count(rex_clang::getAllIds()) ? rex_clang::getStartId() : 0;
        $article_id_rent = (int) rex_config::get('d2u_machinery', 'used_machine_article_id_rent', rex_article::getSiteStartArticleId());
        $login = rex::getUser() instanceof rex_user ? rex::getUser()->getValue('login') : '';

        self::deleteByNamespace('used_rent_machine_id');
        rex_sql::factory()->setQuery('INSERT INTO '. $prefix ."url_generator_profile (`namespace`, `article_id`, `clang_id`, `table_name`, `table_parameters`, `relation_1_table_name`, `relation_1_table_parameters`, `relation_2_table_name`, `relation_2_table_parameters`, `relation_3_table_name`, `relation_3_table_parameters`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
    		('used_rent_machine_id', "
            . $article_id_rent .', '
            . $clang_id .', '
            . "'1_xxx_". $prefix ."d2u_machinery_url_used_machines_rent', "
            . "'{\"column_id\":\"used_machine_id\",\"column_clang_id\":\"clang_id\",\"restriction_1_column\":\"\",\"restriction_1_comparison_operator\":\"=\",\"restriction_1_value\":\"\",\"restriction_2_logical_operator\":\"\",\"restriction_2_column\":\"\",\"restriction_2_comparison_operator\":\"=\",\"restriction_2_value\":\"\",\"restriction_3_logical_operator\":\"\",\"restriction_3_column\":\"\",\"restriction_3_comparison_operator\":\"=\",\"restriction_3_value\":\"\",\"column_segment_part_1\":\"name\",\"column_segment_part_2_separator\":\"\\-\",\"column_segment_part_2\":\"used_machine_id\",\"column_segment_part_3_separator\":\"\\/\",\"column_segment_part_3\":\"\",\"relation_1_column\":\"category_id\",\"relation_1_position\":\"BEFORE\",\"relation_2_column\":\"\",\"relation_2_position\":\"BEFORE\",\"relation_3_column\":\"\",\"relation_3_position\":\"BEFORE\",\"append_user_paths\":\"\",\"append_structure_categories\":\"0\",\"column_seo_title\":\"seo_title\",\"column_seo_description\":\"seo_description\",\"column_seo_image\":\"picture\",\"sitemap_add\":\"1\",\"sitemap_frequency\":\"weekly\",\"sitemap_priority\":\"1.0\",\"column_sitemap_lastmod\":\"updatedate\"}', "
            . "'relation_1_xxx_1_xxx_". $prefix ."d2u_machinery_categories_lang', "
            . "'{\"column_id\":\"category_id\",\"column_clang_id\":\"clang_id\",\"column_segment_part_1\":\"name\",\"column_segment_part_2_separator\":\"\\/\",\"column_segment_part_2\":\"\",\"column_segment_part_3_separator\":\"\\/\",\"column_segment_part_3\":\"\"}', "
            . "'', '[]', '', '[]', CURRENT_TIMESTAMP, '". $login ."', CURRENT_TIMESTAMP, '". $login ."');");
        self::deleteByNamespace('used_rent_category_id');
        rex_sql::factory()->setQuery('INSERT INTO '. $prefix ."url_generator_profile (`namespace`, `article_id`, `clang_id`, `table_name`, `table_parameters`, `relation_1_table_name`, `relation_1_table_parameters`, `relation_2_table_name`, `relation_2_table_parameters`, `relation_3_table_name`, `relation_3_table_parameters`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
    		('used_rent_category_id', "
            . $article_id_rent .', '
            . $clang_id .', '
            . "'1_xxx_". $prefix ."d2u_machinery_url_used_machine_categories_rent', "
            . "'{\"column_id\":\"category_id\",\"column_clang_id\":\"clang_id\",\"restriction_1_column\":\"\",\"restriction_1_comparison_operator\":\"=\",\"restriction_1_value\":\"\",\"restriction_2_logical_operator\":\"\",\"restriction_2_column\":\"\",\"restriction_2_comparison_operator\":\"=\",\"restriction_2_value\":\"\",\"restriction_3_logical_operator\":\"\",\"restriction_3_column\":\"\",\"restriction_3_comparison_operator\":\"=\",\"restriction_3_value\":\"\",\"column_segment_part_1\":\"name\",\"column_segment_part_2_separator\":\"\\/\",\"column_segment_part_2\":\"\",\"column_segment_part_3_separator\":\"\\/\",\"column_segment_part_3\":\"\",\"relation_1_column\":\"\",\"relation_1_position\":\"BEFORE\",\"relation_2_column\":\"\",\"relation_2_position\":\"BEFORE\",\"relation_3_column\":\"\",\"relation_3_position\":\"BEFORE\",\"append_user_paths\":\"\",\"append_structure_categories\":\"0\",\"column_seo_title\":\"seo_title\",\"column_seo_description\":\"seo_description\",\"column_seo_image\":\"picture\",\"sitemap_add\":\"1\",\"sitemap_frequency\":\"weekly\",\"sitemap_priority\":\"0.7\",\"column_sitemap_lastmod\":\"updatedate\"}', "
            . "'', '[]', '', '[]', '', '[]', CURRENT_TIMESTAMP, '". $login ."', CURRENT_TIMESTAMP, '". $login ."');");

        \TobiasKrais\D2UHelper\BackendHelper::generateUrlCache('used_rent_machine_id');
        \TobiasKrais\D2UHelper\BackendHelper::generateUrlCache('used_rent_category_id');
    }

    /**
     * Removes the used machines (rent) url profiles and their database views.
     * No-op when the url addon is missing.
     */
    public static function removeUsedMachinesRent(): void
    {
        if (!rex_addon::get('url')->isAvailable()) {
            return;
        }

        self::deleteByNamespace('used_rent_machine_id');
        self::deleteByNamespace('used_rent_category_id');
        rex_sql::factory()->setQuery('DROP VIEW IF EXISTS '. rex::getTablePrefix() .'d2u_machinery_url_used_machines_rent');
        rex_sql::factory()->setQuery('DROP VIEW IF EXISTS '. rex::getTablePrefix() .'d2u_machinery_url_used_machine_categories_rent');
    }
}
