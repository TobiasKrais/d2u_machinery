<?php
/**
 * Redaxo D2U Machines Addon
 * @author Tobias Krais
 * @author <a href="http://www.design-to-use.de">www.design-to-use.de</a>
 */

/**
 * Agitator Type
 */
class AgitatorType {
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
	 * @var int[] Array with agitator_ids
	 */
	var $agitator_ids = array();
	
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
		$query = "SELECT * FROM ". rex::getTablePrefix() ."d2u_machinery_agitator_types AS agitator_types "
				."LEFT JOIN ". rex::getTablePrefix() ."d2u_machinery_agitator_types_lang AS lang "
					."ON agitator_types.agitator_type_id = lang.agitator_type_id "
				."WHERE agitator_types.agitator_type_id = ". $agitator_type_id ." AND clang_id = ". $this->clang_id ." ";
		$result = rex_sql::factory();
		$result->setQuery($query);
		$num_rows = $result->getRows();

		if ($num_rows > 0) {
			$this->agitator_type_id = $result->getValue("agitator_type_id");
			$this->name = $result->getValue("name");
			$this->pic = $result->getValue("pic");
			$this->agitator_ids = preg_grep('/^\s*$/s', explode("|", $result->getValue("agitator_ids")), PREG_GREP_INVERT);
			$this->translation_needs_update = $result->getValue("translation_needs_update");
		}
	}
	
	/**
	 * Deletes the object in all languages.
	 * @param int $delete_all If TRUE, all translations and main object are deleted. If 
	 * FALSE, only this translation will be deleted.
	 */
	public function delete($delete_all = TRUE) {
		if($delete_all) {
			$query_lang = "DELETE FROM ". rex::getTablePrefix() ."d2u_machinery_agitator_types_lang "
				."WHERE agitator_type_id = ". $this->agitator_type_id;
			$result_lang = rex_sql::factory();
			$result_lang->setQuery($query_lang);

			$query = "DELETE FROM ". rex::getTablePrefix() ."d2u_machinery_agitator_types "
				."WHERE agitator_type_id = ". $this->agitator_type_id;
			$result = rex_sql::factory();
			$result->setQuery($query);
		}
		else {
			$query_lang = "DELETE FROM ". rex::getTablePrefix() ."d2u_machinery_agitator_types_lang "
				."WHERE agitator_type_id = ". $this->agitator_type_id ." AND clang_id = ". $this->clang_id;
			$result_lang = rex_sql::factory();
			$result_lang->setQuery($query_lang);
		}
	}
	
	/**
	 * Get all agitator types.
	 * @param int $clang_id Redaxo clang id.
	 * @return AgitatorType[] Array with AgitatorType objects.
	 */
	public static function getAll($clang_id) {
		$query = "SELECT agitator_type_id FROM ". rex::getTablePrefix() ."d2u_machinery_agitator_types_lang "
			."WHERE clang_id = ". $clang_id ." ";
		$query .= "ORDER BY name";

		$result = rex_sql::factory();
		$result->setQuery($query);
		
		$agitator_types = array();
		for($i = 0; $i < $result->getRows(); $i++) {
			$agitator_types[] = new AgitatorType($result->getValue("agitator_type_id"), $clang_id);
			$result->next();
		}
		return $agitator_types;
	}
	
	/**
	 * Gets the machines reffering to this object.
	 * @return Machine[] Machines reffering to this object.
	 */
	public function getRefferingMachines() {
		$query = "SELECT machine_id FROM ". rex::getTablePrefix() ."d2u_machinery_machines "
			."WHERE agitator_type_id = ". $this->agitator_type_id;
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
	 * Updates or inserts the object into database.
	 * @return boolean TRUE if succesful
	 */
	public function save() {
		$error = 0;

		// Save the not language specific part
		$pre_save_agitator_type = new AgitatorType($this->agitator_type_id, $this->clang_id);
		
		// saving the rest
		if($this->agitator_type_id == 0 || $pre_save_agitator_type != $this) {
			$query = rex::getTablePrefix() ."d2u_machinery_agitator_types SET "
					."agitator_ids = '|". implode("|", $this->agitator_ids) ."|', "
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
		
		if($error == 0) {
			// Save the language specific part
			$pre_save_agitator_type = new AgitatorType($this->agitator_type_id, $this->clang_id);
			if($pre_save_agitator_type != $this) {
				$query = "REPLACE INTO ". rex::getTablePrefix() ."d2u_machinery_agitator_types_lang SET "
						."agitator_type_id = '". $this->agitator_type_id ."', "
						."clang_id = '". $this->clang_id ."', "
						."name = '". $this->name ."', "
						."translation_needs_update = '". $this->translation_needs_update ."' ";

				$result = rex_sql::factory();
				$result->setQuery($query);
				$error = $result->hasError();
			}
		}
 
		return $error;
	}
}