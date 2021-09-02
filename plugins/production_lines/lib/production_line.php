<?php
/**
 * Redaxo D2U Machines Addon
 * @author Tobias Krais
 * @author <a href="http://www.design-to-use.de">www.design-to-use.de</a>
 */

/**
 * Production line
 */
class ProductionLine implements \D2U_Helper\ITranslationHelper {
	/**
	 * @var int Database ID
	 */
	var int $production_line_id = 0;
	
	/**
	 * @var int Redaxo clang id
	 */
	var int $clang_id = 0;
	
	/**
	 * @var string Name
	 */
	var string $line_code = "";
	
	/**
	 * @var int[] Industry sectors ids
	 */
	var array $industry_sector_ids = [];
	
	/**
	 * @var int[] machine ids
	 */
	var array $machine_ids = [];
	
	/**
	 * @var int[] Complementary machine ids
	 */
	var array $complementary_machine_ids = [];
	
	/**
	 * @var string[] Picture file names 
	 */
	var array $pictures = [];

	/**
	 * @var int[] Videomanager video ids
	 */
	var array $video_ids = [];

	/**
	 * @var String Status. Either "online" or "offline".
	 */
	var string $online_status = "offline";
	
	/**
	 * @var string Name
	 */
	var string $name = "";
	
	/**
	 * @var string Teaser
	 */
	var string $teaser = "";

	/**
	 * @var string Short description
	 */
	var string $description_short = "";

	/**
	 * @var string Long description
	 */
	var string $description_long = "";

	/**
	 * @var string[] Unique selling proposition
	 */
	var array $usp_ids = [];
	
	/**
	 * @var string "yes" if translation needs update
	 */
	var $translation_needs_update = "delete";

	/**
	 * @var string URL
	 */
	private $url = "";
	
	/**
	 * Constructor. Reads the object stored in database.
	 * @param int $production_line_id Object ID.
	 * @param int $clang_id Redaxo clang id.
	 */
	 public function __construct($production_line_id, $clang_id) {
		$this->clang_id = $clang_id;
		$query = "SELECT * FROM ". \rex::getTablePrefix() ."d2u_machinery_production_lines AS production_lines "
				."LEFT JOIN ". \rex::getTablePrefix() ."d2u_machinery_production_lines_lang AS lang "
					."ON production_lines.production_line_id = lang.production_line_id "
					."AND clang_id = ". $this->clang_id ." "
				."WHERE production_lines.production_line_id = ". $production_line_id;
		$result = \rex_sql::factory();
		$result->setQuery($query);
		$num_rows = $result->getRows();

		if ($num_rows > 0) {
			$this->production_line_id = $result->getValue("production_line_id");
			$this->complementary_machine_ids = preg_grep('/^\s*$/s', explode("|", $result->getValue("complementary_machine_ids")), PREG_GREP_INVERT);
			$this->description_long = stripslashes($result->getValue("description_long"));
			$this->description_short = stripslashes($result->getValue("description_short"));
			if(rex_plugin::get('d2u_machinery', 'industry_sectors')->isAvailable()) {
				$this->industry_sector_ids = preg_grep('/^\s*$/s', explode("|", $result->getValue("industry_sector_ids")), PREG_GREP_INVERT);
			}
			$this->line_code = stripslashes($result->getValue("line_code"));
			$this->machine_ids = preg_grep('/^\s*$/s', explode("|", $result->getValue("machine_ids")), PREG_GREP_INVERT);
			$this->name = stripslashes($result->getValue("name"));
			$this->online_status = $result->getValue("online_status");
			$this->pictures = preg_grep('/^\s*$/s', explode(",", $result->getValue("pictures")), PREG_GREP_INVERT);
			$this->teaser = stripslashes($result->getValue("teaser"));
			if($result->getValue("translation_needs_update") != "") {
				$this->translation_needs_update = $result->getValue("translation_needs_update");
			}
			$this->usp_ids = preg_grep('/^\s*$/s', explode("|", $result->getValue("ups_ids")), PREG_GREP_INVERT);
			if(rex_addon::get('d2u_videos')->isAvailable()) {
				$this->video_ids = preg_grep('/^\s*$/s', explode("|", $result->getValue("video_ids")), PREG_GREP_INVERT);
			}
		}
	}
	
