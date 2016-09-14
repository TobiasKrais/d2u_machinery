<?php
/**
 * Redaxo D2U Machines Addon
 * @author Tobias Krais
 * @author <a href="http://www.design-to-use.de">www.design-to-use.de</a>
 */

/**
 * Machine Category
 */
class Category {
	/**
	 * @var int Database ID
	 */
	var $category_id = 0;
	
	/**
	 * @var int Redaxo clang id
	 */
	var $clang_id = 0;
	
	/**
	 * @var Category Father category object
	 */
	var $parent_category = FALSE;
	
	/**
	 * @var string Name
	 */
	var $name = "";
	
	/**
	 * @var string Teaser
	 */
	var $teaser = "";

	/**
	 * @var string Name of Usage area
	 */
	var $usage_area = "";
	
	/**
	 * @var string Preview picture file name 
	 */
	var $pic = "";
	
	/**
	 * @var string Language specific preview picture file name 
	 */
	var $pic_lang = "";
	
	/**
	 * @var string Usage area picture file name
	 */
	var $pic_usage = "";
	
	/**
	 * @var int ID of the corresponding machinerypark category
	 */
	var $export_machinerypark_category_id = 0;
	
	/**
	 * @var int ID of the corresponding Europe Machinery category
	 */
	var $export_europemachinery_category_id = 0;
	
	/**
	 * @var string Name of the corresponding Europe Machinery category
	 */
	var $export_europemachinery_category_name = "";
	
	/**
	 * @var string Name of the corresponding Mascus category
	 */
	var $export_mascus_category_name = "";
	
	/**
	 * @var string Title cutting range 
	 */
	var $sawing_machines_cutting_range_title = "";
		
	/**
	 * @var string Cutting range configurator file
	 */
	var $sawing_machines_cutting_range_file = "";

	/**
	 * @var string[] Array with PDF file names.
	 */
	var $pdfs = array();
	
	/**
	 * @var int Sort Priority
	 */
	var $prio = 0;
	
	/**
	 * @var string "yes" if translation needs update
	 */
	var $translation_needs_update = "delete";

	/**
	 * @var int Unix timestamp containing the last update date
	 */
	var $updatedate = 0;
	
	/**
	 * @var string Redaxo update user name
	 */
	var $updateuser = "";
	
	/**
	 * @var string URL
	 */
	var $url = "";

	/**
	 * @var string URL of cutting range configurator
	 */
	var $cutting_range_url = "";

	/**
	 * Constructor. Reads a categorie stored in database.
	 * @param int $category_id Category ID.
	 * @param int $clang_id Redaxo clang id.
	 */
	 public function __construct($category_id, $clang_id) {
		$this->clang_id = $clang_id;
		$query = "SELECT * FROM ". rex::getTablePrefix() ."d2u_machinery_categories AS categories "
				."LEFT JOIN ". rex::getTablePrefix() ."d2u_machinery_categories_lang AS lang "
					."ON categories.category_id = lang.category_id "
				."WHERE categories.category_id = ". $category_id ." AND clang_id = ". $this->clang_id ." ";
		$result = rex_sql::factory();
		$result->setQuery($query);
		$num_rows = $result->getRows();

		if ($num_rows > 0) {
			$this->category_id = $result->getValue("category_id");
			if($result->getValue("parent_category_id") > 0) {
				$this->parent_category = new Category($result->getValue("parent_category_id"), $clang_id);
			}
			$this->name = $result->getValue("name");
			$this->teaser = htmlspecialchars_decode($result->getValue("teaser"));
			$this->usage_area = $result->getValue("usage_area");
			$this->pic = $result->getValue("pic");
			$this->pic_lang = $result->getValue("pic_lang");
			$this->pic_usage = $result->getValue("pic_usage");
			$this->pdfs = preg_grep('/^\s*$/s', explode(",", $result->getValue("pdfs")), PREG_GREP_INVERT);
			$this->prio = $result->getValue("prio");
			$this->translation_needs_update = $result->getValue("translation_needs_update");
			$this->updatedate = $result->getValue("updatedate");
			$this->updateuser = $result->getValue("updateuser");

			// Export plugin fields
			if(rex_plugin::get("d2u_machinery", "export")->isAvailable()) {
				$this->export_europemachinery_category_id = $result->getValue("export_europemachinery_category_id");
				$this->export_europemachinery_category_name = $result->getValue("export_europemachinery_category_name");
				$this->export_machinerypark_category_id = $result->getValue("export_machinerypark_category_id");
				$this->export_mascus_category_name = $result->getValue("export_mascus_category_name");
			}
			
			// Sawing machines plugin fields
			if(rex_plugin::get("d2u_machinery", "sawing_machines")->isAvailable()) {
				$this->sawing_machines_cutting_range_title = $result->getValue("sawing_machines_cutting_range_title");
				$this->sawing_machines_cutting_range_file = $result->getValue("sawing_machines_cutting_range_file");
			}
		}
	}
	
