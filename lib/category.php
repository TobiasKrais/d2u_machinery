<?php
/**
 * Redaxo D2U Machines Addon
 * @author Tobias Krais
 * @author <a href="http://www.design-to-use.de">www.design-to-use.de</a>
 */

/**
 * Machine Category
 */
class Category implements \D2U_Helper\ITranslationHelper{
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
	 * @var int ID of the corresponding Europe Machinery category,
	 * @link http://www.agriaffaires.com/html/rubtableauliste_compact_de.html Full List
	 */
	var $export_europemachinery_category_id = 0;
	
	/**
	 * @var string Name of the corresponding Europe Machinery category
	 * @link http://www.agriaffaires.com/html/rubtableauliste_compact_de.html Full List
	 */
	var $export_europemachinery_category_name = "";
	
	/**
	 * @var string Name of the corresponding Mascus category
	 */
	var $export_mascus_category_name = "";
	
	/**
	 * @var string Title cutting range 
	 */
	var $steel_processing_saw_cutting_range_title = "";
		
	/**
	 * @var string Cutting range configurator file
	 */
	var $steel_processing_saw_cutting_range_file = "";

	/**
	 * @var string Show agitators in machines of this category. Values are "hide" or "show"
	 */
	var $show_agitators = "hide";

	/**
	 * @var string[] Array with PDF file names.
	 */
	var $pdfs = [];
	
	/**
	 * @var Video[] Videomanager videos
	 */
	var $videos = [];
	
	/**
	 * @var int Sort Priority
	 */
	var $priority = 0;
	
	/**
	 * @var string For used machines: offer type. Either "rent" or "sale".
	 */
	private $offer_type;
	
	/**
	 * @var string "yes" if translation needs update
	 */
	var $translation_needs_update = "delete";

