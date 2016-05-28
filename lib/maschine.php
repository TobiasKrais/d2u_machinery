<?php
/**
 * Redaxo D2U Machines Addon
 * @author Tobias Krais
 * @author <a href="http://www.design-to-use.de">www.design-to-use.de</a>
 */

/**
 * Machine
 */
class Machine {
	/**
	 * @var int Machine id
	 */
	var $machine_id = 0;
	
	/**
	 * @var int Redaxo language id
	 */
	var $clang_id = 0;

	/**
	 * @var string Internal machine name. Also used for sorting.
	 */
	var $internal_name = "";

	/**
	 * @var string Machine name
	 */
	var $name = "";

	/**
	 * @var string[] Machine pictures
	 */
	var $pics = array();

	/**
	 * @var string Machine usage picture
	 */
	var $pic_usage = "";

	/**
	 * @var Category Machine category
	 */
	var $category = FALSE;

	/**
	 * @var Contact Machine contact person
	 */
	var $contact = FALSE;

	/**
	 * @var UsageAreas[] Usage areas
	 */
	var $usage_areas = array();

	/**
	 * @var int[] IDs of alternative machines
	 */
	var $alternative_machine_ids = array();

	/**
	 * @var int[] Machine feature ids
	 */
	var $feature_ids = array();

	/**
	 * @var int[] Machine service ids.
	 */
	var $service_ids = array();

	/**
	 * @var int[] Machine accessory ids.
	 */
	var $accessory_ids = array();

	/**
	 * @var string Machine accessory ids.
	 */
	var $product_number = "";

	/**
	 * @var int[] Machine business ids.
	 */
	var $industry_sector_ids = array();

	/**
	 * @var int Redaxo article id for additional software information
	 */
	var $article_id_software = 0;

	/**
	 * @var int Redaxo article id for additional service information
	 */
	var $article_id_service = 0;

	/**
	 * @var int[] Array with Redaxo article ids with customer success stories
	 */
	var $article_ids_references = array();

	/**
	 * @var String Status. Either "online" or "offline".
	 */
	var $online_status = "offline";
	
	/**
	 * @var int[] Videomanager ids
	 */
	var $videomanager_ids = array();

	/**
	 * @var int[] Certificate ids
	 */
	var $certificate_ids = array();

	/**
	 * @var int Construction id
	 */
	var $construction_id = 0;

	/**
	 * @var int Agitator type id
	 */
	var $agitator_type_id = 0;

	/**
	 * @var int[] Process type ids
	 */
	var $process_ids = 0;

	/**
	 * @var int[] Application ids
	 */
	var $application_ids = 0;

	/**
	 * @var int[] Material class ids
	 */
	var $material_class_ids = 0;

	/**
	 * @var int[] Tool ids
	 */
	var $tool_ids = 0;

	/**
	 * @var string Engine power
	 */
	var $engine_power = "";

	/**
	 * @var int Machine length
	 */
	var $length = 0;

	/**
	 * @var int Machine width
	 */
	var $width = 0;

	/**
	 * @var int Machine height
	 */
	var $height = 0;

	/**
	 * @var int Machine depth
	 */
	var $depth = 0;

	/**
	 * @var string Machine weight
	 */
	var $weight = "";	
	
	/**
	 * @var string Empty machine weight (if applicable)
	 */
	var $weight_empty = "";	
	
	/**
	 * @var string Machine operating voltage (v)
	 */
	var $operating_voltage_v = "";	
	
	/**
	 * @var string Machine operating voltage (hz)
	 */
	var $operating_voltage_hz = "";	
	
	/**
	 * @var string Machine operating voltage (a)
	 */
	var $operating_voltage_a = "";	

	/**
	 * @var string Teaser
	 */
	var $teaser = "";

	/**
	 * @var string Machine description
	 */
	var $description = "";

	/**
	 * @var string Machine technical description (if description is not sufficient)
	 */
	var $description_technical = "";

	/**
	 * @var string[] File names of PDF files for the machine
	 */
	var $pdfs = array();

	/**
	 * @var string Describes how machine is connected (if applicable)
	 */
	var $connection_infos = "";

	/**
	 * @var string Basic delivery set information
	 */
	var $delivery_set_basic = "";

	/**
	 * @var string Conversion delivery set information
	 */
	var $delivery_set_conversion = "";

