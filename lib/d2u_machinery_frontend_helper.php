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
		if(filter_input(INPUT_GET, 'machine_id', FILTER_VALIDATE_INT) > 0) {
			$machine = new Machine(filter_input(INPUT_GET, 'machine_id', FILTER_VALIDATE_INT), rex_clang::getCurrentId());
			$category = $machine->category;
		}
		if(filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT) > 0 ) {
			$category = new Category(filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT), rex_clang::getCurrentId());
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
		if(rex_plugin::get("d2u_machinery", "industry_sectors")->isAvailable() && filter_input(INPUT_GET, 'industry_sector_id', FILTER_VALIDATE_INT) > 0) {
			$industry_sector = new IndustrySector(filter_input(INPUT_GET, 'industry_sector_id', FILTER_VALIDATE_INT), rex_clang::getCurrentId());
			$breadcrumbs[] = '<a href="' . $industry_sector->getUrl() . '">' . $industry_sector->name . '</a>';
		}
		
		return $breadcrumbs;
	}
}