	/**
	 * Deletes the object in all languages.
	 * @param int $delete_all If TRUE, all translations and main object are deleted. If 
	 * FALSE, only this translation will be deleted.
	 */
	public function delete($delete_all = TRUE) {
		if($delete_all) {
			$query_lang = "DELETE FROM ". rex::getTablePrefix() ."d2u_machinery_categories_lang "
				."WHERE category_id = ". $this->category_id;
			$result_lang = rex_sql::factory();
			$result_lang->setQuery($query_lang);

			$query = "DELETE FROM ". rex::getTablePrefix() ."d2u_machinery_categories "
				."WHERE category_id = ". $this->category_id;
			$result = rex_sql::factory();
			$result->setQuery($query);
		}
		else {
			$query_lang = "DELETE FROM ". rex::getTablePrefix() ."d2u_machinery_categories_lang "
				."WHERE category_id = ". $this->category_id ." AND clang_id = ". $this->clang_id;
			$result_lang = rex_sql::factory();
			$result_lang->setQuery($query_lang);
		}
	}
	
	/**
	 * Get all categories.
	 * @param int $clang_id Redaxo clang id.
	 * @return Category[] Array with Category objects.
	 */
	public static function getAll($clang_id) {
		$query = "SELECT lang.category_id FROM ". rex::getTablePrefix() ."d2u_machinery_categories_lang AS lang "
			."LEFT JOIN ". rex::getTablePrefix() ."d2u_machinery_categories AS categories "
				."ON lang.category_id = categories.category_id "
			."WHERE clang_id = ". $clang_id ." ";
		if(rex_addon::get('d2u_machinery')->getConfig('default_category_sort') == 'prio') {
			$query .= 'ORDER BY prio';
		}
		else {
			$query .= 'ORDER BY name';
		}
		$result = rex_sql::factory();
		$result->setQuery($query);
		
		$categories = array();
		for($i = 0; $i < $result->getRows(); $i++) {
			$categories[] = new Category($result->getValue("category_id"), $clang_id);
			$result->next();
		}
		return $categories;
	}
	
	/**
	 * Get the <link rel="canonical"> tag for page header.
	 * @return Complete tag.
	 */
	public function getCanonicalTag() {
		return '<link rel="canonical" href="'. $this->getURL() .'">';
	}
	
	/**
	 * Detects usage of this category as parent category and returns categories.
	 * @return Category[] Child categories.
	 */
	public function getChildren() {
		$query = "SELECT category_id FROM ". rex::getTablePrefix() ."d2u_machinery_categories "
			."WHERE parent_category_id = ". $this->category_id;
		$result = rex_sql::factory();
		$result->setQuery($query);
		
		$children =  array();
		for($i = 0; $i < $result->getRows(); $i++) {
			$children[] = new Category($result->getValue("category_id"), $this->clang_id);
			$result->next();
		}
		return $children;
	}
	
