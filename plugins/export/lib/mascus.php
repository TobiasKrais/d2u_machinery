<?php
/**
 * mascus.de always takes a full export, this means the export includes
 * all machines, even ones that are not changed. Deleted machines are not included.
 */
class MacherinyPark extends AExport {

	public function export() {
		// TODO
	}
	
	/**
	 * Creates a xml file for mascus.com
	 * @return int number of objects contained in xml
	 */
	private function createXML($maschinen, $config, $rex_include_path) {
		// Array mit Rueckgabewerten
		$exported_ids = array();
		$ids_counter = 0;	

		// <?xml version="1.0" encoding="UTF-8">
		$xml = new DOMDocument("1.0", "UTF-8");
		$xml->formatOutput = true;
		// <root>
		$root = $xml->createElement("root");
		$xml->appendChild($root);

		foreach($maschinen as $maschine) {
			// Bei Mascus werden alle exportierten Maschinen als neu betrachtet, deshalb
			// darf keine Maschine mit der Aktion "DELETE" im Export auftauchen
			if($maschine->aktion == "NEW" || $maschine->aktion == "CHANGE") {
				// Maschinen dieser Kategorie koennen nicht exportiert werden, da sie im
				// Portal keiner Kategorie zugeordnet werden koennen
				if($maschine->kategorie->kategoriename != "Sonstige Baumaschinen") {
					// <product>
					$product = $xml->createElement("product");

					// <DealerProductID>D40F3F35</DealerProductID>
					$haendler_produktnummer = $xml->createElement("DealerProductID");
					$haendler_produktnummer->appendChild($xml->createTextNode($maschine->maschinen_id));
					$product->appendChild($haendler_produktnummer);

					// <InternalStockNumber>2015</InternalStockNumber>
					$interne_produktnummer = $xml->createElement("InternalStockNumber");
					$interne_produktnummer->appendChild($xml->createTextNode($maschine->interne_produktnummer));
					$product->appendChild($interne_produktnummer);

					// <QCategoryName>crawlerexcavators</QCategoryName>
					$kategorie = $xml->createElement("QCategoryName");
					$kategorie->appendChild($xml->createTextNode($maschine->kategorie->mascus_kategorie_name));
					$product->appendChild($kategorie);

					// <Brand>Caterpillar</Brand>
					$hersteller = $xml->createElement("Brand");
					$hersteller->appendChild($xml->createTextNode($maschine->hersteller));
					$product->appendChild($hersteller);

					// <Model>325BNL</Model>
					$modell = $xml->createElement("Model");
					$modell->appendChild($xml->createTextNode($maschine->modell));
					$product->appendChild($modell);

					// <PriceOriginal>50000</PriceOriginal>
					$preis = $xml->createElement("PriceOriginal");
					$preis->appendChild($xml->createTextNode($maschine->preis));
					$product->appendChild($preis);

					// <PriceOriginalUnit>EUR</PriceOriginalUnit>
					$waehrung = $xml->createElement("PriceOriginalUnit");
					$waehrung->appendChild($xml->createTextNode($maschine->waehrung));
					$product->appendChild($waehrung);

					// <YearOfManufacture>1999</YearOfManufacture>
					$baujahr = $xml->createElement("YearOfManufacture");
					$baujahr->appendChild($xml->createTextNode($maschine->baujahr));
					$product->appendChild($baujahr);

					// <OtherInformation>bra maskin!</OtherInformation>
					$beschreibung = $xml->createElement("OtherInformation");
					$beschreibung->appendChild($xml->createTextNode($maschine->beschreibung));
					$product->appendChild($beschreibung);

					// <AttachmentPath1>D40F3F35_2.jpg</AttachmentPath1>
					$zaehler = 1;

					$bilder = explode(",", $maschine->bilder);
					foreach($bilder as $bild) {
						// Maximal 6 Bilder sind erlaubt
						if(strlen($bild) > 3 && $zaehler <= 6) {
							$anhang = $xml->createElement("AttachmentPath". $zaehler);
							$anhang->appendChild($xml->createTextNode(getPictureNameAndResize($rex_include_path, $config, $bild)));
							$product->appendChild($anhang);

							$zaehler ++;
						}
					}

					// <Salesman1>Thomas Meier</Salesman1>
					$kontakt = $xml->createElement("Salesman1");
					$kontakt->appendChild($xml->createTextNode($maschine->kontaktperson->vorname ." ". $maschine->kontaktperson->nachname));
					$product->appendChild($kontakt);

					// </product>
					$root->appendChild($product);

					// Rueckgabewerte hinzufuegen
					$exported_ids[$ids_counter] = $maschine->maschinen_id;
					$ids_counter++;
				}
			}
		}

		// XML in Datei schreiben
		try {
			if($xml->save($config->export_filename)) {
				return $exported_ids;
			}
			else {
				return false;
			}
		}
		catch(Exception $e) {
			print "Error: ". $e;
			return false;
		}
	}
}