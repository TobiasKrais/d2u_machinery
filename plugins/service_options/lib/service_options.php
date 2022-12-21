<?php
/**
 * Redaxo D2U Machines Addon
 * @author Tobias Krais
 * @author <a href="http://www.design-to-use.de">www.design-to-use.de</a>
 */

/**
 * Service option
 */
class ServiceOption implements \D2U_Helper\ITranslationHelper {
	/**
	 * @var int Database ID
	 */
	var $service_option_id = 0;
	
	/**
	 * @var int Redaxo clang id
	 */
	var $clang_id = 0;
	
	/**
	 * @var string Name
	 */
	var $name = "";
	
	/**
	 * @var string Description
	 */
	var $description = "";

	/**
	 * @var string Picture file name 
	 */
	var $picture = "";
	
	/**
	 * @var String Status. Either "online" or "offline".
	 */
	var $online_status = "offline";
	
	/**
	 * @var string "yes" if translation needs update
	 */
	var $translation_needs_update = "delete";
	
	/**
	 * Constructor. Reads an Service Option stored in database.
	 * @param int $service_option_id Object ID.
	 * @param int $clang_id Redaxo clang id.
	 */
	 public function __construct($service_option_id, $clang_id) {
		$this->clang_id = $clang_id;
		$query = "SELECT * FROM ". \rex::getTablePrefix() ."d2u_machinery_service_options AS service_options "
				."LEFT JOIN ". \rex::getTablePrefix() ."d2u_machinery_service_options_lang AS lang "
					."ON service_options.service_option_id = lang.service_option_id "
					."AND clang_id = ". $this->clang_id ." "
				."WHERE service_options.service_option_id = ". $service_option_id;
		$result = \rex_sql::factory();
		$result->setQuery($query);
		$num_rows = $result->getRows();

		if ($num_rows > 0) {
			$this->service_option_id = $result->getValue("service_option_id");
			$this->name = stripslashes($result->getValue("name"));
			$this->description = stripslashes(htmlspecialchars_decode($result->getValue("description")));
			$this->picture = $result->getValue("picture");
			$this->online_status = $result->getValue("online_status");
			if($result->getValue("translation_needs_update") !== "") {
				$this->translation_needs_update = $result->getValue("translation_needs_update");
			}
		}
	}
	
