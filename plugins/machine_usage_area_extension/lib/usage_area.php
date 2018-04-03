<?php
/**
 * Redaxo D2U Machines Addon
 * @author Tobias Krais
 * @author <a href="http://www.design-to-use.de">www.design-to-use.de</a>
 */

/**
 * Usage Area
 */
class UsageArea implements \D2U_Helper\ITranslationHelper {
	/**
	 * @var int Database ID
	 */
	var $usage_area_id = 0;
	
	/**
	 * @var int Redaxo clang id
	 */
	var $clang_id = 0;
	
	/**
	 * @var int Sort Priority
	 */
	var $priority = 0;
	
	/**
	 * @var string Name
	 */
	var $name = "";
	
	/**
	 * @var int[] Category ids in which object will be available
	 */
	var $category_ids = [];
		
	/**
	 * @var string "yes" if translation needs update
	 */
	var $translation_needs_update = "delete";

	/**
	 * Constructor. Reads an Usage Area stored in database.
	 * @param int $usage_area_id Object ID.
	 * @param int $clang_id Redaxo clang id.
	 */
	 public function __construct($usage_area_id, $clang_id) {
		$this->clang_id = $clang_id;
		$query = "SELECT * FROM ". \rex::getTablePrefix() ."d2u_machinery_usage_areas AS usage_areas "
				."LEFT JOIN ". \rex::getTablePrefix() ."d2u_machinery_usage_areas_lang AS lang "
					."ON usage_areas.usage_area_id = lang.usage_area_id "
					."AND clang_id = ". $this->clang_id ." "
				."WHERE usage_areas.usage_area_id = ". $usage_area_id;
		$result = rex_sql::factory();
		$result->setQuery($query);
		$num_rows = $result->getRows();

		if ($num_rows > 0) {
			$this->usage_area_id = $result->getValue("usage_area_id");
			$this->name = stripslashes($result->getValue("name"));
			$this->priority = $result->getValue("priority");
			$this->category_ids = preg_grep('/^\s*$/s', explode("|", $result->getValue("category_ids")), PREG_GREP_INVERT);
			if($result->getValue("translation_needs_update") != "") {
				$this->translation_needs_update = $result->getValue("translation_needs_update");
			}
		}
	}
	
	/**
	 * Deletes the object in all languages.
	 * @param int $delete_all If TRUE, all translations and main object are deleted. If 
	 * FALSE, only this translation will be deleted.
	 */
	public function delete($delete_all = TRUE) {
		$query_lang = "DELETE FROM ". \rex::getTablePrefix() ."d2u_machinery_usage_areas_lang "
			."WHERE usage_area_id = ". $this->usage_area_id
			. ($delete_all ? '' : ' AND clang_id = '. $this->clang_id) ;
		$result_lang = rex_sql::factory();
		$result_lang->setQuery($query_lang);
		
		// If no more lang objects are available, delete
		$query_main = "SELECT * FROM ". \rex::getTablePrefix() ."d2u_machinery_usage_areas_lang "
			."WHERE usage_area_id = ". $this->usage_area_id;
		$result_main = rex_sql::factory();
		$result_main->setQuery($query_main);
		if($result_main->getRows() == 0) {
			$query = "DELETE FROM ". \rex::getTablePrefix() ."d2u_machinery_usage_areas "
				."WHERE usage_area_id = ". $this->usage_area_id;
			$result = rex_sql::factory();
			$result->setQuery($query);
		}
	}
	