	/**
	 * @var string Unix timestamp containing the last update date
	 */
	var $updatedate = "";
	
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
	 * Constructor. Reads a category stored in database.
	 * @param int $category_id Category ID.
	 * @param int $clang_id Redaxo clang id.
	 */
	 public function __construct($category_id, $clang_id) {
		$this->clang_id = $clang_id;
		$query = "SELECT * FROM ". \rex::getTablePrefix() ."d2u_machinery_categories AS categories "
				."LEFT JOIN ". \rex::getTablePrefix() ."d2u_machinery_categories_lang AS lang "
					."ON categories.category_id = lang.category_id "
					."AND clang_id = ". $this->clang_id ." "
				."WHERE categories.category_id = ". $category_id;
		$result = \rex_sql::factory();
		$result->setQuery($query);
		$num_rows = $result->getRows();

		if ($num_rows > 0) {
			$this->category_id = $result->getValue("category_id");
			if($result->getValue("parent_category_id") > 0) {
				$this->parent_category = new Category($result->getValue("parent_category_id"), $clang_id);
			}
			$this->name = stripslashes(htmlspecialchars_decode($result->getValue("name")));
			$this->teaser = stripslashes(htmlspecialchars_decode($result->getValue("teaser")));
			$this->usage_area = stripslashes(htmlspecialchars_decode($result->getValue("usage_area")));
			$this->pic = $result->getValue("pic");
			$this->pic_lang = $result->getValue("pic_lang");
			$this->pic_usage = $result->getValue("pic_usage");
			$pdfs = preg_grep('/^\s*$/s', explode(",", $result->getValue("pdfs")), PREG_GREP_INVERT);
			$this->pdfs = is_array($pdfs) ? $pdfs : (strlen($pdfs) > 4 ? [$pdfs] : []);
			$this->priority = $result->getValue("priority");
			if($result->getValue("translation_needs_update") != "") {
				$this->translation_needs_update = $result->getValue("translation_needs_update");
			}
			$this->updatedate = $result->getValue("updatedate");
			$this->updateuser = $result->getValue("updateuser");

			// Export plugin fields
			if(rex_plugin::get("d2u_machinery", "export")->isAvailable()) {
				$this->export_europemachinery_category_id = $result->getValue("export_europemachinery_category_id");
				$this->export_europemachinery_category_name = $result->getValue("export_europemachinery_category_name");
				$this->export_machinerypark_category_id = $result->getValue("export_machinerypark_category_id");
				$this->export_mascus_category_name = $result->getValue("export_mascus_category_name");
			}
			
			// Agitator fields
			if(rex_plugin::get("d2u_machinery", "machine_agitator_extension")->isAvailable()) {
				$this->show_agitators = $result->getValue("show_agitators");
			}

			// Sawing machines plugin fields
			if(rex_plugin::get("d2u_machinery", "machine_steel_processing_extension")->isAvailable()) {
				$this->steel_processing_saw_cutting_range_title = $result->getValue("steel_processing_saw_cutting_range_title");
				$this->steel_processing_saw_cutting_range_file = $result->getValue("steel_processing_saw_cutting_range_file");
			}

			// Videos
			if(\rex_addon::get('d2u_videos')->isAvailable() && $result->getValue("video_ids") != "") {
				$video_ids = preg_grep('/^\s*$/s', explode("|", $result->getValue("video_ids")), PREG_GREP_INVERT);
				foreach ($video_ids as $video_id) {
					if($video_id > 0) {
						$video = new Video($video_id, $clang_id);
						if($video->getVideoURL() != "") {
							$this->videos[$video_id] = $video;
						}
					}
				}
			}
		}
		
		// For used machines: set offer type
		if(rex_plugin::get("d2u_machinery", "used_machines")->isAvailable()) {
			$d2u_machinery = rex_addon::get("d2u_machinery");
			$current_article_id = \rex::isBackend() ? 0 : rex_article::getCurrent()->getId();
			if($d2u_machinery->getConfig('used_machine_article_id_rent') == $d2u_machinery->getConfig('used_machine_article_id_sale')
					&& ($current_article_id == $d2u_machinery->getConfig('used_machine_article_id_rent') || $current_article_id == $d2u_machinery->getConfig('used_machine_article_id_sale'))) {
				if(filter_input(INPUT_GET, 'offer_type') == "rent" || filter_input(INPUT_GET, 'offer_type') == "sale") {
					$this->offer_type = filter_input(INPUT_GET, 'offer_type');
				}
				else {
					// Get offer type from used machine
					$used_category_id = 0;
					if(\rex_addon::get("url")->isAvailable()) {
						$url_namespace = d2u_addon_frontend_helper::getUrlNamespace();
						if($url_namespace === "used_rent_category_id" || $url_namespace === "used_sale_category_id") {
							$used_category_id = d2u_addon_frontend_helper::getUrlId();
						}
					}
					else if(filter_input(INPUT_GET, 'used_rent_category_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || filter_input(INPUT_GET, 'used_sale_category_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0) {
						$used_category_id = filter_input(INPUT_GET, 'used_rent_category_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 ? filter_input(INPUT_GET, 'used_rent_category_id', FILTER_VALIDATE_INT) : filter_input(INPUT_GET, 'used_sale_category_id', FILTER_VALIDATE_INT);
					}
					if($used_category_id > 0) {
						$this->offer_type = UsedMachine::getOfferTypeForUsedMachineId($used_category_id);
					}
				}
			}
			else if($current_article_id == $d2u_machinery->getConfig('used_machine_article_id_rent')) {
				$this->offer_type = "rent";
			}
			else {
				$this->offer_type = "sale";
			}
		}
	}
	
	/**
	 * Deletes the object in all languages.
	 * @param int $delete_all If TRUE, all translations and main object are deleted. If 
	 * FALSE, only this translation will be deleted.
	 */
	public function delete($delete_all = TRUE) {
		$query_lang = "DELETE FROM ". \rex::getTablePrefix() ."d2u_machinery_categories_lang "
			."WHERE category_id = ". $this->category_id
			. ($delete_all ? '' : ' AND clang_id = '. $this->clang_id) ;
		$result_lang = \rex_sql::factory();
		$result_lang->setQuery($query_lang);
		
		// If no more lang objects are available, delete
		$query_main = "SELECT * FROM ". \rex::getTablePrefix() ."d2u_machinery_categories_lang "
			."WHERE category_id = ". $this->category_id;
		$result_main = \rex_sql::factory();
		$result_main->setQuery($query_main);
		if($result_main->getRows() == 0) {
			$query = "DELETE FROM ". \rex::getTablePrefix() ."d2u_machinery_categories "
				."WHERE category_id = ". $this->category_id;
			$result = \rex_sql::factory();
			$result->setQuery($query);

			// reset priorities
			$this->setPriority(TRUE);			
		}
	}
	
	/**
	 * Get all categories.
	 * @param int $clang_id Redaxo clang id.
	 * @return Category[] Array with Category objects.
	 */
	public static function getAll($clang_id) {
		$query = "SELECT lang.category_id FROM ". \rex::getTablePrefix() ."d2u_machinery_categories_lang AS lang "
			."LEFT JOIN ". \rex::getTablePrefix() ."d2u_machinery_categories AS categories "
				."ON lang.category_id = categories.category_id "
			."WHERE clang_id = ". $clang_id ." ";
		if(\rex_addon::get('d2u_machinery')->getConfig('default_category_sort') == 'priority') {
			$query .= 'ORDER BY priority';
		}
		else {
			$query .= 'ORDER BY name';
		}
		$result = \rex_sql::factory();
		$result->setQuery($query);
		
		$categories = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$category = new Category($result->getValue("category_id"), $clang_id);
			$categories[] = $category;
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
		$query = "SELECT category_id FROM ". \rex::getTablePrefix() ."d2u_machinery_categories "
			."WHERE parent_category_id = ". $this->category_id;
		$result = \rex_sql::factory();
		$result->setQuery($query);
		
		$children =  [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$children[] = new Category($result->getValue("category_id"), $this->clang_id);
			$result->next();
		}
		return $children;
	}
	
	/**
	 * Get technical data matrix for this category.
	 * @return mixed[] Key is the name of the sprog wildcard describing the field.
	 * Values are the unit ('unit' => $unit) and an array called 'machine_ids'
	 * with the machine id as key and the value as value.
	 */
	public function getTechDataMatrix() {
		$machines = $this->getMachines(TRUE);
		$matrix = [];
		$tech_data_arrays = [];
		$tech_data_wildcards = [];
		// Get technical data
		foreach($machines as $machine) {
			$tech_data_arrays[$machine->machine_id] = $machine->getTechnicalData();
		}
		// Get wildcards
		foreach($tech_data_arrays as $tech_data_array) {
			foreach($tech_data_array as $tech_data) {
				$tech_data_wildcards[$tech_data['description']] = $tech_data['unit'];
			}
		}
		// Create matrix
		foreach($tech_data_wildcards as $wildcard => $unit) {
			$key = ['description' => $wildcard, 'unit' => $unit];
			$matrix[$wildcard] = ['unit' => $unit, 'machine_ids' => []];
			foreach($machines as $machine) {
				$tech_data_array = $tech_data_arrays[$machine->machine_id];
				$matrix[$wildcard]['machine_ids'][$machine->machine_id] = "";
				foreach($tech_data_array as $techdata) {
					if($techdata['description'] == $wildcard) {
						$matrix[$wildcard]['machine_ids'][$machine->machine_id] = $techdata['value'];
						break;
					}
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
		return '<title>'. $this->name .' / '. \rex::getServerName() .'</title>';
	}
	
	/**
	 * Get UsageArea matrix for this category.
	 * @return mixed[] Key is the name of the UsageArea. Values are the machine
	 * ids that use the UsageArea.
	 */
	public function getUsageAreaMatrix() {
		$usage_areas = UsageArea::getAll($this->clang_id, $this->category_id);
		$machines = $this->getMachines(TRUE);
		
		$matrix = [];
		foreach($usage_areas as $usage_area) {
			$values = [];
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
	 * Gets the machines of the category.
	 * @param boolean $online_only TRUE if only online machines should be returned.
	 * @return Machine[] Machines in this category
	 */
	public function getMachines($online_only = FALSE) {
		$query = "SELECT lang.machine_id, IF(lang.lang_name IS NULL or lang.lang_name = '', machines.name, lang.lang_name) as machine_name "
			. "FROM ". \rex::getTablePrefix() ."d2u_machinery_machines_lang AS lang "
			."LEFT JOIN ". \rex::getTablePrefix() ."d2u_machinery_machines AS machines "
					."ON lang.machine_id = machines.machine_id "
			."WHERE category_id = ". $this->category_id ." AND clang_id = ". $this->clang_id .' ';
		if($online_only) {
			$query .= "AND online_status = 'online' ";
		}
		if(rex_config::get('d2u_machinery', 'default_machine_sort', '') == 'priority') {
			$query .= 'ORDER BY priority ASC';
		}
		else {
			$query .= 'ORDER BY machine_name ASC';
		}
		$result = \rex_sql::factory();
		$result->setQuery($query);
		
		$machines = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$machines[$result->getValue("machine_id")] = new Machine($result->getValue("machine_id"), $this->clang_id);
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
		foreach(rex_clang::getAll(TRUE) as $rex_clang) {
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
	 * @param boolean $online_only If true only online used machines are returned
	 * @return UsedMachine[] Used machines of this category
	 */
	public function getUsedMachines($online_only = FALSE) {
		$usedMachines = [];
		if(rex_plugin::get("d2u_machinery", "used_machines")->isAvailable()) {
			$query = "SELECT lang.used_machine_id FROM ". \rex::getTablePrefix() ."d2u_machinery_used_machines_lang AS lang "
				."LEFT JOIN ". \rex::getTablePrefix() ."d2u_machinery_used_machines AS used_machines "
					."ON lang.used_machine_id = used_machines.used_machine_id "
				."WHERE category_id = ". $this->category_id ." AND clang_id = ". $this->clang_id ." ";
			if($online_only) {
				$query .= "AND online_status = 'online' ";
			}
			if($this->offer_type != "") {
				$query .= "AND offer_type = '". $this->offer_type ."' ";
			}
			$query .= "ORDER BY manufacturer, name";
			$result = \rex_sql::factory();
			$result->setQuery($query);

			for($i = 0; $i < $result->getRows(); $i++) {
				$usedMachines[] = new UsedMachine($result->getValue("used_machine_id"), $this->clang_id);
				$result->next();
			}
		}
		return $usedMachines;
	}
	
	/**
	 * Get objects concerning translation updates
	 * @param int $clang_id Redaxo language ID
	 * @param string $type 'update' or 'missing'
	 * @return Category[] Array with Category objects.
	 */
	public static function getTranslationHelperObjects($clang_id, $type) {
		$query = 'SELECT category_id FROM '. \rex::getTablePrefix() .'d2u_machinery_categories_lang '
				."WHERE clang_id = ". $clang_id ." AND translation_needs_update = 'yes' "
				.'ORDER BY name';
		if($type == 'missing') {
			$query = 'SELECT main.category_id FROM '. \rex::getTablePrefix() .'d2u_machinery_categories AS main '
					.'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_categories_lang AS target_lang '
						.'ON main.category_id = target_lang.category_id AND target_lang.clang_id = '. $clang_id .' '
					.'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_categories_lang AS default_lang '
						.'ON main.category_id = default_lang.category_id AND default_lang.clang_id = '. \rex_config::get('d2u_helper', 'default_lang') .' '
					."WHERE target_lang.category_id IS NULL "
					.'ORDER BY default_lang.name';
			$clang_id = \rex_config::get('d2u_helper', 'default_lang');
		}
		$result = \rex_sql::factory();
		$result->setQuery($query);

		$objects = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$objects[] = new Category($result->getValue("category_id"), $clang_id);
			$result->next();
		}
		
		return $objects;
    }

	/*
	 * Returns the URL of this object.
	 * @param string $including_domain TRUE if Domain name should be included
	 * @return string URL
	 */
	public function getURL($including_domain = FALSE) {
		if($this->url == "") {
			$d2u_machinery = rex_addon::get("d2u_machinery");
				
			$parameterArray = [];

			$article_id = $d2u_machinery->getConfig('article_id');
			// In case of used machines
			$current_article_id = rex_article::getCurrentId();
			if(rex_plugin::get("d2u_machinery", "used_machines")->isAvailable() && ($current_article_id == $d2u_machinery->getConfig('used_machine_article_id_rent') || $current_article_id == $d2u_machinery->getConfig('used_machine_article_id_sale'))) {
				if($this->offer_type == "sale") {
					$article_id = $d2u_machinery->getConfig('used_machine_article_id_sale');
				}
				elseif($this->offer_type == "rent") {
					$article_id = $d2u_machinery->getConfig('used_machine_article_id_rent');
				}
					
				// Set parameter key
				if($this->offer_type == "sale") {
					$parameterArray['used_sale_category_id'] = $this->category_id;			
				}
				elseif ($this->offer_type == "rent") {
					$parameterArray['used_rent_category_id'] = $this->category_id;			
				}
				else {
					$parameterArray['used_sale_category_id'] = $this->category_id;			
				}
			}
			else {
				// Case of normal machines
				$parameterArray['category_id'] = $this->category_id;			
			}
			$this->url = rex_getUrl($article_id, $this->clang_id, $parameterArray, "&");
		}

		if($including_domain) {
			if(\rex_addon::get('yrewrite') && \rex_addon::get('yrewrite')->isAvailable())  {
				return str_replace(\rex_yrewrite::getCurrentDomain()->getUrl() .'/', \rex_yrewrite::getCurrentDomain()->getUrl(), \rex_yrewrite::getCurrentDomain()->getUrl() . $this->url);
			}
			else {
				return str_replace(\rex::getServer(). '/', \rex::getServer(), \rex::getServer() . $this->url);
			}
		}
		else {
			return $this->url;
		}
	}
	
	/*
	 * Returns the URL of this object.
	 * @param string $including_domain TRUE if Domain name should be included
	 * @return string URL
	 */
	public function getURLCuttingRangeConfigurator($including_domain = FALSE) {
		if(rex_plugin::get("d2u_machinery", "machine_steel_processing_extension")->isAvailable()) {
			if($this->cutting_range_url == "") {
				$d2u_machinery = rex_addon::get("d2u_machinery");

				$parameterArray = [];
				$parameterArray['category_id'] = $this->category_id;
				$parameterArray['cutting_range_configurator'] = $this->steel_processing_saw_cutting_range_file;

				$article_id = $d2u_machinery->getConfig('article_id');
				$this->cutting_range_url = rex_getUrl($article_id, $this->clang_id, $parameterArray, "&");
			}

			if($including_domain) {
				if(\rex_addon::get('yrewrite') && \rex_addon::get('yrewrite')->isAvailable())  {
					return str_replace(\rex_yrewrite::getCurrentDomain()->getUrl() .'/', \rex_yrewrite::getCurrentDomain()->getUrl(), \rex_yrewrite::getCurrentDomain()->getUrl() . $this->cutting_range_url);
				}
				else {
					return str_replace(\rex::getServer(). '/', \rex::getServer(), \rex::getServer() . $this->cutting_range_url);
				}
			}
			else {
				return $this->cutting_range_url;
			}
		}
		
		return FALSE;
	}

	/**
	 * Detects usage of this category as parent category.
	 * @return boolean TRUE if childs exist, otherwise FALSE
	 */
	public function hasChildren() {
		$query = "SELECT category_id FROM ". \rex::getTablePrefix() ."d2u_machinery_categories "
			."WHERE parent_category_id = ". $this->category_id;
		$result = \rex_sql::factory();
		$result->setQuery($query);
		
		if($result->getRows() > 0) {
			return TRUE;
		}
		else {
			return FALSE;
		}
	}
	
	/**
	 * Detects if machines in this category exist
	 * @return boolean TRUE if child categories exist, otherwise FALSE
	 */
	public function hasMachines() {
		$query = "SELECT lang.machine_id "
			. "FROM ". \rex::getTablePrefix() ."d2u_machinery_machines_lang AS lang "
			."LEFT JOIN ". \rex::getTablePrefix() ."d2u_machinery_machines AS machines "
					."ON lang.machine_id = machines.machine_id "
			."WHERE category_id = ". $this->category_id ." AND clang_id = ". $this->clang_id;
		$result = \rex_sql::factory();
		$result->setQuery($query);
		
		if($result->getRows() > 0) {
			return TRUE;
		}
		else {
			return FALSE;
		}
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
	 * Updates or inserts the object into database.
	 * @return boolean TRUE if successful
	 */
	public function save() {
		$error = FALSE;

		// Save the not language specific part
		$pre_save_category = new Category($this->category_id, $this->clang_id);
	
		// save priority, but only if new or changed
		if($this->priority != $pre_save_category->priority || $this->category_id == 0) {
			$this->setPriority();
		}

		if($this->category_id == 0 || $pre_save_category != $this) {
			$query = \rex::getTablePrefix() ."d2u_machinery_categories SET "
					."parent_category_id = '". $this->parent_category->category_id ."', "
					."priority = '". $this->priority ."', "
					."pic = '". $this->pic ."', "
					."pic_usage = '". $this->pic_usage ."' ";
			if(rex_plugin::get("d2u_machinery", "export")->isAvailable()) {
				$query .= ", export_europemachinery_category_id = '". $this->export_europemachinery_category_id ."', "
					."export_europemachinery_category_name = '". $this->export_europemachinery_category_name ."', "
					."export_machinerypark_category_id = '". $this->export_machinerypark_category_id ."', "
					."export_mascus_category_name = '". $this->export_mascus_category_name ."' ";
			}
			if(rex_plugin::get("d2u_machinery", "machine_agitator_extension")->isAvailable()) {
				$query .= ", show_agitators = '". $this->show_agitators ."' ";
			}

			if(\rex_addon::get('d2u_videos')->isAvailable() && count($this->videos) > 0) {
				$query .= ", video_ids = '|". implode("|", array_keys($this->videos)) ."|' ";
			}
			else {
				$query .= ", video_ids = '' ";
			}

			if($this->category_id == 0) {
				$query = "INSERT INTO ". $query;
			}
			else {
				$query = "UPDATE ". $query ." WHERE category_id = ". $this->category_id;
			}

			$result = \rex_sql::factory();
			$result->setQuery($query);
			if($this->category_id == 0) {
				$this->category_id = $result->getLastId();
				$error = $result->hasError();
			}
		}
		
		if($error === FALSE) {
			// Save the language specific part
			$pre_save_category = new Category($this->category_id, $this->clang_id);
			if($pre_save_category != $this) {
				$query = "REPLACE INTO ". \rex::getTablePrefix() ."d2u_machinery_categories_lang SET "
						."category_id = '". $this->category_id ."', "
						."clang_id = '". $this->clang_id ."', "
						."name = '". addslashes(htmlspecialchars($this->name)) ."', "
						."teaser = '". addslashes(htmlspecialchars($this->teaser)) ."', "
						."usage_area = '". addslashes(htmlspecialchars($this->usage_area)) ."', "
						."pic_lang = '". $this->pic_lang ."', "
						."pdfs = '". implode(",", $this->pdfs) ."', "
						."translation_needs_update = '". $this->translation_needs_update ."', "
						."updatedate = CURRENT_TIMESTAMP, "
						."updateuser = '". \rex::getUser()->getLogin() ."' ";
				if(rex_plugin::get("d2u_machinery", "machine_steel_processing_extension")->isAvailable()) {
					$query .= ", steel_processing_saw_cutting_range_file = '". $this->steel_processing_saw_cutting_range_file ."', "
						."steel_processing_saw_cutting_range_title = '". $this->steel_processing_saw_cutting_range_title ."' ";
				}

				$result = \rex_sql::factory();
				$result->setQuery($query);
				$error = $result->hasError();
			}
		}
		
		// Update URLs
		if(\rex_addon::get("url")->isAvailable()) {
			\UrlGenerator::generatePathFile([]);
		}
		
		return !$error;
	}
	
	/**
	 * Set offer type for used machines. Setter only works if used_machines plugin
	 * is installed.
	 * @param string $offer_type Offer type. Either "sale" or "rent"
	 */
	public function setOfferType($offer_type) {
		if(rex_plugin::get("d2u_machinery", "used_machines")->isAvailable()) {
			$this->offer_type = $offer_type;
		}
	}
	
	/**
	 * Reassigns priorities in database.
	 * @param boolean $delete Reorder priority after deletion
	 */
	private function setPriority($delete = FALSE) {
		// Pull prios from database
		$query = "SELECT category_id, priority FROM ". \rex::getTablePrefix() ."d2u_machinery_categories "
			."WHERE category_id <> ". $this->category_id ." ORDER BY priority";
		$result = \rex_sql::factory();
		$result->setQuery($query);
		
		// When priority is too small, set at beginning
		if($this->priority <= 0) {
			$this->priority = 1;
		}
		
		// When prio is too high or was deleted, simply add at end 
		if($this->priority > $result->getRows() || $delete) {
			$this->priority = $result->getRows() + 1;
		}

		$categories = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$categories[$result->getValue("priority")] = $result->getValue("category_id");
			$result->next();
		}
		array_splice($categories, ($this->priority - 1), 0, array($this->category_id));

		// Save all prios
		foreach($categories as $prio => $category_id) {
			$query = "UPDATE ". \rex::getTablePrefix() ."d2u_machinery_categories "
					."SET priority = ". ($prio + 1) ." " // +1 because array_splice recounts at zero
					."WHERE category_id = ". $category_id;
			$result = \rex_sql::factory();
			$result->setQuery($query);
		}
	}
}