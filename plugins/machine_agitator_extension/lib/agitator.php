<?php
/**
 * Redaxo D2U Machines Addon
 * @author Tobias Krais
 * @author <a href="http://www.design-to-use.de">www.design-to-use.de</a>
 */

/**
 * Agitator
 */
class Agitator {
	/**
	 * @var int Database ID
	 */
	var $agitator_id = 0;
	
	/**
	 * @var int Redaxo clang id
	 */
	var $clang_id = 0;
		
	/**
	 * @var string Name
	 */
	var $name = "";
	
	/**
	 * @var string Name
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
	 * Constructor. Reads an agitator stored in database.
	 * @param int $agitator_id Object ID.
	 * @param int $clang_id Redaxo clang id.
	 */
	 public function __construct($agitator_id, $clang_id) {
		$this->clang_id = $clang_id;
		$query = "SELECT * FROM ". rex::getTablePrefix() ."d2u_machinery_agitators AS agitators "
				."LEFT JOIN ". rex::getTablePrefix() ."d2u_machinery_agitators_lang AS lang "
					."ON agitators.agitator_id = lang.agitator_id "
					."AND clang_id = ". $this->clang_id ." "
				."WHERE agitators.agitator_id = ". $agitator_id;
		$result = rex_sql::factory();
		$result->setQuery($query);
		$num_rows = $result->getRows();

		if ($num_rows > 0) {
			$this->agitator_id = $result->getValue("agitator_id");
			$this->name = $result->getValue("name");
			$this->pic = $result->getValue("pic");
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
	 * Deletes the object.
	 * @param int $delete_all If TRUE, all translations and main object are deleted. If 
	 * FALSE, only this translation will be deleted.
	 */
	public function delete($delete_all = TRUE) {
		$query_lang = "DELETE FROM ". rex::getTablePrefix() ."d2u_machinery_agitators_lang "
			."WHERE agitator_id = ". $this->agitator_id
			. ($delete_all ? '' : ' AND clang_id = '. $this->clang_id) ;
		$result_lang = rex_sql::factory();
		$result_lang->setQuery($query_lang);
		
		// If no more lang objects are available, delete
		$query_main = "SELECT * FROM ". rex::getTablePrefix() ."d2u_machinery_agitators_lang "
			."WHERE agitator_id = ". $this->agitator_id;
		$result_main = rex_sql::factory();
		$result_main->setQuery($query_main);
		if($result_main->getRows() == 0) {
			$query = "DELETE FROM ". rex::getTablePrefix() ."d2u_machinery_agitators "
				."WHERE agitator_id = ". $this->agitator_id;
			$result = rex_sql::factory();
			$result->setQuery($query);
		}
	}
	
	/**
	 * Get all Agitators.
	 * @param int $clang_id Redaxo clang id.
	 * @return Agitator[] Array with Agitator objects.
	 */
	public static function getAll($clang_id) {
		$query = "SELECT agitator_id FROM ". rex::getTablePrefix() ."d2u_machinery_agitators_lang "
			."WHERE clang_id = ". $clang_id ." ";
		$query .= "ORDER BY name";

		$result = rex_sql::factory();
		$result->setQuery($query);
		
		$agitators = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$agitators[] = new Agitator($result->getValue("agitator_id"), $clang_id);
			$result->next();
		}
		return $agitators;
	}
	
	/**
	 * Gets the AgitatorTypes reffering to this object.
	 * @return AgitatorType[] AgitatorType reffering to this object.
	 */
	public function getRefferingAgitatorTypes() {
		$query = "SELECT agitator_type_id FROM ". rex::getTablePrefix() ."d2u_machinery_agitator_types "
			."WHERE agitator_ids LIKE '%|". $this->agitator_id ."|%'";
		$result = rex_sql::factory();
		$result->setQuery($query);
		
		$agitator_types = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$agitator_types[] = new AgitatorType($result->getValue("agitator_type_id"), $this->clang_id);
			$result->next();
		}
		return $agitator_types;
	}
	
	/**
	 * Updates or inserts the object into database.
	 * @return boolean TRUE if succesful
	 */
	public function save() {
		$error = 0;

		// Save the not language specific part
		$pre_save_agitator = new Agitator($this->agitator_id, $this->clang_id);
		
		// saving the rest
		if($this->agitator_id == 0 || $pre_save_agitator != $this) {
			$query = rex::getTablePrefix() ."d2u_machinery_agitators SET "
					."pic = '". $this->pic ."' ";

			if($this->agitator_id == 0) {
				$query = "INSERT INTO ". $query;
			}
			else {
				$query = "UPDATE ". $query ." WHERE agitator_id = ". $this->agitator_id;
			}

			$result = rex_sql::factory();
			$result->setQuery($query);
			if($this->agitator_id == 0) {
				$this->agitator_id = $result->getLastId();
				$error = $result->hasError();
			}
		}
		
		if($error == 0) {
			// Save the language specific part
			$pre_save_agitator = new Agitator($this->agitator_id, $this->clang_id);
			if($pre_save_agitator != $this) {
				$query = "REPLACE INTO ". rex::getTablePrefix() ."d2u_machinery_agitators_lang SET "
						."agitator_id = '". $this->agitator_id ."', "
						."clang_id = '". $this->clang_id ."', "
						."name = '". $this->name ."', "
						."description = '". htmlspecialchars($this->description) ."', "
						."translation_needs_update = '". $this->translation_needs_update ."' ";

				$result = rex_sql::factory();
				$result->setQuery($query);
				$error = $result->hasError();
			}
		}
 
		return $error;
	}
}