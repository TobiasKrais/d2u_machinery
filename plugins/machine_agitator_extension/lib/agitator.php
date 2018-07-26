<?php
/**
 * Redaxo D2U Machines Addon
 * @author Tobias Krais
 * @author <a href="http://www.design-to-use.de">www.design-to-use.de</a>
 */

/**
 * Agitator
 */
class Agitator implements \D2U_Helper\ITranslationHelper {
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
		$query = "SELECT * FROM ". \rex::getTablePrefix() ."d2u_machinery_agitators AS agitators "
				."LEFT JOIN ". \rex::getTablePrefix() ."d2u_machinery_agitators_lang AS lang "
					."ON agitators.agitator_id = lang.agitator_id "
					."AND clang_id = ". $this->clang_id ." "
				."WHERE agitators.agitator_id = ". $agitator_id;
		$result = \rex_sql::factory();
		$result->setQuery($query);
		$num_rows = $result->getRows();

		if ($num_rows > 0) {
			$this->agitator_id = $result->getValue("agitator_id");
			$this->name = stripslashes($result->getValue("name"));
			$this->pic = $result->getValue("pic");
			$this->description = stripslashes(htmlspecialchars_decode($result->getValue("description")));
			if($result->getValue("translation_needs_update") != "") {
				$this->translation_needs_update = $result->getValue("translation_needs_update");
			}
		}
	}
	
	/**
	 * Deletes the object.
	 * @param int $delete_all If TRUE, all translations and main object are deleted. If 
	 * FALSE, only this translation will be deleted.
	 */
	public function delete($delete_all = TRUE) {
		$query_lang = "DELETE FROM ". \rex::getTablePrefix() ."d2u_machinery_agitators_lang "
			."WHERE agitator_id = ". $this->agitator_id
			. ($delete_all ? '' : ' AND clang_id = '. $this->clang_id) ;
		$result_lang = \rex_sql::factory();
		$result_lang->setQuery($query_lang);
		
		// If no more lang objects are available, delete
		$query_main = "SELECT * FROM ". \rex::getTablePrefix() ."d2u_machinery_agitators_lang "
			."WHERE agitator_id = ". $this->agitator_id;
		$result_main = \rex_sql::factory();
		$result_main->setQuery($query_main);
		if($result_main->getRows() == 0) {
			$query = "DELETE FROM ". \rex::getTablePrefix() ."d2u_machinery_agitators "
				."WHERE agitator_id = ". $this->agitator_id;
			$result = \rex_sql::factory();
			$result->setQuery($query);
		}
	}
	
	/**
	 * Get all Agitators.
	 * @param int $clang_id Redaxo clang id.
	 * @return Agitator[] Array with Agitator objects.
	 */
	public static function getAll($clang_id) {
		$query = "SELECT agitator_id FROM ". \rex::getTablePrefix() ."d2u_machinery_agitators_lang "
			."WHERE clang_id = ". $clang_id ." ";
		$query .= "ORDER BY name";

		$result = \rex_sql::factory();
		$result->setQuery($query);
		
		$agitators = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$agitators[] = new Agitator($result->getValue("agitator_id"), $clang_id);
			$result->next();
		}
		return $agitators;
	}
	
	/**
	 * Gets the AgitatorTypes referring to this object.
	 * @return AgitatorType[] AgitatorType referring to this object.
	 */
	public function getReferringAgitatorTypes() {
		$query = "SELECT agitator_type_id FROM ". \rex::getTablePrefix() ."d2u_machinery_agitator_types "
			."WHERE agitator_ids LIKE '%|". $this->agitator_id ."|%'";
		$result = \rex_sql::factory();
		$result->setQuery($query);
		
		$agitator_types = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$agitator_types[] = new AgitatorType($result->getValue("agitator_type_id"), $this->clang_id);
			$result->next();
		}
		return $agitator_types;
	}
	
	/**
	 * Get objects concerning translation updates
	 * @param int $clang_id Redaxo language ID
	 * @param string $type 'update' or 'missing'
	 * @return Agitator[] Array with Agitator objects.
	 */
	public static function getTranslationHelperObjects($clang_id, $type) {
		$query = 'SELECT agitator_id FROM '. \rex::getTablePrefix() .'d2u_machinery_agitators_lang '
				."WHERE clang_id = ". $clang_id ." AND translation_needs_update = 'yes' "
				.'ORDER BY name';
		if($type == 'missing') {
			$query = 'SELECT main.agitator_id FROM '. \rex::getTablePrefix() .'d2u_machinery_agitators AS main '
					.'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_agitators_lang AS target_lang '
						.'ON main.agitator_id = target_lang.agitator_id AND target_lang.clang_id = '. $clang_id .' '
					.'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_agitators_lang AS default_lang '
						.'ON main.agitator_id = default_lang.agitator_id AND default_lang.clang_id = '. \rex_config::get('d2u_helper', 'default_lang') .' '
					."WHERE target_lang.agitator_id IS NULL "
					.'ORDER BY default_lang.name';
			$clang_id = \rex_config::get('d2u_helper', 'default_lang');
		}
		$result = \rex_sql::factory();
		$result->setQuery($query);

		$objects = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$objects[] = new Agitator($result->getValue("agitator_id"), $clang_id);
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
		$pre_save_agitator = new Agitator($this->agitator_id, $this->clang_id);
		
		// saving the rest
		if($this->agitator_id == 0 || $pre_save_agitator != $this) {
			$query = \rex::getTablePrefix() ."d2u_machinery_agitators SET "
					."pic = '". $this->pic ."' ";

			if($this->agitator_id == 0) {
				$query = "INSERT INTO ". $query;
			}
			else {
				$query = "UPDATE ". $query ." WHERE agitator_id = ". $this->agitator_id;
			}

			$result = \rex_sql::factory();
			$result->setQuery($query);
			if($this->agitator_id == 0) {
				$this->agitator_id = $result->getLastId();
				$error = $result->hasError();
			}
		}
		
		if($error === FALSE) {
			// Save the language specific part
			$pre_save_agitator = new Agitator($this->agitator_id, $this->clang_id);
			if($pre_save_agitator != $this) {
				$query = "REPLACE INTO ". \rex::getTablePrefix() ."d2u_machinery_agitators_lang SET "
						."agitator_id = '". $this->agitator_id ."', "
						."clang_id = '". $this->clang_id ."', "
						."name = '". addslashes($this->name) ."', "
						."description = '". addslashes(htmlspecialchars($this->description)) ."', "
						."translation_needs_update = '". $this->translation_needs_update ."' ";

				$result = \rex_sql::factory();
				$result->setQuery($query);
				$error = $result->hasError();
			}
		}
 
		return !$error;
	}
}