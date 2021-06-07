<?php
/**
 * Class managing modules published by www.design-to-use.de
 *
 * @author Tobias Krais
 */
class D2UMachineryModules {
	/**
	 * Get modules offered by this addon.
	 * @return D2UModule[] Modules offered by this addon
	 */
	public static function getModules() {
		$modules = [];
		$modules[] = new D2UModule("90-1",
			"D2U Machinery Addon - Hauptausgabe",
			14);
		if(rex_plugin::get('d2u_machinery', 'industry_sectors')->isAvailable()) {
			$modules[] = new D2UModule("90-2",
				"D2U Machinery Addon - Branchen",
				1);
		}
		$modules[] = new D2UModule("90-3",
			"D2U Machinery Addon - Kategorien",
			4);
		if(rex_plugin::get('d2u_machinery', 'used_machines')->isAvailable()) {
			$modules[] = new D2UModule("90-4",
				"D2U Machinery Addon - Gebrauchtmaschinen",
				15);
		}
		$modules[] = new D2UModule("90-5",
			"D2U Machinery Addon - Box Beratungshinweis",
			1);
		return $modules;
	}
}