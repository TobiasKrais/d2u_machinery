<?php
/**
 * machinerypark.com always takes a full export, this means the export includes
 * all machines, even ones that are not changed. Deleted machines are not included.
 */
class MachineryPark extends AFTPExport {
	/**
	 * @var string Filename of the XML file for this export. MachineryPark
	 * does not require a special name.
	 */
	protected $csv_filename = "machinerypark.csv";
	
	/**
	 * @var string Filename of the ZIP file for this export.
	 */
	protected $zip_filename = "machinerypark.zip";

	/**
	 * Perform the MachineryPark Export.
	 * @return string error message - if no errors occured, empty string is returned.
	 */
	public function export() {
		// Cleanup old export ZIP file
		if(file_exists($this->cache_path . $this->getZipFileName())) {
			unlink($this->cache_path . $this->getZipFileName());
		}

		// Prepare pictures: MachineryPark allows max. 6 pictures / machine
		$this->preparePictures(6);
		$this->files_for_zip = array_unique($this->files_for_zip);
		
		// Create XML file
		$error = $this->createCSV();
		if($error !== "") {
			return $error;
		}
		
		// Create ZIP
		$this->zip($this->csv_filename);
		
		// Cleanup xml file
		unlink($this->cache_path . $this->csv_filename);
		
		// Upload
		$error = $this->upload();
		if($error !== "") {
			return $error;
		}
		
		// Save results in database
		$this->saveExportedMachines();
		
		return "";
	}
	
	/**
	 * Creates a csv file for machinerypark.com
	 * @return string containing error information, if occured
	 */
	private function createCSV() {
		// Column names
		$list = [
			['Referenz', 'Kategorie', 'Hersteller', 'Typ', 'Beschreibung',
					'Postleitzahl', 'Standort', 'Land', 'Preis', 'Baujahr', 'PS',
					'KM', 'BS', 'Gewicht', 'Treibstoff', 'Bild1', 'Bild2', 'Bild3',
					'Bild4', 'Bild5', 'Bild6']];

		foreach($this->exported_used_machines as $exported_used_machine) {
			$used_machine = new UsedMachine($exported_used_machine->used_machine_id, $this->provider->clang_id);
			// deleted machines are not included
			if($exported_used_machine->export_action == "add" || $exported_used_machine->export_action == "update") {
				// Only take used machines with a machinerypark category
				if($used_machine->category->export_machinerypark_category_id > 0) {
					// prepare pics, maximum 6 are allowed
					$pics = ["", "", "", "", "", ""];
					$pics_counter = 0;
					foreach($used_machine->pics as $pic) {
						if($pics_counter < 6) {
							$pics[$pics_counter++] = $pic;
						}
					}

					$list[] = [$used_machine->used_machine_id,
							$used_machine->category->export_machinerypark_category_id,
							utf8_decode(str_replace('"', "'", $used_machine->manufacturer)),
							utf8_decode(str_replace('"', "'", $used_machine->name)),
							utf8_decode(str_replace("\n", " ", str_replace(";", ",", str_replace('"', "'", AExport::convertToExportString($used_machine->description))))),
							"", // ZIP code
							"", // Location
							"", // Country
							str_replace('"', "'", round($used_machine->price)),
							str_replace('"', "'", $used_machine->year_built),
							"", // Horse power
							"", // KM
							"", // BS
							"", // Weight
							"", // Fuel
							$pics[0],
							$pics[1],
							$pics[2],
							$pics[3],
							$pics[4],
							$pics[5]];
				}
			}
		}

		// Write CSV file
		$fp = fopen($this->cache_path . $this->csv_filename, 'w+');
		if($fp === false) {
			return rex_i18n::msg('d2u_machinery_export_csv_cannot_create');
		}
		foreach ($list as $fields) {
			fputcsv($fp, $fields, ';');
		}
		fclose($fp);

		return "";
	}
}