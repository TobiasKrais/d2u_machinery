<h2>Support</h2>
<p>Fehlermeldungen bitte im <a href="https://github.com/TobiasKrais/d2u_machinery" target="_blank">GitHub Repository</a> melden.</p>

<h2>Changelog</h2>
<p>1.6.0-DEV:</p>
<ul>
	<li>Vorbereitung auf Version 2 / R6: Folgende Klassen werden ab Version 2 dieses Addons umbenannt. Schon jetzt stehen die neuen Klassen fuer die Uebergangszeit zur Verfuegung und die alten Klassennamen werden in <code>lib/deprecated_helper_classes.php</code> auf die neuen Klassen gemappt:
		<ul>
			<li><code>AExport</code> wird zu <code>TobiasKrais\D2UMachinery\AExport</code>.</li>
			<li><code>AFTPExport</code> wird zu <code>TobiasKrais\D2UMachinery\AFTPExport</code>.</li>
			<li><code>Agitator</code> wird zu <code>TobiasKrais\D2UMachinery\Agitator</code>.</li>
			<li><code>AgitatorType</code> wird zu <code>TobiasKrais\D2UMachinery\AgitatorType</code>.</li>
			<li><code>Automation</code> wird zu <code>TobiasKrais\D2UMachinery\Automation</code>.</li>
			<li><code>Category</code> wird zu <code>TobiasKrais\D2UMachinery\Category</code>.</li>
			<li><code>Certificate</code> wird zu <code>TobiasKrais\D2UMachinery\Certificate</code>.</li>
			<li><code>D2U_Machinery\Contact</code> wird zu <code>TobiasKrais\D2UMachinery\Contact</code>.</li>
			<li><code>d2u_machinery_export_cronjob</code> wird zu <code>TobiasKrais\D2UMachinery\ExportCronjob</code>.</li>
			<li><code>Equipment</code> wird zu <code>TobiasKrais\D2UMachinery\Equipment</code>.</li>
			<li><code>EquipmentGroup</code> wird zu <code>TobiasKrais\D2UMachinery\EquipmentGroup</code>.</li>
			<li><code>EuropeMachinery</code> wird zu <code>TobiasKrais\D2UMachinery\EuropeMachinery</code>.</li>
			<li><code>ExportedUsedMachine</code> wird zu <code>TobiasKrais\D2UMachinery\ExportedUsedMachine</code>.</li>
			<li><code>Feature</code> wird zu <code>TobiasKrais\D2UMachinery\Feature</code>.</li>
			<li><code>d2u_machinery_frontend_helper</code> wird zu <code>TobiasKrais\D2UMachinery\FrontendHelper</code>.</li>
			<li><code>IndustrySector</code> wird zu <code>TobiasKrais\D2UMachinery\IndustrySector</code>.</li>
			<li><code>d2u_machinery_lang_helper</code> wird zu <code>TobiasKrais\D2UMachinery\LangHelper</code>.</li>
			<li><code>Machine</code> wird zu <code>TobiasKrais\D2UMachinery\Machine</code>.</li>
			<li><code>MachineryPark</code> wird zu <code>TobiasKrais\D2UMachinery\MachineryPark</code>.</li>
			<li><code>Mascus</code> wird zu <code>TobiasKrais\D2UMachinery\Mascus</code>.</li>
			<li><code>Material</code> wird zu <code>TobiasKrais\D2UMachinery\Material</code>.</li>
			<li><code>Module</code> wird zu <code>TobiasKrais\D2UMachinery\Module</code>.</li>
			<li><code>Option</code> wird zu <code>TobiasKrais\D2UMachinery\Option</code>.</li>
			<li><code>Procedure</code> wird zu <code>TobiasKrais\D2UMachinery\Procedure</code>.</li>
			<li><code>Process</code> wird zu <code>TobiasKrais\D2UMachinery\Process</code>.</li>
			<li><code>ProductionLine</code> wird zu <code>TobiasKrais\D2UMachinery\ProductionLine</code>.</li>
			<li><code>Profile</code> wird zu <code>TobiasKrais\D2UMachinery\Profile</code>.</li>
			<li><code>Provider</code> wird zu <code>TobiasKrais\D2UMachinery\Provider</code>.</li>
			<li><code>ServiceOption</code> wird zu <code>TobiasKrais\D2UMachinery\ServiceOption</code>.</li>
			<li><code>SocialExportLinkedIn</code> wird zu <code>TobiasKrais\D2UMachinery\SocialExportLinkedIn</code>.</li>
			<li><code>Supply</code> wird zu <code>TobiasKrais\D2UMachinery\Supply</code>.</li>
			<li><code>Tool</code> wird zu <code>TobiasKrais\D2UMachinery\Tool</code>.</li>
			<li><code>USP</code> wird zu <code>TobiasKrais\D2UMachinery\USP</code>.</li>
			<li><code>UsageArea</code> wird zu <code>TobiasKrais\D2UMachinery\UsageArea</code>.</li>
			<li><code>UsedMachine</code> wird zu <code>TobiasKrais\D2UMachinery\UsedMachine</code>.</li>
			<li><code>Welding</code> wird zu <code>TobiasKrais\D2UMachinery\Welding</code>.</li>
		</ul>
	</li>
	<li>Die Dateinamen der Klassen wurden passend zu den Klassennamen auf CamelCase umgestellt, z.B. <code>machine.php</code> zu <code>Machine.php</code>, <code>category.php</code> zu <code>Category.php</code>, <code>d2u_machinery_lang_helper.php</code> zu <code>LangHelper.php</code> und <code>d2u_machinery_export_cronjob.php</code> zu <code>ExportCronjob.php</code>.</li>
	<li>Backend-Struktur fuer REDAXO 6 von einzelinstallierten Plugins auf zentral schaltbare Erweiterungen im Addon umgestellt.</li>
	<li>Die bisherigen Plugins wurden technisch vollstaendig in das Hauptaddon integriert; eigene Plugin-Verzeichnisse, Plugin-Bootstraps sowie getrennte Installations- und Update-Routinen entfallen.</li>
	<li>Erweiterungen werden jetzt zentral in den Addon-Einstellungen aktiviert. Die Schalter befinden sich auf der Einstellungsseite des Hauptaddons und blenden die zugehoerigen Funktionen und Backend-Bereiche je nach Aktivierung ein.</li>
	<li>Maschinen-Unterseiten im Backend auf kuerzere und konsistente Page-IDs wie machine/options, machine/features und machine/steel_processing umbenannt.</li>
	<li>Alte Wrapper-Dateien in pages/ entfernt; die aktiven Seiten enthalten ihre Logik nun direkt selbst.</li>
	<li>Einstellungsseite der Erweiterungen ueberarbeitet: Untereinstellungen stehen direkt unter den Schaltern und Abhaengigkeiten werden direkt beruecksichtigt.</li>
	<li>Neue Module 90-7 bis 90-12 als Bootstrap-5-Varianten der bestehenden Beispielmodule hinzugefuegt.</li>
	<li>Module 90-1 bis 90-6 als "(BS4, deprecated)" markiert. Die BS4-Varianten werden im naechsten Major Release entfernt.</li>
	<li>BS5-Module auf Bootstrap-5-Carousels und die d2u_helper Lightbox umgestellt.</li>
	<li>Benoetigt d2u_helper &gt;= 2.1.0.</li>
