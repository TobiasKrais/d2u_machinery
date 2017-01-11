<?php
/**
 * Defines methods each export provider has to implement
 */
abstract class AExport {
	/**
	 * @var ExportedUsedMachine[] that need to be exported.
	 */
	protected $exported_used_machines = array();
	
	/**
	 * @var Provider Export provider object.
	 */
	protected $provider;

	/**
	 * Constructor. Initializes variables
	 * @param Provider $provider Export Provider
	 */
	 public function __construct($provider) {
		$this->provider = $provider;
		$this->exported_used_machines = ExportedUsedMachine::getAll($this->provider);
	}

	
	/**
	 * Converts HTML formatted string to string with new lines. Following HTML
	 * tags are converted to new lines: </p>, <br>, </h1>, </h2>, </h3>, </h4>,
	 * </h5>, </h6>, </li>
	 * @param string $html HTML string
	 */
	static protected function convertToExportString($html) {
		$html = str_replace("<br>", PHP_EOL, $html);
		$html = str_replace("</p>", "</p>".PHP_EOL, $html);
		$html = str_replace("</h1>", "</h1>".PHP_EOL, $html);
		$html = str_replace("</h2>", "</h2>".PHP_EOL, $html);
		$html = str_replace("</h3>", "</h3>".PHP_EOL, $html);
		$html = str_replace("</h4>", "</h4>".PHP_EOL, $html);
		$html = str_replace("</h5>", "</h5>".PHP_EOL, $html);
		$html = str_replace("</h6>", "</h6>".PHP_EOL, $html);
		$html = str_replace("</li>", "</li>".PHP_EOL, $html);
		$html = html_entity_decode($html);
		return strip_tags($html);
	}

	/**
	 * Export machines that are added to export list for the provider.
	 */
	public abstract function export();
	
	/**
	 * Creates the scaled image if it does not already exist.
	 * @param string $pic Picture filename
	 * @return string Cached picture filename
	 */
	protected function preparePicture($pic) {
		$media = new rex_managed_media(rex_path::media($pic));
		$media_manager = new rex_media_manager($media);
		$media_manager->setCachePath(rex_path::addonCache("media_manager"));
		try {
			// ApplyEffects is only protected!!
			$media_manager->applyEffects($this->provider->media_manager_type);
			if($media_manager->isCached() == FALSE) {
				ob_start();
				$media->sendMedia($media_manager->getCacheFilename(), $media_manager->getHeaderCacheFilename(), TRUE);
				ob_end_clean();
			}
		}
		catch (Error $e) {
			getimagesize(rex::getServer()."index.php?rex_media_type=". $this->provider->media_manager_type ."&rex_media_file=". $pic);
		}

		if(file_exists($media_manager->getCacheFilename())) {
			// Cached file if successfull
			return $media_manager->getCacheFilename();
		}
		else {
			// Normal media file if not successfull
			return rex_url::media($pic);
		}
	}
	
	/**
	 * Save export results.
	 */
	protected function saveExportedMachines() {
		foreach($this->exported_used_machines as $exported_used_machine) {
			if($exported_used_machine->export_action == "add" || $exported_used_machine->export_action == "update") {
				$exported_used_machine->export_timestamp = time();
				$exported_used_machine->export_action = "";
				$exported_used_machine->save();
			}
			else if ($exported_used_machine->export_action == "delete") {
				$exported_used_machine->delete();
			}
		}
	}
}