	/**
	 * @var string Full delivery set information
	 */
	var $delivery_set_full = "";

	/**
	 * @var string Needs translation update? "no" or "yes"
	 */
	var $translation_needs_update = "delete";

	/**
	 * @var String URL der Maschine
	 */
	var $url = "";

	/**
	 * Fetches a machine object from database or creates an empty machine object.
	 * @param int $machine_id Database machine id
	 * @param int $clang_id Redaxo language id
	 */
	 public function __construct($machine_id, $clang_id) {
		$this->clang_id = $clang_id;
		$query = "SELECT * FROM ". rex::getTablePrefix() ."d2u_machinery_machines AS machines "
				."LEFT JOIN ". rex::getTablePrefix() ."d2u_machinery_machines_lang AS lang "
					."ON machines.machine_id = lang.machine_id "
				."WHERE machines.machine_id = ". $machine_id ." AND clang_id = ". $clang_id;
		$result = rex_sql::factory();
		$result->setQuery($query);
		$num_rows = $result->getRows();

		if($num_rows > 0) {
			$this->machine_id = $result->getValue("machine_id");;
			$this->internal_name = $result->getValue("internal_name");
			$this->name = $result->getValue("name");
			$this->pics = preg_grep('/^\s*$/s', explode(",", $result->getValue("pics")), PREG_GREP_INVERT);
			$this->pic_usage = $result->getValue("pic_usage");
			$this->category = new Category($result->getValue("category_id"), $clang_id);
			$this->alternative_machine_ids = preg_grep('/^\s*$/s', explode("|", $result->getValue("alternative_machine_ids")), PREG_GREP_INVERT);
			$this->service_ids = preg_grep('/^\s*$/s', explode(",", $result->getValue("service_ids")), PREG_GREP_INVERT);
			$this->accessory_ids = preg_grep('/^\s*$/s', explode(",", $result->getValue("accessory_ids")), PREG_GREP_INVERT);
			$this->product_number = $result->getValue("product_number");
			$this->article_id_software = $result->getValue("article_id_software");
			$this->article_id_service = $result->getValue("article_id_service");
			$this->article_ids_references = preg_grep('/^\s*$/s', explode(",", $result->getValue("article_ids_references")), PREG_GREP_INVERT);
			$this->online_status = $result->getValue("online_status");
			$this->engine_power = $result->getValue("engine_power");
			$this->length = $result->getValue("length");
			$this->width = $result->getValue("width");
			$this->height = $result->getValue("height");
			$this->depth = $result->getValue("depth");
			$this->weight = $result->getValue("weight");
			$this->weight_empty = $result->getValue("weight_empty");
			$this->operating_voltage_v = $result->getValue("operating_voltage_v");
			$this->operating_voltage_hz = $result->getValue("operating_voltage_hz");
			$this->operating_voltage_a = $result->getValue("operating_voltage_a");
			$this->teaser = $result->getValue("teaser");
			$this->description = $result->getValue("description");
			$this->description_technical = $result->getValue("description_technical");
			$this->pdfs = preg_grep('/^\s*$/s', explode(",", $result->getValue("pdfs")), PREG_GREP_INVERT);
			$this->connection_infos = $result->getValue("connection_infos");
			$this->delivery_set_basic = $result->getValue("delivery_set_basic");
			$this->delivery_set_conversion = $result->getValue("delivery_set_conversion");
			$this->delivery_set_full = $result->getValue("delivery_set_full");
			$this->translation_needs_update = $result->getValue("translation_needs_update");

			if(rex_plugin::get("d2u_machinery", "machine_features")->isAvailable()) {
				$this->feature_ids = preg_grep('/^\s*$/s', explode("|", $result->getValue("feature_ids")), PREG_GREP_INVERT);
			}

			if(rex_plugin::get("d2u_machinery", "industry_sectors")->isAvailable()) {
				$this->industry_sector_ids = preg_grep('/^\s*$/s', explode("|", $result->getValue("industry_sector_ids")), PREG_GREP_INVERT);
			}
		}
	}