	/**
	 * Changes the online status.
	 */
	public function changeStatus() {
		if($this->online_status == "online") {
			if($this->service_option_id > 0) {
				$query = "UPDATE ". \rex::getTablePrefix() ."d2u_machinery_service_options "
					."SET online_status = 'offline' "
					."WHERE service_option_id = ". $this->service_option_id;
				$result = \rex_sql::factory();
				$result->setQuery($query);
			}
			$this->online_status = "offline";
		}
		else {
			if($this->service_option_id > 0) {
				$query = "UPDATE ". \rex::getTablePrefix() ."d2u_machinery_service_options "
					."SET online_status = 'online' "
					."WHERE service_option_id = ". $this->service_option_id;
				$result = \rex_sql::factory();
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
		$query_lang = "DELETE FROM ". \rex::getTablePrefix() ."d2u_machinery_service_options_lang "
			."WHERE service_option_id = ". $this->service_option_id
			. ($delete_all ? '' : ' AND clang_id = '. $this->clang_id) ;
		$result_lang = \rex_sql::factory();
		$result_lang->setQuery($query_lang);
		
		// If no more lang objects are available, delete
		$query_main = "SELECT * FROM ". \rex::getTablePrefix() ."d2u_machinery_service_options_lang "
			."WHERE service_option_id = ". $this->service_option_id;
		$result_main = \rex_sql::factory();
		$result_main->setQuery($query_main);
		if(intval($result_main->getRows()) === 0) {
			$query = "DELETE FROM ". \rex::getTablePrefix() ."d2u_machinery_service_options "
				."WHERE service_option_id = ". $this->service_option_id;
			$result = \rex_sql::factory();
			$result->setQuery($query);
		}
	}
	
	/**
	 * Get all Service Options.
	 * @param int $clang_id Redaxo clang id.
	 * @param boolean $online_only TRUE if only online objects should be returned.
	 * @return ServiceOption[] Array with ServiceOption objects.
	 */
	public static function getAll($clang_id, $online_only = FALSE) {
		$query = "SELECT service_option_id FROM ". \rex::getTablePrefix() ."d2u_machinery_service_options_lang "
			."WHERE clang_id = ". $clang_id ." ";
		$query .= "ORDER BY name";

		$result = \rex_sql::factory();
		$result->setQuery($query);
		
		$service_options = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$service_option = new ServiceOption($result->getValue("service_option_id"), $clang_id);
			if($online_only && $service_option->online_status == "online") {
				$service_options[] = $service_option;
			}
			else if($online_only === FALSE) {
				$service_options[] = $service_option;
			}
			$result->next();
		}
		return $service_options;
	}
	
	/**
	 * Gets the machines referring to this object.
	 * @param boolean $online_only TRUE if only online machines should be returned.
	 * @return Machine[] Machines referring to this object.
	 */
	public function getReferringMachines($online_only = FALSE) {
		$query = "SELECT machine_id FROM ". \rex::getTablePrefix() ."d2u_machinery_machines "
			."WHERE service_option_ids LIKE '%|". $this->service_option_id ."|%'";
		$result = \rex_sql::factory();
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
	 * Get objects concerning translation updates
	 * @param int $clang_id Redaxo language ID
	 * @param string $type 'update' or 'missing'
	 * @return ServiceOption[] Array with ServiceOption objects.
	 */
	public static function getTranslationHelperObjects($clang_id, $type) {
		$query = 'SELECT service_option_id FROM '. \rex::getTablePrefix() .'d2u_machinery_service_options_lang '
				."WHERE clang_id = ". $clang_id ." AND translation_needs_update = 'yes' "
				.'ORDER BY name';
		if($type == 'missing') {
			$query = 'SELECT main.service_option_id FROM '. \rex::getTablePrefix() .'d2u_machinery_service_options AS main '
					.'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_service_options_lang AS target_lang '
						.'ON main.service_option_id = target_lang.service_option_id AND target_lang.clang_id = '. $clang_id .' '
					.'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_service_options_lang AS default_lang '
						.'ON main.service_option_id = default_lang.service_option_id AND default_lang.clang_id = '. \rex_config::get('d2u_helper', 'default_lang') .' '
					."WHERE target_lang.service_option_id IS NULL "
					.'ORDER BY default_lang.name';
			$clang_id = \rex_config::get('d2u_helper', 'default_lang');
		}
		$result = \rex_sql::factory();
		$result->setQuery($query);

		$objects = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$objects[] = new ServiceOption($result->getValue("service_option_id"), $clang_id);
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
		$pre_save_service_option = new ServiceOption($this->service_option_id, $this->clang_id);
		
		// saving the rest
		if($this->service_option_id == 0 || $pre_save_service_option !== $this) {
			$query = \rex::getTablePrefix() ."d2u_machinery_service_options SET "
					."online_status = '". $this->online_status ."', "
					."picture = '". $this->picture ."' ";

			if($this->service_option_id == 0) {
				$query = "INSERT INTO ". $query;
			}
			else {
				$query = "UPDATE ". $query ." WHERE service_option_id = ". $this->service_option_id;
			}

			$result = \rex_sql::factory();
			$result->setQuery($query);
			if($this->service_option_id == 0) {
				$this->service_option_id = $result->getLastId();
				$error = $result->hasError();
			}
		}
		
		if($error === FALSE) {
			// Save the language specific part
			$pre_save_service_option = new ServiceOption($this->service_option_id, $this->clang_id);
			if($pre_save_service_option !== $this) {
				$query = "REPLACE INTO ". \rex::getTablePrefix() ."d2u_machinery_service_options_lang SET "
						."service_option_id = '". $this->service_option_id ."', "
						."clang_id = '". $this->clang_id ."', "
						."name = '". addslashes($this->name) ."', "
						."description = '". addslashes(htmlspecialchars($this->description)) ."', "
						."translation_needs_update = '". $this->translation_needs_update ."', "
						."updatedate = CURRENT_TIMESTAMP, "
						."updateuser = '". \rex::getUser()->getLogin() ."' ";

				$result = \rex_sql::factory();
				$result->setQuery($query);
				$error = $result->hasError();
			}
		}

		return !$error;
	}
}