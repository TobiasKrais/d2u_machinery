<?php
/**
 * Redaxo D2U Machines Addon
 * @author Tobias Krais
 * @author <a href="http://www.design-to-use.de">www.design-to-use.de</a>
 */

/**
 * Process
 */
class Process implements \D2U_Helper\ITranslationHelper {
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
		$query = "SELECT * FROM ". \rex::getTablePrefix() ."d2u_machinery_steel_process AS process "
				."LEFT JOIN ". \rex::getTablePrefix() ."d2u_machinery_steel_process_lang AS lang "
					."ON process.process_id = lang.process_id "
					."AND clang_id = ". $this->clang_id ." "
				."WHERE process.process_id = ". $process_id;
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
		$query_lang = "DELETE FROM ". \rex::getTablePrefix() ."d2u_machinery_steel_process_lang "
			."WHERE process_id = ". $this->process_id
			. ($delete_all ? '' : ' AND clang_id = '. $this->clang_id) ;
		$result_lang = rex_sql::factory();
		$result_lang->setQuery($query_lang);
		
		// If no more lang objects are available, delete
		$query_main = "SELECT * FROM ". \rex::getTablePrefix() ."d2u_machinery_steel_process_lang "
			."WHERE process_id = ". $this->process_id;
		$result_main = rex_sql::factory();
		$result_main->setQuery($query_main);
		if($result_main->getRows() == 0) {
			$query = "DELETE FROM ". \rex::getTablePrefix() ."d2u_machinery_steel_process "
				."WHERE process_id = ". $this->process_id;
			$result = rex_sql::factory();
			$result->setQuery($query);
		}
	}
	
	/**
	 * Get all process.
	 * @param int $clang_id Redaxo clang id.
	 * @return Process[] Array with Process objects.
	 */
	public static function getAll($clang_id) {
		$query = "SELECT process_id FROM ". \rex::getTablePrefix() ."d2u_machinery_steel_process_lang "
			."WHERE clang_id = ". $clang_id ." ";
		$query .= "ORDER BY name";

		$result = rex_sql::factory();
		$result->setQuery($query);
		
		$process = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$process[] = new Process($result->getValue("process_id"), $clang_id);
			$result->next();
		}
		return $process;
	}
	
	/**
	 * Gets the machines referring to this object.
	 * @return Machine[] Machines referring to this object.
	 */
	public function getReferringMachines() {
		$query = "SELECT machine_id FROM ". \rex::getTablePrefix() ."d2u_machinery_machines "
			."WHERE process_ids LIKE '%|". $this->process_id ."|%'";
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
	 * @return Process[] Array with Process objects.
	 */
	public static function getTranslationHelperObjects($clang_id, $type) {
		$query = 'SELECT process_id FROM '. \rex::getTablePrefix() .'d2u_machinery_steel_process_lang '
				."WHERE clang_id = ". $clang_id ." AND translation_needs_update = 'yes' "
				.'ORDER BY name';
		if($type == 'missing') {
			$query = 'SELECT main.process_id FROM '. \rex::getTablePrefix() .'d2u_machinery_steel_process AS main '
					.'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_steel_process_lang AS target_lang '
						.'ON main.process_id = target_lang.process_id AND target_lang.clang_id = '. $clang_id .' '
					.'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_steel_process_lang AS default_lang '
						.'ON main.process_id = default_lang.process_id AND default_lang.clang_id = '. \rex_config::get('d2u_helper', 'default_lang') .' '
					."WHERE target_lang.process_id IS NULL "
					.'ORDER BY default_lang.name';
			$clang_id = \rex_config::get('d2u_helper', 'default_lang');
		}
		$result = \rex_sql::factory();
		$result->setQuery($query);

		$objects = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$objects[] = new Process($result->getValue("process_id"), $clang_id);
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
		$pre_save_process = new Process($this->process_id, $this->clang_id);
		
		// saving the rest
		if($this->process_id == 0 || $pre_save_process != $this) {
			$query = \rex::getTablePrefix() ."d2u_machinery_steel_process SET "
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
		
		if($error === FALSE) {
			// Save the language specific part
			$pre_save_process = new Process($this->process_id, $this->clang_id);
			if($pre_save_process != $this) {
				$query = "REPLACE INTO ". \rex::getTablePrefix() ."d2u_machinery_steel_process_lang SET "
						."process_id = '". $this->process_id ."', "
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