	/**
	 * Changes the status of a machine
	 */
	public function changeStatus() {
		if($this->online_status == "online") {
			if($this->machine_id > 0) {
				$query = "UPDATE ". rex::getTablePrefix() ."d2u_machinery_machines "
					."SET online_status = 'offline' "
					."WHERE machine_id = ". $this->machine_id;
				$result = rex_sql::factory();
				$result->setQuery($query);
			}
			$this->online_status = "offline";
		}
		else {
			if($this->machine_id > 0) {
				$query = "UPDATE ". rex::getTablePrefix() ."d2u_machinery_machines "
					."SET online_status = 'online' "
					."WHERE machine_id = ". $this->machine_id;
				$result = rex_sql::factory();
				$result->setQuery($query);
			}
			$this->online_status = "online";			
		}
	}
	
	/**
	 * Deletes the object.
	 * @param int $delete_all If TRUE, all translations and main object are deleted. If 
	 * FALSE, only this translation will be deleted.
	 */
	public function delete($delete_all = TRUE) {
		if($delete_all) {
			$query_lang = "DELETE FROM ". rex::getTablePrefix() ."d2u_machinery_machines_lang "
				."WHERE machine_id = ". $this->machine_id;
			$result_lang = rex_sql::factory();
			$result_lang->setQuery($query_lang);

			$query = "DELETE FROM ". rex::getTablePrefix() ."d2u_machinery_machines "
				."WHERE machine_id = ". $this->machine_id;
			$result = rex_sql::factory();
			$result->setQuery($query);
		}
		else {
			$query_lang = "DELETE FROM ". rex::getTablePrefix() ."d2u_machinery_machines_lang "
				."WHERE machine_id = ". $this->machine_id ." AND clang_id = ". $this->clang_id;
			$result_lang = rex_sql::factory();
			$result_lang->setQuery($query_lang);
		}
	}
	
	/**
	 * Get all machines.
	 * @param int $clang_id Redaxo clang id.
	 * @param boolean $only_online Show only online machines
	 * @return Categories[] Array with Category objects.
	 */
	public static function getAll($clang_id, $only_online = FALSE) {
		$query = "SELECT machine_id FROM ". rex::getTablePrefix() ."d2u_machinery_machines ";
		if($only_online) {
			$query .= "WHERE online_status = 'yes' ";
		}
		$query .= "ORDER BY name";
		$result = rex_sql::factory();
		$result->setQuery($query);
		
		$machines = array();
		for($i = 0; $i < $result->getRows(); $i++) {
			$machines[] = new Machine($result->getValue("machine_id"), $clang_id);
			$result->next();
		}
		return $machines;
	}
	
	/*
	 * Gibt die URL einer Maschine zurueck.
	 * @return String URL für Maschine
	 */
	function getURL() {
		if($this->url != "") {
			return $this->url;
		}
		else {
			global $REX;

			$url = $REX['SERVER'];

			$pathname = '';
			if($REX['MOD_REWRITE'] == true && OOAddon::isActivated('seo42')) {
				// Mit SEO42
				require_once dirname(__FILE__) ."/../../seo42/classes/class.seo42_rewrite.inc.php";

				if (count($REX['CLANG']) > 1 && $this->clang_id != $REX['ADDON']['seo42']['settings']['hide_langslug']) {
					// Sprachkuerzel
					$pathname = seo42_appendToPath($pathname, $REX['ADDON']['seo42']['settings']['lang'][$this->clang_id]['code'],
							$REX['START_ARTICLE_ID'], $this->clang_id);
				}

				// Dann Redaxo Artikelfolge
				if($this->category->artikel_id) {
					$kategorie = OOCategory::getCategoryById($this->category->artikel_id,
							$this->clang_id);
					$hauptkategorien = $kategorie->getPathAsArray();
					for($i = 0; $i < count($hauptkategorien); $i++) {
						$hauptkategorie = OOCategory::getCategoryById($hauptkategorien[$i],
								$this->clang_id);
						if($hauptkategorie instanceof OOCategory) {
							$pathname = seo42_appendToPath($pathname, $hauptkategorie->getName(),
									$hauptkategorie->getId(), $this->clang_id);
						}
					}
					$pathname = seo42_appendToPath($pathname, $kategorie->getName(),
							$kategorie->getId(), $this->clang_id);
				}

				// Die Maschinenkategorie
				$pathname = seo42_appendToPath($pathname, $this->category->name,
						$this->category->artikel_id, $this->clang_id);

				// Jetzt endlich die Maschine
				$pathname = seo42_appendToPath($pathname, $this->name,
						$this->category->artikel_id, $this->clang_id);
				$pathname = substr($pathname, 0, -1) . $REX['ADDON']['seo42']['settings']['url_ending'];
			}
			else {
				// Ohne SEO42
				$parameterArray = array();
				$parameterArray['maschinen_id'] = $this->machine_id;
				$pathname = rex_getUrl($this->category->artikel_id, $this->clang_id, $parameterArray, "&");
			}

			$url .= $pathname;

			return $url;
		}
	}

