<?php
/**
 * Redaxo D2U Machines Addon
 * @author Tobias Krais
 * @author <a href="http://www.design-to-use.de">www.design-to-use.de</a>
 */

/**
 * Machine Equipment Group
 */
class EquipmentGroup implements \D2U_Helper\ITranslationHelper {
	/**
	 * @var int Database ID
	 */
	var $group_id = 0;
	
	/**
	 * @var int Redaxo clang id
	 */
	var $clang_id = 0;
	
	/**
	 * @var string Preview picture file name 
	 */
	var $picture = "";
	
	/**
	 * @var int Sort Priority
	 */
	var $priority = 0;
	
	/**
	 * @var string Name
	 */
	var $name = "";
	
	/**
	 * @var string Detailed description
	 */
	var $description = "";
	
	/**
	 * @var string "yes" if translation needs update
	 */
	var $translation_needs_update = "delete";

	/**
	 * Constructor. Reads a object stored in database.
	 * @param int $group_id EquipmentGroup ID.
	 * @param int $clang_id Redaxo clang id.
	 */
	 public function __construct($group_id, $clang_id) {
		$this->clang_id = $clang_id;
		$query = "SELECT * FROM ". \rex::getTablePrefix() ."d2u_machinery_equipment_groups AS equipment_groups "
				."LEFT JOIN ". \rex::getTablePrefix() ."d2u_machinery_equipment_groups_lang AS lang "
					."ON equipment_groups.group_id = lang.group_id "
					."AND clang_id = ". $this->clang_id ." "
				."WHERE equipment_groups.group_id = ". $group_id;
		$result = rex_sql::factory();
		$result->setQuery($query);
		$num_rows = $result->getRows();

		if ($num_rows > 0) {
			$this->group_id = $result->getValue("group_id");
			$this->priority = $result->getValue("priority");
			$this->picture = $result->getValue("picture");
			$this->name = $result->getValue("name");
			$this->description = htmlspecialchars_decode($result->getValue("description"));
			if($result->getValue("translation_needs_update") != "") {
				$this->translation_needs_update = $result->getValue("translation_needs_update");
			}

			// Convert redaxo://123 to URL
			$this->description = preg_replace_callback(
					'@redaxo://(\d+)(?:-(\d+))?/?@i',
					create_function(
							'$matches',
							'return rex_getUrl($matches[1], isset($matches[2]) ? $matches[2] : "");'
					),
					$this->description
			);
		}
	}
	
	/**
	 * Deletes the object in all languages.
	 * @param int $delete_all If TRUE, all translations and main object are deleted. If 
	 * FALSE, only this translation will be deleted.
	 */
	public function delete($delete_all = TRUE) {
		$query_lang = "DELETE FROM ". \rex::getTablePrefix() ."d2u_machinery_equipment_groups_lang "
			."WHERE group_id = ". $this->group_id
			. ($delete_all ? '' : ' AND clang_id = '. $this->clang_id) ;
		$result_lang = rex_sql::factory();
		$result_lang->setQuery($query_lang);
		
		// If no more lang objects are available, delete
		$query_main = "SELECT * FROM ". \rex::getTablePrefix() ."d2u_machinery_equipment_groups_lang "
			."WHERE group_id = ". $this->group_id;
		$result_main = rex_sql::factory();
		$result_main->setQuery($query_main);
		if($result_main->getRows() == 0) {
			$query = "DELETE FROM ". \rex::getTablePrefix() ."d2u_machinery_equipment_groups "
				."WHERE group_id = ". $this->group_id;
			$result = rex_sql::factory();
			$result->setQuery($query);
		}
	}
	
