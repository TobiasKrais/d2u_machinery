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
	var $pics = [];

	/**
	 * @var Category Machine category
	 */
	var $category = FALSE;

	/**
	 * @var int[] Usage area IDs
	 */
	var $usage_area_ids = [];

	/**
	 * @var int[] IDs of alternative machines
	 */
	var $alternative_machine_ids = [];

	/**
	 * @var int[] Machine feature ids
	 */
	var $feature_ids = [];

	/**
	 * @var string Machine accessory ids.
	 */
	var $product_number = "";

	/**
	 * @var int[] Machine business ids.
	 */
	var $industry_sector_ids = [];

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
	var $article_ids_references = [];

	/**
	 * @var String Status. Either "online" or "offline".
	 */
	var $online_status = "offline";
	
	/**
	 * @var int[] Certificate ids
	 */
	var $certificate_ids = [];

	/**
	 * @var int Agitator type id
	 */
	var $agitator_type_id = 0;

	/**
	 * @var int Max. viscosity in mPas
	 */
	var $viscosity = 0;

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
	 * @var int Sort Priority
	 */
	var $priority = 0;
	
	/**
	 * @var string Language specific name
	 */
	var $lang_name = "";

	/**
	 * @var string Teaser
	 */
	var $teaser = "";

	/**
	 * @var string Machine description
	 */
	var $description = "";

	/**
	 * @var string[] File names of PDF files for the machine
	 */
	var $pdfs = [];

	/**
	 * @var string Needs translation update? "no", "yes" or "delete"
	 */
	var $translation_needs_update = "delete";

	/**
	 * @var String URL der Maschine
	 */
	private $url = "";

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
					."AND clang_id = ". $this->clang_id ." "
				."WHERE machines.machine_id = ". $machine_id;
		$result = rex_sql::factory();
		$result->setQuery($query);
		$num_rows = $result->getRows();

		if($num_rows > 0) {
			$this->machine_id = $result->getValue("machine_id");
			$this->internal_name = $result->getValue("internal_name");
			$this->name = $result->getValue("name");
			$this->pics = preg_grep('/^\s*$/s', explode(",", $result->getValue("pics")), PREG_GREP_INVERT);
			$this->category = new Category($result->getValue("category_id"), $clang_id);
			$this->alternative_machine_ids = preg_grep('/^\s*$/s', explode("|", $result->getValue("alternative_machine_ids")), PREG_GREP_INVERT);
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
			$this->operating_voltage_v = $result->getValue("operating_voltage_v");
			$this->operating_voltage_hz = $result->getValue("operating_voltage_hz");
			$this->operating_voltage_a = $result->getValue("operating_voltage_a");
			$this->lang_name = $result->getValue("lang_name");
			$this->teaser = htmlspecialchars_decode($result->getValue("teaser"));
			$this->description = htmlspecialchars_decode($result->getValue("description"));
			$this->pdfs = preg_grep('/^\s*$/s', explode(",", $result->getValue("pdfs")), PREG_GREP_INVERT);
			$this->priority = $result->getValue("priority");
			if($result->getValue("translation_needs_update") != "") {
				$this->translation_needs_update = $result->getValue("translation_needs_update");
			}

			// Convert redaxo://123 to URL
			$this->description = preg_replace_callback(
					'@redaxo://(\d+)(?:-(\d+))?/?@i',
					create_function(
							'$matches',
							'return rex_getUrl($matches[1], isset($matches[2]) ? $matches[2] : "");'
					),
					$this->description
			);
			
			if(rex_plugin::get("d2u_machinery", "industry_sectors")->isAvailable()) {
				$this->industry_sector_ids = preg_grep('/^\s*$/s', explode("|", $result->getValue("industry_sector_ids")), PREG_GREP_INVERT);
			}

			if(rex_plugin::get("d2u_machinery", "machine_certificates_extension")->isAvailable()) {
				$this->certificate_ids = preg_grep('/^\s*$/s', explode("|", $result->getValue("certificate_ids")), PREG_GREP_INVERT);
			}

			if(rex_plugin::get("d2u_machinery", "machine_features_extension")->isAvailable()) {
				$this->feature_ids = preg_grep('/^\s*$/s', explode("|", $result->getValue("feature_ids")), PREG_GREP_INVERT);
			}

			if(rex_plugin::get("d2u_machinery", "machine_agitator_extension")->isAvailable()) {
				$this->agitator_type_id = $result->getValue("agitator_type_id");
				$this->viscosity = $result->getValue("viscosity");
			}

			if(rex_plugin::get("d2u_machinery", "machine_usage_area_extension")->isAvailable()) {
				$this->usage_area_ids = preg_grep('/^\s*$/s', explode("|", $result->getValue("usage_area_ids")), PREG_GREP_INVERT);
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
	 * @return Machines[] Array with Machine objects.
	 */
	public static function getAll($clang_id, $only_online = FALSE) {
		$query = "SELECT machine_id FROM ". rex::getTablePrefix() ."d2u_machinery_machines ";
		if($only_online) {
			$query .= "WHERE online_status = 'online' ";
		}
		if(rex_addon::get('d2u_machinery')->getConfig('default_machine_sort') == 'priority') {
			$query .= 'ORDER BY priority ASC';
		}
		else {
			$query .= 'ORDER BY name ASC';
		}
		$result = rex_sql::factory();
		$result->setQuery($query);
		
		$machines = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$machines[] = new Machine($result->getValue("machine_id"), $clang_id);
			$result->next();
		}
		return $machines;
	}
	
	/**
	 * Get the <link rel="canonical"> tag for page header.
	 * @return Complete tag.
	 */
	public function getCanonicalTag() {
		return '<link rel="canonical" href="'. $this->getURL() .'">';
	}
	
	/**
	 * Get Feature objects related to this machine.
	 * @return Feature[] Array with Feature objects.
	 */
	public function getFeatures() {
		$features = [];
		foreach ($this->feature_ids as $feature_id) {
			$features[] = new Feature($feature_id, $this->clang_id);
		}
		return $features;
	}

	/**
	 * Get the <meta rel="alternate" hreflang=""> tags for page header.
	 * @return Complete tags.
	 */
	public function getMetaAlternateHreflangTags() {
		$hreflang_tags = "";
		foreach(rex_clang::getAll() as $rex_clang) {
			if($rex_clang->getId() == $this->clang_id && $this->translation_needs_update != "delete") {
				$hreflang_tags .= '<link rel="alternate" type="text/html" hreflang="'. $rex_clang->getCode() .'" href="'. $this->getURL() .'" title="'. str_replace('"', '', $this->category->name .': '. $this->name) .'">';
			}
			else {
				$machine = new Machine($this->machine_id, $rex_clang->getId());
				if($machine->translation_needs_update != "delete") {
					$hreflang_tags .= '<link rel="alternate" type="text/html" hreflang="'. $rex_clang->getCode() .'" href="'. $machine->getURL() .'" title="'. str_replace('"', '', $machine->category->name .': '. $machine->name) .'">';
				}
			}
		}
		return $hreflang_tags;
	}
	
	/**
	 * Get the <meta name="description"> tag for page header.
	 * @return Complete tag.
	 */
	public function getMetaDescriptionTag() {
		return '<meta name="description" content="'. $this->teaser .'">';
	}
	
	/**
	 * Gets the machines reffering to this machine as alternate machine.
	 * @return Machine[] Machines reffering to this machine as alternate machine.
	 */
	public function getRefferingMachines() {
		$query = "SELECT machine_id FROM ". rex::getTablePrefix() ."d2u_machinery_machines "
			."WHERE alternative_machine_ids LIKE '%|". $this->machine_id ."|%'";
		$result = rex_sql::factory();
		$result->setQuery($query);
		
		$machines = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$machines[] = new Machine($result->getValue("machine_id"), $this->clang_id);
			$result->next();
		}
		return $machines;
	}
	
	/**
	 * Gets the used machines reffering to this machine.
	 * @return UsedMachine[] Used machines reffering to this machine.
	 */
	public function getRefferingUsedMachines() {
		if(rex_plugin::get("d2u_machinery", "used_machines")->isAvailable()) {
			$query = "SELECT used_machine_id FROM ". rex::getTablePrefix() ."d2u_machinery_used_machines "
				."WHERE machine_id =  ". $this->machine_id;
			$result = rex_sql::factory();
			$result->setQuery($query);

			$used_machines = [];
			for($i = 0; $i < $result->getRows(); $i++) {
				$used_machines[] = new UsedMachine($result->getValue("used_machine_id"), $this->clang_id);
				$result->next();
			}
			return $used_machines;
		}
		else {
			return [];
		}
	}
	
	/**
	 * Get Technical Data as array.
	 * @return string[] Array with technical data. The key is the translation
	 * placeholder, the value is the value.
	 */
	public function getTechnicalData() {
		$tech_data = [];
		
		// Get placeholder wildcard tags
		$sprog = rex_addon::get("sprog");
		$tag_open = $sprog->getConfig('wildcard_open_tag');
		$tag_close = $sprog->getConfig('wildcard_close_tag');
		
		// Max. viscosity
		if(rex_plugin::get("d2u_machinery", "machine_agitator_extension")->isAvailable() && $this->viscosity > 0) {
			$tech_data[$tag_open . "d2u_machinery_agitators_viscosity" . $tag_close] = $this->viscosity ." ". $tag_open . "d2u_machinery_agitators_mpas" . $tag_close;
		}

		// Engine power
		if($this->engine_power != "") {
			$tech_data[$tag_open . "d2u_machinery_engine_power" . $tag_close] = $this->engine_power ." ". $tag_open . "d2u_machinery_unit_kw" . $tag_close;
		}

		// Operating voltage
		if($this->operating_voltage_v != "") {
			$v = $this->operating_voltage_v ." ". $tag_open . "d2u_machinery_unit_v" . $tag_close;
			$h = $this->operating_voltage_hz == "" ? "" : " / ". $this->operating_voltage_hz ." ". $tag_open . "d2u_machinery_unit_hz" . $tag_close;
			$a = $this->operating_voltage_a == "" ? "" : " / ". $this->operating_voltage_a ." ". $tag_open . "d2u_machinery_unit_a" . $tag_close;
			$tech_data[$tag_open . "d2u_machinery_operating_voltage" . $tag_close] = $v . $h . $a;
		}
		
		// Dimensions
		if($this->length > 0 && $this->width > 0 && $this->height > 0) {
			$tech_data[$tag_open . "d2u_machinery_dimensions_length_width_height" . $tag_close] = $this->length .' x '. $this->width .' x '. $this->height ." ". $tag_open . "d2u_machinery_unit_mm" . $tag_close;
		}
		if($this->depth > 0 && $this->width > 0 && $this->height > 0) {
			$tech_data[$tag_open . "d2u_machinery_dimensions_width_height_depth" . $tag_close] = $this->width .' x '. $this->height	.' x '. $this->depth ." ". $tag_open . "d2u_machinery_unit_mm" . $tag_close;
		}

		// Weight
		if($this->weight != "") {
			$tech_data[$tag_open . "d2u_machinery_weight" . $tag_close] = $this->weight ." ". $tag_open . "d2u_machinery_unit_kg" . $tag_close;
		}
		
		return $tech_data;
	}
	
	/**
	 * Get the <title> tag for page header.
	 * @return Complete title tag.
	 */
	public function getTitleTag() {
		return '<title>'. $this->name .' / '. $this->category->name .' / '. rex::getServerName() .'</title>';
	}
	
	/**
	 * Returns the URL of this object.
	 * @param string $including_domain TRUE if Domain name should be included
	 * @return string URL
	 */
	public function getURL($including_domain = FALSE) {
		if($this->url == "") {
			$d2u_machinery = rex_addon::get("d2u_machinery");
				
			$parameterArray = [];
			$parameterArray['machine_id'] = $this->machine_id;
			$this->url = rex_getUrl($d2u_machinery->getConfig('article_id'), $this->clang_id, $parameterArray, "&");
		}

		if($including_domain) {
			return str_replace(rex::getServer(). '/', rex::getServer(), rex::getServer() . $this->url) ;
		}
		else {
			return $this->url;
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

		// save priority, but only if new or changed
		if($this->priority != $pre_save_machine->priority || $this->machine_id == 0) {
			$this->setPriority();
		}

		if($this->machine_id == 0 || $pre_save_machine != $this) {
			$query = rex::getTablePrefix() ."d2u_machinery_machines SET "
					."name = '". $this->name ."', "
					."internal_name = '". $this->internal_name ."', "
					."pics = '". implode(",", $this->pics) ."', "
					."category_id = ". $this->category->category_id .", "
					."alternative_machine_ids = '|". implode("|", $this->alternative_machine_ids) ."|', "
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
					."operating_voltage_v = '". $this->operating_voltage_v ."', "
					."operating_voltage_hz = '". $this->operating_voltage_hz ."', "
					."operating_voltage_a = '". $this->operating_voltage_a ."' ";
			if(rex_plugin::get("d2u_machinery", "industry_sectors")->isAvailable()) {
				$query .= ", industry_sector_ids = '|". implode("|", $this->industry_sector_ids) ."|' ";
			}
			if(rex_plugin::get("d2u_machinery", "machine_certificates_extension")->isAvailable()) {
				$query .= ", certificate_ids = '|". implode("|", $this->certificate_ids) ."|' ";
			}
			if(rex_plugin::get("d2u_machinery", "machine_features_extension")->isAvailable()) {
				$query .= ", feature_ids = '|". implode("|", $this->feature_ids) ."|' ";
			}
			if(rex_plugin::get("d2u_machinery", "machine_agitator_extension")->isAvailable()) {
				$query .= ", agitator_type_id = ". $this->agitator_type_id ." ";
				$query .= ", viscosity = ". $this->viscosity ." ";
			}
			if(rex_plugin::get("d2u_machinery", "machine_usage_area_extension")->isAvailable()) {
				$query .= ", usage_area_ids = '|". implode("|", $this->usage_area_ids) ."|' ";
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
						."lang_name = '". $this->lang_name ."', "
						."teaser = '". htmlspecialchars($this->teaser) ."', "
						."description = '". htmlspecialchars($this->description) ."', "
						."pdfs = '". implode(",", $this->pdfs) ."', "
						."translation_needs_update = '". $this->translation_needs_update ."', "
						."updatedate = ". time() .", "
						."updateuser = '". rex::getUser()->getLogin() ."' ";
				$result = rex_sql::factory();
				$result->setQuery($query);
				$error = $result->hasError();
			}
		}

		// Update URLs
		if(rex_addon::get("url")->isAvailable()) {
			UrlGenerator::generatePathFile([]);
		}
		
		return $error;
	}
	
	/**
	 * Reassigns priority to all Machines in database.
	 */
	private function setPriority() {
		// Pull prios from database
		$query = "SELECT machine_id, priority FROM ". rex::getTablePrefix() ."d2u_machinery_machines "
			."WHERE machine_id <> ". $this->machine_id ." ORDER BY priority";
		$result = rex_sql::factory();
		$result->setQuery($query);
		
		// When priority is too small, set at beginning
		if($this->priority <= 0) {
			$this->priority = 1;
		}
		
		// When prio is too high, simply add at end 
		if($this->priority > $result->getRows()) {
			$this->priority = $result->getRows() + 1;
		}

		$machines = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$machines[$result->getValue("priority")] = $result->getValue("machine_id");
			$result->next();
		}
		array_splice($machines, ($this->priority - 1), 0, array($this->machine_id));

		// Save all prios
		foreach($machines as $prio => $machine_id) {
			$query = "UPDATE ". rex::getTablePrefix() ."d2u_machinery_machines "
					."SET priority = ". ($prio + 1) ." " // +1 because array_splice recounts at zero
					."WHERE machine_id = ". $machine_id;
			$result = rex_sql::factory();
			$result->setQuery($query);
		}
	}
}