<?php
/**
 * Defines methods each export provider has to implement
 */
abstract class AFTPExport extends AExport {
	/**
	 * @var string Path to cache of the plugin. Initialized in constructor. 
	 */
	protected $cache_path = "";

	/**
	 * @var ExportedUsedMachine[] that need to be exported.
	 */
	protected $exported_used_machines = array();
	
	/**
	 * @var Provider Export provider object.
	 */
	protected $provider;
	
	/**
	 * @var string[] list of files that need to be added to ZIP export file. 
	 */
	protected $files_for_zip = [];

	/**
	 * Constructor. Initializes variables
	 * @param Provider $provider Export Provider
	 */
	 public function __construct($provider) {
		parent::__construct($provider);
		 
		$this->cache_path = rex_path::pluginCache("d2u_machinery", "export");
		if(!is_dir($this->cache_path)) {
			mkdir($this->cache_path, 0777, true);
		}
	}
	
	/**
	 * Creates the filename for the zip file
	 * @return string zip filename
	 */
	function getZipFileName() {
		if($this->provider->ftp_filename != "") {
			return $this->provider->ftp_filename;
		}
		else {
			return preg_replace("/[^a-zA-Z0-9]/", "", $this->provider->name) ."_"
					. trim($this->provider->customer_number) ."_". $this->provider->type .".zip";
		}
	}

	/**
	 * Creates the scaled image
	 * @param string $pic Picture filename
	 * @return string Cached picture filename
	 */
	protected function preparePicture($pic) {
		$cached_pic = parent::preparePicture($pic);
		if(!in_array($cached_pic, $this->files_for_zip)) {
			$this->files_for_zip[$pic] = $cached_pic;
		}
		return $cached_pic;
	}

	/**
	 * Prepares all pictures for export.
	 * @param int $max_pics Maximum number of pictures, default is 6
	 */
	protected function preparePictures($max_pics = 6) {
		$pics_counter = 0;
		foreach($this->exported_used_machines as $exported_used_machine) {
			if($exported_used_machine->export_action == "add" || $exported_used_machine->export_action == "update") {
				$used_machine = new UsedMachine($exported_used_machine->used_machine_id, $this->provider->clang_id);
				foreach($used_machine->pics as $pic) {
					if(strlen($pic) > 3 && $pics_counter < $max_pics) {
						$this->preparePicture($pic);
						$pics_counter ++;
					}
				}
			}
		}
	}	
}