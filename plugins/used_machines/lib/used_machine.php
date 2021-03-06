<?php
/**
 * Redaxo D2U Machines Addon - Used Machines Plugin
 * @author Tobias Krais
 * @author <a href="http://www.design-to-use.de">www.design-to-use.de</a>
 */

/**
 * Used machine object.
 */
class UsedMachine implements \D2U_Helper\ITranslationHelper {
	/**
	 * @var int Database ID
	 */
	var $used_machine_id = 0;
	
	/**
	 * @var int Redaxo Clang ID
	 */
	var $clang_id = 0;
	
	/**
	 * @var string Machine manufacturer.
	 */
	var $manufacturer = "";

	/**
	 * @var string Name or machine model
	 */
	var $name = "";
	
	/**
	 * @var Category Used machine category
	 */
	var $category = false;

	/**
	 * @var string Offer type. This could be "sale" or "rent"
	 */
	var $offer_type = "sale";

	/**
	 * @var string Date string with information concerning the availability of the machine.
	 */
	var $availability = "";

	/**
	 * @var string Internal product number of the machine.
	 */
	var $product_number = "";

	/**
	 * @var int Year of construction
	 */
	var $year_built = 0;

	/**
	 * @var int price
	 */
	var $price = 0;

	/**
	 * @var string ISO 4217 currency code. Default is EUR.
	 */
	var $currency_code = "EUR";

	/**
	 * @var int VAT percentage included in price. 0 is without VAT
	 */
	var $vat = 0;

	/**
	 * @var string Online online_status. Either "online" or "offline"
	 */
	var $online_status = "online";
	
	/**
	 * @var string[] Picture filenames
	 */
	var $pics = [];
	
	/**
	 * @var Machine Machine objekt for technical data reference
	 */
	var $machine = FALSE;
	
	/**
	 * @var string Place, where used machine is currently located.
	 */
	var $location = "";
	
	/**
	 * @var string URL with additional information, e.g. an advertisement of the machine.
	 */
	var $external_url = "";

	/**
	 * @var string Teaser. Used for SEO purposes or as teaser in machine list..
	 */
	var $teaser = "";

	/**
	 * @var string Detailed description.
	 */
	var $description = "";

	/**
	 * @var string[] Download filenames
	 */
	var $downloads = [];

	/**
	 * @var Video[] Videomanager videos
	 */
	var $videos = [];

	/**
	 * @var string Needs translation update? "no", "yes" or "delete"
	 */
	var $translation_needs_update = "delete";

	/**
	 * @var string URL of the used machine
	 */
	private $url = "";

