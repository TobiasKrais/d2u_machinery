<?php
/**
 * Class managing modules published by www.design-to-use.de
 *
 * @author Tobias Krais
 */
class D2UMachineryModules {
	/**
	 * Get modules offered by D2U Helper addon.
	 * @return D2UModule[] Modules offered by D2U Helper addon
	 */
	public static function getD2UMachineryModules() {
		$d2u_machinery_modules = [];
		$d2u_machinery_modules[] = new D2UModule("90-1",
			"modules/machines_in.php",
			"modules/machines_out.php",
			"D2U Machinery Addon - Hauptausgabe",
			1);
		$d2u_machinery_modules[] = new D2UModule("90-2",
			"modules/industry_sectors_in.php",
			"modules/industry_sectors_out.php",
			"D2U Machinery Addon - Branchen",
			1);
		$d2u_machinery_modules[] = new D2UModule("90-3",
			"modules/categories_in.php",
			"modules/categories_out.php",
			"D2U Machinery Addon - Kategorien",
			2);
		if(rex_plugin::get('d2u_machinery', 'used_machines')->isAvailable()) {
			$d2u_machinery_modules[] = new D2UModule("90-3",
				"plugins/used_machines/modules/used_machines_in.php",
				"plugins/used_machines/modules/used_machines_out.php",
				"D2U Machinery Addon - Gebrauchtmaschinen",
				1);
		}
		return $d2u_machinery_modules;
	}
}