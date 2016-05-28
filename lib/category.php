<?php
/**
 * Redaxo D2U Machines Addon
 * @author Tobias Krais
 * @author <a href="http://www.design-to-use.de">www.design-to-use.de</a>
 */

/**
 * Machine Category
 */
class Category {
	/**
	 * @var int Database ID
	 */
	var $category_id = 0;
	
	/**
	 * @var int Redaxo clang id
	 */
	var $clang_id = 0;
	
	/**
	 * @var Category Father category object
	 */
	var $parent_category = FALSE;
	
	/**
	 * @var string Name
	 */
	var $name = "";
	
	/**
	 * @var string Name of Usage area
	 */
	var $usage_area = "";
	
	/**
	 * @var string Preview picture file name 
	 */
	var $pic = "";
	
	/**
	 * @var string Language specific preview picture file name 
	 */
	var $pic_lang = "";
	
	/**
	 * @var string Usage area picture file name
	 */
	var $pic_usage = "";
	
	/**
	 * @var int ID of the corresponding machinerypark category
	 */
	var $export_machinerypark_category_id = 0;
	
	/**
	 * @var int ID of the corresponding Europe Machinery category
	 */
	var $export_europemachinery_category_id = 0;
	
	/**
	 * @var string Name of the corresponding Europe Machinery category
	 */
	var $export_europemachinery_category_name = "";
	
	/**
	 * @var string Name of the corresponding Mascus category
	 */
	var $export_mascus_category_name = "";
	
	/**
	 * @var string Title cutting range 
	 */
	var $sawing_machines_cutting_range_title = "";
		
	/**
	 * @var string Cutting range configurator file
	 */
	var $sawing_machines_cutting_range_file = "";

	/**
	 * @var string[] Array with PDF file names.
	 */
	var $pdfs = "";
	
	/**
	 * @var int[] Array with Videomanager IDs
	 */
	var $videomanager_ids = array();
	
	/**
	 * @var string "yes" if translation needs update
	 */
	var $translation_needs_update = "delete";

	/**
	 * @var int Unix timestamp containing the last update date
	 */
	var $updatedate = 0;
	
	/**
	 * @var string Redaxo update user name
	 */
	var $updateuser = "";
	
	/**
	 * @var string URL
	 */
	var $url = "";

	/**
	 * @var string URL of cutting range configurator
	 */
	var $cutting_range_url = "";

