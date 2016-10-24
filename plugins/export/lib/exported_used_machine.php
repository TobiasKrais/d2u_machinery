<?php
/**
 * Redaxo D2U Machines Addon - Export Plugin
 * @author Tobias Krais
 * @author <a href="http://www.design-to-use.de">www.design-to-use.de</a>
 */

/**
 * Exported used machine object.
 */
class ExportedUsedMachine {
	/**
	 * @var int Database ID
	 */
	var $used_machine_id = 0;
	
	/**
	 * @var Provider Export provider object
	 */
	var $provider;

	/**
	 * @var string Export status. Either "add", "update" or "delete"
	 */
	var $export_action = "";
	
	/**
	 * @var string ID provider returned after import. Not all providers return
	 * this value. But some so and it is needed for deleting the machines later
	 * on provider website.
	 */
	var $provider_import_id = "";
	
	/**
	 * @var int Export timestamp.
	 */
	var $export_timestamp = 0;

	/**
	 * Constructor. Fetches the object from database
	 * @param int $used_machine_id Used machine ID
	 * @param int $provider_id Provider ID.
	 */
	 public function __construct($used_machine_id, $provider_id) {
		$query = "SELECT * FROM ". rex::getTablePrefix() ."d2u_machinery_export_machines "
				."WHERE used_machine_id = ". $used_machine_id ." "
					."AND provider_id = ". $provider_id;
		$result = rex_sql::factory();
		$result->setQuery($query);
		$num_rows = $result->getRows();

		$this->used_machine_id = $used_machine_id;
		$this->provider = new Provider($provider_id);
		if ($num_rows > 0) {
			$this->export_action = $result->getValue("export_action");
			$this->provider_import_id = $result->getValue("provider_import_id");
			$this->export_timestamp = $result->getValue("export_timestamp");
		}
	}
	
	/**
	 * Add used machine to export for this provider.
	 */
	public function addToExport() {
		if($this->export_action == "") {
			$this->export_action = "add";
		}
		else if($this->export_action == "add" || $this->export_action == "delete") {
			$this->export_action = "update";
		}
		
		$this->save();
	}

	/**
	 * Add all machines to export for given provider.
	 * @param int $provider_id Provider id
	 */
	public static function addAllToExport($provider_id) {
		$provider = new Provider($provider_id);
		$used_machines = UsedMachine::getAll($provider->clang_id, TRUE);
		foreach($used_machines as $used_machine) {
			$exported_used_machine = new ExportedUsedMachine($used_machine->used_machine_id, $provider_id);
			if($exported_used_machine->export_action == "") {
				$exported_used_machine->export_action = "add";
			}
			else if($exported_used_machine->export_action == "add" || $exported_used_machine->export_action == "delete") {
				$exported_used_machine->export_action = "update";
			}
			$exported_used_machine->save();
		}
	}
	
	/**
	 * Deletes the object.
	 */
	public function delete() {
		$query = "DELETE FROM ". rex::getTablePrefix() ."d2u_machinery_export_machines "
			."WHERE used_machine_id = ". $this->used_machine_id ." AND provider_id = ". $this->provider->provider_id;
		$result_lang = rex_sql::factory();
		$result_lang->setQuery($query);
	}
	
	
	/**
	 * Get all exported used machines.
	 * @param Provider $provider Optional provider object
	 * @return ExportedUsedMachine[] Array with ExportedUsedMachine objects.
	 */
	public static function getAll($provider = FALSE) {
		$query = "SELECT used_machine_id, provider_id FROM ". rex::getTablePrefix() ."d2u_machinery_export_machines AS export";
		if ($provider !== FALSE && $provider->provider_id > 0) {
			$query .= " WHERE provider_id = ". $provider->provider_id;
		}
		$result = rex_sql::factory();
		$result->setQuery($query);
		
		$exported_used_machines = array();
		for($i = 0; $i < $result->getRows(); $i++) {
			$exported_used_machines[] = new ExportedUsedMachine($result->getValue("used_machine_id"), $result->getValue("provider_id"));
			$result->next();
		}
		return $exported_used_machines;
	}
	
	/**
	 * Proves, if UsedMachine is set for export for this provider.
	 * @return boolean TRUE if set, FALSE if not
	 */
	public function isSetForExport() {
		if($this->export_action == "add" || $this->export_action == "update") {
			return TRUE;
		}
		else {
			return FALSE;
		}
	}
	
	/**
	 * Add all machines to export for given provider.
	 * @param int $provider_id Provider id
	 */
	public static function removeAllFromExport($provider_id) {
		$provider = new Provider($provider_id);
		$used_machines = UsedMachine::getAll($provider->clang_id, TRUE);
		foreach($used_machines as $used_machine) {
			$exported_used_machine = new ExportedUsedMachine($used_machine->used_machine_id, $provider_id);
			if($exported_used_machine->export_action == "add" || $exported_used_machine->export_action == "update") {
				$exported_used_machine->export_action = "delete";
				$exported_used_machine->save();
			}
		}
	}
	
	/**
	 * Remove from export list for this provider.
	 */
	public function removeFromExport() {
		$this->export_action = "delete";
		$this->save();
	}

	/**
	 * Updates or inserts the object into database.
	 * @return boolean TRUE if successful
	 */
	public function save() {
		$query = "REPLACE INTO ". rex::getTablePrefix() ."d2u_machinery_export_machines SET "
				."used_machine_id = ". $this->used_machine_id .", "
				."provider_id = ". $this->provider->provider_id .", "
				."export_action = '". $this->export_action ."', "
				."provider_import_id = '". $this->provider_import_id ."', "
				."export_timestamp = '". $this->export_timestamp ."'";

		$result = rex_sql::factory();
		$result->setQuery($query);
		
		return !$result->hasError();
	}
}