	/**
	 * Get technical data matrix for this category.
	 * @return mixed[] Key is the name of the sprog wildcard describing the field.
	 * Values are an array with the machine id as key and the value as value.
	 */
	public function getTechDataMatrix() {
		$machines = $this->getMachines();
		$matrix = array();
		$tech_data_arrays = array();
		$tech_data_wildcards = array();
		// Get technical data
		foreach($machines as $machine) {
			$tech_data_arrays[$machine->machine_id] = $machine->getTechnicalData();
		}
		// Get wildcards
		foreach($tech_data_arrays as $tech_data_array) {
			$tech_data_wildcards = array_unique(array_merge(array_keys($tech_data_array), $tech_data_wildcards));
		}
		// Create matrix
		foreach($tech_data_wildcards as $wildcard) {
			$matrix[$wildcard] = array();
			foreach($machines as $machine) {
				$tech_data_array = $tech_data_arrays[$machine->machine_id];
				if(array_key_exists($wildcard, $tech_data_array)) {
					$matrix[$wildcard][$machine->machine_id] = $tech_data_array[$wildcard];
				}
				else {
					$matrix[$wildcard][$machine->machine_id] = "";
				}
			}
		}
		return $matrix;
	}
	
	/**
	 * Get the <title> tag for page header.
	 * @return Complete title tag.
	 */
	public function getTitleTag() {
		return '<title>'. $this->name .' / '. rex::getServerName() .'</title>';
	}
	
	/**
	 * Get UsageArea matrix for this category.
	 * @return mixed[] Key is the name of the UsageArea. Values are the machine
	 * ids that use the UsageArea.
	 */
	public function getUsageAreaMatrix() {
		$usage_areas = UsageArea::getAll($this->clang_id, $this->category_id);
		$machines = $this->getMachines();
		
		$matrix = array();
		foreach($usage_areas as $usage_area) {
			$values = array();
			foreach($machines as $machine) {
				if(in_array($usage_area->usage_area_id, $machine->usage_area_ids)) {
					$values[] = $machine->machine_id;
				}
			}
			$matrix[$usage_area->name] = $values;
		}
		return $matrix;
	}
	
	/**
	 * Detects whether category is child or not.
	 * @return boolean TRUE if category has father.
	 */
	public function isChild() {
		if($this->parent_category === FALSE) {
			return FALSE;
		}
		else {
			return TRUE;
		}
	}
	
	/**
	 * Gets the machines of the category.
	 * @return Machine[] Machines in this category
	 */
	public function getMachines() {
		$query = "SELECT machine_id FROM ". rex::getTablePrefix() ."d2u_machinery_machines "
			."WHERE category_id = ". $this->category_id;
		$result = rex_sql::factory();
		$result->setQuery($query);
		
		$machines = array();
		for($i = 0; $i < $result->getRows(); $i++) {
			$machines[] = new Machine($result->getValue("machine_id"), $this->clang_id);
			$result->next();
		}
		return $machines;
	}

