<?php

use TobiasKrais\D2UMachinery\Module;

// update modules
$d2u_module_manager = new \TobiasKrais\D2UHelper\ModuleManager(Module::getModules(), '', 'd2u_machinery');

// \TobiasKrais\D2UHelper\ModuleManager actions
$d2u_module_id = rex_request('d2u_module_id', 'string');
$paired_module = rex_request('pair_'. $d2u_module_id, 'int');
$function = rex_request('function', 'string');
if ('' !== $d2u_module_id) {
    $d2u_module_manager->doActions($d2u_module_id, $function, $paired_module);
}

// \TobiasKrais\D2UHelper\ModuleManager show list
$d2u_module_manager->showManagerList();

/*
 * Templates
 */
?>
<h2>Template</h2>
<p>Beispielseiten</p>
<ul>
	<li>Maschinen und Gebrauchtmaschinen Addon: <a href="https://www.kaltenbach.com/" target="_blank">
		Kaltenbach GmbH + Co. KG</a>.</li>
	<li>Maschinen Addon: <a href="https://www.promitec.de" target="_blank">
		Promitec</a>.</li>
	<li>Maschinen Addon: <a href="https://www.inotec-gmbh.com" target="_blank">
		Inotec GmbH</a>.</li>
	<li>Gebrauchtmaschinen Plugin: <a href="https://meier-krantechnik.de" target="_blank">
		Meier Krantechnik</a>.</li>
</ul>
<p>Im D2U Helper Addon kann das 00-1 "Big Header Template", 02-3 "Header Slider Template with Slogan",
	04-2 "Header Slider Template" oder installiert werden. In diesem Templates
	sind alle nötigen Änderungen integriert.</p>