	/**
	 * Constructor. Fetches the object from database
	 * @param int $used_machine_id Object ID
	 * @param int $clang_id Redaxo language ID.
	 */
	 public function __construct($used_machine_id, $clang_id) {
		$this->clang_id = $clang_id;
		$query = "SELECT * FROM ". \rex::getTablePrefix() ."d2u_machinery_used_machines AS used_machines "
				."LEFT JOIN ". \rex::getTablePrefix() ."d2u_machinery_used_machines_lang AS lang "
					."ON used_machines.used_machine_id = lang.used_machine_id "
					."AND clang_id = ". $this->clang_id ." "
				."WHERE used_machines.used_machine_id = ". $used_machine_id;
		$result = \rex_sql::factory();
		$result->setQuery($query);
		$num_rows = $result->getRows();

		if ($num_rows > 0) {
			$this->used_machine_id = $result->getValue("used_machines.used_machine_id");
			$this->name = stripslashes($result->getValue("name"));
			$this->category = new Category($result->getValue("category_id"), $this->clang_id);
			$this->offer_type = $result->getValue("offer_type");
			$this->category->setOfferType($this->offer_type);
			$this->availability = $result->getValue("availability");
			$this->product_number = $result->getValue("product_number");
			$this->manufacturer = $result->getValue("manufacturer");
			$this->year_built = $result->getValue("year_built");
			$this->price = $result->getValue("price");
			$this->currency_code = $result->getValue("currency_code");
			$this->vat = $result->getValue("vat");
			if($result->getValue("online_status") != "") {
				$this->online_status = $result->getValue("online_status");
			}
			$this->pics = preg_grep('/^\s*$/s', explode(",", $result->getValue("pics")), PREG_GREP_INVERT);
			if($result->getValue("machine_id") > 0) {
				$this->machine = new Machine($result->getValue("machine_id"), $clang_id);
			}
			$this->location = $result->getValue("location");
			$this->external_url = $result->getValue("external_url");
			$this->description = stripslashes(htmlspecialchars_decode($result->getValue("description")));
			$this->downloads = preg_grep('/^\s*$/s', explode(",", $result->getValue("downloads")), PREG_GREP_INVERT);
			$this->teaser = stripslashes(htmlspecialchars_decode($result->getValue("teaser")));
			if($result->getValue("translation_needs_update") != "") {
				$this->translation_needs_update = $result->getValue("translation_needs_update");
			}

			// URL des externen Links bei Bedarf korrigieren
			if(strlen($this->external_url) > 3 && substr($this->external_url, 0, 4) != "http") {
				$this->external_url = "http://". $this->external_url;
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
	}

	/**
	 * Changes the status of a machine
	 */
	public function changeStatus() {
		if($this->online_status == "online") {
			if($this->used_machine_id > 0) {
				$query = "UPDATE ". \rex::getTablePrefix() ."d2u_machinery_used_machines "
					."SET online_status = 'offline' "
					."WHERE used_machine_id = ". $this->used_machine_id;
				$result = \rex_sql::factory();
				$result->setQuery($query);
			}
			$this->online_status = "offline";

			// Remove from export
			if(rex_plugin::get("d2u_machinery", "export")->isAvailable()) {
				ExportedUsedMachine::removeMachineFromAllExports($this->used_machine_id);
			}
		}
		else {
			if($this->used_machine_id > 0) {
				$query = "UPDATE ". \rex::getTablePrefix() ."d2u_machinery_used_machines "
					."SET online_status = 'online' "
					."WHERE used_machine_id = ". $this->used_machine_id;
				$result = \rex_sql::factory();
				$result->setQuery($query);
			}
			$this->online_status = "online";			
		}
		
		// Don't forget to regenerate URL cache / search_it index
		\d2u_addon_backend_helper::generateUrlCache();
	}
	
	/**
	 * Deletes the object.
	 * @param int $delete_all If TRUE, all translations and main object are deleted. If 
	 * FALSE, only this translation will be deleted.
	 */
	public function delete($delete_all = TRUE) {
		$query_lang = "DELETE FROM ". \rex::getTablePrefix() ."d2u_machinery_used_machines_lang "
			."WHERE used_machine_id = ". $this->used_machine_id
			. ($delete_all ? '' : ' AND clang_id = '. $this->clang_id) ;
		$result_lang = \rex_sql::factory();
		$result_lang->setQuery($query_lang);
		
		// If no more lang objects are available, delete
		$query_main = "SELECT * FROM ". \rex::getTablePrefix() ."d2u_machinery_used_machines_lang "
			."WHERE used_machine_id = ". $this->used_machine_id;
		$result_main = \rex_sql::factory();
		$result_main->setQuery($query_main);
		if($result_main->getRows() == 0) {
			$query = "DELETE FROM ". \rex::getTablePrefix() ."d2u_machinery_used_machines "
				."WHERE used_machine_id = ". $this->used_machine_id;
			$result = \rex_sql::factory();
			$result->setQuery($query);
		}

		// Remove from export
		if(rex_plugin::get("d2u_machinery", "export")->isAvailable()) {
			ExportedUsedMachine::removeMachineFromAllExports($this->used_machine_id);
		}

		// Don't forget to regenerate URL cache / search_it index
		\d2u_addon_backend_helper::generateUrlCache();

		// Delete from YRewrite forward list
		if(rex_addon::get('yrewrite')->isAvailable()) {
			if($delete_all) {
				foreach(rex_clang::getAllIds() as $clang_id) {
					$lang_object = new self($this->used_machine_id, $clang_id);
					$query_forward = "DELETE FROM ". \rex::getTablePrefix() ."yrewrite_forward "
						."WHERE extern = '". $lang_object->getURL(TRUE) ."'";
					$result_forward = \rex_sql::factory();
					$result_forward->setQuery($query_forward);
				}
			}
			else {
				$query_forward = "DELETE FROM ". \rex::getTablePrefix() ."yrewrite_forward "
					."WHERE extern = '". $this->getURL(TRUE) ."'";
				$result_forward = \rex_sql::factory();
				$result_forward->setQuery($query_forward);
			}
		}
	}
	
	/**
	 * Get all used machines.
	 * @param int $clang_id Redaxo clang id.
	 * @param boolean $only_online Show only online used machines
	 * @param string $offer_type Either "sale", "rent" or leave string empty for both
	 * @return UsedMachine[] Array with UsedMachines objects. The key is used_machine_id
	 */
	public static function getAll($clang_id, $only_online = FALSE, $offer_type = "") {
		$query = "SELECT lang.used_machine_id FROM ". \rex::getTablePrefix() ."d2u_machinery_used_machines_lang AS lang "
			."LEFT JOIN ". \rex::getTablePrefix() ."d2u_machinery_used_machines AS machine "
				."ON lang.used_machine_id = machine.used_machine_id AND clang_id = ". $clang_id ." ";
		if($only_online || $offer_type != "") {
			$where = [];
			if($only_online) {
				$where[] = "online_status = 'online'";
			}
			if($offer_type != "") {
				$where[] = "offer_type = '". $offer_type ."'";
			}
			$query .= "WHERE ". implode(" AND ", $where);
		}
		$query .= " ORDER BY manufacturer, name";
		$result = \rex_sql::factory();
		$result->setQuery($query);
		
		$used_machines = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$used_machines[$result->getValue("used_machine_id")] = new UsedMachine($result->getValue("used_machine_id"), $clang_id);
			$result->next();
		}
		return $used_machines;
	}
	
	/**
	 * Get an extended Teaser. This is the teaser combined with year built and
	 * price.
	 * @return string Extended Teaser
	 */
	public function getExtendedTeaser() {
		$extended_teaser = "";
		if ($this->offer_type == "rent") {
			$extended_teaser .= \Sprog\Wildcard::get('d2u_machinery_used_machines_offer_rent', $this->clang_id) .": ";
		}
		if (strlen($this->teaser) > 0) {
			$extended_teaser .= $this->teaser ."; ";
		}
		if ($this->year_built > 0) {
			$extended_teaser .= \Sprog\Wildcard::get('d2u_machinery_used_machines_year_built', $this->clang_id) .":&nbsp;". $this->year_built ."; ";
		}
		if ($this->price > 0) {
			$extended_teaser .= \Sprog\Wildcard::get('d2u_machinery_used_machines_price', $this->clang_id) .":&nbsp;".
			number_format($this->price, 2, ",", ".") .'&nbsp;'.$this->currency_code ."; ";
		}
		else {
			$extended_teaser .= \Sprog\Wildcard::get('d2u_machinery_used_machines_price', $this->clang_id) .":&nbsp;".
				\Sprog\Wildcard::get('d2u_machinery_used_machines_price_on_request', $this->clang_id);
		}
		return $extended_teaser;
	}
	
	/**
	 * Gets the simply the offer type for used machine.
	 * @param int $used_machine_id Used machine id
	 * @return string Offer type. Either "rent" or "sale"
	 */
	public static function getOfferTypeForUsedMachineId($used_machine_id) {
		$query = "SELECT offer_type FROM ". \rex::getTablePrefix() ."d2u_machinery_used_machines "
				."WHERE used_machine_id = ". $used_machine_id;
		$result = \rex_sql::factory();
		$result->setQuery($query);

		if($result->getRows() > 0) {
			return $result->getValue("offer_type");
		}
		
		return "";
	}
	
	/**
	 * Gets the used machines for a machine. Only machines with online status "online"
	 * will be returned.
	 * @param Machine $machine Machine for which used machines should be searched for
	 * @return UsedMachine[] Array full of used machines ;-)
	 */
	public static function getUsedMachinesForMachine($machine) {
		$query = "SELECT used_machine_id FROM ". \rex::getTablePrefix() ."d2u_machinery_used_machines "
				."WHERE machine_id = ". $machine->machine_id ." "
					."AND online_status = 'online'";

		$result = \rex_sql::factory();
		$result->setQuery($query);
		
		$used_machines = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$used_machines[] = new UsedMachine($result->getValue("used_machine_id"), $machine->clang_id);
			$result->next();
		}
		
		return $used_machines;
	}
	
