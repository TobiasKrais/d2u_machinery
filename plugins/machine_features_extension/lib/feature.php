<?php
/**
 * Redaxo D2U Machines Addon
 * @author Tobias Krais
 * @author <a href="http://www.design-to-use.de">www.design-to-use.de</a>
 */

/**
 * Machine Feature
 */
class Feature implements \D2U_Helper\ITranslationHelper {
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
	var $priority = 0;
	
	/**
	 * @var string Name
	 */
	var $name = "";
	
	/**
	 * @var string Title
	 */
	var $title = "";
	
	/**
	 * @var string Preview picture file name 
	 */
	var $pic = "";
	
	/**
	 * @var Video Videomanager video
	 */
	var $video = FALSE;
	
	/**
	 * @var int[] Category ids in which the feature will be available
	 */
	var $category_ids = [];
	
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
		$query = "SELECT * FROM ". \rex::getTablePrefix() ."d2u_machinery_features AS features "
				."LEFT JOIN ". \rex::getTablePrefix() ."d2u_machinery_features_lang AS lang "
					."ON features.feature_id = lang.feature_id "
					."AND clang_id = ". $this->clang_id ." "
				."WHERE features.feature_id = ". $feature_id;
		$result = rex_sql::factory();
		$result->setQuery($query);
		$num_rows = $result->getRows();

