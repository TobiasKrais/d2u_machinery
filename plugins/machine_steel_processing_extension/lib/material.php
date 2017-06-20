<?php
/**
 * Redaxo D2U Machines Addon
 * @author Tobias Krais
 * @author <a href="http://www.design-to-use.de">www.design-to-use.de</a>
 */

/**
 * Material
 */
class Material {
	/**
	 * @var int Database ID
	 */
	var $material_id = 0;
	
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
	 * @param int $material_id Object ID.
	 * @param int $clang_id Redaxo clang id.
	 */
	 public function __construct($material_id, $clang_id) {
		$this->clang_id = $clang_id;
		$query = "SELECT * FROM ". rex::getTablePrefix() ."d2u_machinery_steel_material AS materials "
				."LEFT JOIN ". rex::getTablePrefix() ."d2u_machinery_steel_material_lang AS lang "
					."ON materials.material_id = lang.material_id "
					."AND clang_id = ". $this->clang_id ." "
				."WHERE materials.material_id = ". $material_id;
		$result = rex_sql::factory();
		$result->setQuery($query);
		$num_rows = $result->getRows();

		if ($num_rows > 0) {
			$this->material_id = $result->getValue("material_id");
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
			$query_lang = "DELETE FROM ". rex::getTablePrefix() ."d2u_machinery_steel_material_lang "
				."WHERE material_id = ". $this->material_id;
			$result_lang = rex_sql::factory();
			$result_lang->setQuery($query_lang);

			$query = "DELETE FROM ". rex::getTablePrefix() ."d2u_machinery_steel_material "
				."WHERE material_id = ". $this->material_id;
			$result = rex_sql::factory();
			$result->setQuery($query);
		}
		else {
			$query_lang = "DELETE FROM ". rex::getTablePrefix() ."d2u_machinery_steel_material_lang "
				."WHERE material_id = ". $this->material_id ." AND clang_id = ". $this->clang_id;
			$result_lang = rex_sql::factory();
			$result_lang->setQuery($query_lang);
		}
	}
	
	/**
	 * Get all materials.
	 * @param int $clang_id Redaxo clang id.
	 * @return Material[] Array with Material objects.
	 */
	public static function getAll($clang_id) {
		$query = "SELECT material_id FROM ". rex::getTablePrefix() ."d2u_machinery_steel_material_lang "
			."WHERE clang_id = ". $clang_id ." ";
		$query .= "ORDER BY name";

		$result = rex_sql::factory();
		$result->setQuery($query);
		
		$materials = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$materials[] = new Material($result->getValue("material_id"), $clang_id);
			$result->next();
		}
		return $materials;
	}
	
	/**
	 * Gets the machines reffering to this object.
	 * @return Machine[] Machines reffering to this object.
	 */
	public function getRefferingMachines() {
		$query = "SELECT machine_id FROM ". rex::getTablePrefix() ."d2u_machinery_machines "
			."WHERE material_ids LIKE '%|". $this->material_id ."|%'";
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
		$pre_save_material = new Material($this->material_id, $this->clang_id);
		
		// saving the rest
		if($this->material_id == 0 || $pre_save_material != $this) {
			$query = rex::getTablePrefix() ."d2u_machinery_steel_material SET "
					."internal_name = '". $this->internal_name ."' ";
			if($this->material_id == 0) {
				$query = "INSERT INTO ". $query;
			}
			else {
				$query = "UPDATE ". $query ." WHERE material_id = ". $this->material_id;
			}

			$result = rex_sql::factory();
			$result->setQuery($query);
			if($this->material_id == 0) {
				$this->material_id = $result->getLastId();
				$error = $result->hasError();
			}
		}
		
		if($error == 0) {
			// Save the language specific part
			$pre_save_material = new Material($this->material_id, $this->clang_id);
			if($pre_save_material != $this) {
				$query = "REPLACE INTO ". rex::getTablePrefix() ."d2u_machinery_steel_material_lang SET "
						."material_id = '". $this->material_id ."', "
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