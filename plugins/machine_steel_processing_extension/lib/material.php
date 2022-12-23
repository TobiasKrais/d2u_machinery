<?php
/**
 * Redaxo D2U Machines Addon
 * @author Tobias Krais
 * @author <a href="http://www.design-to-use.de">www.design-to-use.de</a>
 */

/**
 * Material
 */
class Material implements \D2U_Helper\ITranslationHelper {
	/**
	 * @var int Database ID
	 */
	public int $material_id = 0;
	
	/**
	 * @var int Redaxo clang id
	 */
	public int $clang_id = 0;
	
	/**
	 * @var string Internal name
	 */
	public string $internal_name = "";

	/**
	 * @var string Name
	 */
	public string $name = "";
	
	/**
	 * @var string "yes" if translation needs update
	 */
	public string $translation_needs_update = "delete";

	/**
	 * Constructor. Reads object stored in database.
	 * @param int $material_id Object ID.
	 * @param int $clang_id Redaxo clang id.
	 */
	 public function __construct($material_id, $clang_id) {
		$this->clang_id = $clang_id;
		$query = "SELECT * FROM ". \rex::getTablePrefix() ."d2u_machinery_steel_material AS materials "
				."LEFT JOIN ". \rex::getTablePrefix() ."d2u_machinery_steel_material_lang AS lang "
					."ON materials.material_id = lang.material_id "
					."AND clang_id = ". $this->clang_id ." "
				."WHERE materials.material_id = ". $material_id;
		$result = \rex_sql::factory();
		$result->setQuery($query);
		$num_rows = $result->getRows();

		if ($num_rows > 0) {
			$this->material_id = (int) $result->getValue("material_id");
			$this->internal_name = (string) $result->getValue("internal_name");
			$this->name = stripslashes((string) $result->getValue("name"));
			if($result->getValue("translation_needs_update") !== "") {
				$this->translation_needs_update = (string) $result->getValue("translation_needs_update");
			}
		}
	}
	
	/**
	 * Deletes the object in all languages.
	 * @param bool $delete_all If true, all translations and main object are deleted. If 
	 * false, only this translation will be deleted.
	 */
	public function delete($delete_all = true):void {
		$query_lang = "DELETE FROM ". \rex::getTablePrefix() ."d2u_machinery_steel_material_lang "
			."WHERE material_id = ". $this->material_id
			. ($delete_all ? '' : ' AND clang_id = '. $this->clang_id) ;
		$result_lang = \rex_sql::factory();
		$result_lang->setQuery($query_lang);
		
		// If no more lang objects are available, delete
		$query_main = "SELECT * FROM ". \rex::getTablePrefix() ."d2u_machinery_steel_material_lang "
			."WHERE material_id = ". $this->material_id;
		$result_main = \rex_sql::factory();
		$result_main->setQuery($query_main);
		if(intval($result_main->getRows()) === 0) {
			$query = "DELETE FROM ". \rex::getTablePrefix() ."d2u_machinery_steel_material "
				."WHERE material_id = ". $this->material_id;
			$result = \rex_sql::factory();
			$result->setQuery($query);
		}
	}
	
	/**
	 * Get all materials.
	 * @param int $clang_id Redaxo clang id.
	 * @return Material[] Array with Material objects.
	 */
	public static function getAll($clang_id) {
		$query = "SELECT material_id FROM ". \rex::getTablePrefix() ."d2u_machinery_steel_material_lang "
			."WHERE clang_id = ". $clang_id ." ";
		$query .= "ORDER BY name";

		$result = \rex_sql::factory();
		$result->setQuery($query);
		
		$materials = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$materials[] = new Material((int) $result->getValue("material_id"), $clang_id);
			$result->next();
		}
		return $materials;
	}
	
	/**
	 * Gets the machines referring to this object.
	 * @return Machine[] Machines referring to this object.
	 */
	public function getReferringMachines() {
		$query = "SELECT machine_id FROM ". \rex::getTablePrefix() ."d2u_machinery_machines "
			."WHERE material_ids LIKE '%|". $this->material_id ."|%'";
		$result = \rex_sql::factory();
		$result->setQuery($query);
		
		$machines = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$machines[] = new Machine((int) $result->getValue("machine_id"), $this->clang_id);
			$result->next();
		}
		return $machines;
	}
	
	/**
	 * Get objects concerning translation updates
	 * @param int $clang_id Redaxo language ID
	 * @param string $type 'update' or 'missing'
	 * @return Material[] Array with Material objects.
	 */
	public static function getTranslationHelperObjects($clang_id, $type) {
		$query = 'SELECT material_id FROM '. \rex::getTablePrefix() .'d2u_machinery_steel_material_lang '
				."WHERE clang_id = ". $clang_id ." AND translation_needs_update = 'yes' "
				.'ORDER BY name';
		if($type === 'missing') {
			$query = 'SELECT main.material_id FROM '. \rex::getTablePrefix() .'d2u_machinery_steel_material AS main '
					.'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_steel_material_lang AS target_lang '
						.'ON main.material_id = target_lang.material_id AND target_lang.clang_id = '. $clang_id .' '
					.'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_steel_material_lang AS default_lang '
						.'ON main.material_id = default_lang.material_id AND default_lang.clang_id = '. \rex_config::get('d2u_helper', 'default_lang') .' '
					."WHERE target_lang.material_id IS NULL "
					.'ORDER BY default_lang.name';
			$clang_id = intval(\rex_config::get('d2u_helper', 'default_lang'));
		}
		$result = \rex_sql::factory();
		$result->setQuery($query);

		$objects = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$objects[] = new Material((int) $result->getValue("material_id"), $clang_id);
			$result->next();
		}
		
		return $objects;
    }
	
	/**
	 * Updates or inserts the object into database.
	 * @return boolean true if successful
	 */
	public function save() {
		$error = false;

		// Save the not language specific part
		$pre_save_material = new Material($this->material_id, $this->clang_id);
		
		// saving the rest
		if($this->material_id === 0 || $pre_save_material !== $this) {
			$query = \rex::getTablePrefix() ."d2u_machinery_steel_material SET "
					."internal_name = '". $this->internal_name ."' ";
			if($this->material_id === 0) {
				$query = "INSERT INTO ". $query;
			}
			else {
				$query = "UPDATE ". $query ." WHERE material_id = ". $this->material_id;
			}

			$result = \rex_sql::factory();
			$result->setQuery($query);
			if($this->material_id === 0) {
				$this->material_id = intval($result->getLastId());
				$error = $result->hasError();
			}
		}
		
		if($error === false) {
			// Save the language specific part
			$pre_save_material = new Material($this->material_id, $this->clang_id);
			if($pre_save_material !== $this) {
				$query = "REPLACE INTO ". \rex::getTablePrefix() ."d2u_machinery_steel_material_lang SET "
						."material_id = '". $this->material_id ."', "
						."clang_id = '". $this->clang_id ."', "
						."name = '". addslashes($this->name) ."', "
						."translation_needs_update = '". $this->translation_needs_update ."' ";

				$result = \rex_sql::factory();
				$result->setQuery($query);
				$error = $result->hasError();
			}
		}
		
		return !$error;
	}
}