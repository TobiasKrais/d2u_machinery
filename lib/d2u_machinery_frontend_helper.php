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
		$breadcrumbs = array();

		// Prepare objects first for sorting in correct order
		$category = FALSE;
		$machine = FALSE;
		$url_data = array();
		if(rex_addon::get("url")->isAvailable()) {
			$url_data = UrlGenerator::getData();
		}
		if(filter_input(INPUT_GET, 'machine_id', FILTER_VALIDATE_INT) > 0 || (rex_addon::get("url")->isAvailable() && $url_data->urlParamKey === "machine_id")) {
			$machine_id = filter_input(INPUT_GET, 'machine_id', FILTER_VALIDATE_INT);
			if(rex_addon::get("url")->isAvailable() && UrlGenerator::getId() > 0) {
				$machine_id = UrlGenerator::getId();
			}
			$machine = new Machine($machine_id, rex_clang::getCurrentId());
			$category = $machine->category;
		}
		if(filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT) > 0 || (rex_addon::get("url")->isAvailable() && $url_data->urlParamKey === "category_id")) {
			$category_id = filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT);
			if(rex_addon::get("url")->isAvailable() && UrlGenerator::getId() > 0) {
				$category_id = UrlGenerator::getId();
			}
			$category = new Category($category_id, rex_clang::getCurrentId());
		}

		// Breadcrumbs
		if($category !== FALSE) {
			if($category->parent_category !== FALSE) {
				$breadcrumbs[] = '<a href="' . $category->parent_category->getUrl() . '">' . $category->parent_category->name . '</a>';
			}
			$breadcrumbs[] = '<a href="' . $category->getUrl() . '">' . $category->name . '</a>';
		}
		if($machine !== FALSE) {
			$breadcrumbs[] = '<a href="' . $machine->getUrl() . '">' . $machine->name . '</a>';
		}
		
		// Industry sectors
		if(rex_plugin::get("d2u_machinery", "industry_sectors")->isAvailable() && (filter_input(INPUT_GET, 'industry_sector_id', FILTER_VALIDATE_INT) > 0 || (rex_addon::get("url")->isAvailable() && $url_data->urlParamKey === "industry_sector_id"))) {
			$industry_sector_id = filter_input(INPUT_GET, 'industry_sector_id', FILTER_VALIDATE_INT);
			if(rex_addon::get("url")->isAvailable() && UrlGenerator::getId() > 0) {
				$industry_sector_id = UrlGenerator::getId();
			}
			$industry_sector = new IndustrySector($industry_sector_id, rex_clang::getCurrentId());
			$breadcrumbs[] = '<a href="' . $industry_sector->getUrl() . '">' . $industry_sector->name . '</a>';
		}
		
		return $breadcrumbs;
	}
}