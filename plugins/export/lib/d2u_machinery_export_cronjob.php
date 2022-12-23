<?php
/**
 * Offers helper functions for export plugin
 */
class d2u_machinery_export_cronjob extends D2U_Helper\ACronJob  {
	/**
	 * Create a new instance of object
	 * @return d2u_machinery_export_cronjob CronJob object
	 */
	public static function factory() {
		$cronjob = new self();
		$cronjob->name = "D2U Machinery Autoexport";
		return $cronjob;
	}
	
	/**
	 * Install CronJob. Its also activated.
	 */
	public function install():void {
		$description = 'Exports used machines automatically to FTP based export providers';
		$php_code = '<?php Provider::autoexport(); ?>';
		$interval = '{\"minutes\":[0],\"hours\":[21],\"days\":\"all\",\"weekdays\":\"all\",\"months\":\"all\"}';
		self::save($description, $php_code, $interval);
	}
}