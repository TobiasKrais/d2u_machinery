<?php
/**
 * Offers helper functions for frontend
 */
class d2u_machinery_frontend_helper {
	/**
	 * Returns alternate URLs. Key is Redaxo language id, value is URL
	 * @return string[] alternate URLs
	 */
	public static function getAlternateURLs() {
		$alternate_URLs = [];

		// Prepare objects first for sorting in correct order
		$url_namespace = d2u_addon_frontend_helper::getUrlNamespace();
		$url_id = d2u_addon_frontend_helper::getUrlId();
		
		if(filter_input(INPUT_GET, 'machine_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || $url_namespace === "machine_id") {
			$machine_id = filter_input(INPUT_GET, 'machine_id', FILTER_VALIDATE_INT);
			if(rex_addon::get("url")->isAvailable() && $url_id > 0) {
				$machine_id = $url_id;
			}

			if($machine_id > 0) {
				$machine = new Machine($machine_id, rex_clang::getCurrentId());
				foreach(rex_clang::getAllIds(TRUE) as $this_lang_key) {
					$lang_machine = new Machine($machine_id, $this_lang_key);
					if($lang_machine->translation_needs_update != "delete") {
						$alternate_URLs[$this_lang_key] = $lang_machine->getURL();
					}
				}
			}
		}
		else if(filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || $url_namespace === "category_id"
				|| filter_input(INPUT_GET, 'used_rent_category_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || $url_namespace === "used_rent_category_id"
				|| filter_input(INPUT_GET, 'used_sale_category_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || $url_namespace === "used_sale_category_id"
			) {

			// Category for normal machines
			$category_id = filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT);
			$offer_type = '';
			if(rex_plugin::get("d2u_machinery", "used_machines")->isAvailable() && filter_input(INPUT_GET, 'used_rent_category_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0) {
				// Category for used machines (rent)
				$category_id = filter_input(INPUT_GET, 'used_rent_category_id', FILTER_VALIDATE_INT);
				$offer_type = 'rent';
			}
			elseif(rex_plugin::get("d2u_machinery", "used_machines")->isAvailable() && filter_input(INPUT_GET, 'used_sale_category_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0) {
				// Category for used machines (sale)
				$category_id = filter_input(INPUT_GET, 'used_sale_category_id', FILTER_VALIDATE_INT);
				$offer_type = 'sale';
			}
			if(rex_addon::get("url")->isAvailable() && $url_id > 0) {
				$category_id = $url_id;
				if($url_namespace === "used_rent_category_id") {
					$offer_type = 'rent';
				}
				elseif($url_namespace === "used_sale_category_id") {
					$offer_type = 'sale';
				}
			}

			if($category_id > 0) {
				$category = new Category($category_id, rex_clang::getCurrentId());
				$category->setOfferType($offer_type);
				foreach(rex_clang::getAllIds(TRUE) as $this_lang_key) {
					$lang_category = new Category($category_id, $this_lang_key);
					if($lang_category->translation_needs_update != "delete") {
						$alternate_URLs[$this_lang_key] = $lang_category->getURL();
					}
				}
			}
		}
		else if(rex_plugin::get("d2u_machinery", "industry_sectors")->isAvailable() && (filter_input(INPUT_GET, 'industry_sector_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || $url_namespace === "industry_sector_id")) {
			$industry_sector_id = filter_input(INPUT_GET, 'industry_sector_id', FILTER_VALIDATE_INT);
			if(rex_addon::get("url")->isAvailable() && $url_id > 0) {
				$industry_sector_id = $url_id;
			}

			if($industry_sector_id > 0) {
				$industry_sector = new IndustrySector($industry_sector_id, rex_clang::getCurrentId());
				foreach(rex_clang::getAllIds(TRUE) as $this_lang_key) {
					$lang_industry_sector = new IndustrySector($industry_sector_id, $this_lang_key);
					if($lang_industry_sector->translation_needs_update != "delete") {
						$alternate_URLs[$this_lang_key] = $lang_industry_sector->getURL();
					}
				}
			}
		}
		else if((filter_input(INPUT_GET, 'used_rent_machine_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || $url_namespace === "used_rent_machine_id")
				|| (filter_input(INPUT_GET, 'used_sale_machine_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || $url_namespace === "used_sale_machine_id")) {
			$used_machine_id = filter_input(INPUT_GET, 'used_sale_machine_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 ? filter_input(INPUT_GET, 'used_sale_machine_id', FILTER_VALIDATE_INT) : filter_input(INPUT_GET, 'used_rent_machine_id', FILTER_VALIDATE_INT);
			if(rex_addon::get("url")->isAvailable() && $url_id > 0) {
				$used_machine_id = $url_id;
			}

			if($used_machine_id > 0) { 
				$used_machine = new UsedMachine($used_machine_id, rex_clang::getCurrentId());
				$current_category = $used_machine->category;
				foreach(rex_clang::getAllIds(TRUE) as $this_lang_key) {
					$lang_used_machine = new UsedMachine($used_machine_id, $this_lang_key);
					if($lang_used_machine->translation_needs_update != "delete") {
						$alternate_URLs[$this_lang_key] = $lang_used_machine->getURL();
					}
				}
			}
		}
		
		return $alternate_URLs;
	}

	/**
	 * Returns breadcrumbs. Not from article path, but only part from this addon.
	 * @return string[] Breadcrumb elements
	 */
	public static function getBreadcrumbs() {
		$breadcrumbs = [];

		// Prepare objects first for sorting in correct order
		$category = FALSE;
		$machine = FALSE;
		$used_machine = FALSE;

		$url_namespace = d2u_addon_frontend_helper::getUrlNamespace();
		$url_id = d2u_addon_frontend_helper::getUrlId();
		
		if(filter_input(INPUT_GET, 'machine_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || $url_namespace === "machine_id") {
			$machine_id = filter_input(INPUT_GET, 'machine_id', FILTER_VALIDATE_INT);
			if(\rex_addon::get("url")->isAvailable() && $url_id > 0) {
				$machine_id = $url_id;
			}

			if($machine_id > 0) {
				$machine = new Machine($machine_id, rex_clang::getCurrentId());
				$category = $machine->category;
			}
		}
		if(filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || $url_namespace === "category_id"
				|| filter_input(INPUT_GET, 'used_rent_category_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || $url_namespace === "used_rent_category_id"
				|| filter_input(INPUT_GET, 'used_sale_category_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || $url_namespace === "used_sale_category_id") {
			// Category for normal machines
			$category_id = filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT);
			if(rex_plugin::get("d2u_machinery", "used_machines")->isAvailable() && filter_input(INPUT_GET, 'used_rent_category_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0) {
				// Category for used machines (rent)
				$category_id = filter_input(INPUT_GET, 'used_rent_category_id', FILTER_VALIDATE_INT);
			}
			elseif(rex_plugin::get("d2u_machinery", "used_machines")->isAvailable() && filter_input(INPUT_GET, 'used_sale_category_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0) {
				// Category for used machines (sale)
				$category_id = filter_input(INPUT_GET, 'used_sale_category_id', FILTER_VALIDATE_INT);
			}
			if(\rex_addon::get("url")->isAvailable() && $url_id > 0) {
				$category_id = $url_id;
			}
			
			if($category_id > 0) {
				$category = new Category($category_id, rex_clang::getCurrentId());
			}
		}
		if(filter_input(INPUT_GET, 'used_rent_machine_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || filter_input(INPUT_GET, 'used_sale_machine_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || ($url_namespace === "used_rent_machine_id" || $url_namespace === "used_sale_machine_id")) {
			$used_machine_id = filter_input(INPUT_GET, 'used_rent_machine_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 ? filter_input(INPUT_GET, 'used_rent_machine_id', FILTER_VALIDATE_INT) : filter_input(INPUT_GET, 'used_sale_machine_id', FILTER_VALIDATE_INT);
			if(\rex_addon::get("url")->isAvailable() && $url_id > 0) {
				$used_machine_id = $url_id;
			}
			
			if($used_machine_id > 0) {
				$used_machine = new UsedMachine($used_machine_id, rex_clang::getCurrentId());
				if($used_machine->category !== FALSE) {
					$category = $used_machine->category;
					$category->setOfferType($used_machine->offer_type);
				}
			}
		}

		// Breadcrumbs
		if($category !== FALSE) {
			if($category->parent_category !== FALSE) {
				$breadcrumbs[] = '<a href="' . $category->parent_category->getUrl() . '">' . $category->parent_category->name . '</a>';
			}
			$breadcrumbs[] = '<a href="' . $category->getUrl() . '">' . $category->name . '</a>';
		}
		if($machine !== FALSE) {
			$breadcrumbs[] = '<a href="' . $machine->getUrl() . '">' . ($machine->lang_name == "" ? $machine->name : $machine->lang_name) . '</a>';
		}
		if($used_machine !== FALSE) {
			$breadcrumbs[] = '<a href="' . $used_machine->getUrl() . '">' . $used_machine->manufacturer ." ". $used_machine->name . '</a>';
		}
		
		// Industry sectors
		if(rex_plugin::get("d2u_machinery", "industry_sectors")->isAvailable() && (filter_input(INPUT_GET, 'industry_sector_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || $url_namespace === "industry_sector_id")) {
			$industry_sector_id = filter_input(INPUT_GET, 'industry_sector_id', FILTER_VALIDATE_INT);
			if(\rex_addon::get("url")->isAvailable() && $url_id > 0) {
				$industry_sector_id = $url_id;
			}
			$industry_sector = new IndustrySector($industry_sector_id, rex_clang::getCurrentId());
			$breadcrumbs[] = '<a href="' . $industry_sector->getUrl() . '">' . $industry_sector->name . '</a>';
		}
		
		return $breadcrumbs;
	}
	
	/**
	 * Prints Responive Multilevel Menu submenu für D2U Machinery Addon.
	 */
	public static function getD2UMachineryResponsiveMultiLevelSubmenu() {
		if(rex_config::get('d2u_machinery', 'show_categories_navi', 'hide') == 'show') {
			// Get current IDs
			$current_category_id = 0;
			$current_machine_id = 0;

			$url_namespace = d2u_addon_frontend_helper::getUrlNamespace();
			$url_id = d2u_addon_frontend_helper::getUrlId();

			if(filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || $url_namespace === "category_id") {
				// Category for normal machines
				$current_category_id = filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT);
				if(\rex_addon::get("url")->isAvailable() && $url_id > 0) {
					$current_category_id = $url_id;
				}
			}
			if(filter_input(INPUT_GET, 'machine_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || $url_namespace === "machine_id") {
				$current_machine_id = filter_input(INPUT_GET, 'machine_id', FILTER_VALIDATE_INT);
				if(\rex_addon::get("url")->isAvailable() && $url_id > 0) {
					$current_machine_id = $url_id;
				}
				if($current_machine_id > 0) {
					$current_machine = new Machine($current_machine_id, rex_clang::getCurrentId());
					$current_category_id = $current_machine->category->category_id;
				}
			}
			
			// Navi
			$categories = Category::getAll(rex_clang::getCurrentId());
			foreach ($categories as $category) {
				print '<li'. ($category->category_id == $current_category_id ? ' class="current"' : '') .'><a href="'. $category->getUrl() .'">'. $category->name .'</a>';
				if(rex_config::get('d2u_machinery', 'show_machines_navi', 'hide') == 'show') {
					print '<ul class="dl-submenu">';
					print '<li class="dl-back"><a href="#">&nbsp;</a></li>';
					print '<li><a href="'. $category->getUrl() .'">'. strtoupper($category->name) .'</a></li>';
					$machines = $category->getMachines(TRUE);
					foreach ($machines as $machine) {
						print '<li'. ($machine->machine_id == $current_machine_id ? ' class="current"' : '') .'><a href="'. $machine->getUrl() .'">'. $machine->name .'</a></li>';
					}
					print '</ul>';
					print '</li>';
				}
			}
		}
	}

	/**
	 * Returns Meta Tags for this addon.
	 * @return string Meta tags
	 */
	public static function getMetaTags() {
		$meta_tags = "";

		// Prepare objects first for sorting in correct order
		$url_namespace = d2u_addon_frontend_helper::getUrlNamespace();
		$url_id = d2u_addon_frontend_helper::getUrlId();

		// Machines
		if(filter_input(INPUT_GET, 'machine_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || $url_namespace === "machine_id") {
			$machine_id = filter_input(INPUT_GET, 'machine_id', FILTER_VALIDATE_INT);
			if(rex_addon::get("url")->isAvailable() && $url_id > 0) {
				$machine_id = $url_id;
			}

			if($machine_id > 0) {
				$machine = new Machine($machine_id, rex_clang::getCurrentId());
				$meta_tags .= $machine->getMetaAlternateHreflangTags();
				$meta_tags .= $machine->getCanonicalTag();
				$meta_tags .= $machine->getMetaDescriptionTag();
				$meta_tags .= $machine->getTitleTag();
			}
		}
		// Category
		else if(filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || $url_namespace === "category_id"
				|| filter_input(INPUT_GET, 'used_rent_category_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || $url_namespace === "used_rent_category_id"
				|| filter_input(INPUT_GET, 'used_sale_category_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || $url_namespace === "used_sale_category_id"
			) {

			// Category for normal machines
			$category_id = filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT);
			$offer_type = '';
			if(rex_plugin::get("d2u_machinery", "used_machines")->isAvailable() && filter_input(INPUT_GET, 'used_rent_category_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0) {
				// Category for used machines (rent)
				$category_id = filter_input(INPUT_GET, 'used_rent_category_id', FILTER_VALIDATE_INT);
				$offer_type = 'rent';
			}
			elseif(rex_plugin::get("d2u_machinery", "used_machines")->isAvailable() && filter_input(INPUT_GET, 'used_sale_category_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0) {
				// Category for used machines (sale)
				$category_id = filter_input(INPUT_GET, 'used_sale_category_id', FILTER_VALIDATE_INT);
				$offer_type = 'sale';
			}
			if(rex_addon::get("url")->isAvailable() && $url_id > 0) {
				$category_id = $url_id;
				if($url_namespace === "used_rent_category_id") {
					$offer_type = 'rent';
				}
				elseif($url_namespace === "used_sale_category_id") {
					$offer_type = 'sale';
				}
			}

			if($category_id > 0) {
				$category = new Category($category_id, rex_clang::getCurrentId());
				$category->setOfferType($offer_type);
				$meta_tags .= $category->getMetaAlternateHreflangTags();
				$meta_tags .= $category->getCanonicalTag();
				$meta_tags .= $category->getMetaDescriptionTag();
				$meta_tags .= $category->getTitleTag();
			}
		}
		// Industry sector plugin
		else if(rex_plugin::get("d2u_machinery", "industry_sectors")->isAvailable() && (filter_input(INPUT_GET, 'industry_sector_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || $url_namespace === "industry_sector_id")) {
			$industry_sector_id = filter_input(INPUT_GET, 'industry_sector_id', FILTER_VALIDATE_INT);
			if(rex_addon::get("url")->isAvailable() && $url_id > 0) {
				$industry_sector_id = $url_id;
			}

			if($industry_sector_id > 0) {
				$industry_sector = new IndustrySector($industry_sector_id, rex_clang::getCurrentId());
				$meta_tags .= $industry_sector->getMetaAlternateHreflangTags();
				$meta_tags .= $industry_sector->getCanonicalTag();
				$meta_tags .= $industry_sector->getMetaDescriptionTag();
				$meta_tags .= $industry_sector->getTitleTag();
			}
		}
		// Used Machine
		else if((filter_input(INPUT_GET, 'used_rent_machine_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || $url_namespace === "used_rent_machine_id")
				|| (filter_input(INPUT_GET, 'used_sale_machine_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || $url_namespace === "used_sale_machine_id")) {
			$used_machine_id = filter_input(INPUT_GET, 'used_sale_machine_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 ? filter_input(INPUT_GET, 'used_sale_machine_id', FILTER_VALIDATE_INT) : filter_input(INPUT_GET, 'used_rent_machine_id', FILTER_VALIDATE_INT);
			if(rex_addon::get("url")->isAvailable() && $url_id > 0) {
				$used_machine_id = $url_id;
			}

			if($used_machine_id > 0) { 
				$used_machine = new UsedMachine($used_machine_id, rex_clang::getCurrentId());
				$meta_tags .= $used_machine->getMetaAlternateHreflangTags();
				$meta_tags .= $used_machine->getCanonicalTag();
				$meta_tags .= $used_machine->getMetaDescriptionTag();
				$meta_tags .= $used_machine->getTitleTag();
			}
		}

		return $meta_tags;
	}
}