<?php
/**
 * Redaxo D2U Machines Addon
 * @author Tobias Krais
 * @author <a href="http://www.design-to-use.de">www.design-to-use.de</a>
 */

/**
 * Welding
 */
class Welding {
	/**
	 * @var int Database ID
	 */
	var $welding_id = 0;
	
	/**
	 * @var int Redaxo clang id
	 */
	var $clang_id = 0;
	
	/**
	 * @var string Internal name
	 */
	var $internal_name = "";

	/**
	 * @var string Name
	 */
	var $name = "";
	
	/**
	 * @var string "yes" if translation needs update
	 */
	var $translation_needs_update = "delete";

	/**
	 * Constructor. Reads object stored in database.
	 * @param int $welding_id Object ID.
	 * @param int $clang_id Redaxo clang id.
	 */
	 public function __construct($welding_id, $clang_id) {
		$this->clang_id = $clang_id;
		$query = "SELECT * FROM ". rex::getTablePrefix() ."d2u_machinery_steel_welding AS weldings "
				."LEFT JOIN ". rex::getTablePrefix() ."d2u_machinery_steel_welding_lang AS lang "
					."ON weldings.welding_id = lang.welding_id "
					."AND clang_id = ". $this->clang_id ." "
				."WHERE weldings.welding_id = ". $welding_id;
		$result = rex_sql::factory();
		$result->setQuery($query);
		$num_rows = $result->getRows();

		if ($num_rows > 0) {
			$this->welding_id = $result->getValue("welding_id");
			$this->internal_name = $result->getValue("internal_name");
			$this->name = $result->getValue("name");
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
		$query_lang = "DELETE FROM ". rex::getTablePrefix() ."d2u_machinery_steel_welding_lang "
			."WHERE welding_id = ". $this->welding_id
			. ($delete_all ? '' : ' AND clang_id = '. $this->clang_id) ;
		$result_lang = rex_sql::factory();
		$result_lang->setQuery($query_lang);
		
		// If no more lang objects are available, delete
		$query_main = "SELECT * FROM ". rex::getTablePrefix() ."d2u_machinery_steel_welding_lang "
			."WHERE welding_id = ". $this->welding_id;
		$result_main = rex_sql::factory();
		$result_main->setQuery($query_main);
		if($result_main->getRows() == 0) {
			$query = "DELETE FROM ". rex::getTablePrefix() ."d2u_machinery_steel_welding "
				."WHERE welding_id = ". $this->welding_id;
			$result = rex_sql::factory();
			$result->setQuery($query);
		}
	}
	
	/**
	 * Get all weldings.
	 * @param int $clang_id Redaxo clang id.
	 * @return Welding[] Array with Welding objects.
	 */
	public static function getAll($clang_id) {
		$query = "SELECT welding_id FROM ". rex::getTablePrefix() ."d2u_machinery_steel_welding_lang "
			."WHERE clang_id = ". $clang_id ." ";
		$query .= "ORDER BY name";

		$result = rex_sql::factory();
		$result->setQuery($query);
		
		$weldings = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$weldings[] = new Welding($result->getValue("welding_id"), $clang_id);
			$result->next();
		}
		return $weldings;
	}
	
	/**
	 * Gets the machines reffering to this object.
	 * @return Machine[] Machines reffering to this object.
	 */
	public function getRefferingMachines() {
		$query = "SELECT machine_id FROM ". rex::getTablePrefix() ."d2u_machinery_machines "
			."WHERE welding_process_ids LIKE '%|". $this->welding_id ."|%'";
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
	 * Updates or inserts the object into database.
	 * @return boolean TRUE if succesful
	 */
	public function save() {
		$error = 0;

		// Save the not language specific part
		$pre_save_welding = new Welding($this->welding_id, $this->clang_id);
		
		// saving the rest
		if($this->welding_id == 0 || $pre_save_welding != $this) {
			$query = rex::getTablePrefix() ."d2u_machinery_steel_welding SET "
					."internal_name = '". $this->internal_name ."' ";
			if($this->welding_id == 0) {
				$query = "INSERT INTO ". $query;
			}
			else {
				$query = "UPDATE ". $query ." WHERE welding_id = ". $this->welding_id;
			}

			$result = rex_sql::factory();
			$result->setQuery($query);
			if($this->welding_id == 0) {
				$this->welding_id = $result->getLastId();
				$error = $result->hasError();
			}
		}
		
		if($error == 0) {
			// Save the language specific part
			$pre_save_welding = new Welding($this->welding_id, $this->clang_id);
			if($pre_save_welding != $this) {
				$query = "REPLACE INTO ". rex::getTablePrefix() ."d2u_machinery_steel_welding_lang SET "
						."welding_id = '". $this->welding_id ."', "
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