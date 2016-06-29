<?php
/**
 * Redaxo D2U Machines Addon - Used Machines Plugin
 * @author Tobias Krais
 * @author <a href="http://www.design-to-use.de">www.design-to-use.de</a>
 */

/**
 * Used machine object.
 */
class UsedMachine {
	/**
	 * @var int Database ID
	 */
	var $used_machine_id = 0;
	
	/**
	 * @var int Redaxo Clang ID
	 */
	var $clang_id = 0;
	
	/**
	 * @var String Name
	 */
	var $name = "";
	
	/**
	 * @var Category Machine category
	 */
	var $category = false;

	/**
	 * @var String[] Picture filenames
	 */
	var $pics = array();
	
	/**
	 * @var Machine Machine objekt for technical data reference
	 */
	var $machine = false;
	
	/**
	 * @var int Year of construction
	 */
	var $year_built = 0;

	/**
	 * @var String Standort der Gebrauchtmaschine.
	 */
	var $standort = "";
	
	/**
	 * @var String URL mit weiterfuehrenden Informationen der Gebrauchtmaschine,
	 * z.B. Anzeige des Anbieters auf einer anderen Webseite.
	 */
	var $link = "";
	
	/**
	 * @var int Redaxo Artikel ID.
	 */
	var $artikel_id = 0;

	/**
	 * @var String Status der Maschine. Entweder online oder offline.
	 */
	var $status = "online";
	
	/**
	 * @var String Kurzbeschreibung als Dreizeiler oder Teaser der Maschine.
	 */
	var $kurzbeschreibung = "";

	/**
	 * @var String Beschreibung mit Details zur Maschine. Im HTML Format.
	 */
	var $beschreibung = "";

	/**
	 * @var String URL der Gebrauchtmaschine
	 */
	var $url = "";

	/**
	 * Stellt die Daten der Gebrauchtmaschine aus der Datenbank zusammen.
	 * @param int $gebrauchtmaschinen_id GebrauchtmaschinenID aus der Datenbank.
	 * @param int $clang_id Redaxo SprachID.
	 * @param String $table_prefix. Redaxo Tabellen Praefix ($REX['TABLE_PREFIX'])
	 */
	 public function __construct($gebrauchtmaschinen_id, $clang_id) {
		$this->table_prefix = $table_prefix;
		
		// Sprachfallback
		$query_prove = "SELECT * FROM ". $this->table_prefix ."d2u_ktb_maschinen_gebrauchtmaschinen_lang AS lang "
				."WHERE gebrauchtmaschinen_id = ". $gebrauchtmaschinen_id ." "
				."ORDER BY clang_id DESC";
		$result_prove = new rex_sql();
		$result_prove->setQuery($query_prove);
		$num_rows_prove = $result_prove->getRows();

		$is_lang_available = false;
		$fallback_lang_id = 0;
		for($i = 0; $i < $num_rows_prove; $i++) {
			if($result_prove->getValue("lang.clang_id") == $clang_id) {
				$is_lang_available = true;
			}
			else {
				$fallback_lang_id = $result_prove->getValue("lang.clang_id");
			}
			$result_prove->next();
		}

		$sql_clang_id = $clang_id;
		if($is_lang_available == false) {
			$sql_clang_id = $fallback_lang_id;
		}

		$query = "SELECT * FROM ". $this->table_prefix ."d2u_ktb_maschinen_gebrauchtmaschinen AS gebrauchtmaschinen "
				."LEFT JOIN ". $this->table_prefix ."d2u_ktb_maschinen_gebrauchtmaschinen_lang AS lang "
					."ON gebrauchtmaschinen.gebrauchtmaschinen_id = lang.gebrauchtmaschinen_id "
				."WHERE gebrauchtmaschinen.gebrauchtmaschinen_id = ". $gebrauchtmaschinen_id ." "
					."AND (clang_id = ". $sql_clang_id ." OR clang_id IS NULL) "
				."LIMIT 0, 1";
		$result = new rex_sql();
		$result->setQuery($query);
		$num_rows = $result->getRows();

		if ($num_rows > 0) {
			$this->used_machine_id = $result->getValue("gebrauchtmaschinen.gebrauchtmaschinen_id");
			if($result->getValue("clang_id") != "") {
				$this->clang_id = $result->getValue("clang_id");
			}
			$this->name = $result->getValue("name");
			$this->pics = preg_grep('/^\s*$/s', explode(",", $result->getValue("bilder")), PREG_GREP_INVERT);
			$this->category = new Category($result->getValue("kategorie_id"), $clang_id, $this->table_prefix);
			$this->zustand = $result->getValue("zustand");
			if($result->getValue("maschinen_id") > 0) {
				$this->machine = new Maschine($result->getValue("maschinen_id"), $clang_id, $this->table_prefix);
			}
			$this->year_built = $result->getValue("baujahr");
			$this->standort = $result->getValue("standort");
			$this->link = $result->getValue("url");
			$this->artikel_id = $result->getValue("artikel_id");
			if($result->getValue("status") != "") {
				$this->status = $result->getValue("status");
			}
			$this->beschreibung = $result->getValue("beschreibung");
			$this->kurzbeschreibung = $result->getValue("kurzbeschreibung");

			// URL des externen Links bei Bedarf korrigieren
			if(strlen($this->link) > 3 && substr($this->link, 0, 4) != "http") {
				$this->link = "http://". $this->link;
			}

			// redaxo://123 in URL umwandeln
			$this->beschreibung = preg_replace_callback(
					'@redaxo://(\d+)(?:-(\d+))?/?@i',
					create_function(
							'$matches',
							'return rex_getUrl($matches[1], isset($matches[2]) ? $matches[2] : "");'
					),
					$this->beschreibung
			);
		}
	}

