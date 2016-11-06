<?php
/**
 * europemachinery.com always takes a full export, this means the export includes
 * all machines, even ones that are not changed. Deleted machines are not included.
 * @link http://www.agriaffaires.com/html/rubtableauliste_compact_de.html Full List
 */
class EuropeMachinery extends AFTPExport {
	/**
	 * @var string Filename of the XML file for this export. EuropeMachinery
	 * does not require a special name.
	 */
	protected $xml_filename = "export.xml";
	
	/**
	 * Perform the EuropeMachinery Export.
	 * @return string error message - if no errors occured, emtpy string is returned.
	 */
	public function export() {
		// Cleanup old export ZIP file
		unlink($this->cache_path . $this->getZipFileName());
		
		// Prepare pictures: Europemachinery allows max. 10 pictures
		$this->preparePictures(10);
		$this->files_for_zip = array_unique($this->files_for_zip);

		// Create XML file
		$error = $this->createXML();
		if($error != "") {
			return $error;
		}
		
		// Create ZIP
		$this->zip($this->xml_filename);
		
		// Cleanup xml file
		unlink($this->cache_path . $this->xml_filename);
		
		// Upload
		$error = $this->upload();
		if($error != "") {
			return $error;
		}
		
		// Save results in database
		$this->saveExportedMachines();
		
		return "";
	}
	
	/**
	 * Creates a xml file for europemachinery.com
	 * @return int number of objects contained in xml
	 */
	private function createXML() {
		// <?xml version="1.0" encoding="UTF-8">
		$xml = new DOMDocument("1.0", "UTF-8");
		$xml->formatOutput = true;
		// <importfile version="2.0">
		$importfile = $xml->createElement("importfile");
		$importfile_version = $xml->createAttribute("version");
		$importfile_version->appendChild($xml->createTextNode("2.0"));
		$importfile->appendChild($importfile_version);
		$xml->appendChild($importfile);

		foreach($this->exported_used_machines as $exported_used_machine) {
			$used_machine = new UsedMachine($exported_used_machine->used_machine_id, $this->provider->clang_id);
			// deleted machines are not included
			if($exported_used_machine->export_action == "add" || $exported_used_machine->export_action == "update") {
				// Only take used machines with a europemachinery category
				if($used_machine->category->export_europemachinery_category_id > 0) {
					// <equipment id="1459">
					$equipment = $xml->createElement("equipment");
					$equipment_id = $xml->createAttribute("id");
					$equipment_id->appendChild($xml->createTextNode($used_machine->used_machine_id));
					$equipment->appendChild($equipment_id);

					// <stockid>D1459</stockid>
					$stockid = $xml->createElement("stockid");
					$stockid->appendChild($xml->createTextNode($used_machine->product_number));
					$equipment->appendChild($stockid);

					// <category id="10">Dozer</category>
					$category = $xml->createElement("category");
					$category_id = $xml->createAttribute("id");
					$category_id->appendChild($xml->createTextNode($used_machine->category->export_europemachinery_category_id));
					$category->appendChild($category_id);
					$category->appendChild($xml->createTextNode($used_machine->category->export_europemachinery_category_name));
					$equipment->appendChild($category);

					// <comment>Current Location Lansing, MI - Bargain Lot, Base Model Dozers, Trans Type PowerShift, lever steer</comment>
					$comment = $xml->createElement("comment");
					$comment->appendChild($xml->createTextNode($used_machine->description));
					$equipment->appendChild($comment);

					// <pictures>
					//     <picture>10381_D1459.jpg</picture>
					// </pictures>
					$pictures = $xml->createElement("pictures");
					$pics_counter = 1;

					foreach($used_machine->pics as $pic) {
						// only first 10 pics are inculded
						if(strlen($pic) > 3 && $pics_counter <= 10) {
							$picture = $xml->createElement("picture");
							$picture->appendChild($xml->createTextNode($pic));
							$pictures->appendChild($picture);

							$pics_counter ++;
						}
					}
					if($pics_counter == 1) {
						// If there are no pics available: <picture></picture>
						$picture = $xml->createElement("picture");
						$pictures->appendChild($picture);
					}
					$equipment->appendChild($pictures);

					// <properties>
					$properties = $xml->createElement("properties");

					// <property id="1" name="Make">Caterpillar</property>
					$hersteller = $xml->createElement("property");

					$hersteller_id = $xml->createAttribute("id");
					$hersteller_id->appendChild($xml->createTextNode("1"));
					$hersteller->appendChild($hersteller_id);

					$hersteller_name = $xml->createAttribute("name");
					$hersteller_name->appendChild($xml->createTextNode("Hersteller"));
					$hersteller->appendChild($hersteller_name);

					$hersteller->appendChild($xml->createTextNode($used_machine->manufacturer));
					$properties->appendChild($hersteller);

					// <property id="2" name="Model">D4C XL III</property>
					$modell = $xml->createElement("property");

					$modell_id = $xml->createAttribute("id");
					$modell_id->appendChild($xml->createTextNode("2"));
					$modell->appendChild($modell_id);

					$modell_name = $xml->createAttribute("name");
					$modell_name->appendChild($xml->createTextNode("Modell"));
					$modell->appendChild($modell_name);

					$modell->appendChild($xml->createTextNode($used_machine->name));
					$properties->appendChild($modell);

					// <property id="3" name="Year">1998</property>
					$baujahr = $xml->createElement("property");

					$baujahr_id = $xml->createAttribute("id");
					$baujahr_id->appendChild($xml->createTextNode("3"));
					$baujahr->appendChild($baujahr_id);

					$baujahr_name = $xml->createAttribute("name");
					$baujahr_name->appendChild($xml->createTextNode("Baujahr"));
					$baujahr->appendChild($baujahr_name);

					$baujahr->appendChild($xml->createTextNode($used_machine->year_built));
					$properties->appendChild($baujahr);

					// <property id="6" name="Price">18960</property>
					$preis = $xml->createElement("property");

					$preis_id = $xml->createAttribute("id");
					$preis_id->appendChild($xml->createTextNode("4"));
					$preis->appendChild($preis_id);

					$preis_name = $xml->createAttribute("name");
					$preis_name->appendChild($xml->createTextNode("Preis"));
					$preis->appendChild($preis_name);

					$preis->appendChild($xml->createTextNode($used_machine->price));
					$properties->appendChild($preis);

					// </properties>
					$equipment->appendChild($properties);

					// </product>
					$importfile->appendChild($equipment);
				}
			}
		}

		// write XML file
		try {
			if($xml->save($this->cache_path . $this->xml_filename) === FALSE) {
				return rex_i18n::msg('d2u_machinery_export_xml_cannot_create');
			}
			else {
				return "";
			}
		}
		catch(Exception $e) {
			return rex_i18n::msg('d2u_machinery_export_xml_cannot_create') . " - ". $e->getMessage();
		}
	}
}