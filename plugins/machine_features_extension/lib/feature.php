<?php
/**
 * Redaxo D2U Machines Addon
 * @author Tobias Krais
 * @author <a href="http://www.design-to-use.de">www.design-to-use.de</a>
 */

/**
 * Machine Feature
 */
class Feature {
	/**
	 * @var int Database ID
	 */
	var $feature_id = 0;
	
	/**
	 * @var int Redaxo clang id
	 */
	var $clang_id = 0;
	
	/**
	 * @var int Sort Priority
	 */
	var $prio = 0;
	
	/**
	 * @var string Title
	 */
	var $title = "";
	
	/**
	 * @var string Preview picture file name 
	 */
	var $pic = "";
	
	/**
	 * @var int[] Category ids in which the feature will be available
	 */
	var $category_ids = array();
	
	/**
	 * @var string Detailed description
	 */
	var $description = "";
	
	/**
	 * @var string "yes" if translation needs update
	 */
	var $translation_needs_update = "delete";

	/**
	 * Constructor. Reads a feature stored in database.
	 * @param int $feature_id Feature ID.
	 * @param int $clang_id Redaxo clang id.
	 */
	 public function __construct($feature_id, $clang_id) {
		$this->clang_id = $clang_id;
		$query = "SELECT * FROM ". rex::getTablePrefix() ."d2u_machinery_features AS features "
				."LEFT JOIN ". rex::getTablePrefix() ."d2u_machinery_features_lang AS lang "
					."ON features.feature_id = lang.feature_id "
				."WHERE features.feature_id = ". $feature_id ." AND clang_id = ". $this->clang_id ." ";
		$result = rex_sql::factory();
		$result->setQuery($query);
		$num_rows = $result->getRows();

		if ($num_rows > 0) {
			$this->feature_id = $result->getValue("feature_id");
			$this->prio = $result->getValue("prio");
			$this->title = $result->getValue("title");
			$this->pic = $result->getValue("pic");
			$this->category_ids = preg_grep('/^\s*$/s', explode("|", $result->getValue("category_ids")), PREG_GREP_INVERT);
			$this->description = htmlspecialchars_decode($result->getValue("description"));
			$this->translation_needs_update = $result->getValue("translation_needs_update");

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
		if($delete_all) {
			$query_lang = "DELETE FROM ". rex::getTablePrefix() ."d2u_machinery_features_lang "
				."WHERE feature_id = ". $this->feature_id;
			$result_lang = rex_sql::factory();
			$result_lang->setQuery($query_lang);

			$query = "DELETE FROM ". rex::getTablePrefix() ."d2u_machinery_features "
				."WHERE feature_id = ". $this->feature_id;
			$result = rex_sql::factory();
			$result->setQuery($query);
		}
		else {
			$query_lang = "DELETE FROM ". rex::getTablePrefix() ."d2u_machinery_features_lang "
				."WHERE feature_id = ". $this->feature_id ." AND clang_id = ". $this->clang_id;
			$result_lang = rex_sql::factory();
			$result_lang->setQuery($query_lang);
		}
	}
	
	/**
	 * Get all features.
	 * @param int $clang_id Redaxo clang id.
	 * @param int $category_id Return only features available in this category.
	 * @return Feature[] Array with Feature objects.
	 */
	public static function getAll($clang_id, $category_id = 0) {
		$query = "SELECT lang.feature_id FROM ". rex::getTablePrefix() ."d2u_machinery_features_lang AS lang "
			."LEFT JOIN ". rex::getTablePrefix() ."d2u_machinery_features AS features "
				."ON lang.feature_id = features.feature_id "
			."WHERE clang_id = ". $clang_id ." ";
		if($category_id > 0) {
			$query .= "AND category_ids LIKE '%|". $category_id ."|%' ";
		}
		$query .= "ORDER BY prio";
		$result = rex_sql::factory();
		$result->setQuery($query);
		
		$features = array();
		for($i = 0; $i < $result->getRows(); $i++) {
			$features[] = new Feature($result->getValue("feature_id"), $clang_id);
			$result->next();
		}
		return $features;
	}
	
	/**
	 * Gets the machines reffering to this object.
	 * @return Machine[] Machines reffering to this object.
	 */
	public function getRefferingMachines() {
		$query = "SELECT machine_id FROM ". rex::getTablePrefix() ."d2u_machinery_machines "
			."WHERE feature_ids LIKE '%|". $this->feature_id ."|%'";
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
	 * @return boolean TRUE if successful
	 */
	public function save() {
		$error = 0;

		// Save the not language specific part
		$pre_save_feature = new Feature($this->feature_id, $this->clang_id);
		
		// save priority, but only if new or changed
		if($this->prio != $pre_save_feature->prio || $this->feature_id == 0) {
			$this->setPrio();
		}
		
		// saving the rest
		if($this->feature_id == 0 || $pre_save_feature != $this) {
			$query = rex::getTablePrefix() ."d2u_machinery_features SET "
					."pic = '". $this->pic ."', "
					."category_ids = '|". implode("|", $this->category_ids) ."|', "
					."prio = '". $this->prio ."' ";

			if($this->feature_id == 0) {
				$query = "INSERT INTO ". $query;
			}
			else {
				$query = "UPDATE ". $query ." WHERE feature_id = ". $this->feature_id;
			}

			$result = rex_sql::factory();
			$result->setQuery($query);
			if($this->feature_id == 0) {
				$this->feature_id = $result->getLastId();
				$error = $result->hasError();
			}
		}
		
		if($error == 0) {
			// Save the language specific part
			$pre_save_feature = new Feature($this->feature_id, $this->clang_id);
			if($pre_save_feature != $this) {
				$query = "REPLACE INTO ". rex::getTablePrefix() ."d2u_machinery_features_lang SET "
						."feature_id = '". $this->feature_id ."', "
						."clang_id = '". $this->clang_id ."', "
						."title = '". $this->title ."', "
						."description = '". htmlspecialchars($this->description) ."', "
						."translation_needs_update = '". $this->translation_needs_update ."' ";

				$result = rex_sql::factory();
				$result->setQuery($query);
				$error = $result->hasError();
			}
		}
 
		return $error;
	}
	
	/**
	 * Reassigns priority to all Features in database.
	 */
	private function setPrio() {
		// Pull prios from database
		$query = "SELECT feature_id, prio FROM ". rex::getTablePrefix() ."d2u_machinery_features "
			."WHERE feature_id <> ". $this->feature_id ." ORDER BY prio";
		$result = rex_sql::factory();
		$result->setQuery($query);
		
		// When prio is too small, set at beginning
		if($this->prio <= 0) {
			$this->prio = 1;
		}
		
		// When prio is too high, simply add at end 
		if($this->prio > $result->getRows()) {
			$this->prio = $result->getRows() + 1;
		}

		$features = array();
		for($i = 0; $i < $result->getRows(); $i++) {
			$features[$result->getValue("prio")] = $result->getValue("feature_id");
			$result->next();
		}
		array_splice($features, ($this->prio - 1), 0, array($this->feature_id));

		// Save all prios
		foreach($features as $prio => $feature_id) {
			$query = "UPDATE ". rex::getTablePrefix() ."d2u_machinery_features "
					."SET prio = ". ($prio + 1) ." " // +1 because array_splice recounts at zero
					."WHERE feature_id = ". $feature_id;
			$result = rex_sql::factory();
			$result->setQuery($query);
		}
	}
}