</ul>
<p>1.5.1-DEV:</p>
<ul>
	<li>Export Plugin: Linkedin API auf 202511 erhoeht.</li>
	<li>Export Plugin: PHP Warnung im CSV Export verhindert.</li>
	<li>Module "90-1 D2U Machinery Addon - Hauptausgabe" und "90-4 D2U Machinery Addon - Gebrauchtmaschinen": Direkte Plyr-Sonderbehandlung entfernt. Die Videoausgabe laeuft jetzt vollstaendig ueber das D2U Videos Addon.</li>
</ul>
<p>1.5.0:</p>
<ul>
	<li>Export Plugin: kann Gebrauchtmaschinen nun wieder auf Beitraegen von Personen und Unternehmen posten und diese beim Loeschen des Angebots entfernen (Linkedin Rest API Version 202401).</li>
	<li>Bugfix: wenn ein Artikellink entfernt wurde, gab es beim Speichern einen Fehler.</li>
	<li>Anpassungen an D2U Helper Version &gt;= 1.14.0, auch die kommende Version 2.0.</li>
	<li>Container Kapazitaet ist nun die Einheit (kg, l oder m3) einstellbar.</li>
	<li>Moeglichkeit Referenzen aus dem D2U Referenzen Addon mit Kategorien, Maschinen und Produktionslinien zu verknuepfen hinzugefuegt.</li>
	<li>Modul "90-1 D2U Machinery Addon - Hauptausgabe": An D2U Videomanager Addon &gt;= 1.2 angepasst.</li>
	<li>Modul "90-4 D2U Machinery Addon - Gebrauchtmaschinen": An D2U Videomanager Addon &gt;= 1.2 angepasst.</li>