	/**
	 * Gibt die Gebrauchtmaschinen für eine Maschine aus
	 * @param int $maschinen_id ID der Maschine zu der die passenden Gebrauchtmaschinen gesucht werden.
	 * @param int $clang_id Redaxo SprachID.
	 * @param String $table_prefix. Redaxo Tabellen Praefix ($REX['TABLE_PREFIX'])
	 * @return Array mit Gebrauchtmaschinen
	 */
	static function getGebrauchtmaschinenForMaschinenID($maschinen_id, $clang_id, $table_prefix = "rex_") {
		$gebrauchtmaschinen = array();
		
		$query = "SELECT gebrauchtmaschinen_id FROM ". $table_prefix ."d2u_ktb_maschinen_gebrauchtmaschinen "
				."WHERE maschinen_id = ". $maschinen_id ." "
					."AND status = 'online'";

		$result = new rex_sql();
		$result->setQuery($query);
		
		for($i = 0; $i < $result->getRows(); $i++) {
			$gebrauchtmaschinen[] = new UsedMachine($result->getValue("gebrauchtmaschinen_id"), $clang_id, $table_prefix);
			$result->next();
		}
		
		return $gebrauchtmaschinen;
	}
	
	/**
	 * Gibt die Gebrauchtmaschinen für eine Kategorie aus
	 * @param int $kategorie_id ID der Katgrgorie zu der die passenden Gebrauchtmaschinen gesucht werden.
	 * @param int $clang_id Redaxo SprachID.
	 * @param String $table_prefix. Redaxo Tabellen Praefix ($REX['TABLE_PREFIX'])
	 * @return Array mit Gebrauchtmaschinen
	 */
	static function getGebrauchtmaschinenForKategorieID($kategorie_id, $clang_id, $table_prefix = "rex_") {
		$gebrauchtmaschinen = array();
		
		$query = "SELECT gebrauchtmaschinen_id FROM ". $table_prefix ."d2u_ktb_maschinen_gebrauchtmaschinen "
				."WHERE kategorie_id = ". $kategorie_id ." "
					."AND status = 'online'";
		$result = new rex_sql();
		$result->setQuery($query);
		
		for($i = 0; $i < $result->getRows(); $i++) {
			$gebrauchtmaschinen[] = new UsedMachine($result->getValue("gebrauchtmaschinen_id"), $clang_id, $table_prefix);
			$result->next();
		}
		
		return $gebrauchtmaschinen;
	}
	
	/**
	 * Gibt die URL einer Gebrauchtmaschine zurueck.
	 * @return String URL für diese Gebrauchtmaschine.
	 */
	function getURL() {
		if($this->url != "") {
			return $this->url;
		}
		else {
			global $REX;

			$url = "http://";
			if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != "off") {
				$url = "https://";
			}
			if($_SERVER['SERVER_NAME'] != "localhost") {
				$url .= $_SERVER['SERVER_NAME'];
			}
			else {
				$url .= "localhost/~tobias/www.kaltenbach.com";
			}

			$pathname = '/';
			if($REX['MOD_REWRITE'] == true && OOAddon::isActivated('seo42')) {
				// Mit RexSEO
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
				// Jetzt die Gebrauchtmaschine
				$pathname = seo42_appendToPath($pathname, $this->name, $this->artikel_id,
						$this->clang_id);
				$pathname = seo42_appendToPath($pathname, $this->used_machine_id,
						$this->artikel_id, $this->clang_id);
				$pathname = substr($pathname, 0, -1) . $REX['ADDON']['seo42']['settings']['url_ending'];
			}
			else {
				// Ohne RexSEO
				$parameterArray = array();
				$parameterArray['gebrauchtmaschinen_id'] = $this->used_machine_id;
				$pathname = rex_getUrl($this->artikel_id, $this->clang_id, $parameterArray, "&");
			}

			$url .= $pathname;

			return $url;
		}
	}
}
?>