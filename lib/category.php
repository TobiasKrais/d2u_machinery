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
	public int $category_id = 0;
	
	/**
	 * @var int Redaxo clang id
	 */
	public int $clang_id = 0;
	
	/**
	 * @var Category|boolean Father category object
	 */
	var $parent_category = false;
	
	/**
	 * @var string Name
	 */
	public string $name = "";
	
	/**
	 * @var string Teaser
	 */
	public string $teaser = "";

	/**
	 * @var string Description
	 */
	public string $description = "";

	/**
	 * @var string Name of Usage area
	 */
	public string $usage_area = "";
	
	/**
	 * @var string Preview picture file name 
	 */
	public string $pic = "";
	
	/**
	 * @var string Language specific preview picture file name 
	 */
	public string $pic_lang = "";
	
	/**
	 * @var string Usage area picture file name
	 */
	public string $pic_usage = "";
	
	/**
	 * @var int ID of the corresponding machinerypark category
	 */
	public int $export_machinerypark_category_id = 0;
	
	/**
	 * @var int ID of the corresponding Europe Machinery category,
	 * @link http://www.agriaffaires.com/html/rubtableauliste_compact_de.html Full List
	 */
	public int $export_europemachinery_category_id = 0;
	
	/**
	 * @var string Name of the corresponding Europe Machinery category
	 * @link http://www.agriaffaires.com/html/rubtableauliste_compact_de.html Full List
	 */
	public string $export_europemachinery_category_name = "";
	
	/**
	 * @var string Name of the corresponding Mascus category
	 */
	public string $export_mascus_category_name = "";
	
	/**
	 * @var string Show agitators in machines of this category. Values are "hide" or "show"
	 */
	public string $show_agitators = "hide";

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
	public int $priority = 0;
	
	/**
	 * @var string For used machines: offer type. Either "rent" or "sale".
	 */
	private string $offer_type = '';
	
	/**
	 * @var string "yes" if translation needs update
	 */
	public string $translation_needs_update = "delete";
	
	/**
	 * @var string URL
	 */
	private string $url = "";

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
			$this->category_id = (int) $result->getValue("category_id");
			if($result->getValue("parent_category_id") > 0) {
				$this->parent_category = new Category((int) $result->getValue("parent_category_id"), $clang_id);
			}
			$this->name = stripslashes(htmlspecialchars_decode((string) $result->getValue("lang.name")));
			$this->teaser = stripslashes(htmlspecialchars_decode((string) $result->getValue("teaser")));
			$this->description = stripslashes(htmlspecialchars_decode((string) $result->getValue("description")));
			$this->usage_area = stripslashes(htmlspecialchars_decode((string) $result->getValue("usage_area")));
			$this->pic = (string) $result->getValue("pic");
			$this->pic_lang = (string) $result->getValue("pic_lang");
			$this->pic_usage = (string) $result->getValue("pic_usage");
			$pdfs = preg_grep('/^\s*$/s', explode(",", (string) $result->getValue("pdfs")), PREG_GREP_INVERT);
			$this->pdfs = is_array($pdfs) ? $pdfs : [];
			$this->priority = (int) $result->getValue("priority");
			if($result->getValue("translation_needs_update") !== "") {
				$this->translation_needs_update = (string) $result->getValue("translation_needs_update");
			}

			// Export plugin fields
			if(rex_plugin::get("d2u_machinery", "export")->isAvailable()) {
				$this->export_europemachinery_category_id = (int) $result->getValue("export_europemachinery_category_id");
				$this->export_europemachinery_category_name = (string) $result->getValue("export_europemachinery_category_name");
				$this->export_machinerypark_category_id = (int) $result->getValue("export_machinerypark_category_id");
				$this->export_mascus_category_name = (string) $result->getValue("export_mascus_category_name");
			}
			
			// Agitator fields
			if(rex_plugin::get("d2u_machinery", "machine_agitator_extension")->isAvailable()) {
				$this->show_agitators = (string) $result->getValue("show_agitators");
			}

			// Videos
			if(\rex_addon::get('d2u_videos') instanceof rex_addon && \rex_addon::get('d2u_videos')->isAvailable() && $result->getValue("video_ids") !== "") {
				$video_ids = preg_grep('/^\s*$/s', explode("|", (string) $result->getValue("video_ids")), PREG_GREP_INVERT);
				if(is_array($video_ids)) {
					foreach ($video_ids as $video_id) {
						if($video_id > 0) {
							$video = new Video($video_id, $clang_id);
							if($video->getVideoURL() !== "") {
								$this->videos[$video_id] = $video;
							}
						}
					}
				}
			}
		}
		
		// For used machines: set offer type
		if(rex_plugin::get("d2u_machinery", "used_machines")->isAvailable()) {
			$d2u_machinery = rex_addon::get("d2u_machinery");
			$current_article_id = \rex::isBackend() || rex_article::getCurrent() === null ? 0 : rex_article::getCurrent()->getId();
			if(intval($d2u_machinery->getConfig('used_machine_article_id_rent')) === intval($d2u_machinery->getConfig('used_machine_article_id_sale'))
					&& ($current_article_id === intval($d2u_machinery->getConfig('used_machine_article_id_rent')) || $current_article_id === intval($d2u_machinery->getConfig('used_machine_article_id_sale')))) {
				if(filter_input(INPUT_GET, 'offer_type') === "rent" || filter_input(INPUT_GET, 'offer_type') === "sale") {
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
						$used_category_id = intval(filter_input(INPUT_GET, 'used_rent_category_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 ? filter_input(INPUT_GET, 'used_rent_category_id', FILTER_VALIDATE_INT) : filter_input(INPUT_GET, 'used_sale_category_id', FILTER_VALIDATE_INT));
					}
					if($used_category_id > 0) {
						$this->offer_type = UsedMachine::getOfferTypeForUsedMachineId($used_category_id);
					}
				}
			}
			else if($current_article_id === $d2u_machinery->getConfig('used_machine_article_id_rent')) {
				$this->offer_type = "rent";
			}
			else {
				$this->offer_type = "sale";
			}
		}
	}
	
	/**
	 * Deletes the object in all languages.
	 * @param boolean $delete_all If true, all translations and main object are deleted. If 
	 * false, only this translation will be deleted.
	 */
	public function delete($delete_all = true):void {
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
		if($result_main->getRows() === 0) {
			$query = "DELETE FROM ". \rex::getTablePrefix() ."d2u_machinery_categories "
				."WHERE category_id = ". $this->category_id;
			$result = \rex_sql::factory();
			$result->setQuery($query);

			// reset priorities
			$this->setPriority(true);			
		}

		// Don't forget to regenerate URL list and search_it index
		\d2u_addon_backend_helper::generateUrlCache();

		// Delete from YRewrite forward list
		if(rex_addon::get('yrewrite')->isAvailable()) {
			if($delete_all) {
				foreach(rex_clang::getAllIds() as $clang_id) {
					$lang_object = new self($this->category_id, $clang_id);
					$query_forward = "DELETE FROM ". \rex::getTablePrefix() ."yrewrite_forward "
						."WHERE extern = '". $lang_object->getURL(true) ."'";
					$result_forward = \rex_sql::factory();
					$result_forward->setQuery($query_forward);
				}
			}
			else {
				$query_forward = "DELETE FROM ". \rex::getTablePrefix() ."yrewrite_forward "
					."WHERE extern = '". $this->getURL(true) ."'";
				$result_forward = \rex_sql::factory();
				$result_forward->setQuery($query_forward);
			}
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
		if(\rex_addon::get('d2u_machinery')->getConfig('default_category_sort') === 'priority') {
			$query .= 'ORDER BY categories.priority';
		}
		else {
			$query .= 'ORDER BY lang.name';
		}
		$result = \rex_sql::factory();
		$result->setQuery($query);
		
		$categories = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$category = new Category((int) $result->getValue("category_id"), $clang_id);
			$categories[] = $category;
			$result->next();
		}
		return $categories;
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
			$children[] = new Category((int) $result->getValue("category_id"), $this->clang_id);
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
		$machines = $this->getMachines(true);
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
				/** @var string[] $tech_data */
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
					/** @var string[] $techdata */
					if($techdata['description'] === $wildcard) {
						$matrix[$wildcard]['machine_ids'][$machine->machine_id] = $techdata['value'];
						break;
					}
				}
			}
		}
		return $matrix;
	}
	
	/**
	 * Get UsageArea matrix for this category.
	 * @return mixed[] Key is the name of the UsageArea. Values are the machine
	 * ids that use the UsageArea.
	 */
	public function getUsageAreaMatrix() {
		$usage_areas = UsageArea::getAll($this->clang_id, $this->category_id);
		$machines = $this->getMachines(true);

		$matrix = [];
		foreach($usage_areas as $usage_area) {
			$values = [];
			foreach($machines as $machine) {
				if(in_array($usage_area->usage_area_id, $machine->usage_area_ids, true)) {
					$values[] = $machine->machine_id;
				}
			}
			$matrix[$usage_area->name] = $values;
		}
		return $matrix;
	}
	
	/**
	 * Gets the machines of the category.
	 * @param boolean $online_only true if only online machines should be returned.
	 * @return Machine[] Machines in this category
	 */
	public function getMachines($online_only = false) {
		$query = "SELECT lang.machine_id, IF(lang.lang_name IS NULL or lang.lang_name = '', machines.name, lang.lang_name) as machine_name "
			. "FROM ". \rex::getTablePrefix() ."d2u_machinery_machines_lang AS lang "
			."LEFT JOIN ". \rex::getTablePrefix() ."d2u_machinery_machines AS machines "
					."ON lang.machine_id = machines.machine_id "
			."WHERE category_id = ". $this->category_id ." AND clang_id = ". $this->clang_id .' ';
		if($online_only) {
			$query .= "AND online_status = 'online' ";
		}
		if(rex_config::get('d2u_machinery', 'default_machine_sort', '') === 'priority') {
			$query .= 'ORDER BY priority ASC';
		}
		else {
			$query .= 'ORDER BY machine_name ASC';
		}
		$result = \rex_sql::factory();
		$result->setQuery($query);
		
		$machines = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$machines[(int)$result->getValue("machine_id")] = new Machine((int) $result->getValue("machine_id"), $this->clang_id);
			$result->next();
		}
		return $machines;
	}
	
	/**
	 * Gets the UsedMachines of the category if plugin ist installed.
	 * @param boolean $online_only If true only online used machines are returned
	 * @return UsedMachine[] Used machines of this category
	 */
	public function getUsedMachines($online_only = false) {
		$usedMachines = [];
		if(rex_plugin::get("d2u_machinery", "used_machines")->isAvailable()) {
			$query = "SELECT lang.used_machine_id FROM ". \rex::getTablePrefix() ."d2u_machinery_used_machines_lang AS lang "
				."LEFT JOIN ". \rex::getTablePrefix() ."d2u_machinery_used_machines AS used_machines "
					."ON lang.used_machine_id = used_machines.used_machine_id "
				."WHERE category_id = ". $this->category_id ." AND clang_id = ". $this->clang_id ." ";
			if($online_only) {
				$query .= "AND online_status = 'online' ";
			}
			if($this->offer_type !== "") {
				$query .= "AND offer_type = '". $this->offer_type ."' ";
			}
			$query .= "ORDER BY manufacturer, name";
			$result = \rex_sql::factory();
			$result->setQuery($query);

			for($i = 0; $i < $result->getRows(); $i++) {
				$usedMachines[] = new UsedMachine((int) $result->getValue("used_machine_id"), $this->clang_id);
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
		if($type === 'missing') {
			$query = 'SELECT main.category_id FROM '. \rex::getTablePrefix() .'d2u_machinery_categories AS main '
					.'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_categories_lang AS target_lang '
						.'ON main.category_id = target_lang.category_id AND target_lang.clang_id = '. $clang_id .' '
					.'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_categories_lang AS default_lang '
						.'ON main.category_id = default_lang.category_id AND default_lang.clang_id = '. \rex_config::get('d2u_helper', 'default_lang') .' '
					."WHERE target_lang.category_id IS NULL "
					.'ORDER BY default_lang.name';
			$clang_id = intval(\rex_config::get('d2u_helper', 'default_lang'));
		}
		$result = \rex_sql::factory();
		$result->setQuery($query);

		$objects = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$objects[] = new Category((int) $result->getValue("category_id"), $clang_id);
			$result->next();
		}
		
		return $objects;
    }

	/**
	 * Returns the URL of this object.
	 * @param boolean $including_domain true if Domain name should be included
	 * @param int $current_article_id Redaxo article id. Category can be used for different
	 * articles, e.g. machines or used machines. When passing article id, 
	 * category URL can be securely determined, otherwise there might be some
	 * rare cases, the URL is parsed not correct, because article ID is set to
	 * rex_article::getCurrentId(). The parameter is optional to be
	 * backward compatible.
	 * @return string URL
	 */
	public function getURL($including_domain = false, $current_article_id = 0) {
		if($this->url === "") {
			$d2u_machinery = rex_addon::get("d2u_machinery");
				
			$parameterArray = [];

			$article_id = intval($d2u_machinery->getConfig('article_id'));
			
			// In case of used machines
			if($current_article_id === 0) {
				$current_article_id = rex_article::getCurrentId();
			}

			if(rex_plugin::get("d2u_machinery", "used_machines")->isAvailable() && ($current_article_id === intval($d2u_machinery->getConfig('used_machine_article_id_rent')) || $current_article_id === intval($d2u_machinery->getConfig('used_machine_article_id_sale')))) {
				if($this->offer_type === "sale") {
					$article_id = intval($d2u_machinery->getConfig('used_machine_article_id_sale'));
					// Set parameter key
					$parameterArray['used_sale_category_id'] = $this->category_id;			
				}
				elseif($this->offer_type === "rent") {
					$article_id = intval($d2u_machinery->getConfig('used_machine_article_id_rent'));
					// Set parameter key
					$parameterArray['used_rent_category_id'] = $this->category_id;			
				}
			}
			else {
				// Case of normal machines
				$parameterArray['category_id'] = $this->category_id;			
			}
			$this->url = rex_getUrl($article_id, $this->clang_id, $parameterArray, "&");
		}

		if($including_domain) {
			if(\rex_addon::get('yrewrite') instanceof rex_addon && \rex_addon::get('yrewrite')->isAvailable())  {
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

	/**
	 * Detects usage of this category as parent category.
	 * @return boolean true if childs exist, otherwise false
	 */
	public function hasChildren() {
		$query = "SELECT category_id FROM ". \rex::getTablePrefix() ."d2u_machinery_categories "
			."WHERE parent_category_id = ". $this->category_id;
		$result = \rex_sql::factory();
		$result->setQuery($query);
		
		if($result->getRows() > 0) {
			return true;
		}
		else {
			return false;
		}
	}
	
	/**
	 * Detects if machines in this category exist
	 * @param boolean $ignore_offlines Ignore offline machines, default is false
	 * @return boolean true if machines exist, otherwise false
	 */
	public function hasMachines($ignore_offlines = false) {
		$query = "SELECT lang.machine_id "
			. "FROM ". \rex::getTablePrefix() ."d2u_machinery_machines_lang AS lang "
			."LEFT JOIN ". \rex::getTablePrefix() ."d2u_machinery_machines AS machines "
					."ON lang.machine_id = machines.machine_id "
			."WHERE category_id = ". $this->category_id ." AND clang_id = ". $this->clang_id
			.($ignore_offlines ? ' AND online_status = "online"' : '');
		$result = \rex_sql::factory();
		$result->setQuery($query);
		
		if($result->getRows() > 0) {
			return true;
		}
		else {
			return false;
		}
	}
	
	/**
	 * Detects if used machines in this category exist
	 * @param boolean $ignore_offlines Ignore offline used machines, default is false
	 * @return boolean true if used machines exist, otherwise false
	 */
	public function hasUsedMachines($ignore_offlines = false) {
		if(rex_plugin::get("d2u_machinery", "used_machines")->isAvailable()) {
			$query = "SELECT lang.used_machine_id FROM ". \rex::getTablePrefix() ."d2u_machinery_used_machines_lang AS lang "
				."LEFT JOIN ". \rex::getTablePrefix() ."d2u_machinery_used_machines AS used_machines "
					."ON lang.used_machine_id = used_machines.used_machine_id "
				."WHERE category_id = ". $this->category_id ." AND clang_id = ". $this->clang_id ." ";
			if($ignore_offlines) {
				$query .= "AND online_status = 'online' ";
			}
			if($this->offer_type !== "") {
				$query .= "AND offer_type = '". $this->offer_type ."' ";
			}

			$result = \rex_sql::factory();
			$result->setQuery($query);

			if($result->getRows() > 0) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Detects whether category is child or not.
	 * @return boolean true if category has father.
	 */
	public function isChild() {
		if($this->parent_category === false) {
			return false;
		}
		else {
			return true;
		}
	}
	
	/**
	 * Updates or inserts the object into database.
	 * @return boolean true if successful
	 */
	public function save() {
		$error = false;

		// Save the not language specific part
		$pre_save_object = new Category($this->category_id, $this->clang_id);
	
		// save priority, but only if new or changed
		if($this->priority !== $pre_save_object->priority || $this->category_id === 0) {
			$this->setPriority();
		}

		if($this->category_id === 0 || $pre_save_object !== $this) {
			$query = \rex::getTablePrefix() ."d2u_machinery_categories SET "
					."parent_category_id = '". ($this->parent_category instanceof Category ? $this->parent_category->category_id : 0) ."', "
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

			if(\rex_addon::get('d2u_videos') instanceof rex_addon && \rex_addon::get('d2u_videos')->isAvailable() && count($this->videos) > 0) {
				$query .= ", video_ids = '|". implode("|", array_keys($this->videos)) ."|' ";
			}
			else {
				$query .= ", video_ids = '' ";
			}

			if($this->category_id === 0) {
				$query = "INSERT INTO ". $query;
			}
			else {
				$query = "UPDATE ". $query ." WHERE category_id = ". $this->category_id;
			}

			$result = \rex_sql::factory();
			$result->setQuery($query);
			if($this->category_id === 0) {
				$this->category_id = (int) $result->getLastId();
				$error = $result->hasError();
			}
		}
		
		$regenerate_urls = false;
		if($error === false) {
			// Save the language specific part
			$pre_save_object = new Category($this->category_id, $this->clang_id);
			if($pre_save_object !== $this) {
				$query = "REPLACE INTO ". \rex::getTablePrefix() ."d2u_machinery_categories_lang SET "
						."category_id = '". $this->category_id ."', "
						."clang_id = '". $this->clang_id ."', "
						."name = '". addslashes(htmlspecialchars($this->name)) ."', "
						."description = '". addslashes(htmlspecialchars($this->description)) ."', "
						."teaser = '". addslashes(htmlspecialchars($this->teaser)) ."', "
						."usage_area = '". addslashes(htmlspecialchars($this->usage_area)) ."', "
						."pic_lang = '". $this->pic_lang ."', "
						."pdfs = '". implode(",", $this->pdfs) ."', "
						."translation_needs_update = '". $this->translation_needs_update ."', "
						."updatedate = CURRENT_TIMESTAMP, "
						."updateuser = '". (\rex::getUser() instanceof rex_user ? \rex::getUser()->getLogin() : '') ."' ";

				$result = \rex_sql::factory();
				$result->setQuery($query);
				$error = $result->hasError();
				
				if(!$error && $pre_save_object->name !== $this->name) {
					$regenerate_urls = true;
				}
			}
		}
		
		// Update URLs
		if($regenerate_urls) {
			\d2u_addon_backend_helper::generateUrlCache('category_id');
			\d2u_addon_backend_helper::generateUrlCache('machine_id');
			if(rex_plugin::get("d2u_machinery", "used_machines")->isAvailable()) {
				\d2u_addon_backend_helper::generateUrlCache('used_rent_category_id');
				\d2u_addon_backend_helper::generateUrlCache('used_rent_machine_id');
				\d2u_addon_backend_helper::generateUrlCache('used_sale_category_id');
				\d2u_addon_backend_helper::generateUrlCache('used_sale_machine_id');
			}
		}
		
		return !$error;
	}
	
	/**
	 * Set offer type for used machines. Setter only works if used_machines plugin
	 * is installed.
	 * @param string $offer_type Offer type. Either "sale" or "rent"
	 */
	public function setOfferType($offer_type):void {
		if(rex_plugin::get("d2u_machinery", "used_machines")->isAvailable()) {
			$this->offer_type = $offer_type;
		}
	}
	
	/**
	 * Reassigns priorities in database.
	 * @param boolean $delete Reorder priority after deletion
	 */
	private function setPriority($delete = false):void {
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
			$this->priority = (int) $result->getRows() + 1;
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
					."SET priority = ". ((int) $prio + 1) ." " // +1 because array_splice recounts at zero
					."WHERE category_id = ". $category_id;
			$result = \rex_sql::factory();
			$result->setQuery($query);
		}
	}
}