<?php
/**
 * Redaxo D2U Machines Addon
 * @author Tobias Krais
 * @author <a href="http://www.design-to-use.de">www.design-to-use.de</a>
 */

/**
 * Supply
 */
class Supply implements \D2U_Helper\ITranslationHelper {
	/**
	 * @var int Database ID
	 */
	var $supply_id = 0;
	
	/**
	 * @var int Redaxo clang id
	 */
	var $clang_id = 0;
	
	/**
	 * @var String Status. Either "online" or "offline".
	 */
	var $online_status = "online";
	
	/**
	 * @var string Name
	 */
	var $name = "";
	
	/**
	 * @var string Title
	 */
	var $title = "";
	
	/**
	 * @var string Description
	 */
	var $description = "";
	
	/**
	 * @var string Picture
	 */
	var $pic = "";
	
	/**
	 * @var Video Videomanager video
	 */
	var $video = FALSE;
	
	/**
	 * @var string "yes" if translation needs update
	 */
	var $translation_needs_update = "delete";

	/**
	 * Constructor. Reads object stored in database.
	 * @param int $supply_id Object ID.
	 * @param int $clang_id Redaxo clang id.
	 */
	 public function __construct($supply_id, $clang_id) {
		$this->clang_id = $clang_id;
		$query = "SELECT * FROM ". \rex::getTablePrefix() ."d2u_machinery_steel_supply AS supplys "
				."LEFT JOIN ". \rex::getTablePrefix() ."d2u_machinery_steel_supply_lang AS lang "
					."ON supplys.supply_id = lang.supply_id "
					."AND clang_id = ". $this->clang_id ." "
				."WHERE supplys.supply_id = ". $supply_id;
		$result = \rex_sql::factory();
		$result->setQuery($query);
		$num_rows = $result->getRows();

		if ($num_rows > 0) {
			$this->supply_id = $result->getValue("supply_id");
			$this->online_status = $result->getValue("online_status");
			$this->name = stripslashes($result->getValue("name"));
			$this->title = $result->getValue("title");
			$this->description = $result->getValue("description");
			$this->pic = $result->getValue("pic");
			if($result->getValue("translation_needs_update") != "") {
				$this->translation_needs_update = $result->getValue("translation_needs_update");
			}

			if(\rex_addon::get('d2u_videos')->isAvailable() && $result->getValue("video_id") > 0) {
				$this->video = new Video($result->getValue("video_id"), $clang_id);
			}
		}
	}
	

	/**
	 * Changes the status of the object
	 */
	public function changeStatus() {
		if($this->online_status == "online") {
			if($this->supply_id > 0) {
				$query = "UPDATE ". \rex::getTablePrefix() ."d2u_machinery_steel_supply "
					."SET online_status = 'offline' "
					."WHERE supply_id = ". $this->supply_id;
				$result = \rex_sql::factory();
				$result->setQuery($query);
			}
			$this->online_status = "offline";
		}
		else {
			if($this->supply_id > 0) {
				$query = "UPDATE ". \rex::getTablePrefix() ."d2u_machinery_steel_supply "
					."SET online_status = 'online' "
					."WHERE supply_id = ". $this->supply_id;
				$result = \rex_sql::factory();
				$result->setQuery($query);
			}
			$this->online_status = "online";			
		}
	}

	/**
	 * Deletes the object in all languages.
	 * @param int $delete_all If TRUE, all translations and main object are deleted. If 
	 * FALSE, only this translation will be deleted.
	 */
	public function delete($delete_all = TRUE) {
		$query_lang = "DELETE FROM ". \rex::getTablePrefix() ."d2u_machinery_steel_supply_lang "
			."WHERE supply_id = ". $this->supply_id
			. ($delete_all ? '' : ' AND clang_id = '. $this->clang_id) ;
		$result_lang = \rex_sql::factory();
		$result_lang->setQuery($query_lang);
		
		// If no more lang objects are available, delete
		$query_main = "SELECT * FROM ". \rex::getTablePrefix() ."d2u_machinery_steel_supply_lang "
			."WHERE supply_id = ". $this->supply_id;
		$result_main = \rex_sql::factory();
		$result_main->setQuery($query_main);
		if($result_main->getRows() == 0) {
			$query = "DELETE FROM ". \rex::getTablePrefix() ."d2u_machinery_steel_supply "
				."WHERE supply_id = ". $this->supply_id;
			$result = \rex_sql::factory();
			$result->setQuery($query);
		}
	}
	