	/**
	 * Constructor. Reads a categorie stored in database.
	 * @param int $category_id Category ID.
	 * @param int $clang_id Redaxo clang id.
	 */
	 public function __construct($category_id, $clang_id) {
		$this->clang_id = $clang_id;
		$query = "SELECT * FROM ". rex::getTablePrefix() ."d2u_machinery_categories AS categories "
				."LEFT JOIN ". rex::getTablePrefix() ."d2u_machinery_categories_lang AS lang "
					."ON categories.category_id = lang.category_id "
				."WHERE categories.category_id = ". $category_id ." AND clang_id = ". $this->clang_id ." ";
		$result = rex_sql::factory();
		$result->setQuery($query);
		$num_rows = $result->getRows();

		if ($num_rows > 0) {
			$this->category_id = $result->getValue("category_id");
			if($result->getValue("parent_category_id") != "") {
				$this->parent_category = new Category($result->getValue("parent_category_id"), $clang_id);
			}
			$this->name = $result->getValue("name");
			$this->usage_area = $result->getValue("usage_area");
			$this->pic = $result->getValue("pic");
			$this->pic_lang = $result->getValue("pic_lang");
			$this->pic_usage = $result->getValue("pic_usage");
			$this->pdfs = preg_grep('/^\s*$/s', explode(",", $result->getValue("pdfs")), PREG_GREP_INVERT);
			$this->videomanager_ids = preg_grep('/^\s*$/s', explode("|", $result->getValue("videomanager_ids")), PREG_GREP_INVERT); // TODO
			$this->translation_needs_update = $result->getValue("translation_needs_update");
			$this->updatedate = $result->getValue("updatedate");
			$this->updateuser = $result->getValue("updateuser");

			// Export plugin fields
			if(rex_plugin::get("d2u_machinery", "export")->isAvailable()) {
				$this->export_europemachinery_category_id = $result->getValue("export_europemachinery_category_id");
				$this->export_europemachinery_category_name = $result->getValue("export_europemachinery_category_name");
				$this->export_machinerypark_category_id = $result->getValue("export_machinerypark_category_id");
				$this->export_mascus_category_name = $result->getValue("export_mascus_category_name");
			}
			
			// Sawing machines plugin fields
			if(rex_plugin::get("d2u_machinery", "sawing_machines")->isAvailable()) {
				$this->sawing_machines_cutting_range_title = $result->getValue("sawing_machines_cutting_range_title");
				$this->sawing_machines_cutting_range_file = $result->getValue("sawing_machines_cutting_range_file");
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
			$query_lang = "DELETE FROM ". rex::getTablePrefix() ."d2u_machinery_categories_lang "
				."WHERE category_id = ". $this->category_id;
			$result_lang = rex_sql::factory();
			$result_lang->setQuery($query_lang);

			$query = "DELETE FROM ". rex::getTablePrefix() ."d2u_machinery_categories "
				."WHERE category_id = ". $this->category_id;
			$result = rex_sql::factory();
			$result->setQuery($query);
		}
		else {
			$query_lang = "DELETE FROM ". rex::getTablePrefix() ."d2u_machinery_categories_lang "
				."WHERE category_id = ". $this->category_id ." AND clang_id = ". $this->clang_id;
			$result_lang = rex_sql::factory();
			$result_lang->setQuery($query_lang);
		}
	}
	
	/**
	 * Get all categories.
	 * @param int $clang_id Redaxo clang id.
	 * @return Category[] Array with Category objects.
	 */
	public static function getAll($clang_id) {
		$query = "SELECT category_id FROM ". rex::getTablePrefix() ."d2u_machinery_categories_lang "
			."WHERE clang_id = ". $clang_id ." "
			."ORDER BY name";
		$result = rex_sql::factory();
		$result->setQuery($query);
		
		$categories = array();
		for($i = 0; $i < $result->getRows(); $i++) {
			$categories[] = new Category($result->getValue("category_id"), $clang_id);
			$result->next();
		}
		return $categories;
	}

	/**
	 * Detects whether category is child or not.
	 * @return boolean TRUE if category has father.
	 */
	public function isChild() {
		if($this->parent_category === FALSE) {
			return FALSE;
		}
		else {
			return TRUE;
		}
	}
	
	/**
	 * Detects usage of this category by machines.
	 * @return boolean TRUE if used.
	 */
	public function isUsedByMachines() {
		$query = "SELECT category_id FROM ". rex::getTablePrefix() ."d2u_machinery_machines_lang "
			."WHERE clang_id = ". $this->clang_id;
		$result = rex_sql::factory();
		$result->setQuery($query);
		
		if($result->getRows() > 0) {
			return TRUE;
		}
		else {
			return FALSE;
		}
	}

	/**
	 * Detects usage of this category by used_machines plugin. If not installed,
	 * FALSE ist returned.
	 * @return boolean TRUE if used.
	 */
	public function isUsedByUsedMachines() {
		if(rex_plugin::get("d2u_machinery", "used_machines")->isAvailable()) {
			$query = "SELECT category_id FROM ". rex::getTablePrefix() ."d2u_machinery_used_machines_lang "
				."WHERE clang_id = ". $this->clang_id;
			$result = rex_sql::factory();
			$result->setQuery($query);

			if($result->getRows() > 0) {
				return TRUE;
			}
			else {
				return FALSE;
			}
		}
		else {
			return FALSE;
		}
	}
	
	/*
	 * Gibt die URL der Kategorie zurueck.
	 * @return string URL für Kategorie
	 */
	public function getURL() {
		if($this->url == "") {
			global $REX;

			$url = $REX['SERVER'];
			
			$pathname = '';
			if($REX['MOD_REWRITE'] == true && OOAddon::isActivated('seo42')) {
				// Mit SEO42
				require_once dirname(__FILE__) ."/../../seo42/classes/class.seo42_rewrite.inc.php";

				if (count($REX['CLANG']) > 1 && $this->clang_id != $REX['ADDON']['seo42']['settings']['hide_langslug']) {
					// Sprachkuerzel
					$pathname = seo42_appendToPath($pathname, $REX['ADDON']['seo42']['settings']['lang'][$this->clang_id]['code'],
							$REX['START_ARTICLE_ID'], $this->clang_id);
				}

				// Dann Redaxo Artikelfolge
				if($this->artikel_id > 0) {
					$kategorie = OOCategory::getCategoryById($this->artikel_id, $this->clang_id);
					$hauptkategorien = $kategorie->getPathAsArray();
					for($i = 0; $i < count($hauptkategorien); $i++) {
						$hauptkategorie = OOCategory::getCategoryById($hauptkategorien[$i],
								$this->clang_id);
						if($hauptkategorie instanceof OOCategory) {
							$pathname = seo42_appendToPath($pathname, $hauptkategorie->getName(),
									$hauptkategorie->getId(), $this->clang_id);
						}
					}
					$pathname = seo42_appendToPath($pathname, $kategorie->getName(),
							$kategorie->getId(), $this->clang_id);
				}

				// Die Kategorie
				$pathname = seo42_appendToPath($pathname, $this->name,
						$this->artikel_id, $this->clang_id);
				$pathname = substr($pathname, 0, -1) . $REX['ADDON']['seo42']['settings']['url_ending'];
			}
			else {
				// Ohne SEO42
				$parameterArray = array();
				$parameterArray['category_id'] = $this->category_id;
				$pathname = rex_getUrl($this->artikel_id, $this->clang_id, $parameterArray, "&");
			}

			$url .= $pathname;

			$this->url = $url;
		}

		return $this->url;
	}
	
	/*
	 * Gibt die URL für den Schnittbereichskonfigurator der Kategorie zurueck.
	 * @return string URL für den Schnittbereichskonfigurator, false wenn nicht vorhanden
	 */
	function getURLSchnittbereichskonfigurator() {
		if($this->sawing_machines_cutting_range_title == "" || $this->sawing_machines_cutting_range_file == "") {
			return false;
		}
		if($this->cutting_range_url == "") {
			global $REX;

			$url = $REX['SERVER'];
			
			$pathname = '';
			if($REX['MOD_REWRITE'] == true && OOAddon::isActivated('seo42')) {
				// Mit SEO42
				require_once dirname(__FILE__) ."/../../seo42/classes/class.seo42_rewrite.inc.php";

				if (count($REX['CLANG']) > 1 && $this->clang_id != $REX['ADDON']['seo42']['settings']['hide_langslug']) {
					// Sprachkuerzel
					$pathname = seo42_appendToPath($pathname, $REX['ADDON']['seo42']['settings']['lang'][$this->clang_id]['code'],
							$REX['START_ARTICLE_ID'], $this->clang_id);
				}

				// Dann Redaxo Artikelfolge
				if($this->artikel_id > 0) {
					$kategorie = OOCategory::getCategoryById($this->artikel_id, $this->clang_id);
					$hauptkategorien = $kategorie->getPathAsArray();
					for($i = 0; $i < count($hauptkategorien); $i++) {
						$hauptkategorie = OOCategory::getCategoryById($hauptkategorien[$i],
								$this->clang_id);
						if($hauptkategorie instanceof OOCategory) {
							$pathname = seo42_appendToPath($pathname, $hauptkategorie->getName(),
									$hauptkategorie->getId(), $this->clang_id);
						}
					}
					$pathname = seo42_appendToPath($pathname, $kategorie->getName(),
							$kategorie->getId(), $this->clang_id);
				}

				// Die Kategorie
				$pathname = seo42_appendToPath($pathname, $this->name,
						$this->artikel_id, $this->clang_id);
				// Der Konfigurator
				$pathname = seo42_appendToPath($pathname, $this->sawing_machines_cutting_range_title,
						$this->artikel_id, $this->clang_id);
				$pathname = substr($pathname, 0, -1) . $REX['ADDON']['seo42']['settings']['url_ending'];
			}
			else {
				// without SEO42
				$parameterArray = array();
				$parameterArray['category_id'] = $this->category_id;
				$parameterArray['schnittbereichskonfigurator'] = $this->sawing_machines_cutting_range_file;
				$pathname = rex_getUrl($this->artikel_id, $this->clang_id, $parameterArray, "&");
			}

			$url .= $pathname;
			$this->cutting_range_url = $url;
		}

		return $this->cutting_range_url;
	}
	
	/**
	 * Updates or inserts the object into database.
	 * @return boolean TRUE if successful
	 */
	public function save() {
		$error = 0;

		// Save the not language specific part
		$pre_save_category = new Category($this->category_id, $this->clang_id);
		if($this->category_id == 0 || $pre_save_category != $this) {
			$query = rex::getTablePrefix() ."d2u_machinery_categories SET "
					."parent_category_id = '". $this->parent_category->category_id ."', "
					."pic = '". $this->pic ."', "
					."pic_usage = '". $this->pic_usage ."' ";
			if(rex_plugin::get("d2u_machinery", "export")->isAvailable()) {
				$query .= ", export_europemachinery_category_id = '". $this->export_europemachinery_category_id ."', "
					."export_europemachinery_category_name = '". $this->export_europemachinery_category_name ."', "
					."export_machinerypark_category_id = '". $this->export_machinerypark_category_id ."', "
					."export_mascus_category_name = '". $this->export_mascus_category_name ."' ";
			}
			if(rex_addon::get("d2u_videos")->isAvailable()) {
				$query .= ", videomanager_ids = '". implode(",", $this->videomanager_ids) ."' ";
			}

			if($this->category_id == 0) {
				$query = "INSERT INTO ". $query;
			}
			else {
				$query = "UPDATE ". $query ." WHERE category_id = ". $this->category_id;
			}

			$result = rex_sql::factory();
			$result->setQuery($query);
			if($this->category_id == 0) {
				$this->category_id = $result->getLastId();
				$error = $result->hasError();
			}
		}
		
		if($error == 0) {
			// Save the language specific part
			$pre_save_category = new Category($this->category_id, $this->clang_id);
			if($pre_save_category != $this) {
				$query = "REPLACE INTO ". rex::getTablePrefix() ."d2u_machinery_categories_lang SET "
						."category_id = '". $this->category_id ."', "
						."clang_id = '". $this->clang_id ."', "
						."name = '". htmlspecialchars($this->name) ."', "
						."usage_area = '". htmlspecialchars($this->usage_area) ."', "
						."pic_lang = '". $this->pic_lang ."', "
						."pdfs = '". implode(",", $this->pdfs) ."', "
						."translation_needs_update = '". $this->translation_needs_update ."', "
						."updatedate = ". time() .", "
						."updateuser = '". rex::getUser()->getLogin() ."' ";

				$result = rex_sql::factory();
				$result->setQuery($query);
				$error = $result->hasError();
			}
		}
		return $error;
	}
}