	/**
	 * Get all equipment_groups.
	 * @param int $clang_id Redaxo clang id.
	 * @return EquipmentGroup[] Array with EquipmentGroup objects.
	 */
	public static function getAll($clang_id) {
		$query = "SELECT lang.group_id FROM ". \rex::getTablePrefix() ."d2u_machinery_equipment_groups_lang AS lang "
			."LEFT JOIN ". \rex::getTablePrefix() ."d2u_machinery_equipment_groups AS equipment_groups "
				."ON lang.group_id = equipment_groups.group_id "
			."WHERE clang_id = ". $clang_id ." "
			."ORDER BY priority";
		$result = rex_sql::factory();
		$result->setQuery($query);
		
		$equipment_groups = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$equipment_groups[] = new EquipmentGroup($result->getValue("group_id"), $clang_id);
			$result->next();
		}
		return $equipment_groups;
	}
	
	/**
	 * Gets the equipments referring to this object.
	 * @return Equipment[] Equipment referring to this object.
	 */
	public function getReferringEquipments() {
		$query = "SELECT equipment_id FROM ". \rex::getTablePrefix() ."d2u_machinery_equipments "
			."WHERE group_id = ". $this->group_id;
		$result = rex_sql::factory();
		$result->setQuery($query);
		
		$equipments = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$equipments[] = new Equipment($result->getValue("equipment_id"), $this->clang_id);
			$result->next();
		}
		return $equipments;
	}
	
	/**
	 * Get objects concerning translation updates
	 * @param int $clang_id Redaxo language ID
	 * @param string $type 'update' or 'missing'
	 * @return EquipmentGroup[] Array with EquipmentGroup objects.
	 */
	public static function getTranslationHelperObjects($clang_id, $type) {
		$query = 'SELECT group_id FROM '. \rex::getTablePrefix() .'d2u_machinery_equipment_groups_lang '
				."WHERE clang_id = ". $clang_id ." AND translation_needs_update = 'yes' "
				.'ORDER BY name';
		if($type == 'missing') {
			$query = 'SELECT main.group_id FROM '. \rex::getTablePrefix() .'d2u_machinery_equipment_groups AS main '
					.'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_equipment_groups_lang AS target_lang '
						.'ON main.group_id = target_lang.group_id AND target_lang.clang_id = '. $clang_id .' '
					.'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_equipment_groups_lang AS default_lang '
						.'ON main.group_id = default_lang.group_id AND default_lang.clang_id = '. \rex_config::get('d2u_helper', 'default_lang') .' '
					."WHERE target_lang.group_id IS NULL "
					.'ORDER BY default_lang.name';
			$clang_id = \rex_config::get('d2u_helper', 'default_lang');
		}
		$result = \rex_sql::factory();
		$result->setQuery($query);

		$objects = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$objects[] = new EquipmentGroup($result->getValue("group_id"), $clang_id);
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
		$pre_save_object = new EquipmentGroup($this->group_id, $this->clang_id);
		
		// save priority, but only if new or changed
		if($this->priority != $pre_save_object->priority || $this->group_id == 0) {
			$this->setPriority();
		}
		
		// saving the rest
		if($this->group_id == 0 || $pre_save_object != $this) {
			$query = \rex::getTablePrefix() ."d2u_machinery_equipment_groups SET "
					."picture = '". $this->picture ."', "
					."priority = '". $this->priority ."' ";

			if($this->group_id == 0) {
				$query = "INSERT INTO ". $query;
			}
			else {
				$query = "UPDATE ". $query ." WHERE group_id = ". $this->group_id;
			}

			$result = rex_sql::factory();
			$result->setQuery($query);
			if($this->group_id == 0) {
				$this->group_id = $result->getLastId();
				$error = $result->hasError();
			}
		}
		
		if($error === FALSE) {
			// Save the language specific part
			$pre_save_object = new EquipmentGroup($this->group_id, $this->clang_id);
			if($pre_save_object != $this) {
				$query = "REPLACE INTO ". \rex::getTablePrefix() ."d2u_machinery_equipment_groups_lang SET "
						."group_id = '". $this->group_id ."', "
						."clang_id = '". $this->clang_id ."', "
						."name = '". $this->name ."', "
						."description = '". htmlspecialchars($this->description) ."', "
						."translation_needs_update = '". $this->translation_needs_update ."' ";

				$result = rex_sql::factory();
				$result->setQuery($query);
				$error = $result->hasError();
			}
		}
 
		return !$error;
	}
	
	/**
	 * Reassigns priority to all EquipmentGroups in database.
	 */
	private function setPriority() {
		// Pull priorities from database
		$query = "SELECT group_id, priority FROM ". \rex::getTablePrefix() ."d2u_machinery_equipment_groups "
			."WHERE group_id <> ". $this->group_id ." ORDER BY priority";
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

		$equipment_groups = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$equipment_groups[$result->getValue("priority")] = $result->getValue("group_id");
			$result->next();
		}
		array_splice($equipment_groups, ($this->priority - 1), 0, array($this->group_id));

		// Save all prios
		foreach($equipment_groups as $prio => $group_id) {
			$query = "UPDATE ". \rex::getTablePrefix() ."d2u_machinery_equipment_groups "
					."SET priority = ". ($prio + 1) ." " // +1 because array_splice recounts at zero
					."WHERE group_id = ". $group_id;
			$result = rex_sql::factory();
			$result->setQuery($query);
		}
	}
}