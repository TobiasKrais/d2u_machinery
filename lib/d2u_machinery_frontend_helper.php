<?php
/**
 * Offers helper functions for frontend
 */
class d2u_machinery_frontend_helper {
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
		$urlParamKey = "";
		if(rex_addon::get("url")->isAvailable()) {
			$url_data = UrlGenerator::getData();
			$urlParamKey = isset($url_data->urlParamKey) ? $url_data->urlParamKey : "";
		}		
		
		if(filter_input(INPUT_GET, 'machine_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || (rex_addon::get("url")->isAvailable() && $urlParamKey === "machine_id")) {
			$machine_id = filter_input(INPUT_GET, 'machine_id', FILTER_VALIDATE_INT);
			if(rex_addon::get("url")->isAvailable() && UrlGenerator::getId() > 0) {
				$machine_id = UrlGenerator::getId();
			}

			if($machine_id > 0) {
				$machine = new Machine($machine_id, rex_clang::getCurrentId());
				$category = $machine->category;
			}
		}
		if(filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || (rex_addon::get("url")->isAvailable() && $urlParamKey === "category_id")
				|| filter_input(INPUT_GET, 'used_rent_category_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || (rex_addon::get("url")->isAvailable() && $urlParamKey === "used_rent_category_id")
				|| filter_input(INPUT_GET, 'used_sale_category_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || (rex_addon::get("url")->isAvailable() && $urlParamKey === "used_sale_category_id")) {
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
			if(rex_addon::get("url")->isAvailable() && UrlGenerator::getId() > 0) {
				$category_id = UrlGenerator::getId();
			}
			
			if($category_id > 0) {
				$category = new Category($category_id, rex_clang::getCurrentId());
			}
		}
		if(filter_input(INPUT_GET, 'used_rent_machine_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || filter_input(INPUT_GET, 'used_sale_machine_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || (rex_addon::get("url")->isAvailable() && isset($url_data->urlParamKey) && ($url_data->urlParamKey === "used_rent_machine_id" || $url_data->urlParamKey === "used_sale_machine_id"))) {
			$used_machine_id = filter_input(INPUT_GET, 'used_rent_machine_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 ? filter_input(INPUT_GET, 'used_rent_machine_id', FILTER_VALIDATE_INT) : filter_input(INPUT_GET, 'used_sale_machine_id', FILTER_VALIDATE_INT);
			if(rex_addon::get("url")->isAvailable() && UrlGenerator::getId() > 0) {
				$used_machine_id = UrlGenerator::getId();
			}
			
			if($used_machine_id > 0) {
				$used_machine = new UsedMachine($used_machine_id, rex_clang::getCurrentId());
				$category = $used_machine->category;
				$category->setOfferType($used_machine->offer_type);
			}
		}

		// Breadcrumbs
		if($category !== FALSE) {
			if($category->parent_category !== FALSE) {
				$breadcrumbs[] = '<a href="' . $category->parent_category->getUrl() . '">' . $category->parent_category->name . '</a>';
			}
			$breadcrumbs[] = '<a href="' . $category->getUrl() . '">' . $category->name . '</a>';
			// Cutting range configurator
			if(rex_plugin::get("d2u_machinery", "machine_steel_processing_extension")->isAvailable() && filter_input(INPUT_GET, 'cutting_range_configurator') != "") {
				$breadcrumbs[] = '<a href="' . $category->getURLCuttingRangeConfigurator() . '">' . $category->steel_processing_saw_cutting_range_title . '</a>';
			}
		}
		if($machine !== FALSE) {
			$breadcrumbs[] = '<a href="' . $machine->getUrl() . '">' . ($machine->lang_name == "" ? $machine->name : $machine->lang_name) . '</a>';
		}
		if($used_machine !== FALSE) {
			$breadcrumbs[] = '<a href="' . $used_machine->getUrl() . '">' . $used_machine->manufacturer ." ". $used_machine->name . '</a>';
		}
		
		// Industry sectors
		if(rex_plugin::get("d2u_machinery", "industry_sectors")->isAvailable() && (filter_input(INPUT_GET, 'industry_sector_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || (rex_addon::get("url")->isAvailable() && isset($url_data->urlParamKey) && $url_data->urlParamKey === "industry_sector_id"))) {
			$industry_sector_id = filter_input(INPUT_GET, 'industry_sector_id', FILTER_VALIDATE_INT);
			if(rex_addon::get("url")->isAvailable() && UrlGenerator::getId() > 0) {
				$industry_sector_id = UrlGenerator::getId();
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
			$urlParamKey = "";
			if(rex_addon::get("url")->isAvailable()) {
				$url_data = UrlGenerator::getData();
				$urlParamKey = isset($url_data->urlParamKey) ? $url_data->urlParamKey : "";
			}		
			if(filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || (rex_addon::get("url")->isAvailable() && $urlParamKey === "category_id")) {
				// Category for normal machines
				$current_category_id = filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT);
				if(rex_addon::get("url")->isAvailable() && UrlGenerator::getId() > 0) {
					$current_category_id = UrlGenerator::getId();
				}
			}
			if(filter_input(INPUT_GET, 'machine_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || (rex_addon::get("url")->isAvailable() && $urlParamKey === "machine_id")) {
				$current_machine_id = filter_input(INPUT_GET, 'machine_id', FILTER_VALIDATE_INT);
				if(rex_addon::get("url")->isAvailable() && UrlGenerator::getId() > 0) {
					$current_machine_id = UrlGenerator::getId();
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
}