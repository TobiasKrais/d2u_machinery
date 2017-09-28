<?php
/**
 * Redaxo D2U Machines Addon
 * @author Tobias Krais
 * @author <a href="http://www.design-to-use.de">www.design-to-use.de</a>
 */

/**
 * Industry sector
 */
class IndustrySector {
	/**
	 * @var int Database ID
	 */
	var $industry_sector_id = 0;
	
	/**
	 * @var int Redaxo clang id
	 */
	var $clang_id = 0;
	
	/**
	 * @var string Name
	 */
	var $name = "";
	
	/**
	 * @var string Teaser
	 */
	var $teaser = "";

	/**
	 * @var string Preview picture file name 
	 */
	var $pic = "";
	
	/**
	 * @var String Status. Either "online" or "offline".
	 */
	var $online_status = "offline";
	
	/**
	 * @var string "yes" if translation needs update
	 */
	var $translation_needs_update = "delete";

	/**
	 * @var string URL
	 */
	private $url = "";
	
	/**
	 * Constructor. Reads an Industry Sector stored in database.
	 * @param int $industry_sector_id Object ID.
	 * @param int $clang_id Redaxo clang id.
	 */
	 public function __construct($industry_sector_id, $clang_id) {
		$this->clang_id = $clang_id;
		$query = "SELECT * FROM ". rex::getTablePrefix() ."d2u_machinery_industry_sectors AS industry_sectors "
				."LEFT JOIN ". rex::getTablePrefix() ."d2u_machinery_industry_sectors_lang AS lang "
					."ON industry_sectors.industry_sector_id = lang.industry_sector_id "
					."AND clang_id = ". $this->clang_id ." "
				."WHERE industry_sectors.industry_sector_id = ". $industry_sector_id;
		$result = rex_sql::factory();
		$result->setQuery($query);
		$num_rows = $result->getRows();

		if ($num_rows > 0) {
			$this->industry_sector_id = $result->getValue("industry_sector_id");
			$this->name = $result->getValue("name");
			$this->teaser = htmlspecialchars_decode($result->getValue("teaser"));
			$this->pic = $result->getValue("pic");
			$this->online_status = $result->getValue("online_status");
			if($result->getValue("translation_needs_update") != "") {
				$this->translation_needs_update = $result->getValue("translation_needs_update");
			}
		}
	}
	
	/**
	 * Changes the online status.
	 */
	public function changeStatus() {
		if($this->online_status == "online") {
			if($this->industry_sector_id > 0) {
				$query = "UPDATE ". rex::getTablePrefix() ."d2u_machinery_industry_sectors "
					."SET online_status = 'offline' "
					."WHERE industry_sector_id = ". $this->industry_sector_id;
				$result = rex_sql::factory();
				$result->setQuery($query);
			}
			$this->online_status = "offline";
		}
		else {
			if($this->industry_sector_id > 0) {
				$query = "UPDATE ". rex::getTablePrefix() ."d2u_machinery_industry_sectors "
					."SET online_status = 'online' "
					."WHERE industry_sector_id = ". $this->industry_sector_id;
				$result = rex_sql::factory();
				$result->setQuery($query);
			}
			$this->online_status = "online";			
		}
	}
	
	/**
	 * Deletes the object in all languages.
	 * @param int $delete_all If TRUE, all translations and main object are deleted. If 
	 * FALSE, only this translation will be deleted.
	 */
	public function delete($delete_all = TRUE) {
		$query_lang = "DELETE FROM ". rex::getTablePrefix() ."d2u_machinery_industry_sectors_lang "
			."WHERE industry_sector_id = ". $this->industry_sector_id
			. ($delete_all ? '' : ' AND clang_id = '. $this->clang_id) ;
		$result_lang = rex_sql::factory();
		$result_lang->setQuery($query_lang);
		
		// If no more lang objects are available, delete
		$query_main = "SELECT * FROM ". rex::getTablePrefix() ."d2u_machinery_industry_sectors_lang "
			."WHERE industry_sector_id = ". $this->industry_sector_id;
		$result_main = rex_sql::factory();
		$result_main->setQuery($query_main);
		if($result_main->getRows() == 0) {
			$query = "DELETE FROM ". rex::getTablePrefix() ."d2u_machinery_industry_sectors "
				."WHERE industry_sector_id = ". $this->industry_sector_id;
			$result = rex_sql::factory();
			$result->setQuery($query);
		}
	}
	
