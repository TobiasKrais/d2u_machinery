<?php
/**
 * Redaxo D2U Machines Addon - Used Machines Plugin
 * @author Tobias Krais
 * @author <a href="http://www.design-to-use.de">www.design-to-use.de</a>
 */

/**
 * Used machine object.
 */
class UsedMachine {
	/**
	 * @var int Database ID
	 */
	var $used_machine_id = 0;
	
	/**
	 * @var int Redaxo Clang ID
	 */
	var $clang_id = 0;
	
	/**
	 * @var String Name or machine model
	 */
	var $name = "";
	
	/**
	 * @var Category Machine category
	 */
	var $category = false;

	/**
	 * @var String Date string with information concerning the availability of the machine.
	 */
	var $availability = "";

	/**
	 * @var String Internal product number of the machine.
	 */
	var $product_number = "";

	/**
	 * @var String Machine manufacturer.
	 */
	var $manufacturer = "";

	/**
	 * @var int Year of construction
	 */
	var $year_built = 0;

	/**
	 * @var int price
	 */
	var $price = 0;

	/**
	 * @var String ISO 4217 currency code. Default is EUR.
	 */
	var $currency_code = "EUR";

	/**
	 * @var int VAT percentage included in price. 0 is without VAT
	 */
	var $vat = 0;

	/**
	 * @var String Online online_status. Either "online" or "offline"
	 */
	var $online_status = "online";
	
	/**
	 * @var String[] Picture filenames
	 */
	var $pics = array();
	
	/**
	 * @var Machine Machine objekt for technical data reference
	 */
	var $machine = false;
	
	/**
	 * @var String Place, where used machine is currently located.
	 */
	var $location = "";
	
	/**
	 * @var String URL with additional information, e.g. an advertisement of the machine.
	 */
	var $external_url = "";

	/**
	 * @var String Teaser. Used for SEO purposes or as teaser in machine list..
	 */
	var $teaser = "";

	/**
	 * @var String Detailed description.
	 */
	var $description = "";

	/**
	 * @var string Needs translation update? "no", "yes" or "delete"
	 */
	var $translation_needs_update = "delete";

	/**
	 * @var String URL of the used machine
	 */
	var $url = "";

