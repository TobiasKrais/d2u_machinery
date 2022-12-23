<?php
/**
 * mascus.de always takes a full export, this means the export includes
 * all machines, even ones that are not changed. Deleted machines are not included.
 */
class Mascus extends AFTPExport {
	/**
	 * @var string Filename of the XML file for this export. Mascus
	 * does not require a special name.
	 */
	protected $xml_filename = "export.xml";
	
	public function export() {
		// Cleanup old export ZIP file
		if(file_exists($this->cache_path . $this->getZipFileName())) {
			unlink($this->cache_path . $this->getZipFileName());
		}
		
		// Prepare pictures: Mascus allows max. 6 pictures / machine
		$this->preparePictures(6);
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
	 * Creates a xml file for mascus.com
	 * @return string containing error information, if occured
	 */
	private function createXML() {
		// <?xml version="1.0" encoding="UTF-8">
		$xml = new DOMDocument("1.0", "UTF-8");
		$xml->formatOutput = true;
		// <root>
		$root = $xml->createElement("root");
		$xml->appendChild($root);

		foreach($this->exported_used_machines as $exported_used_machine) {
			$used_machine = new UsedMachine($exported_used_machine->used_machine_id, $this->provider->clang_id);
			// deleted machines are not included
			if($exported_used_machine->export_action === "add" || $exported_used_machine->export_action === "update") {
				// Only take used machines with a mascus category
				if($used_machine->category instanceof Category && $used_machine->category->export_mascus_category_name !== "") {
					// <product>
					$product = $xml->createElement("product");

					// <DealerProductID>D40F3F35</DealerProductID>
					$dealerProductID = $xml->createElement("DealerProductID");
					$dealerProductID->appendChild($xml->createTextNode((string) $used_machine->used_machine_id));
					$product->appendChild($dealerProductID);

					// <InternalStockNumber>2015</InternalStockNumber>
					$internalStockNumber = $xml->createElement("InternalStockNumber");
					$internalStockNumber->appendChild($xml->createTextNode($used_machine->product_number));
					$product->appendChild($internalStockNumber);

					// <QCategoryName>crawlerexcavators</QCategoryName>
					$category = $xml->createElement("QCategoryName");
					$category->appendChild($xml->createTextNode($used_machine->category->export_mascus_category_name));
					$product->appendChild($category);

					// <Brand>Caterpillar</Brand>
					$manufacturer = $xml->createElement("Brand");
					$manufacturer->appendChild($xml->createTextNode($used_machine->manufacturer));
					$product->appendChild($manufacturer);

					// <Model>325BNL</Model>
					$model = $xml->createElement("Model");
					$model->appendChild($xml->createTextNode($used_machine->name));
					$product->appendChild($model);

					// <PriceOriginal>50000</PriceOriginal>
					$price = $xml->createElement("PriceOriginal");
					$price->appendChild($xml->createTextNode((string) $used_machine->price));
					$product->appendChild($price);

					// <PriceOriginalUnit>EUR</PriceOriginalUnit>
					$currencyCode = $xml->createElement("PriceOriginalUnit");
					$currencyCode->appendChild($xml->createTextNode($used_machine->currency_code));
					$product->appendChild($currencyCode);

					// <YearOfManufacture>1999</YearOfManufacture>
					$yearBuilt = $xml->createElement("YearOfManufacture");
					$yearBuilt->appendChild($xml->createTextNode((string) $used_machine->year_built));
					$product->appendChild($yearBuilt);

					// <OtherInformation>bra maskin!</OtherInformation>
					$description = $xml->createElement("OtherInformation");
					$description->appendChild($xml->createTextNode(AExport::convertToExportString($used_machine->description)));
					$product->appendChild($description);

					// <AttachmentPath1>D40F3F35_2.jpg</AttachmentPath1>
					$pics_counter = 1;

					foreach($used_machine->pics as $pic) {
						// only first 6 pics are inculded
						if(strlen($pic) > 3 && $pics_counter <= 6) {
							$attachment = $xml->createElement("AttachmentPath". $pics_counter);
							$attachment->appendChild($xml->createTextNode($pic));
							$product->appendChild($attachment);

							$pics_counter ++;
						}
					}

					// </product>
					$root->appendChild($product);
				}
			}
		}

		// write XML file
		try {
			if($xml->save($this->cache_path . $this->xml_filename) === false) {
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