	/**
	 * Get all industry sectors.
	 * @param int $clang_id Redaxo clang id.
	 * @param boolean $online_only TRUE if only online objects should be returned.
	 * @return IndustrySector[] Array with IndustrySector objects.
	 */
	public static function getAll($clang_id, $online_only = FALSE) {
		$query = "SELECT industry_sector_id FROM ". rex::getTablePrefix() ."d2u_machinery_industry_sectors_lang "
			."WHERE clang_id = ". $clang_id ." ";
		$query .= "ORDER BY name";

		$result = rex_sql::factory();
		$result->setQuery($query);
		
		$industry_sectors = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$industry_sector = new IndustrySector($result->getValue("industry_sector_id"), $clang_id);
			if($online_only && $industry_sector->isOnline()) {
				$industry_sectors[] = $industry_sector;
			}
			else if($online_only === FALSE) {
				$industry_sectors[] = $industry_sector;
			}
			$result->next();
		}
		return $industry_sectors;
	}
	
	/**
	 * Get the <link rel="canonical"> tag for page header.
	 * @return Complete tag.
	 */
	public function getCanonicalTag() {
		return '<link rel="canonical" href="'. $this->getURL() .'">';
	}
	
	/**
	 * Gets the machines reffering to this object.
	 * @param boolean $online_only TRUE if only online machines should be returned.
	 * @return Machine[] Machines reffering to this object.
	 */
	public function getMachines($online_only = FALSE) {
		$query = "SELECT machine_id FROM ". rex::getTablePrefix() ."d2u_machinery_machines "
			."WHERE industry_sector_ids LIKE '%|". $this->industry_sector_id ."|%'";
		$result = rex_sql::factory();
		$result->setQuery($query);
		
		$machines = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$machine = new Machine($result->getValue("machine_id"), $this->clang_id);
			if($online_only === FALSE || ($online_only && $machine->online_status == "online")) {
				$machines[] = $machine;
			}
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
				$industry_sector = new IndustrySector($this->industry_sector_id, $rex_clang->getId());
				if($industry_sector->translation_needs_update != "delete" && $industry_sector->online_status == "online") {
					$hreflang_tags .= '<link rel="alternate" type="text/html" hreflang="'. $rex_clang->getCode() .'" href="'. $industry_sector->getURL() .'" title="'. str_replace('"', '', $industry_sector->name) .'">';
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
		return '<title>'. $this->name .' / '. rex::getServerName() .'</title>';
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
			$parameterArray['industry_sector_id'] = $this->industry_sector_id;
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
	 * Returns if object is used and thus online.
	 * @return boolean TRUE if object is online, otherwise FALSE.
	 */
	public function isOnline() {
		$query = "SELECT industry_sector_id FROM ". rex::getTablePrefix() ."d2u_machinery_industry_sectors "
			."WHERE industry_sector_ids LIKE '%|". $this->industry_sector_id ."|%'";
		$result = rex_sql::factory();
		$result->setQuery($query);
		
		if($result->getRows() > 0) {
			return TRUE;
		}
		else {
			return FALSE;
		}
	}	
	
	/**
	 * Updates or inserts the object into database.
	 * @return boolean TRUE if succesful
	 */
	public function save() {
		$error = 0;

		// Save the not language specific part
		$pre_save_industry_sector = new IndustrySector($this->industry_sector_id, $this->clang_id);
		
		// saving the rest
		if($this->industry_sector_id == 0 || $pre_save_industry_sector != $this) {
			$query = rex::getTablePrefix() ."d2u_machinery_industry_sectors SET "
					."online_status = '". $this->online_status ."', "
					."pic = '". $this->pic ."' ";

			if($this->industry_sector_id == 0) {
				$query = "INSERT INTO ". $query;
			}
			else {
				$query = "UPDATE ". $query ." WHERE industry_sector_id = ". $this->industry_sector_id;
			}

			$result = rex_sql::factory();
			$result->setQuery($query);
			if($this->industry_sector_id == 0) {
				$this->industry_sector_id = $result->getLastId();
				$error = $result->hasError();
			}
		}
		
		if($error == 0) {
			// Save the language specific part
			$pre_save_industry_sector = new IndustrySector($this->industry_sector_id, $this->clang_id);
			if($pre_save_industry_sector != $this) {
				$query = "REPLACE INTO ". rex::getTablePrefix() ."d2u_machinery_industry_sectors_lang SET "
						."industry_sector_id = '". $this->industry_sector_id ."', "
						."clang_id = '". $this->clang_id ."', "
						."name = '". $this->name ."', "
						."teaser = '". htmlspecialchars($this->teaser) ."', "
						."translation_needs_update = '". $this->translation_needs_update ."' "
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