	/**
	 * Get all supplys.
	 * @param int $clang_id Redaxo clang id.
	 * @param boolean $only_online Show only online objects
	 * @return Supply[] Array with Supply objects.
	 */
	public static function getAll($clang_id, $only_online = FALSE) {
		$query = "SELECT lang.supply_id FROM ". \rex::getTablePrefix() ."d2u_machinery_steel_supply_lang AS lang "
			. "LEFT JOIN ". \rex::getTablePrefix() ."d2u_machinery_steel_supply AS supply "
				. "ON lang.supply_id = supply.supply_id "
			."WHERE clang_id = ". $clang_id ." ";
		if($only_online) {
			$query .= "WHERE online_status = 'online' ";
		}

		$query .= "ORDER BY name";

		$result = \rex_sql::factory();
		$result->setQuery($query);
		
		$supplys = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$supplys[] = new Supply($result->getValue("supply_id"), $clang_id);
			$result->next();
		}
		return $supplys;
	}
	
	/**
	 * Gets the machines referring to this object.
	 * @return Machine[] Machines referring to this object.
	 */
	public function getReferringMachines() {
		$query = "SELECT machine_id FROM ". \rex::getTablePrefix() ."d2u_machinery_machines "
			."WHERE automation_supply_ids LIKE '%|". $this->supply_id ."|%'";
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
	 * @return Supply[] Array with Supply objects.
	 */
	public static function getTranslationHelperObjects($clang_id, $type) {
		$query = 'SELECT supply_id FROM '. \rex::getTablePrefix() .'d2u_machinery_steel_supply_lang '
				."WHERE clang_id = ". $clang_id ." AND translation_needs_update = 'yes' "
				.'ORDER BY name';
		if($type == 'missing') {
			$query = 'SELECT main.supply_id FROM '. \rex::getTablePrefix() .'d2u_machinery_steel_supply AS main '
					.'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_steel_supply_lang AS target_lang '
						.'ON main.supply_id = target_lang.supply_id AND target_lang.clang_id = '. $clang_id .' '
					.'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_steel_supply_lang AS default_lang '
						.'ON main.supply_id = default_lang.supply_id AND default_lang.clang_id = '. \rex_config::get('d2u_helper', 'default_lang') .' '
					."WHERE target_lang.supply_id IS NULL "
					.'ORDER BY default_lang.name';
			$clang_id = \rex_config::get('d2u_helper', 'default_lang');
		}
		$result = \rex_sql::factory();
		$result->setQuery($query);

		$objects = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$objects[] = new Supply($result->getValue("supply_id"), $clang_id);
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
		$pre_save_supply = new Supply($this->supply_id, $this->clang_id);
		
		// saving the rest
		if($this->supply_id == 0 || $pre_save_supply != $this) {
			$query = \rex::getTablePrefix() ."d2u_machinery_steel_supply SET "
					."online_status = '". $this->online_status ."', "
					."pic = '". $this->pic ."' ";
			if(\rex_addon::get('d2u_videos')->isAvailable() && $this->video !== FALSE) {
				$query .= ", video_id = ". $this->video->video_id;
			}
			else {
				$query .= ", video_id = NULL";
			}

			if($this->supply_id == 0) {
				$query = "INSERT INTO ". $query;
			}
			else {
				$query = "UPDATE ". $query ." WHERE supply_id = ". $this->supply_id;
			}

			$result = \rex_sql::factory();
			$result->setQuery($query);
			if($this->supply_id == 0) {
				$this->supply_id = $result->getLastId();
				$error = $result->hasError();
			}
		}
		
		if($error === FALSE) {
			// Save the language specific part
			$pre_save_supply = new Supply($this->supply_id, $this->clang_id);
			if($pre_save_supply != $this) {
				$query = "REPLACE INTO ". \rex::getTablePrefix() ."d2u_machinery_steel_supply_lang SET "
						."supply_id = '". $this->supply_id ."', "
						."clang_id = '". $this->clang_id ."', "
						."name = '". addslashes($this->name) ."', "
						."title = '". $this->title ."', "
						."description = '". $this->description ."', "
						."translation_needs_update = '". $this->translation_needs_update ."' ";

				$result = \rex_sql::factory();
				$result->setQuery($query);
				$error = $result->hasError();
			}
		}
		
		return !$error;
	}
}