	/**
	 * Get the <meta rel="alternate" hreflang=""> tags for page header.
	 * @return Complete tags.
	 */
	public function getMetaAlternateHreflangTags() {
		$hreflang_tags = "";
		foreach(rex_clang::getAll() as $rex_clang) {
			if($rex_clang->getId() == $this->clang_id && $this->translation_needs_update != "delete") {
				$hreflang_tags .= '<link rel="alternate" type="text/html" hreflang="'. $rex_clang->getCode() .'" href="'. $this->getURL() .'" title="'. str_replace('"', '', $this->name) .'">';
			}
			else {
				$category = new Category($this->category_id, $rex_clang->getId());
				if($category->translation_needs_update != "delete") {
					$hreflang_tags .= '<link rel="alternate" type="text/html" hreflang="'. $rex_clang->getCode() .'" href="'. $category->getURL() .'" title="'. str_replace('"', '', $category->name) .'">';
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
	 * Gets the UsedMachines of the category if plugin ist installed.
	 * @return UsedMachine[] Used machines of this category
	 */
	public function getUsedMachines() {
		$usedMachines = array();
		if(rex_plugin::get("d2u_machinery", "used_machines")->isAvailable()) {
			$query = "SELECT used_machine_id FROM ". rex::getTablePrefix() ."d2u_machinery_used_machines "
				."WHERE category_id = ". $this->category_id;
			$result = rex_sql::factory();
			$result->setQuery($query);

			for($i = 0; $i < $result->getRows(); $i++) {
				$usedMachines = new UsedMachine($result->getValue("used_machine_id"), $this->clang_id);
				$result->next();
			}
		}
		return $usedMachines;
	}
	
	/*
	 * Returns the URL of this object.
	 * @param string $including_domain TRUE if Domain name should be included
	 * @return string URL
	 */
	public function getURL($including_domain = FALSE) {
		if($this->url == "") {
			// Without SEO Plugins
			$d2u_machinery = rex_addon::get("d2u_machinery");
				
			$parameterArray = array();
			$parameterArray['category_id'] = $this->category_id;
			$this->url = rex_getUrl($d2u_machinery->getConfig('article_id'), $this->clang_id, $parameterArray, "&");
		}

		if($including_domain) {
			return str_replace(rex::getServer(). '/', rex::getServer(), rex::getServer() . $this->url);
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
		$pre_save_category = new Category($this->category_id, $this->clang_id);
	
		// save priority, but only if new or changed
		if($this->prio != $pre_save_category->prio || $this->category_id == 0) {
			$this->setPrio();
		}

		if($this->category_id == 0 || $pre_save_category != $this) {
			$query = rex::getTablePrefix() ."d2u_machinery_categories SET "
					."parent_category_id = '". $this->parent_category->category_id ."', "
					."prio = '". $this->prio ."', "
					."pic = '". $this->pic ."', "
					."pic_usage = '". $this->pic_usage ."' ";
			if(rex_plugin::get("d2u_machinery", "export")->isAvailable()) {
				$query .= ", export_europemachinery_category_id = '". $this->export_europemachinery_category_id ."', "
					."export_europemachinery_category_name = '". $this->export_europemachinery_category_name ."', "
					."export_machinerypark_category_id = '". $this->export_machinerypark_category_id ."', "
					."export_mascus_category_name = '". $this->export_mascus_category_name ."' ";
			}

			if($this->category_id == 0) {
				$query = "INSERT INTO ". $query;
			}
			else {
				$query = "UPDATE ". $query ." WHERE category_id = ". $this->category_id;
			}

			$result = rex_sql::factory();
			$result->setQuery($query);
			if($this->category_id == 0) {
				$this->category_id = $result->getLastId();
				$error = $result->hasError();
			}
		}
		
		if($error == 0) {
			// Save the language specific part
			$pre_save_category = new Category($this->category_id, $this->clang_id);
			if($pre_save_category != $this) {
				$query = "REPLACE INTO ". rex::getTablePrefix() ."d2u_machinery_categories_lang SET "
						."category_id = '". $this->category_id ."', "
						."clang_id = '". $this->clang_id ."', "
						."name = '". htmlspecialchars($this->name) ."', "
						."teaser = '". htmlspecialchars($this->teaser) ."', "
						."usage_area = '". htmlspecialchars($this->usage_area) ."', "
						."pic_lang = '". $this->pic_lang ."', "
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
	 * Reassigns priority to all Categories in database.
	 */
	private function setPrio() {
		// Pull prios from database
		$query = "SELECT category_id, prio FROM ". rex::getTablePrefix() ."d2u_machinery_categories "
			."WHERE category_id <> ". $this->category_id ." ORDER BY prio";
		$result = rex_sql::factory();
		$result->setQuery($query);
		
		// When prio is too small, set at beginning
		if($this->prio <= 0) {
			$this->prio = 1;
		}
		
		// When prio is too high, simply add at end 
		if($this->prio > $result->getRows()) {
			$this->prio = $result->getRows() + 1;
		}

		$categories = array();
		for($i = 0; $i < $result->getRows(); $i++) {
			$categories[$result->getValue("prio")] = $result->getValue("category_id");
			$result->next();
		}
		array_splice($categories, ($this->prio - 1), 0, array($this->category_id));

		// Save all prios
		foreach($categories as $prio => $category_id) {
			$query = "UPDATE ". rex::getTablePrefix() ."d2u_machinery_categories "
					."SET prio = ". ($prio + 1) ." " // +1 because array_splice recounts at zero
					."WHERE category_id = ". $category_id;
			$result = rex_sql::factory();
			$result->setQuery($query);
		}
	}
}