<?php
/*
 * Modules
 */
$d2u_module_manager = new D2UModuleManager(D2UMachineryModules::getModules(), "modules/", "d2u_machinery");

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
<h2>Support</h2>
<p>Fehlermeldungen bitte im <a href="https://github.com/TobiasKrais/d2u_machinery" target="_blank">GitHub Repository</a> melden.</p>
<h2>Changelog</h2>
<p>1.2.5-DEV:</p>
<ul>
	<li>YForm 3 Anpassungen an Module.</li>
	<li>Sprachdetails werden ausgeblendet, wenn Speicherung der Sprache nicht vorgesehen ist.</li>
	<li>Bugfix: Prioritäten wurden beim Löschen nicht reorganisiert.</li>
	<li>Bugfix: Gebrauchtmaschinen Addon: Löschen Button innerhalb der Maschine hat nicht funktioniert.</li>
	<li>Modul 90-4 "D2U Machinery Addon - Gebrauchtmaschinen" um Optionen zur Personalisierung ergänzt. U.a. können Bilder jetzt in eigenem Tab dargestellt werden.</li>
</ul>
<p>1.2.4:</p>
<ul>
	<li>Bugfix: Deaktiviertes Addon zu deinstallieren führte zu fatal error.</li>
	<li>In den Einstellungen gibt es jetzt eine Option, eigene Übersetzungen in SProg dauerhaft zu erhalten.</li>
	<li>Bugfix: CronJob wird - wenn installiert - nicht immer richtig aktiviert.</li>
	<li>Deinstallation hatte VIEWs und Übersetzungen in Datenbank hinterlassen. Das ist jetzt behoben.</li>
	<li>Usability in Formularen verbessert.</li>
</ul>
<p>1.2.3:</p>
<ul>
	<li>Bugfix Modul 90-4 Gebrauchtmaschinen: Verfügbarkeit wird nun korrekt angezeigt.</li>
	<li>Methode zum Erstellen von Meta Tags d2u_machinery_frontend_helper::getAlternateURLs() hinzugefügt.</li>
	<li>Methode zum Erstellen von Meta Tags d2u_machinery_frontend_helper::getMetaTags() hinzugefügt.</li>
	<li>Spamschutz: Kontaktformulare in Modulen mit 10 Sekunden Timer als Spamschutz versehen.</li>
	<li>Bugfix: Bilder im Lieferumfang konnten gelöscht werden.</li>
	<li>Neues Modul: Box mit Beratungshinweis jetzt separat in Modul erhältlich.</li>
</ul>
<p>1.2.2:</p>
<ul>
	<li>Modul Hauptausgabe: Zurück zur Maschinenliste eingefügt.</li>
	<li>Modul Hauptausgabe: Features, Rührer, Service, ... Bilder mit Lightbox versehen, wenn noch nicht vorhanden.</li>
	<li>Modul Hauptausgabe: Anwendungsgebiete optional auch in Maschine anzeigbar.</li>
	<li>Modul Hauptausgabe: Detailverbesserungen und "zurück zur Maschinenliste" hinzugefügt.</li>
</ul>
<p>1.2.1:</p>
<ul>
	<li>Feld Datenschutzerklärung akzeptiert im Frontend Formular hinzugefügt.</li>
	<li>Baustellenausrüstung Plugin: Bild zu Lieferumfang hinzugefügt.</li>
	<li>Einstellungen: Option zum Einblenden von Kategorien und Maschinen und D2U Helper Navigation.</li>
	<li>Module: Slider jetzt mit Fade.</li>
	<li>Nicht mehr benötigte Features können automatisch gelöscht werden.</li>
	<li>Bugfix: Speichern von einfachem Anführungszeichen schlug manchmal fehl.</li>
	<li>Bugfix: Update schlug fehl.</li>
	<li>Bugfix machine_steel_processing_extension: Eingabefelder für Schnittbereichskonfigurator hinzugefügt.</li>
</ul>
<p>1.2.0:</p>
<ul>
	<li>YRewrite Multidomain Anpassungen.</li>
	<li>Vereinheitlicht: Übersetzungen ins D2U Helper Addon umgezogen.</li>
	<li>Bugfix Maschinen technische Daten: Hz-Wert wurde fälschlicherweise statt V-Wert ausgegeben.</li>
	<li>Export Plugin: Portale können offline geschaltet werden.</li>
	<li>Export Plugin: Bei Installation des Autoexportes künftig Ausführung im Frontend und Backend.</li>
	<li>Open Graph für Gebrauchtmaschinen hinzugefügt.</li>
	<li>Facebook Export für Gebrauchtmaschinenexport hinzugefügt.</li>
	<li>Erweiterung für Baustellenausrüstung hinzugefügt (machine_construction_equipment_extension)</li>
	<li>Erweiterung für Serviceoptionen hinzugefügt (service_options)</li>
	<li>Erweiterung für Zubehör hinzugefügt (equipment)</li>
	<li>Upgrade auf Bootstrap 4.</li>
	<li>Internen Namen bei Maschinen entfernt.</li>
	<li>Backendsprache Englisch hinzugefügt.</li>
	<li>ycom/auth_media Dateirechte werden bei Downloads abgefragt.</li>
	<li>Bugfix: Speichern wenn zweite Sprache als Standardsprache gesetzt ist schlug fehl.</li>
	<li>Eine ganze Reihe kleinerer Bugfixes auch in den Plugins.</li>
	<li>Editierrechte für Übersetzer eingeschränkt.</li>
	<li>Vereinheitlichung: Rechte zum Editieren von Sprachübergreifenden Inhalten umbenannt.</li>
</ul>
<p>1.1.3:</p>
<ul>
	<li>Unterstützung für D2U Videos Addon um Videos darstellen zu können.</li>
	<li>Upgrade zu Bootstrap 4 beta.</li>
	<li>Upgrade URL Addon 1.0.0beta5.</li>
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