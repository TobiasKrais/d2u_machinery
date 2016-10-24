<?php
/**
 * Defines methods each export provider has to implement
 */
abstract class AExport {
	/**
	 * @var ExportedUsedMachine[] that need to be exported.
	 */
	var $exported_used_machines = array();
	
	/**
	 * @var Provider Export provider object.
	 */
	var $provider;

	/**
	 * Constructor. Initializes variables
	 * @param Provider $provider Export Provider
	 */
	 public function __construct($provider) {
		 $this->provider = $provider;
		 $this->exported_used_machines = ExportedUsedMachine::getAll($this->provider);
	}

	/**
	 * Export machines that are added to export list for the provider.
	 */
	public function export();
	
	/**
	 * Creates the scaled image
	 * @param String $include_path Inhalt von $REX['INCLUDE_PATH'].
	 * @param ExportConfig $config Exportkonfiguration.
	 * @param String $pic_name Name des Bildes
	 * @return String Name des resized Bildes
	 */
	static function preparePicture($include_path, $config, $pic_name) {
		// Rueckgabewert
		$resize_pic_name = "";

		// URL von Redaxo
		$url_path = "http://". $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
		$url_path = substr($url_path, 0, strrpos($url_path, "/") + 1);

		// Pfad zu den Bildern im Redaxo Backend
		$rex_resize = '../index.php?rex_resize=';

		// Bildpfad
		$image_folder = $include_path . DIRECTORY_SEPARATOR
				."generated". DIRECTORY_SEPARATOR ."files".	DIRECTORY_SEPARATOR;
		$image_resize_file = "image_resize__". $config->resize_objekt ."a__". $pic_name;
		$image_manager_file = "image_manager__RR_". $config->resize_objekt ."a__". $pic_name;

		// Bild nur verkleinern wenn es nicht schon getan wurde
		if(file_exists($image_folder . $image_resize_file)) {
			// Wenn Bild vom image_resize Addon verkleinert wurde
			$resize_pic_name = $image_resize_file;
		}
		else if(file_exists($image_folder . $image_manager_file)) {
			// Wenn Bild vom image_manager image_resize Plugin verkleinert wurde
			$resize_pic_name = $image_manager_file;
		}
		else {
			// Bild verkleinern
			getimagesize($url_path . $rex_resize . $config->resize_objekt. "a__". $pic_name);

			if(file_exists($image_folder . $image_resize_file)) {
				// Wenn Bild vom image_resize Addon verkleinert wurde
				$resize_pic_name = $image_resize_file;
			}
			else if(file_exists($image_folder . $image_manager_file)) {
				// Wenn Bild vom image_manager image_resize Plugin verkleinert wurde
				$resize_pic_name = $image_manager_file;
			}
			else {
				// Falls verkleinern scheitert
				$resize_pic_name = trim($pic_name);
			}
		}

		return $resize_pic_name;
	}
}
