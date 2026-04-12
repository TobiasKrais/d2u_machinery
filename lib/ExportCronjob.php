<?php

namespace TobiasKrais\D2UMachinery;

use DOMDocument;
use Exception;
use ZipArchive;
use rex;
use rex_addon;
use rex_article;
use rex_be_controller;
use rex_clang;
use rex_config;
use rex_dir;
use rex_escape;
use rex_i18n;
use rex_mailer;
use rex_media;
use rex_media_manager;
use rex_path;
use rex_plugin;
use rex_request;
use rex_sql;
use rex_url;
use rex_user;
use rex_version;
use rex_view;
use rex_yrewrite;

/**
 * Offers helper functions for export plugin.
 */
class ExportCronjob extends \TobiasKrais\D2UHelper\ACronJob
{
    /**
     * Create a new instance of object.
    * @return ExportCronjob CronJob object
     */
    public static function factory()
    {
        $cronjob = new self();
        $cronjob->name = 'D2U Machinery Autoexport';
        return $cronjob;
    }

    /**
     * Install CronJob. Its also activated.
     */
    public function install(): void
    {
        $description = 'Exports used machines automatically to FTP based export providers';
        $php_code = '<?php Provider::autoexport(); ?>';
        $interval = '{\"minutes\":[0],\"hours\":[21],\"days\":\"all\",\"weekdays\":\"all\",\"months\":\"all\"}';
        self::save($description, $php_code, $interval);
    }
}
