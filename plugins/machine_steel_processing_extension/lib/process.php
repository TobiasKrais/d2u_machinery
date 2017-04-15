<?php
/**
 * Redaxo D2U Machines Addon
 * @author Tobias Krais
 * @author <a href="http://www.design-to-use.de">www.design-to-use.de</a>
 */

/**
 * Process
 */
class Process {
	/**
	 * @var int Database ID
	 */
	var $process_id = 0;
	
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
	 * @param int $process_id Object ID.
	 * @param int $clang_id Redaxo clang id.
	 */
	 public function __construct($process_id, $clang_id) {
		$this->clang_id = $clang_id;
		$query = "SELECT * FROM ". rex::getTablePrefix() ."d2u_machinery_steel_process AS processs "
				."LEFT JOIN ". rex::getTablePrefix() ."d2u_machinery_steel_process_lang AS lang "
					."ON processs.process_id = lang.process_id "
					."AND clang_id = ". $this->clang_id ." "
				."WHERE processs.process_id = ". $process_id;
		$result = rex_sql::factory();
		$result->setQuery($query);
		$num_rows = $result->getRows();

		if ($num_rows > 0) {
			$this->process_id = $result->getValue("process_id");
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
		if($delete_all) {
			$query_lang = "DELETE FROM ". rex::getTablePrefix() ."d2u_machinery_steel_process_lang "
				."WHERE process_id = ". $this->process_id;
			$result_lang = rex_sql::factory();
			$result_lang->setQuery($query_lang);

			$query = "DELETE FROM ". rex::getTablePrefix() ."d2u_machinery_steel_process "
				."WHERE process_id = ". $this->process_id;
			$result = rex_sql::factory();
			$result->setQuery($query);
		}
		else {
			$query_lang = "DELETE FROM ". rex::getTablePrefix() ."d2u_machinery_steel_process_lang "
				."WHERE process_id = ". $this->process_id ." AND clang_id = ". $this->clang_id;
			$result_lang = rex_sql::factory();
			$result_lang->setQuery($query_lang);
		}
	}
	
	/**
	 * Get all processs.
	 * @param int $clang_id Redaxo clang id.
	 * @return Process[] Array with Process objects.
	 */
	public static function getAll($clang_id) {
		$query = "SELECT process_id FROM ". rex::getTablePrefix() ."d2u_machinery_steel_process_lang "
			."WHERE clang_id = ". $clang_id ." ";
		$query .= "ORDER BY name";

		$result = rex_sql::factory();
		$result->setQuery($query);
		
		$processs = array();
		for($i = 0; $i < $result->getRows(); $i++) {
			$processs[] = new Process($result->getValue("process_id"), $clang_id);
			$result->next();
		}
		return $processs;
	}
	
	/**
	 * Gets the machines reffering to this object.
	 * @return Machine[] Machines reffering to this object.
	 */
	public function getRefferingMachines() {
		$query = "SELECT machine_id FROM ". rex::getTablePrefix() ."d2u_machinery_machines "
			."WHERE process_ids LIKE '%|". $this->process_id ."|%'";
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
		$pre_save_process = new Process($this->process_id, $this->clang_id);
		
		// saving the rest
		if($this->process_id == 0 || $pre_save_process != $this) {
			$query = rex::getTablePrefix() ."d2u_machinery_steel_process SET "
					."internal_name = '". $this->internal_name ."' ";
			if($this->process_id == 0) {
				$query = "INSERT INTO ". $query;
			}
			else {
				$query = "UPDATE ". $query ." WHERE process_id = ". $this->process_id;
			}

			$result = rex_sql::factory();
			$result->setQuery($query);
			if($this->process_id == 0) {
				$this->process_id = $result->getLastId();
				$error = $result->hasError();
			}
		}
		
		if($error == 0) {
			// Save the language specific part
			$pre_save_process = new Process($this->process_id, $this->clang_id);
			if($pre_save_process != $this) {
				$query = "REPLACE INTO ". rex::getTablePrefix() ."d2u_machinery_steel_process_lang SET "
						."process_id = '". $this->process_id ."', "
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