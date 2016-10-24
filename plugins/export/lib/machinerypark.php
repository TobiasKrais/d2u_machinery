<?php
/**
 * machinerypark.com always takes a full export, this means the export includes
 * all machines, even ones that are not changed. Deleted machines are not included.
 */
class MacherinyPark extends AExport {

	public function export() {
		// TODO
	}
	
	/**
	 * Creates a csv file for machinerypark.com
	 * @return int number of objects contained in xml
	 */
	private function createCSV() {
		// return value
		$machine_counter = 0;	

		// Column names
		$list = array (
			array('Referenz', 'Kategorie', 'Hersteller', 'Typ', 'Beschreibung',
					'Postleitzahl', 'Standort', 'Land', 'Preis', 'Baujahr', 'PS',
					'KM', 'BS', 'Gewicht', 'Treibstoff', 'Bild1', 'Bild2', 'Bild3',
					'Bild4', 'Bild5', 'Bild6'));

		foreach($this->exported_used_machines as $exported_used_machine) {
			$used_machine = new UsedMachine($exported_used_machine->used_machine_id, $this->provider->clang_id);
			// deleted machines are not included
			if($exported_used_machine->export_action == "add" || $exported_used_machine->export_action == "update") {
				// Only take used machines with a europemachinery category
				if($used_machine->category->export_machinerypark_category_id > 0) {
					// prepare pics, maximum 6 are allowed
					$temp_pics = explode(",", $used_machine->pics);
					$pics = array("", "", "", "", "", "");
					for($i = 0; $i < count($temp_pics); $i++) {
						if($i < 6) {
							$pics[$i] = $this->preparePicture($temp_pics[$i]); //TODO
						}
					}

					$list[] = array($used_machine->used_machine_id,
							$used_machine->category->export_machinerypark_category_id,
							utf8_decode(str_replace('"', "'", $used_machine->manufacturer)),
							utf8_decode(str_replace('"', "'", $used_machine->name)),
							utf8_decode(str_replace("\n", " ", str_replace('"', "'", $used_machine->description))),
							"", // ZIP code
							"", // Location
							"", // Country
							str_replace('"', "'", round($used_machine->price)),
							str_replace('"', "'", $used_machine->year_built),
							"", // PS
							"", // KM
							"", // BS
							"", // Weight
							"", // Fuel
							$pics[0],
							$pics[1],
							$pics[2],
							$pics[3],
							$pics[4],
							$pics[5]);

					// Count used machines
					$machine_counter++;
				}
			}
		}

		// Create CSV file
		$fp = fopen($this->provider->export_filename, 'w+');

		if($fp === false) {
			return false;
		}

		foreach ($list as $fields) {
			fputcsv($fp, $fields, ';');
		}

		fclose($fp);

		return $machine_counter;
	}
}