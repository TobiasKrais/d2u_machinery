<?php
/**
 * Redaxo D2U Machines Addon
 * @author Tobias Krais
 * @author <a href="http://www.design-to-use.de">www.design-to-use.de</a>
 */

/**
 * Machine Options
 */
class Option implements \D2U_Helper\ITranslationHelper {
	/**
	 * @var int Database ID
	 */
	var $option_id = 0;
	
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
	 * @var string Preview picture file name 
	 */
	var $pic = "";
	
	/**
	 * @var Video Videomanager video
	 */
	var $video = FALSE;
	
	/**
	 * @var int[] Category ids in which the option will be available
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
	 * Constructor. Reads a option stored in database.
	 * @param int $option_id Option ID.
	 * @param int $clang_id Redaxo clang id.
	 */
	 public function __construct($option_id, $clang_id) {
		$this->clang_id = $clang_id;
		$query = "SELECT * FROM ". \rex::getTablePrefix() ."d2u_machinery_options AS options "
				."LEFT JOIN ". \rex::getTablePrefix() ."d2u_machinery_options_lang AS lang "
					."ON options.option_id = lang.option_id "
					."AND clang_id = ". $this->clang_id ." "
				."WHERE options.option_id = ". $option_id;
		$result = \rex_sql::factory();
		$result->setQuery($query);
		$num_rows = $result->getRows();

		if ($num_rows > 0) {
			$this->option_id = $result->getValue("option_id");
			$this->priority = $result->getValue("priority");
			$this->name = stripslashes($result->getValue("name"));
			$this->pic = $result->getValue("pic");
			$this->category_ids = preg_grep('/^\s*$/s', explode("|", $result->getValue("category_ids")), PREG_GREP_INVERT);
			$this->description = htmlspecialchars_decode(stripslashes($result->getValue("description")));
			if($result->getValue("translation_needs_update") != "") {
				$this->translation_needs_update = $result->getValue("translation_needs_update");
			}
			
			if(\rex_addon::get('d2u_videos')->isAvailable() && $result->getValue("video_id") > 0) {
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
		$query_lang = "DELETE FROM ". \rex::getTablePrefix() ."d2u_machinery_options_lang "
			."WHERE option_id = ". $this->option_id
			. ($delete_all ? '' : ' AND clang_id = '. $this->clang_id) ;
		$result_lang = \rex_sql::factory();
		$result_lang->setQuery($query_lang);
		
		// If no more lang objects are available, delete
		$query_main = "SELECT * FROM ". \rex::getTablePrefix() ."d2u_machinery_options_lang "
			."WHERE option_id = ". $this->option_id;
		$result_main = \rex_sql::factory();
		$result_main->setQuery($query_main);
		if($result_main->getRows() == 0) {
			$query = "DELETE FROM ". \rex::getTablePrefix() ."d2u_machinery_options "
				."WHERE option_id = ". $this->option_id;
			$result = \rex_sql::factory();
			$result->setQuery($query);

			// reset priorities
			$this->setPriority(TRUE);			
		}
	}
	
	/**
	 * Get all options.
	 * @param int $clang_id Redaxo clang id.
	 * @param int $category_id Return only options available in this category.
	 * @return Option[] Array with Option objects.
	 */
	public static function getAll($clang_id, $category_id = 0) {
		$query = "SELECT lang.option_id FROM ". \rex::getTablePrefix() ."d2u_machinery_options_lang AS lang "
			."LEFT JOIN ". \rex::getTablePrefix() ."d2u_machinery_options AS options "
				."ON lang.option_id = options.option_id "
			."WHERE clang_id = ". $clang_id ." ";
		if($category_id > 0) {
			$query .= "AND category_ids LIKE '%|". $category_id ."|%' ";
		}
		$query .= "ORDER BY priority";
		$result = \rex_sql::factory();
		$result->setQuery($query);
		
		$options = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$options[$result->getValue("option_id")] = new Option($result->getValue("option_id"), $clang_id);
			$result->next();
		}
		return $options;
	}
	
	/**
	 * Gets the machines referring to this object.
	 * @return Machine[] Machines referring to this object.
	 */
	public function getReferringMachines() {
		$query = "SELECT machine_id FROM ". \rex::getTablePrefix() ."d2u_machinery_machines "
			."WHERE option_ids LIKE '%|". $this->option_id ."|%'";
		$result = \rex_sql::factory();
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
	 * @return Option[] Array with Option objects.
	 */
	public static function getTranslationHelperObjects($clang_id, $type) {
		$query = 'SELECT option_id FROM '. \rex::getTablePrefix() .'d2u_machinery_options_lang '
				."WHERE clang_id = ". $clang_id ." AND translation_needs_update = 'yes' "
				.'ORDER BY name';
		if($type == 'missing') {
			$query = 'SELECT main.option_id FROM '. \rex::getTablePrefix() .'d2u_machinery_options AS main '
					.'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_options_lang AS target_lang '
						.'ON main.option_id = target_lang.option_id AND target_lang.clang_id = '. $clang_id .' '
					.'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_options_lang AS default_lang '
						.'ON main.option_id = default_lang.option_id AND default_lang.clang_id = '. \rex_config::get('d2u_helper', 'default_lang') .' '
					."WHERE target_lang.option_id IS NULL "
					.'ORDER BY default_lang.name';
			$clang_id = \rex_config::get('d2u_helper', 'default_lang');
		}
		$result = \rex_sql::factory();
		$result->setQuery($query);

		$objects = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$objects[] = new Option($result->getValue("option_id"), $clang_id);
			$result->next();
		}
		
		return $objects;
    }
	
	/**
	 * returned all unused objects in database.
	 * @return boolean|Option[] Array with unused options
	 */
	public static function getUnused() {
		$options = Option::getAll(rex_config::get("d2u_helper", "default_lang", rex_clang::getStartId()));
		
		foreach ($options as $option) {
			if(count($option->getReferringMachines()) > 0) {
				unset($options[$option->option_id]);
			}
		}

		if(count($options) > 0) {
			return $options;
		}
		else {
			return FALSE;
		}
	}
	
	/**
	 * Updates or inserts the object into database.
	 * @return boolean TRUE if successful
	 */
	public function save() {
		$error = FALSE;

		// Save the not language specific part
		$pre_save_option = new Option($this->option_id, $this->clang_id);
		
		// save priority, but only if new or changed
		if($this->priority != $pre_save_option->priority || $this->option_id == 0) {
			$this->setPriority();
		}
		
		// saving the rest
		if($this->option_id == 0 || $pre_save_option != $this) {
			$query = \rex::getTablePrefix() ."d2u_machinery_options SET "
					."pic = '". $this->pic ."', "
					."category_ids = '|". implode("|", $this->category_ids) ."|', "
					."priority = ". $this->priority ." ";
			if(\rex_addon::get('d2u_videos')->isAvailable() && $this->video !== FALSE) {
				$query .= ", video_id = ". $this->video->video_id;
			}
			else {
				$query .= ", video_id = NULL";
			}

			if($this->option_id == 0) {
				$query = "INSERT INTO ". $query;
			}
			else {
				$query = "UPDATE ". $query ." WHERE option_id = ". $this->option_id;
			}

			$result = \rex_sql::factory();
			$result->setQuery($query);
			if($this->option_id == 0) {
				$this->option_id = $result->getLastId();
				$error = $result->hasError();
			}
		}
		
		if($error === FALSE) {
			// Save the language specific part
			$pre_save_option = new Option($this->option_id, $this->clang_id);
			if($pre_save_option != $this) {
				$query = "REPLACE INTO ". \rex::getTablePrefix() ."d2u_machinery_options_lang SET "
						."option_id = '". $this->option_id ."', "
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
	
	/**
	 * Reassigns priorities in database.
	 * @param boolean $delete Reorder priority after deletion
	 */
	private function setPriority($delete = FALSE) {
		// Pull priorities from database
		$query = "SELECT option_id, priority FROM ". \rex::getTablePrefix() ."d2u_machinery_options "
			."WHERE option_id <> ". $this->option_id ." ORDER BY priority";
		$result = \rex_sql::factory();
		$result->setQuery($query);
		
		// When prio is too small, set at beginning
		if($this->priority <= 0) {
			$this->priority = 1;
		}
		
		// When prio is too high or was deleted, simply add at end 
		if($this->priority > $result->getRows() || $delete) {
			$this->priority = $result->getRows() + 1;
		}

		$options = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$options[$result->getValue("priority")] = $result->getValue("option_id");
			$result->next();
		}
		array_splice($options, ($this->priority - 1), 0, array($this->option_id));

		// Save all prios
		foreach($options as $prio => $option_id) {
			$query = "UPDATE ". \rex::getTablePrefix() ."d2u_machinery_options "
					."SET priority = ". ($prio + 1) ." " // +1 because array_splice recounts at zero
					."WHERE option_id = ". $option_id;
			$result = \rex_sql::factory();
			$result->setQuery($query);
		}
	}
}