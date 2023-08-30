<?php
/**
 * Defines methods each export provider has to implement.
 */
abstract class AExport
{
    /** @var array<ExportedUsedMachine> Used machines that need to be exported */
    protected array $exported_used_machines = [];

    /** @var string complete file name including parent folders containing log file */
    public string $log_file = '';

    /** @var Provider export provider object */
    protected Provider $provider;

    /**
     * Constructor. Initializes variables.
     * @param Provider $provider Export Provider
     */
    public function __construct($provider)
    {
        $this->provider = $provider;
        $this->exported_used_machines = ExportedUsedMachine::getAll($provider);

        if (!file_exists(rex_path::pluginData('d2u_machinery', 'export'))) {
            rex_dir::create(rex_path::pluginData('d2u_machinery', 'export'));
        }
        $this->log_file = rex_path::pluginData('d2u_machinery', 'export', 'export_'. date('Y-m-d_H-i-s', time()) .'_'. $this->provider->type .'.log');
    }

    /**
     * Converts HTML formatted string to string with new lines. Following HTML
     * tags are converted to new lines: </p>, <br>, </h1>, </h2>, </h3>, </h4>,
     * </h5>, </h6>, </li>.
     * @param string $html HTML string
     * @return string Converted string
     */
    protected static function convertToExportString($html)
    {
        $html = str_replace('<br>', PHP_EOL, $html);
        $html = str_replace('</p>', '</p>'.PHP_EOL, $html);
        $html = str_replace('</h1>', '</h1>'.PHP_EOL, $html);
        $html = str_replace('</h2>', '</h2>'.PHP_EOL, $html);
        $html = str_replace('</h3>', '</h3>'.PHP_EOL, $html);
        $html = str_replace('</h4>', '</h4>'.PHP_EOL, $html);
        $html = str_replace('</h5>', '</h5>'.PHP_EOL, $html);
        $html = str_replace('</h6>', '</h6>'.PHP_EOL, $html);
        $html = str_replace('</li>', '</li>'.PHP_EOL, $html);
        $html = html_entity_decode($html);
        return strip_tags($html);
    }

    /**
     * Export machines that are added to export list for the provider.
     * @return string error message - if no errors occured, empty string is returned
     */
    abstract public function export();

    /**
     * Logs message.
     * @param string $message Message to be logged
     */
    protected function log($message): void
    {
        $log = file_exists($this->log_file) ? file_get_contents($this->log_file) : '';

        $microtime = microtime(true);
        $log .= date('d.m.Y H:i:s', time()) .'.'. sprintf('%03d', ($microtime - floor($microtime)) * 1000) .': '. $message . PHP_EOL;

        // Write to log
        file_put_contents($this->log_file, $log);
    }

    /**
     * Creates the scaled image if it does not already exist.
     * @param string $pic Picture filename
     * @return string Cached picture filename
     */
    protected function preparePicture($pic)
    {
        $media = rex_media::get($pic);
        if ($media instanceof rex_media && false !== filesize(rex_path::media($pic)) && filesize(rex_path::media($pic)) < 1572864) {
            $media_manager = rex_media_manager::create($this->provider->media_manager_type, $pic);
            if (file_exists($media_manager->getCacheFilename())) {
                // Cached file if successfull
                return $media_manager->getCacheFilename();
            }

            // Normal media file if not successfull
            return rex_url::media($pic);

        }

        echo rex_view::warning($pic .': '. rex_i18n::msg('d2u_machinery_export_image_too_large'));
        return rex_url::media($pic);

    }

    /**
     * Save export results.
     */
    protected function saveExportedMachines(): void
    {
        foreach ($this->exported_used_machines as $exported_used_machine) {
            if ('add' === $exported_used_machine->export_action || 'update' === $exported_used_machine->export_action) {
                $exported_used_machine->export_timestamp = date('Y-m-d H:i:s');
                $exported_used_machine->export_action = '';
                $exported_used_machine->save();
            } elseif ('delete' === $exported_used_machine->export_action) {
                $exported_used_machine->delete();
            }
        }
    }
}
