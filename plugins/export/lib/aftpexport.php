<?php
/**
 * Defines methods each export provider has to implement.
 */
abstract class AFTPExport extends AExport
{
    /** @var string Path to cache of the plugin. Initialized in constructor. */
    protected $cache_path = '';

    /** @var array<string> list of files that need to be added to ZIP export file */
    protected $files_for_zip = [];

    /** @var string filename of the ZIP file for this export */
    protected $zip_filename = '';

    /**
     * Constructor. Initializes variables.
     * @param Provider $provider Export Provider
     */
    public function __construct($provider)
    {
        parent::__construct($provider);

        // Set exported used machines without export action to action "update"
        foreach ($this->exported_used_machines as $exported_used_machine) {
            if ('' === $exported_used_machine->export_action) {
                $exported_used_machine->export_action = 'update';
                $exported_used_machine->save();
            }
        }
        $this->exported_used_machines = ExportedUsedMachine::getAll($this->provider);

        // Check if cache path exists - if not create it
        $this->cache_path = rex_path::pluginCache('d2u_machinery', 'export');
        if (!is_dir($this->cache_path)) {
            mkdir($this->cache_path, 0o777, true);
        }
    }

    /**
     * Creates the filename for the zip file.
     * @return string zip filename
     */
    protected function getZipFileName()
    {
        if ('' !== $this->provider->ftp_filename) {
            $this->zip_filename = $this->provider->ftp_filename;
        } elseif ('' === $this->zip_filename) {
            $this->zip_filename = preg_replace('/[^a-zA-Z0-9]/', '', $this->provider->name) .'_'
                . trim($this->provider->customer_number) .'_'. $this->provider->type .'.zip';
        }
        return $this->zip_filename;
    }

    /**
     * Creates the scaled image.
     * @param string $pic Picture filename
     * @return string Cached picture filename
     */
    protected function preparePicture($pic)
    {
        $cached_pic = parent::preparePicture($pic);
        if (!in_array($cached_pic, $this->files_for_zip, true)) {
            $this->files_for_zip[$pic] = $cached_pic;
        }
        return $cached_pic;
    }

    /**
     * Prepares all pictures for export.
     * @param int $max_pics Maximum number of pictures, default is 6
     */
    protected function preparePictures($max_pics = 6): void
    {
        foreach ($this->exported_used_machines as $exported_used_machine) {
            $pics_counter = 0;
            if ('add' === $exported_used_machine->export_action || 'update' === $exported_used_machine->export_action) {
                $used_machine = new UsedMachine($exported_used_machine->used_machine_id, $this->provider->clang_id);
                foreach ($used_machine->pics as $pic) {
                    if (strlen($pic) > 3 && $pics_counter < $max_pics) {
                        $this->preparePicture($pic);
                        ++$pics_counter;
                    }
                }
            }
        }
    }

    /**
     * Uploads the zip file using FTP.
     * @return string error message
     */
    protected function upload()
    {
        // Establish connection and ...
        $connection_id = ftp_ssl_connect($this->provider->ftp_server);
        if (!$connection_id instanceof \FTP\Connection) {
            return rex_i18n::msg('d2u_machinery_export_ftp_error_connection');
        }

        // ... login
        $login_result = ftp_login($connection_id, $this->provider->ftp_username, $this->provider->ftp_password);
        if (!$login_result) {
            // login failed
            if (extension_loaded('ssh2')) {
                // try to connect via SSH
                $connection_id_ssh = ssh2_connect($this->provider->ftp_server);
                ssh2_auth_password($connection_id_ssh, $this->provider->ftp_username, $this->provider->ftp_password);
                if (!ssh2_scp_send($connection_id_ssh, $this->cache_path . $this->getZipFileName(), $this->zip_filename, 0644)) {
                    return rex_i18n::msg('d2u_machinery_export_ftp_error_upload');
                }
                // Close SSH connection
                ssh2_disconnect($connection_id_ssh);

                return '';
            }
            // try not connect without SSL
            $connection_id = ftp_connect($this->provider->ftp_server);
            if (!$connection_id instanceof \FTP\Connection) {
                return rex_i18n::msg('d2u_machinery_export_ftp_error_connection');
            }
            $login_result = ftp_login($connection_id, $this->provider->ftp_username, $this->provider->ftp_password);
            if (!$login_result) {
                return rex_i18n::msg('d2u_machinery_export_ftp_error_connection');
            }
        }
        // Passive mode
        ftp_pasv($connection_id, true);

        // Upload
        if (!ftp_put($connection_id, $this->zip_filename, $this->cache_path . $this->getZipFileName(), FTP_BINARY)) {
            return rex_i18n::msg('d2u_machinery_export_ftp_error_upload');
        }

        // Close connection
        ftp_close($connection_id);

        return '';
    }

    /**
     * ZIPs pictures and machine filename.
     * @param string $filename
     * @return string error message or empty if no errors occur
     */
    protected function zip($filename)
    {
        // Create ZIP
        $zip = new ZipArchive();
        if (true !== $zip->open($this->cache_path . $this->getZipFileName(), ZipArchive::CREATE)) {
            return rex_i18n::msg('d2u_machinery_export_zip_cannot_create');
        }
        $zip->addFile($this->cache_path . $filename, $filename);
        foreach ($this->files_for_zip as $original_filename => $cachefilename) {
            $zip->addFile($cachefilename, $original_filename);
        }
        $zip->close();
        return '';
    }
}