</ul>
<p>1.4.3:</p>
<ul>
	<li>Nutzt das neue Bilderliste Feld mit Vorschaufunktion der Bilder.</li>
	<li>README mit Addon und Pluginbeschreibung, sowie Installationsanweisungen hinzugefuegt.</li>
	<li>Hilfe, Changelog, Einstellungen und Setup jetzt als Unterpunkte im Menuepunkt Einstellungen verfuegbar.</li>
	<li>Modul "90-4 D2U Machinery Addon - Gebrauchtmaschinen": fehlende ")" hinzugefuegt.</li>
</ul>
<p>1.4.2:</p>
<ul>
	<li>Modul "90-1 D2U Machinery Addon - Hauptausgabe": CSS fuer Nav-Pills fuer Modul isoliert, so dass sie das CSS fuer andere Addons nicht beeinflusst.</li>
	<li>Modul "90-1 D2U Machinery Addon - Hauptausgabe": Fehler im Spamschutz / CSRF Schutz behoben.</li>
	<li>Modul "90-4 D2U Machinery Addon - Gebrauchtmaschinen": Fehler im Spamschutz / CSRF Schutz behoben.</li>
</ul>
<p>1.4.1:</p>
<ul>
	<li>PHP-CS-Fixer Code Verbesserungen.</li>
	<li>Weitere rexstan Verbesserungen.</li>
	<li>Modul "90-1 D2U Machinery Addon - Hauptausgabe": Anfrageformular mit Formularnamen versehen um bessere YForm Spamprotection Kompatibilitaet bei mehreren Formularen auf einer Seite herzustellen.</li>
	<li>Modul "90-4 D2U Machinery Addon - Gebrauchtmaschinen": Anfrageformular mit Formularnamen versehen um bessere YForm Spamprotection Kompatibilitaet bei mehreren Formularen auf einer Seite herzustellen.</li>
</ul>
<p>1.4.0:</p>
<ul>
	<li>.github Verzeichnis aus Installer Action ausgeschlossen.</li>
	<li>Ueber 2.500 rexstan Hinweise korrigiert.</li>
	<li>install.php und update.php auf Redaxo Art umgestellt und vereinfacht.</li>
	<li>Wenn D2U Videomanager installiert ist, werden Videos von Maschinen und Kategorien in der Sitemap hinzugefuegt.</li>
	<li>Bugfix used_machines Plugin: Wenn in den Einstellungen der gleiche Artikel fuer Maschinen, Gebrauchtmaschinen (Verkauf oder Miete) eingestellt wird, erfolgt ein Warnhinweis.</li>
	<li>Bugfix machine_construction_equipment_extension Plugin: Einheit fuer Arbeitsdruck von Bodenschleifmaschinen auf kg/cm2 korrigiert.</li>
	<li>Bugfix production_lines Plugin: Breadcrumbs werden nun korrekt ausgegeben.</li>
	<li>Modul "90-1 D2U Machinery Addon - Hauptausgabe": Unterstuetzt bei installiertem D2U Videomanager nun die Ausgabe der Videos mit Plyr.</li>
	<li>Modul "90-3 D2U Machinery Addon - Kategorien" kann nun auch Gebrauchtmaschinenkategorien ausgeben und laesst die Wahl zwischen 3 oder 4 Bloecken je Zeile. Auch Bugfix bei der Ausgabe der Kategorien.</li>
	<li>Modul "90-4 D2U Machinery Addon - Gebrauchtmaschinen": Leere Standardkachel entfernt und Paginierung auf 100 Maschinen erweitert. Unterstuetzt bei installiertem D2U Videomanager nun die Ausgabe der Videos mit Plyr.</li>
	<li>Modul "90-6 D2U Machinery Addon - Gebrauchtmaschinen Topangebote" hinzugefuegt.</li>
</ul>