	/**
	 * Get all usage areas.
	 * @param int $clang_id Redaxo clang id.
	 * @param int $category_id Category ID (optional)
	 * @return UsageArea[] Array with UsageArea objects.
	 */
	public static function getAll($clang_id, $category_id = 0) {
		$query = "SELECT lang.usage_area_id FROM ". \rex::getTablePrefix() ."d2u_machinery_usage_areas_lang AS lang "
			."LEFT JOIN ". \rex::getTablePrefix() ."d2u_machinery_usage_areas AS areas "
				."ON lang.usage_area_id = areas.usage_area_id "
			."WHERE clang_id = ". $clang_id ." ";
		if($category_id > 0) {
			$query .= "AND category_ids LIKE '%|". $category_id ."|%' ";
		}
		$query .= "ORDER BY name";

		$result = rex_sql::factory();
		$result->setQuery($query);
		
		$usage_areas = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$usage_areas[] = new UsageArea($result->getValue("usage_area_id"), $clang_id);
			$result->next();
		}
		return $usage_areas;
	}
	
	/**
	 * Gets the machines referring to this object.
	 * @return Machine[] Machines referring to this object.
	 */
	public function getMachines() {
		$query = "SELECT machine_id FROM ". \rex::getTablePrefix() ."d2u_machinery_machines "
			."WHERE usage_area_ids LIKE '%|". $this->usage_area_id ."|%'";
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
	 * Get objects concerning translation updates
	 * @param int $clang_id Redaxo language ID
	 * @param string $type 'update' or 'missing'
	 * @return UsageArea[] Array with UsageArea objects.
	 */
	public static function getTranslationHelperObjects($clang_id, $type) {
		$query = 'SELECT usage_area_id FROM '. \rex::getTablePrefix() .'d2u_machinery_usage_areas_lang '
				."WHERE clang_id = ". $clang_id ." AND translation_needs_update = 'yes' "
				.'ORDER BY name';
		if($type == 'missing') {
			$query = 'SELECT main.usage_area_id FROM '. \rex::getTablePrefix() .'d2u_machinery_usage_areas AS main '
					.'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_usage_areas_lang AS target_lang '
						.'ON main.usage_area_id = target_lang.usage_area_id AND target_lang.clang_id = '. $clang_id .' '
					.'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_usage_areas_lang AS default_lang '
						.'ON main.usage_area_id = default_lang.usage_area_id AND default_lang.clang_id = '. \rex_config::get('d2u_helper', 'default_lang') .' '
					."WHERE target_lang.usage_area_id IS NULL "
					.'ORDER BY default_lang.name';
			$clang_id = \rex_config::get('d2u_helper', 'default_lang');
		}
		$result = \rex_sql::factory();
		$result->setQuery($query);

		$objects = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$objects[] = new UsageArea($result->getValue("usage_area_id"), $clang_id);
			$result->next();
		}
		
		return $objects;
    }
	
	/**
	 * Updates or inserts the object into database.
	 * @return boolean TRUE if successful
	 */
	public function save() {
		$error = FALSE;

		// Save the not language specific part
		$pre_save_usage_area = new UsageArea($this->usage_area_id, $this->clang_id);
		
		// save priority, but only if new or changed
		if($this->priority != $pre_save_usage_area->priority || $this->usage_area_id == 0) {
			$this->setPriority();
		}

		if($this->usage_area_id == 0 || $pre_save_usage_area != $this) {
			$query = \rex::getTablePrefix() ."d2u_machinery_usage_areas SET "
					."category_ids = '|". implode("|", $this->category_ids) ."|', "
					."priority = '". $this->priority ."' ";

			if($this->usage_area_id == 0) {
				$query = "INSERT INTO ". $query;
			}
			else {
				$query = "UPDATE ". $query ." WHERE usage_area_id = ". $this->usage_area_id;
			}

			$result = rex_sql::factory();
			$result->setQuery($query);
			if($this->usage_area_id == 0) {
				$this->usage_area_id = $result->getLastId();
				$error = $result->hasError();
			}
		}
		
		if($error === FALSE) {
			// Save the language specific part
			$pre_save_usage_area = new IndustrySector($this->usage_area_id, $this->clang_id);
			if($pre_save_usage_area != $this) {
				$query = "REPLACE INTO ". \rex::getTablePrefix() ."d2u_machinery_usage_areas_lang SET "
						."usage_area_id = '". $this->usage_area_id ."', "
						."clang_id = '". $this->clang_id ."', "
						."name = '". addslashes($this->name) ."', "
						."translation_needs_update = '". $this->translation_needs_update ."' ";

				$result = rex_sql::factory();
				$result->setQuery($query);
				$error = $result->hasError();
			}
		}
 
		return !$error;
	}

	/**
	 * Reassigns priority to all Usage Areas in database.
	 */
	private function setPriority() {
		// Pull prios from database
		$query = "SELECT usage_area_id, priority FROM ". \rex::getTablePrefix() ."d2u_machinery_usage_areas "
			."WHERE usage_area_id <> ". $this->usage_area_id ." ORDER BY priority";
		$result = rex_sql::factory();
		$result->setQuery($query);
		
		// When prio is too small, set at beginning
		if($this->priority <= 0) {
			$this->priority = 1;
		}
		
		// When prio is too high, simply add at end 
		if($this->priority > $result->getRows()) {
			$this->priority = $result->getRows() + 1;
		}

		$usage_areas = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$usage_areas[$result->getValue("priority")] = $result->getValue("usage_area_id");
			$result->next();
		}
		array_splice($usage_areas, ($this->priority - 1), 0, array($this->usage_area_id));

		// Save all priorities
		foreach($usage_areas as $prio => $usage_area_id) {
			$query = "UPDATE ". \rex::getTablePrefix() ."d2u_machinery_usage_areas "
					."SET priority = ". ($prio + 1) ." " // +1 because array_splice recounts at zero
					."WHERE usage_area_id = ". $usage_area_id;
			$result = rex_sql::factory();
			$result->setQuery($query);
		}
	}
}