	/**
	 * Changes the online status.
	 */
	public function changeStatus() {
		if($this->online_status == "online") {
			if($this->production_line_id > 0) {
				$query = "UPDATE ". \rex::getTablePrefix() ."d2u_machinery_production_lines "
					."SET online_status = 'offline' "
					."WHERE production_line_id = ". $this->production_line_id;
				$result = \rex_sql::factory();
				$result->setQuery($query);
			}
			$this->online_status = "offline";
		}
		else {
			if($this->production_line_id > 0) {
				$query = "UPDATE ". \rex::getTablePrefix() ."d2u_machinery_production_lines "
					."SET online_status = 'online' "
					."WHERE production_line_id = ". $this->production_line_id;
				$result = \rex_sql::factory();
				$result->setQuery($query);
			}
			$this->online_status = "online";			
		}
		
		// Don't forget to regenerate URL cache / search_it index
		\d2u_addon_backend_helper::generateUrlCache("production_line_id");
	}
	
	/**
	 * Deletes the object in all languages.
	 * @param int $delete_all If TRUE, all translations and main object are deleted. If 
	 * FALSE, only this translation will be deleted.
	 */
	public function delete($delete_all = TRUE) {
		$query_lang = "DELETE FROM ". \rex::getTablePrefix() ."d2u_machinery_production_lines_lang "
			."WHERE production_line_id = ". $this->production_line_id
			. ($delete_all ? '' : ' AND clang_id = '. $this->clang_id) ;
		$result_lang = \rex_sql::factory();
		$result_lang->setQuery($query_lang);
		
		// If no more lang objects are available, delete
		$query_main = "SELECT * FROM ". \rex::getTablePrefix() ."d2u_machinery_production_lines_lang "
			."WHERE production_line_id = ". $this->production_line_id;
		$result_main = \rex_sql::factory();
		$result_main->setQuery($query_main);
		if($result_main->getRows() == 0) {
			$query = "DELETE FROM ". \rex::getTablePrefix() ."d2u_machinery_production_lines "
				."WHERE production_line_id = ". $this->production_line_id;
			$result = \rex_sql::factory();
			$result->setQuery($query);
		}

		// Don't forget to regenerate URL cache / search_it index
		\d2u_addon_backend_helper::generateUrlCache("production_line_id");

		// Delete from YRewrite forward list
		if(rex_addon::get('yrewrite')->isAvailable()) {
			if($delete_all) {
				foreach(rex_clang::getAllIds() as $clang_id) {
					$lang_object = new self($this->production_line_id, $clang_id);
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
	 * Get all objects.
	 * @param int $clang_id Redaxo clang id.
	 * @param boolean $online_only TRUE if only online objects should be returned.
	 * @return ProductionLine[] Array with production line objects.
	 */
	public static function getAll($clang_id, $online_only = FALSE) {
		$query = "SELECT production_line_id FROM ". \rex::getTablePrefix() ."d2u_machinery_production_lines_lang "
			."WHERE clang_id = ". $clang_id ." ";
		$query .= "ORDER BY name";

		$result = \rex_sql::factory();
		$result->setQuery($query);
		
		$production_lines = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$production_line = new ProductionLine($result->getValue("production_line_id"), $clang_id);
			if($online_only && $production_line->isOnline()) {
				$production_lines[] = $production_line;
			}
			else if($online_only === FALSE) {
				$production_lines[] = $production_line;
			}
			$result->next();
		}
		return $production_lines;
	}
	
	/**
	 * Gets the machines referring to this object.
	 * @param boolean $online_only TRUE if only online machines should be returned.
	 * @return Machine[] Machines referring to this object.
	 */
	public function getMachines($online_only = FALSE) {
		$machines = [];
		foreach ($this->machine_ids as $machine_id) {
			$machine = new Machine($machine_id, $this->clang_id);
			if($online_only === FALSE || ($online_only && $machine->online_status == "online")) {
				$machines[] = $machine;
			}
		}
		return $machines;
	}
	
	/**
	 * Get objects concerning translation updates
	 * @param int $clang_id Redaxo language ID
	 * @param string $type 'update' or 'missing'
	 * @return ProductionLine[] Array with ProductionLine objects.
	 */
	public static function getTranslationHelperObjects($clang_id, $type) {
		$query = 'SELECT production_line_id FROM '. \rex::getTablePrefix() .'d2u_machinery_production_lines_lang '
				."WHERE clang_id = ". $clang_id ." AND translation_needs_update = 'yes' "
				.'ORDER BY name';
		if($type == 'missing') {
			$query = 'SELECT main.production_line_id FROM '. \rex::getTablePrefix() .'d2u_machinery_production_lines AS main '
					.'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_production_lines_lang AS target_lang '
						.'ON main.production_line_id = target_lang.production_line_id AND target_lang.clang_id = '. $clang_id .' '
					.'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_production_lines_lang AS default_lang '
						.'ON main.production_line_id = default_lang.production_line_id AND default_lang.clang_id = '. \rex_config::get('d2u_helper', 'default_lang') .' '
					."WHERE target_lang.production_line_id IS NULL "
					.'ORDER BY default_lang.name';
			$clang_id = \rex_config::get('d2u_helper', 'default_lang');
		}
		$result = \rex_sql::factory();
		$result->setQuery($query);

		$objects = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$objects[] = new ProductionLine($result->getValue("production_line_id"), $clang_id);
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
			$parameterArray['production_line_id'] = $this->production_line_id;
			$this->url = rex_getUrl($d2u_machinery->getConfig('article_id'), $this->clang_id, $parameterArray, "&");
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
	 * Returns if object is used and thus online.
	 * @return boolean TRUE if object is online, otherwise FALSE.
	 */
	public function isOnline() {
		return ($this->online_status == 'online');
	}	
	
	/**
	 * Updates or inserts the object into database.
	 * @return boolean TRUE if successful
	 */
	public function save() {
		$error = FALSE;

		// Save the not language specific part
		$pre_save_object = new ProductionLine($this->production_line_id, $this->clang_id);
		
		// saving the rest
		if($this->production_line_id == 0 || $pre_save_object != $this) {
			$query = \rex::getTablePrefix() ."d2u_machinery_production_lines SET "
					."complementary_machine_ids = '|". implode("|", $this->complementary_machine_ids) ."|', "
					."industry_sector_ids = '|". implode("|", $this->industry_sector_ids) ."|', "
					."line_code = '". $this->line_code ."', "
					."machine_ids = '|". implode("|", $this->machine_ids) ."|', "
					."online_status = '". $this->online_status ."', "
					."pictures = '". implode(",", $this->pictures) ."', "
					."usp_ids = '|". implode("|", $this->usp_ids) ."|', "
					."video_ids = '|". implode("|", $this->video_ids) ."|' ";

			if($this->production_line_id == 0) {
				$query = "INSERT INTO ". $query;
			}
			else {
				$query = "UPDATE ". $query ." WHERE production_line_id = ". $this->production_line_id;
			}

			$result = \rex_sql::factory();
			$result->setQuery($query);
			if($this->production_line_id == 0) {
				$this->production_line_id = $result->getLastId();
				$error = $result->hasError();
			}
		}
		
		$regenerate_urls = false;
		if($error === FALSE) {
			// Save the language specific part
			$pre_save_object = new ProductionLine($this->production_line_id, $this->clang_id);
			if($pre_save_object != $this) {
				$query = "REPLACE INTO ". \rex::getTablePrefix() ."d2u_machinery_production_lines_lang SET "
						."production_line_id = '". $this->production_line_id ."', "
						."clang_id = '". $this->clang_id ."', "
						."description_long = '". addslashes($this->description_long) ."', "
						."description_short = '". addslashes($this->description_short) ."', "
						."name = '". addslashes($this->name) ."', "
						."teaser = '". addslashes($this->teaser) ."', "
						."translation_needs_update = '". $this->translation_needs_update ."', "
						."updatedate = CURRENT_TIMESTAMP, "
						."updateuser = '". \rex::getUser()->getLogin() ."' ";

				$result = \rex_sql::factory();
				$result->setQuery($query);
				$error = $result->hasError();
				
				if(!$error && $pre_save_object->name != $this->name) {
					$regenerate_urls = true;
				}
			}
		}
 
		// Update URLs
		if($regenerate_urls) {
			\d2u_addon_backend_helper::generateUrlCache('production_line_id');
		}

		return !$error;
	}
}