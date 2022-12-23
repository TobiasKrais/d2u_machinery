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
	 * @return string error message - if no errors occured, empty string is returned.
	 */
	public function export() {
		// Cleanup old export ZIP file
		if(file_exists($this->cache_path . $this->getZipFileName())) {
			unlink($this->cache_path . $this->getZipFileName());
		}
		
		// Prepare pictures: Europemachinery allows max. 10 pictures / machine
		$this->preparePictures(10);
		$this->files_for_zip = array_unique($this->files_for_zip);

		// Create XML file
		$error = $this->createXML();
		if($error !== "") {
			return $error;
		}
		
		// Create ZIP
		$this->zip($this->xml_filename);
		
		// Cleanup xml file
		unlink($this->cache_path . $this->xml_filename);
		
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
	 * Creates a xml file for europemachinery.com
	 * @return string containing error information, if occured
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
			if($exported_used_machine->export_action === "add" || $exported_used_machine->export_action === "update") {
				// Only take used machines with a europemachinery category
				if($used_machine->category instanceof Category && $used_machine->category->export_europemachinery_category_id > 0) {
					// <equipment id="1459">
					$equipment = $xml->createElement("equipment");
					$equipment_id = $xml->createAttribute("id");
					$equipment_id->appendChild($xml->createTextNode((string) $used_machine->used_machine_id));
					$equipment->appendChild($equipment_id);

					// <stockid>D1459</stockid>
					$stockid = $xml->createElement("stockid");
					$stockid->appendChild($xml->createTextNode($used_machine->product_number));
					$equipment->appendChild($stockid);

					// <category id="10">Dozer</category>
					$category = $xml->createElement("category");
					$category_id = $xml->createAttribute("id");
					$category_id->appendChild($xml->createTextNode((string) $used_machine->category->export_europemachinery_category_id));
					$category->appendChild($category_id);
					$category->appendChild($xml->createTextNode($used_machine->category->export_europemachinery_category_name));
					$equipment->appendChild($category);

					// <comment>Current Location Lansing, MI - Bargain Lot, Base Model Dozers, Trans Type PowerShift, lever steer</comment>
					$comment = $xml->createElement("comment");
					$comment->appendChild($xml->createTextNode(AExport::convertToExportString($used_machine->description)));
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
					if($pics_counter === 1) {
						// If there are no pics available: <picture></picture>
						$picture = $xml->createElement("picture");
						$pictures->appendChild($picture);
					}
					$equipment->appendChild($pictures);

					// <properties>
					$properties = $xml->createElement("properties");

					// <property id="1" name="Make">Caterpillar</property>
					$manufacturer = $xml->createElement("property");

					$manufacturer_id = $xml->createAttribute("id");
					$manufacturer_id->appendChild($xml->createTextNode("1"));
					$manufacturer->appendChild($manufacturer_id);

					$manufacturer_name = $xml->createAttribute("name");
					$manufacturer_name->appendChild($xml->createTextNode("Hersteller"));
					$manufacturer->appendChild($manufacturer_name);

					$manufacturer->appendChild($xml->createTextNode($used_machine->manufacturer));
					$properties->appendChild($manufacturer);

					// <property id="2" name="Model">D4C XL III</property>
					$model = $xml->createElement("property");

					$model_id = $xml->createAttribute("id");
					$model_id->appendChild($xml->createTextNode("2"));
					$model->appendChild($model_id);

					$model_name = $xml->createAttribute("name");
					$model_name->appendChild($xml->createTextNode("Modell"));
					$model->appendChild($model_name);

					$model->appendChild($xml->createTextNode($used_machine->name));
					$properties->appendChild($model);

					// <property id="3" name="Year">1998</property>
					$yearBuilt = $xml->createElement("property");

					$yearBuiltID = $xml->createAttribute("id");
					$yearBuiltID->appendChild($xml->createTextNode("3"));
					$yearBuilt->appendChild($yearBuiltID);

					$yearBuiltName = $xml->createAttribute("name");
					$yearBuiltName->appendChild($xml->createTextNode("Baujahr"));
					$yearBuilt->appendChild($yearBuiltName);

					$yearBuilt->appendChild($xml->createTextNode((string) $used_machine->year_built));
					$properties->appendChild($yearBuilt);

					// <property id="6" name="Price">18960</property>
					$price = $xml->createElement("property");

					$priceID = $xml->createAttribute("id");
					$priceID->appendChild($xml->createTextNode("4"));
					$price->appendChild($priceID);

					$priceName = $xml->createAttribute("name");
					$priceName->appendChild($xml->createTextNode("Preis"));
					$price->appendChild($priceName);

					$price->appendChild($xml->createTextNode((string) $used_machine->price));
					$properties->appendChild($price);

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