	/**
	 * Gets the used machines for a category. Only machines with online status "online"
	 * will be returned.
	 * @param Category $category Category.
	 * @param string $offer_type Either "sale", "rent" or leave string empty for both
	 * @return UsedMachine[] Array full of used machines ;-)
	 */
	static function getUsedMachinesForCategory($category, $offer_type = "") {
		$query = "SELECT used_machine_id FROM ". \rex::getTablePrefix() ."d2u_machinery_used_machines "
				."WHERE category_id = ". $category->category_id ." "
					."AND online_status = 'online' ";
		if($offer_type != "") {
			$query .= "AND offer_type = '". $offer_type ."' ";

		}
		$query .= "ORDER BY manufacturer, name";
		$result = \rex_sql::factory();
		$result->setQuery($query);
		
		$used_machines = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$used_machines[] = new UsedMachine($result->getValue("used_machine_id"), $category->clang_id);
			$result->next();
		}
		
		return $used_machines;
	}
	
	/**
	 * Get objects concerning translation updates
	 * @param int $clang_id Redaxo language ID
	 * @param string $type 'update' or 'missing'
	 * @return UsedMachine[] Array with UsedMachine objects.
	 */
	public static function getTranslationHelperObjects($clang_id, $type) {
		$query = 'SELECT lang.used_machine_id FROM '. \rex::getTablePrefix() .'d2u_machinery_used_machines_lang AS lang '
					.'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_used_machines AS main '
						.'ON main.used_machine_id = lang.used_machine_id '
				."WHERE clang_id = ". $clang_id ." AND translation_needs_update = 'yes' "
				.'ORDER BY manufacturer, name';
		if($type == 'missing') {
			$query = 'SELECT main.used_machine_id FROM '. \rex::getTablePrefix() .'d2u_machinery_used_machines AS main '
					.'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_used_machines_lang AS target_lang '
						.'ON main.used_machine_id = target_lang.used_machine_id AND target_lang.clang_id = '. $clang_id .' '
					.'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_used_machines_lang AS default_lang '
						.'ON main.used_machine_id = default_lang.used_machine_id AND default_lang.clang_id = '. \rex_config::get('d2u_helper', 'default_lang') .' '
					."WHERE target_lang.used_machine_id IS NULL "
					.'ORDER BY manufacturer, name';
			$clang_id = \rex_config::get('d2u_helper', 'default_lang');
		}
		$result = \rex_sql::factory();
		$result->setQuery($query);

		$objects = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$objects[] = new UsedMachine($result->getValue("used_machine_id"), $clang_id);
			$result->next();
		}
		
		return $objects;
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
			if($this->offer_type == 'rent') {
				$parameterArray['used_rent_machine_id'] = $this->used_machine_id;
			}
			else {
				$parameterArray['used_sale_machine_id'] = $this->used_machine_id;
			}
			$article_id = $this->offer_type == "sale" ? $d2u_machinery->getConfig('used_machine_article_id_sale') : $d2u_machinery->getConfig('used_machine_article_id_rent');
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
	
	/**
	 * Updates or inserts the object into database.
	 * @return boolean TRUE if successful
	 */
	public function save() {
		$error = FALSE;
		
		$this->price = $this->price == "" ? 0 : $this->price;
		$this->vat = $this->vat == "" ? 0 : $this->vat;
		$this->year_built = $this->year_built == "" ? 0 : $this->year_built;

		// Save the not language specific part
		$pre_save_object = new UsedMachine($this->used_machine_id, $this->clang_id);
		$regenerate_urls = false;
		if($this->used_machine_id == 0 || $pre_save_object != $this) {
			$query = \rex::getTablePrefix() ."d2u_machinery_used_machines SET "
					."name = '". addslashes($this->name) ."', "
					."category_id = ". $this->category->category_id .", "
					."offer_type = '". $this->offer_type ."', "
					."availability = '". $this->availability ."', "
					."product_number = '". $this->product_number ."', "
					."manufacturer = '". $this->manufacturer ."', "
					."year_built = ". $this->year_built .", "
					."price = ". $this->price .", "
					."currency_code = '". $this->currency_code ."', "
					."vat = ". $this->vat .", "
					."online_status = '". $this->online_status ."', "
					."pics = '". implode(",", $this->pics) ."', "
					."machine_id = ". ($this->machine === FALSE ? 0 : $this->machine->machine_id) .", "
					."location = '". $this->location ."', "
					."external_url = '". $this->external_url ."' ";
			if(\rex_addon::get('d2u_videos')->isAvailable() && count($this->videos) > 0) {
				$query .= ", video_ids = '|". implode("|", array_keys($this->videos)) ."|' ";
			}
			else {
				$query .= ", video_ids = '' ";
			}

			if($this->used_machine_id == 0) {
				$query = "INSERT INTO ". $query;
			}
			else {
				$query = "UPDATE ". $query ." WHERE used_machine_id = ". $this->used_machine_id;
			}
			$result = \rex_sql::factory();
			$result->setQuery($query);
			if($this->used_machine_id == 0) {
				$this->used_machine_id = $result->getLastId();
				$error = $result->hasError();
			}
			
			if(!$error && $pre_save_object->name != $this->name) {
				$regenerate_urls = true;
			}
		}
		
		if($error === FALSE) {
			// Save the language specific part
			$pre_save_object = new UsedMachine($this->used_machine_id, $this->clang_id);
			if($pre_save_object != $this) {
				$query = "REPLACE INTO ". \rex::getTablePrefix() ."d2u_machinery_used_machines_lang SET "
						."used_machine_id = '". $this->used_machine_id ."', "
						."clang_id = '". $this->clang_id ."', "
						."teaser = '". htmlspecialchars(addslashes($this->teaser)) ."', "
						."description = '". htmlspecialchars(addslashes($this->description)) ."', "
						."downloads = '". implode(",", $this->downloads) ."', "
						."translation_needs_update = '". $this->translation_needs_update ."', "
						."updatedate = CURRENT_TIMESTAMP, "
						."updateuser = '". \rex::getUser()->getLogin() ."' ";
				$result = \rex_sql::factory();
				$result->setQuery($query);
				$error = $result->hasError();
			}
		}

		// Remove from export
		if($this->online_status == "offline" && rex_plugin::get("d2u_machinery", "export")->isAvailable()) {
			ExportedUsedMachine::removeMachineFromAllExports($this->used_machine_id);
		}

		// Update URLs
		if($regenerate_urls) {
			if($this->offer_type == "rent") {
				\d2u_addon_backend_helper::generateUrlCache('used_rent_machine_id');
			}
			else {
				\d2u_addon_backend_helper::generateUrlCache('used_sale_machine_id');
			}
		}
		
		return !$error;
	}
}