<?php
/**
 * Redaxo D2U Machines Addon
 * @author Tobias Krais
 * @author <a href="http://www.design-to-use.de">www.design-to-use.de</a>
 */

/**
 * Agitator Type
 */
class AgitatorType implements \D2U_Helper\ITranslationHelper {
	/**
	 * @var int Database ID
	 */
	var $agitator_type_id = 0;
	
	/**
	 * @var int Redaxo clang id
	 */
	var $clang_id = 0;
	
	/**
	 * @var string Name
	 */
	var $name = "";
	
	/**
	 * @var string Preview picture file name 
	 */
	var $pic = "";
	
	/**
	 * @var int[] Array with agitator_type_ids
	 */
	var $agitator_type_ids = [];
	
	/**
	 * @var string "yes" if translation needs update
	 */
	var $translation_needs_update = "delete";

	/**
	 * Constructor. Reads a agitator type from database.
	 * @param int $agitator_type_id Agitator type ID.
	 * @param int $clang_id Redaxo clang id.
	 */
	 public function __construct($agitator_type_id, $clang_id) {
		$this->clang_id = $clang_id;
		$query = "SELECT * FROM ". \rex::getTablePrefix() ."d2u_machinery_agitator_types AS agitator_types "
				."LEFT JOIN ". \rex::getTablePrefix() ."d2u_machinery_agitator_types_lang AS lang "
					."ON agitator_types.agitator_type_id = lang.agitator_type_id "
					."AND clang_id = ". $this->clang_id ." "
				."WHERE agitator_types.agitator_type_id = ". $agitator_type_id;
		$result = rex_sql::factory();
		$result->setQuery($query);
		$num_rows = $result->getRows();

		if ($num_rows > 0) {
			$this->agitator_type_id = $result->getValue("agitator_type_id");
			$this->name = $result->getValue("name");
			$this->pic = $result->getValue("pic");
			$this->agitator_type_ids = preg_grep('/^\s*$/s', explode("|", $result->getValue("agitator_type_ids")), PREG_GREP_INVERT);
			if($result->getValue("translation_needs_update") != "") {
				$this->translation_needs_update = $result->getValue("translation_needs_update");
			}
		}
	}
	
	/**
	 * Deletes the object.
	 * @param int $delete_all If TRUE, all translations and main object are deleted. If 
	 * FALSE, only this translation will be deleted.
	 */
	public function delete($delete_all = TRUE) {
		$query_lang = "DELETE FROM ". \rex::getTablePrefix() ."d2u_machinery_agitator_types_lang "
			."WHERE agitator_type_id = ". $this->agitator_type_id
			. ($delete_all ? '' : ' AND clang_id = '. $this->clang_id) ;
		$result_lang = rex_sql::factory();
		$result_lang->setQuery($query_lang);
		
		// If no more lang objects are available, delete
		$query_main = "SELECT * FROM ". \rex::getTablePrefix() ."d2u_machinery_agitator_types_lang "
			."WHERE agitator_type_id = ". $this->agitator_type_id;
		$result_main = rex_sql::factory();
		$result_main->setQuery($query_main);
		if($result_main->getRows() == 0) {
			$query = "DELETE FROM ". \rex::getTablePrefix() ."d2u_machinery_agitator_types "
				."WHERE agitator_type_id = ". $this->agitator_type_id;
			$result = rex_sql::factory();
			$result->setQuery($query);
		}
	}
	
	/**
	 * Get all agitator for this type.
	 * @return Agitator[] Array with Agitator objects.
	 */
	public function getAgitators() {
		$agitators = [];
		foreach($this->agitator_type_ids as $agitator_type_id) {
			$agitators[] = new Agitator($agitator_type_id, $this->clang_id);
		}
		return $agitators;
	}
	
	/**
	 * Get all agitator types.
	 * @param int $clang_id Redaxo clang id.
	 * @return AgitatorType[] Array with AgitatorType objects.
	 */
	public static function getAll($clang_id) {
		$query = "SELECT agitator_type_id FROM ". \rex::getTablePrefix() ."d2u_machinery_agitator_types_lang "
			."WHERE clang_id = ". $clang_id ." ";
		$query .= "ORDER BY name";

		$result = rex_sql::factory();
		$result->setQuery($query);
		
		$agitator_types = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$agitator_types[] = new AgitatorType($result->getValue("agitator_type_id"), $clang_id);
			$result->next();
		}
		return $agitator_types;
	}
	
	/**
	 * Gets the machines referring to this object.
	 * @return Machine[] Machines referring to this object.
	 */
	public function getReferringMachines() {
		$query = "SELECT machine_id FROM ". \rex::getTablePrefix() ."d2u_machinery_machines "
			."WHERE agitator_type_id = ". $this->agitator_type_id;
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
	 * @return AgitatorType[] Array with AgitatorType objects.
	 */
	public static function getTranslationHelperObjects($clang_id, $type) {
		$query = 'SELECT agitator_type_id FROM '. \rex::getTablePrefix() .'d2u_machinery_agitator_types_lang '
				."WHERE clang_id = ". $clang_id ." AND translation_needs_update = 'yes' "
				.'ORDER BY name';
		if($type == 'missing') {
			$query = 'SELECT main.agitator_type_id FROM '. \rex::getTablePrefix() .'d2u_machinery_agitator_types AS main '
					.'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_agitator_types_lang AS target_lang '
						.'ON main.agitator_type_id = target_lang.agitator_type_id AND target_lang.clang_id = '. $clang_id .' '
					.'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_agitator_types_lang AS default_lang '
						.'ON main.agitator_type_id = default_lang.agitator_type_id AND default_lang.clang_id = '. \rex_config::get('d2u_helper', 'default_lang') .' '
					."WHERE target_lang.agitator_type_id IS NULL "
					.'ORDER BY default_lang.name';
			$clang_id = \rex_config::get('d2u_helper', 'default_lang');
		}
		$result = \rex_sql::factory();
		$result->setQuery($query);

		$objects = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$objects[] = new AgitatorType($result->getValue("agitator_type_id"), $clang_id);
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
		$pre_save_agitator_type = new AgitatorType($this->agitator_type_id, $this->clang_id);
		
		// saving the rest
		if($this->agitator_type_id == 0 || $pre_save_agitator_type != $this) {
			$query = \rex::getTablePrefix() ."d2u_machinery_agitator_types SET "
					."agitator_type_ids = '|". implode("|", $this->agitator_type_ids) ."|', "
					."pic = '". $this->pic ."' ";

			if($this->agitator_type_id == 0) {
				$query = "INSERT INTO ". $query;
			}
			else {
				$query = "UPDATE ". $query ." WHERE agitator_type_id = ". $this->agitator_type_id;
			}

			$result = rex_sql::factory();
			$result->setQuery($query);
			if($this->agitator_type_id == 0) {
				$this->agitator_type_id = $result->getLastId();
				$error = $result->hasError();
			}
		}
		
		if($error === FALSE) {
			// Save the language specific part
			$pre_save_agitator_type = new AgitatorType($this->agitator_type_id, $this->clang_id);
			if($pre_save_agitator_type != $this) {
				$query = "REPLACE INTO ". \rex::getTablePrefix() ."d2u_machinery_agitator_types_lang SET "
						."agitator_type_id = '". $this->agitator_type_id ."', "
						."clang_id = '". $this->clang_id ."', "
						."name = '". $this->name ."', "
						."translation_needs_update = '". $this->translation_needs_update ."' ";

				$result = rex_sql::factory();
				$result->setQuery($query);
				$error = $result->hasError();
			}
		}
 
		return !$error;
	}
}