	/**
	 * Constructor. Fetches the object from database
	 * @param int $used_machine_id Object ID
	 * @param int $clang_id Redaxo language ID.
	 */
	 public function __construct($used_machine_id, $clang_id) {
		$this->clang_id = $clang_id;
		$query = "SELECT * FROM ". rex::getTablePrefix() ."d2u_machinery_used_machines AS used_machines "
				."LEFT JOIN ". rex::getTablePrefix() ."d2u_machinery_used_machines_lang AS lang "
					."ON used_machines.used_machines_id = lang.used_machines_id "
				."WHERE used_machines.used_machines_id = ". $used_machine_id ." "
					."AND clang_id = ". $this->clang_id;
		$result = rex_sql::factory();
		$result->setQuery($query);
		$num_rows = $result->getRows();

		if ($num_rows > 0) {
			$this->used_machine_id = $result->getValue("used_machines.used_machines_id");
			$this->name = $result->getValue("name");
			$this->category = new Category($result->getValue("category_id"), $this->clang_id);
			$this->availability = $result->getValue("availability");
			$this->product_number = $result->getValue("product_number");
			$this->manufacturer = $result->getValue("manufacturer");
			$this->year_built = $result->getValue("baujahr");
			$this->price = $result->getValue("price");
			$this->currency_code = $result->getValue("currency_code");
			$this->vat = $result->getValue("vat");
			if($result->getValue("online_status") != "") {
				$this->online_status = $result->getValue("online_status");
			}
			$this->pics = preg_grep('/^\s*$/s', explode(",", $result->getValue("bilder")), PREG_GREP_INVERT);
			if($result->getValue("machine_id") > 0) {
				$this->machine = new Maschine($result->getValue("machine_id"), $clang_id);
			}
			$this->location = $result->getValue("location");
			$this->external_url = $result->getValue("external_url");
			$this->description = $result->getValue("description");
			$this->teaser = $result->getValue("teaser");
			$this->translation_needs_update = $result->getValue("translation_needs_update");

			// URL des externen Links bei Bedarf korrigieren
			if(strlen($this->external_url) > 3 && substr($this->external_url, 0, 4) != "http") {
				$this->external_url = "http://". $this->external_url;
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
		}
	}

	/**
	 * Changes the status of a machine
	 */
	public function changeStatus() {
		if($this->online_status == "online") {
			if($this->used_machine_id > 0) {
				$query = "UPDATE ". rex::getTablePrefix() ."d2u_machinery_used_machines "
					."SET online_status = 'offline' "
					."WHERE used_machine_id = ". $this->used_machine_id;
				$result = rex_sql::factory();
				$result->setQuery($query);
			}
			$this->online_status = "offline";
		}
		else {
			if($this->used_machine_id > 0) {
				$query = "UPDATE ". rex::getTablePrefix() ."d2u_machinery_used_machines "
					."SET online_status = 'online' "
					."WHERE used_machine_id = ". $this->used_machine_id;
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
			$query_lang = "DELETE FROM ". rex::getTablePrefix() ."d2u_machinery_used_machines_lang "
				."WHERE used_machine_id = ". $this->used_machine_id;
			$result_lang = rex_sql::factory();
			$result_lang->setQuery($query_lang);

			$query = "DELETE FROM ". rex::getTablePrefix() ."d2u_machinery_used_machines "
				."WHERE used_machine_id = ". $this->used_machine_id;
			$result = rex_sql::factory();
			$result->setQuery($query);
		}
		else {
			$query_lang = "DELETE FROM ". rex::getTablePrefix() ."d2u_machinery_used_machines_lang "
				."WHERE used_machine_id = ". $this->used_machine_id ." AND clang_id = ". $this->clang_id;
			$result_lang = rex_sql::factory();
			$result_lang->setQuery($query_lang);
		}
	}
	
	/**
	 * Get all used machines.
	 * @param int $clang_id Redaxo clang id.
	 * @param boolean $only_online Show only online used machines
	 * @return UsedMachines[] Array with UsedMachines objects.
	 */
	public static function getAll($clang_id, $only_online = FALSE) {
		$query = "SELECT used_machine_id FROM ". rex::getTablePrefix() ."d2u_machinery_used_machines ";
		if($only_online) {
			$query .= "WHERE online_status = 'online' ";
		}
		$query .= "ORDER BY name";
		$result = rex_sql::factory();
		$result->setQuery($query);
		
		$used_machines = array();
		for($i = 0; $i < $result->getRows(); $i++) {
			$used_machines[] = new UsedMachine($result->getValue("used_machine_id"), $clang_id);
			$result->next();
		}
		return $used_machines;
	}
	
	/**
	 * Get the <link rel="canonical"> tag for page header.
	 * @return Complete tag.
	 */
	public function getCanonicalTag() {
		return '<link rel="canonical" href="'. $this->getURL() .'">';
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
				$used_machine = new UsedMachine($this->used_machine_id, $rex_clang->getId());
				if($used_machine->translation_needs_update != "delete") {
					$hreflang_tags .= '<link rel="alternate" type="text/html" hreflang="'. $rex_clang->getCode() .'" href="'. $used_machine->getURL() .'" title="'. str_replace('"', '', $used_machine->category->name .': '. $used_machine->name) .'">';
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
	 * Get the <title> tag for page header.
	 * @return Complete title tag.
	 */
	public function getTitleTag() {
		return '<title>'. $this->name .' / '. $this->category->name .' / '. rex::getServerName() .'</title>';
	}
	
	/**
	 * Gets the used machines for a machine. Only machines with online status "online"
	 * will be returned.
	 * @param Machine $machine Machine for which used machines should be searched for
	 * @return UsedMachine[] Array full of used machines ;-)
	 */
	static function getUsedMachinesForMachine($machine) {
		$query = "SELECT used_machines_id FROM ". rex::getTablePrefix() ."d2u_machinery_used_machines "
				."WHERE machine_id = ". $machine->machine_id ." "
					."AND online_status = 'online'";

		$result = rex_sql::factory();
		$result->setQuery($query);
		
		$used_machines = array();
		for($i = 0; $i < $result->getRows(); $i++) {
			$used_machines[] = new UsedMachine($result->getValue("used_machines_id"), $machine->clang_id);
			$result->next();
		}
		
		return $used_machines;
	}
	
	/**
	 * Gets the used machines for a category. Only machines with online status "online"
	 * will be returned.
	 * @param Category $category Category.
	 * @param int $clang_id Redaxo language ID.
	 * @return UsedMachine[] Array full of used machines ;-)
	 */
	static function getUsedMachinesForCategory($category) {
		$query = "SELECT used_machines_id FROM ". rex::getTablePrefix() ."d2u_machinery_used_machines "
				."WHERE category_id = ". $category->category_id ." "
					."AND online_status = 'online'";
		$result = rex_sql::factory();
		$result->setQuery($query);
		
		$used_machines = array();
		for($i = 0; $i < $result->getRows(); $i++) {
			$used_machines[] = new UsedMachine($result->getValue("used_machines_id"), $category->clang_id);
			$result->next();
		}
		
		return $used_machines;
	}
	
	/**
	 * Returns the URL of this object.
	 * @param string $including_domain TRUE if Domain name should be included
	 * @return string URL
	 */
	public function getURL($including_domain = TRUE) {
		if($this->url == "") {
			// Without SEO Plugins
			$d2u_machinery = rex_addon::get("d2u_machinery");
				
			$parameterArray = array();
			$parameterArray['used_machine_id'] = $this->used_machine_id;
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
		$pre_save_used_machine = new UsedMachine($this->used_machine_id, $this->clang_id);
		if($this->used_machine_id == 0 || $pre_save_used_machine != $this) {
			$query = rex::getTablePrefix() ."d2u_machinery_used_machines SET "
					."name = '". $this->name ."', "
					."category_id = ". $this->category->category_id .", "
					."availability = '". $this->availability ."', "
					."product_number = '". $this->product_number ."', "
					."manufacturer = '". $this->manufacturer ."', "
					."year_built = ". $this->year_built .", "
					."price = ". $this->price .", "
					."currency_code = '". $this->currency_code ."', "
					."vat = '". $this->vat ."', "
					."online_status = '". $this->online_status ."', "
					."pics = '". implode(",", $this->pics) ."', "
					."machine_id = ". $this->machine->machine_id .", "
					."location = '". $this->location ."', "
					."external_url = '". $this->external_url ."' ";

			if($this->used_machine_id == 0) {
				$query = "INSERT INTO ". $query;
			}
			else {
				$query = "UPDATE ". $query ." WHERE used_machine_id = ". $this->used_machine_id;
			}
			$result = rex_sql::factory();
			$result->setQuery($query);
			if($this->used_machine_id == 0) {
				$this->used_machine_id = $result->getLastId();
				$error = $result->hasError();
			}
		}
		
		if($error == 0) {
			// Save the language specific part
			$pre_save_used_machine = new UsedMachine($this->used_machine_id, $this->clang_id);
			if($pre_save_used_machine != $this) {
				$query = "REPLACE INTO ". rex::getTablePrefix() ."d2u_machinery_used_machines_lang SET "
						."used_machine_id = '". $this->used_machine_id ."', "
						."clang_id = '". $this->clang_id ."', "
						."teaser = '". htmlspecialchars($this->teaser) ."', "
						."description = '". htmlspecialchars($this->description) ."', "
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
}