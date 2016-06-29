<?php
/**
 * Redaxo D2U Machines Addon
 * @author Tobias Krais
 * @author <a href="http://www.design-to-use.de">www.design-to-use.de</a>
 */

/**
 * Mechanical Construction
 */
class MechanicalConstruction {
	/**
	 * @var int Database ID
	 */
	var $mechanical_construction_id = 0;
	
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
	 * @var string Preview picture file name 
	 */
	var $pic = "";
	
	/**
	 * @var string "yes" if translation needs update
	 */
	var $translation_needs_update = "delete";

	/**
	 * Constructor. Reads a Mechanical Construction stored in database.
	 * @param int $mechanical_construction_id Object ID.
	 * @param int $clang_id Redaxo clang id.
	 */
	 public function __construct($mechanical_construction_id, $clang_id) {
		$this->clang_id = $clang_id;
		$query = "SELECT * FROM ". rex::getTablePrefix() ."d2u_machinery_mechanical_constructions AS mechanical_constructions "
				."LEFT JOIN ". rex::getTablePrefix() ."d2u_machinery_mechanical_constructions_lang AS lang "
					."ON mechanical_constructions.mechanical_construction_id = lang.mechanical_construction_id "
				."WHERE mechanical_constructions.mechanical_construction_id = ". $mechanical_construction_id ." AND clang_id = ". $this->clang_id ." ";
		$result = rex_sql::factory();
		$result->setQuery($query);
		$num_rows = $result->getRows();

		if ($num_rows > 0) {
			$this->mechanical_construction_id = $result->getValue("mechanical_construction_id");
			$this->name = $result->getValue("name");
			$this->description = $result->getValue("description");
			$this->pic = $result->getValue("pic");
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
			$query_lang = "DELETE FROM ". rex::getTablePrefix() ."d2u_machinery_mechanical_constructions_lang "
				."WHERE mechanical_construction_id = ". $this->mechanical_construction_id;
			$result_lang = rex_sql::factory();
			$result_lang->setQuery($query_lang);

			$query = "DELETE FROM ". rex::getTablePrefix() ."d2u_machinery_mechanical_constructions "
				."WHERE mechanical_construction_id = ". $this->mechanical_construction_id;
			$result = rex_sql::factory();
			$result->setQuery($query);
		}
		else {
			$query_lang = "DELETE FROM ". rex::getTablePrefix() ."d2u_machinery_mechanical_constructions_lang "
				."WHERE mechanical_construction_id = ". $this->mechanical_construction_id ." AND clang_id = ". $this->clang_id;
			$result_lang = rex_sql::factory();
			$result_lang->setQuery($query_lang);
		}
	}
	
	/**
	 * Get all mechanical constructions.
	 * @param int $clang_id Redaxo clang id.
	 * @return MechanicalConstructions[] Array with objects.
	 */
	public static function getAll($clang_id) {
		$query = "SELECT mechanical_construction_id FROM ". rex::getTablePrefix() ."d2u_machinery_mechanical_constructions_lang "
			."WHERE clang_id = ". $clang_id ." ";
		$query .= "ORDER BY name";

		$result = rex_sql::factory();
		$result->setQuery($query);
		
		$mechanical_constructions = array();
		for($i = 0; $i < $result->getRows(); $i++) {
			$mechanical_constructions[] = new MechanicalConstruction($result->getValue("mechanical_construction_id"), $clang_id);
			$result->next();
		}
		return $mechanical_constructions;
	}
	
	/**
	 * Gets the machines reffering to this object.
	 * @return Machine[] Machines reffering to this object.
	 */
	public function getRefferingMachines() {
		$query = "SELECT machine_id FROM ". rex::getTablePrefix() ."d2u_machinery_machines "
			."WHERE mechanical_construction_id = ". $this->mechanical_construction_id;
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
		$pre_save_mechanical_construction = new MechanicalConstruction($this->mechanical_construction_id, $this->clang_id);
		
		// saving the rest
		if($this->mechanical_construction_id == 0 || $pre_save_mechanical_construction != $this) {
			$query = rex::getTablePrefix() ."d2u_machinery_mechanical_constructions SET "
					."pic = '". $this->pic ."' ";

			if($this->mechanical_construction_id == 0) {
				$query = "INSERT INTO ". $query;
			}
			else {
				$query = "UPDATE ". $query ." WHERE mechanical_construction_id = ". $this->mechanical_construction_id;
			}

			$result = rex_sql::factory();
			$result->setQuery($query);
			if($this->mechanical_construction_id == 0) {
				$this->mechanical_construction_id = $result->getLastId();
				$error = $result->hasError();
			}
		}
		
		if($error == 0) {
			// Save the language specific part
			$pre_save_mechanical_construction = new MechanicalConstruction($this->mechanical_construction_id, $this->clang_id);
			if($pre_save_mechanical_construction != $this) {
				$query = "REPLACE INTO ". rex::getTablePrefix() ."d2u_machinery_mechanical_constructions_lang SET "
						."mechanical_construction_id = '". $this->mechanical_construction_id ."', "
						."clang_id = '". $this->clang_id ."', "
						."name = '". $this->name ."', "
						."description = '". $this->description ."', "
						."translation_needs_update = '". $this->translation_needs_update ."' ";

				$result = rex_sql::factory();
				$result->setQuery($query);
				$error = $result->hasError();
			}
		}
 
		return $error;
	}
}