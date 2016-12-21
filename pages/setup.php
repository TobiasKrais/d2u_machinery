<?php
/*
 * Modules
 */
$d2u_module_manager = new D2UModuleManager(D2UMachineryModules::getD2UMachineryModules(), "", "d2u_machinery");

// D2UModuleManager actions
$d2u_module_id = rex_request('d2u_module_id', 'string');
$paired_module = rex_request('pair_'. $d2u_module_id, 'int');
$function = rex_request('function', 'string');
if($d2u_module_id != "") {
	$d2u_module_manager->doActions($d2u_module_id, $function, $paired_module);
}

// D2UModuleManager show list
$d2u_module_manager->showManagerList();

/*
 * Templates
 */
?>
<h2>Einbindung ins Template</h2>
<p>Beispielseite für das Gebrauchtmaschinen Plugin: <a href="http://www.meier-krantechnik.de" target="_blank">
		Meier Krantechnik</a>.</p>
<p>Folgender Code beinhaltet alle für ein Template relevaten Bereiche:</p>
<div style="background-color: #f9f2f4; border: solid 1px #dfe3e9; padding: 15px; height: 30em; overflow-y: scroll;">
	<?php
		$this->module_addon = rex_addon::get("d2u_machinery");
		highlight_file($this->module_addon->getPath("templates/template-default.php"));
	?>
</div>
<h2>Notwendiger CSS Teil</h2>
<p>Folgender Code enthält den für das Maschinen Addon relevanten CSS Code:</p>
<div style="background-color: #f9f2f4; border: solid 1px #dfe3e9; padding: 15px; height: 30em; overflow-y: scroll;">
	<?php
		highlight_file($this->module_addon->getPath("templates/template-default.css"));
	?>
</div>
<h2>Support</h2>
<p>Fehlermeldungen bitte im <a href="https://github.com/TobiasKrais/d2u_machinery" target="_blank">GitHub Repository</a> melden.</p>