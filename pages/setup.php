<?php
/*
 * Modules
 */
$d2u_module_manager = new D2UModuleManager(D2UMachineryModules::getD2UMachineryModules(), "modules/", "d2u_machinery");

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
<h2>Template</h2>
<p>Beispielseiten</p>
<ul>
	<li>Maschinen Addon: <a href="http://www.promitec.de" target="_blank">
		Promitec</a>.</li>
	<li>Gebrauchtmaschinen Plugin: <a href="http://www.meier-krantechnik.de" target="_blank">
		Meier Krantechnik</a>.</li>
</ul>
<p>Im D2U Helper Addon kann das "Big Header Template" installiert werden. In
	diesem Template sind alle nötigen Änderungen integriert.</p>
<h2>Support</h2>
<p>Fehlermeldungen bitte im <a href="https://github.com/TobiasKrais/d2u_machinery" target="_blank">GitHub Repository</a> melden.</p>
<h2>Changelog</h2>
<p>1.1.4:</p>
<ul>
	<li>Upgrade zu Bootstrap 4 beta.</li>
	<li>Upgrade URL Addon 1.0.0beta5.</li>
</ul>
<p>1.1.3:</p>
<ul>
	<li>Unterstützung für D2U Videos Addon um Videos darstellen zu können.</li>
</ul>
<p>1.1.2:</p>
<ul>
	<li>Plugin für Stahlverarbeitung hinzugefügt.</li>
	<li>Feature Plugin um neue Felder erweitert.</li>
	<li>Für Maschinen kann jetzt ein sprachspezifischer Namen eingegeben werden,
		der den allgemeinen Namen überschreibt.</li>
	<li>Anpassungen an D2U Helper Version 1.2</li>
	<li>Eine Reihe kleinerer Bugfixes</li>
</ul>