	/**
	 * Updates or inserts the object into database.
	 * @return boolean TRUE if successful
	 */
	public function save() {
		$error = 0;

		// Save the not language specific part
		$pre_save_machine = new Machine($this->machine_id, $this->clang_id);
		if($this->machine_id == 0 || $pre_save_machine != $this) {
			$query = rex::getTablePrefix() ."d2u_machinery_machines SET "
					."name = '". $this->name ."', "
					."internal_name = '". $this->internal_name ."', "
					."pics = '". implode(",", $this->pics) ."', "
					."pic_usage = '". $this->pic_usage ."', "
					."category_id = ". $this->category->category_id .", "
					."alternative_machine_ids = '|". implode("|", $this->alternative_machine_ids) ."|', "
					."service_ids = '". implode(",", $this->service_ids) ."', "
					."accessory_ids = '". implode(",", $this->accessory_ids) ."', "
					."product_number = '". $this->product_number ."', "
					."article_id_software = '". $this->article_id_software ."', "
					."article_id_service = '". $this->article_id_service ."', "
					."article_ids_references = '". implode(",", $this->article_ids_references) ."', "
					."engine_power = '". $this->engine_power ."', "
					."length = '". $this->length ."', "
					."width = '". $this->width ."', "
					."height = '". $this->height ."', "
					."depth = '". $this->depth ."', "
					."weight = '". $this->weight ."', "
					."weight_empty = '". $this->weight_empty ."', "
					."operating_voltage_v = '". $this->operating_voltage_v ."', "
					."operating_voltage_hz = '". $this->operating_voltage_hz ."', "
					."operating_voltage_a = '". $this->operating_voltage_a ."' ";
			if(rex_plugin::get("d2u_machinery", "machine_features")->isAvailable()) {
				$query .= ", feature_ids = '|". implode("|", $this->feature_ids) ."|' ";
			}
			if(rex_plugin::get("d2u_machinery", "industry_sectors")->isAvailable()) {
				$query .= ", industry_sector_ids = '|". implode("|", $this->industry_sector_ids) ."|' ";
			}

			if($this->machine_id == 0) {
				$query = "INSERT INTO ". $query;
			}
			else {
				$query = "UPDATE ". $query ." WHERE machine_id = ". $this->machine_id;
			}

			$result = rex_sql::factory();
			$result->setQuery($query);
			if($this->machine_id == 0) {
				$this->machine_id = $result->getLastId();
				$error = $result->hasError();
			}
		}
		
		if($error == 0) {
			// Save the language specific part
			$pre_save_machine = new Machine($this->machine_id, $this->clang_id);
			if($pre_save_machine != $this) {
				$query = "REPLACE INTO ". rex::getTablePrefix() ."d2u_machinery_machines_lang SET "
						."machine_id = '". $this->machine_id ."', "
						."clang_id = '". $this->clang_id ."', "
						."teaser = '". htmlspecialchars($this->teaser) ."', "
						."description = '". htmlspecialchars($this->description) ."', "
						."description_technical = '". htmlspecialchars($this->description_technical) ."', "
						."pdfs = '". implode(",", $this->pdfs) ."', "
						."connection_infos = '". htmlspecialchars($this->connection_infos) ."', "
						."delivery_set_basic = '". htmlspecialchars($this->delivery_set_basic) ."', "
						."delivery_set_conversion = '". htmlspecialchars($this->delivery_set_conversion) ."', "
						."delivery_set_full = '". htmlspecialchars($this->delivery_set_full) ."', "
						."translation_needs_update = '". $this->translation_needs_update ."', "
						."updatedate = ". time() .", "
						."updateuser = '". rex::getUser()->getLogin() ."' ";
				$result = rex_sql::factory();
				$result->setQuery($query);
				$error = $result->hasError();
			}
		}
		return $error;
	}
}