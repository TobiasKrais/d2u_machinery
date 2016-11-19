<?php
/**
 * Offers helper functions for export plugin
 */
class export_backend_helper  {
	/**
	 * Deactivate autoexport.
	 */
	public static function autoexportDelete() {
		if(rex_addon::get('cronjob')->isAvailable()) {
			$query = "DELETE FROM `". rex::getTablePrefix() ."cronjob` WHERE `name` = 'D2U Machinery Autoexport'";
			$sql = rex_sql::factory();
			$sql->setQuery($query);
		}
	}

	/**
	 * Activate autoexport.
	 */
	public static function autoexportInstall() {
		if(rex_addon::get('cronjob')->isAvailable()) {
			$query = "INSERT INTO `". rex::getTablePrefix() ."cronjob` (`name`, `description`, `type`, `parameters`, `interval`, `nexttime`, `environment`, `execution_moment`, `execution_start`, `status`, `createdate`, `createuser`) VALUES "
				."('D2U Machinery Autoexport', 'Exports used machines automatically to FTP based export providers', 'rex_cronjob_phpcode', '{\"rex_cronjob_phpcode_code\":\"<?php\\r\\nProvider::autoexport();\\r\\n?>\"}', '{\"minutes\":[0],\"hours\":[21],\"days\":\"all\",\"weekdays\":\"all\",\"months\":\"all\"}', '". date("Y-m-d H:i:s") ."', '|frontend|', 0, '1970-01-01 01:00:00', 1, '". date("Y-m-d H:i:s") ."', 'd2u_machinery');";
			$sql = rex_sql::factory();
			$sql->setQuery($query);
		}
	}

	/**
	 * Checks if autoexport is installed.
	 * @return boolean TRUE if Cronjob is installed, otherwise false.
	 */
	public static function autoexportIsInstalled() {
		if(rex_addon::get('cronjob')->isAvailable()) {
			$query = "SELECT `name` FROM `". rex::getTablePrefix() ."cronjob` WHERE `name` = 'D2U Machinery Autoexport'";
			$sql = rex_sql::factory();
			$sql->setQuery($query);
			if($sql->getRows() > 0) {
				return TRUE;
			}
			else {
				return FALSE;
			}
		}
	}
}