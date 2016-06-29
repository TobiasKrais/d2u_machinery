<?php
/**
 * Redaxo D2U Machines Addon
 * @author Tobias Krais
 * @author <a href="http://www.design-to-use.de">www.design-to-use.de</a>
 */

/**
 * Industry sector
 */
class Certificate {
	/**
	 * @var int Database ID
	 */
	var $certificate_id = 0;
	
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
	 * Constructor. Reads an Certificate stored in database.
	 * @param int $certificate_id Object ID.
	 * @param int $clang_id Redaxo clang id.
	 */
	 public function __construct($certificate_id, $clang_id) {
		$this->clang_id = $clang_id;
		$query = "SELECT * FROM ". rex::getTablePrefix() ."d2u_machinery_certificates AS certificates "
				."LEFT JOIN ". rex::getTablePrefix() ."d2u_machinery_certificates_lang AS lang "
					."ON certificates.certificate_id = lang.certificate_id "
				."WHERE certificates.certificate_id = ". $certificate_id ." AND clang_id = ". $this->clang_id ." ";
		$result = rex_sql::factory();
		$result->setQuery($query);
		$num_rows = $result->getRows();

		if ($num_rows > 0) {
			$this->certificate_id = $result->getValue("certificate_id");
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
			$query_lang = "DELETE FROM ". rex::getTablePrefix() ."d2u_machinery_certificates_lang "
				."WHERE certificate_id = ". $this->certificate_id;
			$result_lang = rex_sql::factory();
			$result_lang->setQuery($query_lang);

			$query = "DELETE FROM ". rex::getTablePrefix() ."d2u_machinery_certificates "
				."WHERE certificate_id = ". $this->certificate_id;
			$result = rex_sql::factory();
			$result->setQuery($query);
		}
		else {
			$query_lang = "DELETE FROM ". rex::getTablePrefix() ."d2u_machinery_certificates_lang "
				."WHERE certificate_id = ". $this->certificate_id ." AND clang_id = ". $this->clang_id;
			$result_lang = rex_sql::factory();
			$result_lang->setQuery($query_lang);
		}
	}
	
	/**
	 * Get all certificates.
	 * @param int $clang_id Redaxo clang id.
	 * @return Certificate[] Array with Certificate objects.
	 */
	public static function getAll($clang_id) {
		$query = "SELECT certificate_id FROM ". rex::getTablePrefix() ."d2u_machinery_certificates_lang "
			."WHERE clang_id = ". $clang_id ." ";
		$query .= "ORDER BY name";

		$result = rex_sql::factory();
		$result->setQuery($query);
		
		$certificates = array();
		for($i = 0; $i < $result->getRows(); $i++) {
			$certificates[] = new Certificate($result->getValue("certificate_id"), $clang_id);
			$result->next();
		}
		return $certificates;
	}
	
	/**
	 * Gets the machines reffering to this object.
	 * @return Machine[] Machines reffering to this object.
	 */
	public function getRefferingMachines() {
		$query = "SELECT machine_id FROM ". rex::getTablePrefix() ."d2u_machinery_machines "
			."WHERE certificate_ids LIKE '%|". $this->certificate_id ."|%'";
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
		$pre_save_certificate = new Certificate($this->certificate_id, $this->clang_id);
		
		// saving the rest
		if($this->certificate_id == 0 || $pre_save_certificate != $this) {
			$query = rex::getTablePrefix() ."d2u_machinery_certificates SET "
					."pic = '". $this->pic ."' ";

			if($this->certificate_id == 0) {
				$query = "INSERT INTO ". $query;
			}
			else {
				$query = "UPDATE ". $query ." WHERE certificate_id = ". $this->certificate_id;
			}

			$result = rex_sql::factory();
			$result->setQuery($query);
			if($this->certificate_id == 0) {
				$this->certificate_id = $result->getLastId();
				$error = $result->hasError();
			}
		}
		
		if($error == 0) {
			// Save the language specific part
			$pre_save_certificate = new Certificate($this->certificate_id, $this->clang_id);
			if($pre_save_certificate != $this) {
				$query = "REPLACE INTO ". rex::getTablePrefix() ."d2u_machinery_certificates_lang SET "
						."certificate_id = '". $this->certificate_id ."', "
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