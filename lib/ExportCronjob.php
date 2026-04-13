<?php

namespace TobiasKrais\D2UMachinery;

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