		if ($num_rows > 0) {
			$this->feature_id = $result->getValue("feature_id");
			$this->priority = $result->getValue("priority");
			$this->name = $result->getValue("name");
			$this->title = $result->getValue("title");
			$this->pic = $result->getValue("pic");
			$this->category_ids = preg_grep('/^\s*$/s', explode("|", $result->getValue("category_ids")), PREG_GREP_INVERT);
			$this->description = htmlspecialchars_decode(stripslashes($result->getValue("description")));
			if($result->getValue("translation_needs_update") != "") {
				$this->translation_needs_update = $result->getValue("translation_needs_update");
			}
			
			if(rex_addon::get('d2u_videos')->isAvailable() && $result->getValue("video_id") > 0) {
				$this->video = new Video($result->getValue("video_id"), $clang_id);
			}
		}
	}
	
	/**
	 * Deletes the object in all languages.
	 * @param int $delete_all If TRUE, all translations and main object are deleted. If 
	 * FALSE, only this translation will be deleted.
	 */
	public function delete($delete_all = TRUE) {
		$query_lang = "DELETE FROM ". \rex::getTablePrefix() ."d2u_machinery_features_lang "
			."WHERE feature_id = ". $this->feature_id
			. ($delete_all ? '' : ' AND clang_id = '. $this->clang_id) ;
		$result_lang = rex_sql::factory();
		$result_lang->setQuery($query_lang);
		
		// If no more lang objects are available, delete
		$query_main = "SELECT * FROM ". \rex::getTablePrefix() ."d2u_machinery_features_lang "
			."WHERE feature_id = ". $this->feature_id;
		$result_main = rex_sql::factory();
		$result_main->setQuery($query_main);
		if($result_main->getRows() == 0) {
			$query = "DELETE FROM ". \rex::getTablePrefix() ."d2u_machinery_features "
				."WHERE feature_id = ". $this->feature_id;
			$result = rex_sql::factory();
			$result->setQuery($query);
		}
	}
	
	/**
	 * Get all features.
	 * @param int $clang_id Redaxo clang id.
	 * @param int $category_id Return only features available in this category.
	 * @return Feature[] Array with Feature objects.
	 */
	public static function getAll($clang_id, $category_id = 0) {
		$query = "SELECT lang.feature_id FROM ". \rex::getTablePrefix() ."d2u_machinery_features_lang AS lang "
			."LEFT JOIN ". \rex::getTablePrefix() ."d2u_machinery_features AS features "
				."ON lang.feature_id = features.feature_id "
			."WHERE clang_id = ". $clang_id ." ";
		if($category_id > 0) {
			$query .= "AND category_ids LIKE '%|". $category_id ."|%' ";
		}
		$query .= "ORDER BY priority";
		$result = rex_sql::factory();
		$result->setQuery($query);
		
		$features = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$features[] = new Feature($result->getValue("feature_id"), $clang_id);
			$result->next();
		}
		return $features;
	}
	
	/**
	 * Gets the machines referring to this object.
	 * @return Machine[] Machines referring to this object.
	 */
	public function getReferringMachines() {
		$query = "SELECT machine_id FROM ". \rex::getTablePrefix() ."d2u_machinery_machines "
			."WHERE feature_ids LIKE '%|". $this->feature_id ."|%'";
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
	 * @return Feature[] Array with Feature objects.
	 */
	public static function getTranslationHelperObjects($clang_id, $type) {
		$query = 'SELECT feature_id FROM '. \rex::getTablePrefix() .'d2u_machinery_features_lang '
				."WHERE clang_id = ". $clang_id ." AND translation_needs_update = 'yes' "
				.'ORDER BY name';
		if($type == 'missing') {
			$query = 'SELECT main.feature_id FROM '. \rex::getTablePrefix() .'d2u_machinery_features AS main '
					.'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_features_lang AS target_lang '
						.'ON main.feature_id = target_lang.feature_id AND target_lang.clang_id = '. $clang_id .' '
					.'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_features_lang AS default_lang '
						.'ON main.feature_id = default_lang.feature_id AND default_lang.clang_id = '. \rex_config::get('d2u_helper', 'default_lang') .' '
					."WHERE target_lang.feature_id IS NULL "
					.'ORDER BY default_lang.name';
			$clang_id = \rex_config::get('d2u_helper', 'default_lang');
		}
		$result = \rex_sql::factory();
		$result->setQuery($query);

		$objects = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$objects[] = new Feature($result->getValue("feature_id"), $clang_id);
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
		$pre_save_feature = new Feature($this->feature_id, $this->clang_id);
		
		// save priority, but only if new or changed
		if($this->priority != $pre_save_feature->priority || $this->feature_id == 0) {
			$this->setPriority();
		}
		
		// saving the rest
		if($this->feature_id == 0 || $pre_save_feature != $this) {
			$query = \rex::getTablePrefix() ."d2u_machinery_features SET "
					."pic = '". $this->pic ."', "
					."category_ids = '|". implode("|", $this->category_ids) ."|', "
					."priority = '". $this->priority ."' ";
			if(rex_addon::get('d2u_videos')->isAvailable() && $this->video !== FALSE) {
				$query .= ", video_id = ". $this->video->video_id;
			}
			else {
				$query .= ", video_id = NULL";
			}

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
		
		if($error === FALSE) {
			// Save the language specific part
			$pre_save_feature = new Feature($this->feature_id, $this->clang_id);
			if($pre_save_feature != $this) {
				$query = "REPLACE INTO ". \rex::getTablePrefix() ."d2u_machinery_features_lang SET "
						."feature_id = '". $this->feature_id ."', "
						."clang_id = '". $this->clang_id ."', "
						."name = '". $this->name ."', "
						."title = '". $this->title ."', "
						."description = '". addslashes(htmlspecialchars($this->description)) ."', "
						."translation_needs_update = '". $this->translation_needs_update ."' ";

				$result = rex_sql::factory();
				$result->setQuery($query);
				$error = $result->hasError();
			}
		}
 
		return !$error;
	}
	
	/**
	 * Reassigns priority to all Features in database.
	 */
	private function setPriority() {
		// Pull priorities from database
		$query = "SELECT feature_id, priority FROM ". \rex::getTablePrefix() ."d2u_machinery_features "
			."WHERE feature_id <> ". $this->feature_id ." ORDER BY priority";
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

		$features = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$features[$result->getValue("priority")] = $result->getValue("feature_id");
			$result->next();
		}
		array_splice($features, ($this->priority - 1), 0, array($this->feature_id));

		// Save all prios
		foreach($features as $prio => $feature_id) {
			$query = "UPDATE ". \rex::getTablePrefix() ."d2u_machinery_features "
					."SET priority = ". ($prio + 1) ." " // +1 because array_splice recounts at zero
					."WHERE feature_id = ". $feature_id;
			$result = rex_sql::factory();
			$result->setQuery($query);
		}
	}
}