<h2>Support</h2>
<p>Fehlermeldungen bitte im <a href="https://github.com/TobiasKrais/d2u_machinery" target="_blank">GitHub Repository</a> melden.</p>
<fieldset style='padding: 1em; border: 1px solid #dfe3e9;'>
	<p style="margin-bottom: 0.5em;">Sag einfach Danke und unterstütze die Weiterentwicklung durch deine Spende:</p>
	<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
		<input type="hidden" name="cmd" value="_s-xclick" />
		<input type="hidden" name="hosted_button_id" value="CB7B6QTLM76N6" />
		<input type="image" src="https://www.paypalobjects.com/de_DE/DE/i/btn/btn_donateCC_LG.gif" name="submit" title="PayPal - The safer, easier way to pay online!" alt="Spenden mit dem PayPal-Button" />
		<img alt="" src="https://www.paypal.com/de_DE/i/scr/pixel.gif" width="1" height="1" />
	</form>
</fieldset>

<h2>Changelog</h2>
<p>1.5.0:</p>
<ul>
	<li>Export Plugin: kann Gebrauchtmaschinen nun wieder auf Beiträgen von Personen und Unternehmen posten und diese beim Löschen des Angebots entfernen (Linkedin Rest API Version 202401).</li>
	<li>Bugfix: wenn ein Artikellink entfernt wurde, gab es beim Speichern einen Fehler.</li>
	<li>Anpassungen an D2U Helper Version >= 1.14.0, auch die kommende Version 2.0.</li>
	<li>Container Kapazität ist nun die Einheit (kg, l oder m³) einstellbar.</li>
	<li>Möglichkeit Referenzen aus dem D2U Referenzen Addon mit Kategorien, Maschinen und Produktionslinien zu verknüpfen hinzugefügt.</li>
	<li>Modul "90-1 D2U Machinery Addon - Hauptausgabe": An D2U Videomanager Addon >= 1.2 angepasst.</li>
	<li>Modul "90-4 D2U Machinery Addon - Gebrauchtmaschinen": An D2U Videomanager Addon >= 1.2 angepasst.</li>
</ul>
<p>1.4.3:</p>
<ul>
	<li>Nutzt das neue Bilderliste Feld mit Vorschaufunktion der Bilder.</li>
	<li>README mit Addon und Pluginbeschreibung, sowie Installationsanweisungen hinzugefügt.</li>
	<li>Hilfe, Changelog, Einstellungen und Setup jetzt als Unterpunkte im Menüpunkt Einstellungen verfügbar.</li>
	<li>Modul "90-4 D2U Machinery Addon - Gebrauchtmaschinen": fehlende ")" hinzugefügt.</li>
</ul>
<p>1.4.2:</p>
<ul>
	<li>Modul "90-1 D2U Machinery Addon - Hauptausgabe": CSS für Nav-Pills für Modul isoliert, so dass sie das CSS für andere Addons nicht beeinflusst.</li>
	<li>Modul "90-1 D2U Machinery Addon - Hauptausgabe": Fehler im Spamschutz / CSRF Schutz behoben.</li>
	<li>Modul "90-4 D2U Machinery Addon - Gebrauchtmaschinen": Fehler im Spamschutz / CSRF Schutz behoben.</li>
</ul>
<p>1.4.1:</p>
<ul>
	<li>PHP-CS-Fixer Code Verbesserungen.</li>
	<li>Weitere rexstan Verbesserungen.</li>
	<li>Modul "90-1 D2U Machinery Addon - Hauptausgabe": Anfrageformular mit Formularnamen versehen um bessere YForm Spamprotection Kompatibilität bei mehreren Formularen auf einer Seite herzustellen.</li>
	<li>Modul "90-4 D2U Machinery Addon - Gebrauchtmaschinen": Anfrageformular mit Formularnamen versehen um bessere YForm Spamprotection Kompatibilität bei mehreren Formularen auf einer Seite herzustellen.</li>
</ul>
<p>1.4.0:</p>
<ul>
	<li>.github Verzeichnis aus Installer Action ausgeschlossen.</li>
	<li>Über 2.500 rexstan Hinweise korrigiert.</li>
	<li>install.php und update.php auf Redaxo Art umgestellt und vereinfacht.</li>
	<li>Wenn D2U Videomanager installiert ist, werden Videos von Maschinen und Kategorien in der Sitemap hinzugefügt.</li>
	<li>Bugfix used_machines Plugin: Wenn in den Einstellungen der gleiche Artikel für Maschinen, Gebrauchtmaschinen (Verkauf oder Miete) eingestellt wird, erfolgt ein Warnhinweis.</li>
	<li>Bugfix machine_construction_equipment_extension Plugin: Einheit für Arbeitsdruck von Bodenschleifmaschinen auf kg/cm² korrigiert.</li>
	<li>Bugfix production_lines Plugin: Breadcrumbs werden nun korrekt ausgegeben.</li>
	<li>Modul "90-1 D2U Machinery Addon - Hauptausgabe": Unterstützt bei installiertem D2U Videomanager nun die Ausgabe der Videos mit Plyr.</li>
	<li>Modul "90-3 D2U Machinery Addon - Kategorien" kann nun auch Gebrauchtmaschinenkategorien ausgeben und lässt die Wahl zwischen 3 oder 4 Blöcken je Zeile. Auch Bugfix bei der Ausgabe der Kategorien.</li>
	<li>Modul "90-4 D2U Machinery Addon - Gebrauchtmaschinen": Leere Standardkachel entfernt und Paginierung auf 100 Maschinen erweitert. Unterstützt bei installiertem D2U Videomanager nun die Ausgabe der Videos mit Plyr.</li>
	<li>Modul "90-6 D2U Machinery Addon - Gebrauchtmaschinen Topangebote" hinzugefügt.</li>
</ul>
<p>1.3.6:</p>
<ul>
	<li>Anpassungen an Publish Github Release to Redaxo.</li>
	<li>Plugin contacts hinzugefügt: Maschinen und Gebrauchtmaschinen können Kontakte zugewiesen werden.</li>
	<li>Unterstützt nur noch URL Addon >= 2.0.</li>
	<li>Bugfix machine_features_extension Plugin: wurden keine Kategorien ausgewählt, konnte nicht gespeichert werden.</li>
	<li>Bugfix: Beim Löschen von Artikeln und Medien die vom Addon verlinkt werden wurde der Name der verlinkenden Quelle in der Warnmeldung nicht immer korrekt angegeben.</li>
	<li>Bugfix: Beim Speichern von Anwendungsgebieten konnte ein Fatal Error auftreten.</li>
	<li>Modul "90-1 D2U Machinery Addon - Hauptausgabe": Zugewiesener Kontakt aus contacts Plugin erhält Maschinenanfragen.</li>
	<li>Modul "90-3 D2U Machinery Addon - Kategorien" Editor Feld auf D2U Helper Lieblingseditor umgestellt.</li>
	<li>Modul "90-4 D2U Machinery Addon - Gebrauchtmaschinen": Zugewiesener Kontakt aus contacts Plugin erhält Maschinenanfragen.</li>
</ul>
<p>1.3.5:</p>
<ul>
	<li>Methode d2u_machinery_frontend_helper::getMetaTags() entfernt, da das URL Addon eine bessere Funktion anbietet.
		Ebenso die Methoden getMetaAlternateHreflangTags(), getMetaDescriptionTag(), getCanonicalTag und getTitleTag() der Klassen, die diese Methoden angeboten hatten.</li>
	<li>Maschinen: Warnung beim Erstellen der Meta Tags entfernt.</li>
	<li>Maschinen: Felder "Vorteile auf einen Blick" (kurz und lang) und Leaflet (Prospekt) hinzugefügt.</li>
	<li>Kategorien: Feld Beschreibung hinzugefügt.</li>
	<li>In den Einstellungen kann nun dem Branchen Addon ein eigener Artikel zugewiesen werden und es können mehrere Beratungsbilder eingestellt werden.</li>
	<li>Italienische, polnische und Tschechische Sprog Übersetzungen entfernt.</li>
	<li>Sprog Übersetzungen der Formulare in den Modulen ins D2U Helper Addon umgezogen.</li>
	<li>industry_sectors_plugin: Feld Beschreibung und Piktogramm hinzugefügt.</li>
	<li>machine_options_extension Plugin hinzugefügt: Dieses Plugin gleicht dem machine_features_extension Plugin. Hier werden zubaubare Optionen für Maschinen verwaltet.</li>
	<li>machine_steel_processing_extension Plugin: Priorität der Zu-/Abfuhrseite hinzugefügt.</li>
	<li>machine_steel_processing_extension Plugin: Anzeigefehler für Vor-/Rücklauf bei nicht vorhandener Eingabe behoben.</li>
	<li>machine_features_extension Plugin: Sortierung der Features nun korrekt nach Priorität.</li>
	<li>used_machines Plugin: PHP Warnung entfernt und Produktnummer in Übersichtsliste übernommen.</li>
	<li>Modul 90-1 "D2U Machinery Addon - Hauptausgabe": Bugfix: Absenden Button des Anfrageformulars war ausgeblendet.</li>
	<li>Modul 90-4 "D2U Machinery Addon - Gebrauchtmaschinen": verbesserte Darstellung der Checkbox im Backend.</li>
</ul>
<p>1.3.4:</p>
<ul>
	<li>machine_steel_processing_extension Plugin: Schnittbereichskonfigurator entfernt.</li>
	<li>used_machines Plugin Fehler beim Speichern behoben.</li>
	<li>machine_features_extension Plugin: Die Kategorien in den Features werden nun ausgeschrieben.</li>
	<li>Untermenü für Smartmenu hinzugefügt.</li>
	<li>Module 90-1 "D2U Machinery Addon - Hauptausgabe" und 90-4 "D2U Machinery Addon - Gebrauchtmaschinen" verwenden das Addon yform_spam_protection falls installiert.</li>
	<li>Module 90-1 "D2U Machinery Addon - Hauptausgabe" und 90-4 "D2U Machinery Addon - Gebrauchtmaschinen" CSS Fehler behoben.</li>
</ul>
<p>1.3.3:</p>
<ul>
	<li>Benötigt Redaxo >= 5.10, da die neue Klasse rex_version verwendet wird.</li>
	<li>Anpassung an aktuelle Git Version des URL Addons (Version vom 22.06.2020).</li>
	<li>Modul 90-1 "D2U Machinery Addon - Hauptausgabe" leitet Offlinemaschinen auf die Fehlerseite weiter.</li>
	<li>Module 90-1 "D2U Machinery Addon - Hauptausgabe" und 90-4 "D2U Machinery Addon - Gebrauchtmaschinen" an YForm 3.4 angepasst.</li>
	<li>Spanische Frontend Übersetzungen aktualisiert.</li>
	<li>machine_steel_processing_extension Plugin: neues Feld Sägeblatt-Vorschub.</li>
	<li>machine_steel_processing_extension Plugin: in der Zu-/Abfuhr wurde das Feld Überschrift entfernt (statt dessen Name verwenden).</li>
	<li>used_machines Plugin: Beim umstellen des Status in der Maschinenliste wird der Benutzer gespeichert.</li>
	<li>Alle Module: wenn Google Analytics in den Einstellungen aktiviert ist wird der Google Code nicht ausgegeben, wenn search_it die Seite zur Indexierung aufruft.</li>
</ul>
<p>1.3.2:</p>
<ul>
	<li>Kontaktformulare aller Module mit zweitem Spamschutz versehen: Honeypot.</li>
	<li>Facebook Export für Gebrauchtmaschinen entfernt.</li>
	<li>Backend: Beim online stellen einer Maschine in der Maschinenliste gab es beim Aufruf im Frontend einen Fatal Error, da der URL cache nicht neu generiert wurde.</li>
	<li>Branchen Plugin Backend: Beim online stellen einer Branche in der Branchenliste gab es beim Aufruf im Frontend einen Fatal Error, da der URL cache nicht neu generiert wurde.</li>
	<li>Gebrauchtmaschinen Plugin Backend: Beim online stellen einer Gebrauchtmaschine in der Gebrauchtmaschinenliste gab es beim Aufruf im Frontend einen Fatal Error, da der URL cache nicht neu generiert wurde.</li>
	<li>Features Plugin: title wurde entfernt und statt dessen der Name verwendet.</li>
	<li>Bugfix: Update über Installer auf Version 1.3.1 war nicht möglich.</li>
	<li>machine_steel_processing_extension Plugin: Feld Bohreinheiten von unten (U-Achse) hinzugefügt.</li>
	<li>machine_construction_equipment_extension Plugin: die jeweils drei Felder für verschiedene Materialsorten der Förderweite, -leistung und -höhe wurden zu je einem Feld zusammen gefasst.</li>
</ul>
<p>1.3.1:</p>
<ul>
	<li>Features Plugin: ab sofort werden Name und Überschrift in der Übersichtsliste angezeigt.</li>
	<li>Backend: Einstellungen und Setup Tabs rechts eingeordnet um sie vom Inhalt besser zu unterscheiden.</li>
	<li>Bugfix: das Löschen eines Bildes im Medienpool wurde unter Umständen mit der Begründung verhindert, dass das Bild in Benutzung sei, obwohl das nicht der Fall war.</li>
	<li>Kontaktformular funktioniert nun auch wenn über Office 365 versendet wird.</li>
	<li>Ein paar Warnungen entfernt.</li>
	<li>URL 2.x Anpassungen können jetzt auch mit urlencoded() URLs umgehen, für den Fall, dass das YRewrite Schema aus dem D2U Helper Addon verwendet wird.</li>
	<li>URL 2.x Anpassung: Extension Point wurde auf dem Weg zur beta-4 umbenannt.</li>
	<li>Gebrauchtmaschinen Plugin: Videos aus D2U Videomanager hinzugefügt.</li>
	<li>Gebrauchtmaschinen Plugin Bugfix: War eine Kategorie nicht übersetzt, aber eine Maschine vorhanden, wurde für die Maschine keine URL erzeugt.</li>
	<li>Category::getUrl() hat jetzt Artikel URL als zweiten, optionalen Parameter. Wenn Maschinen und Gebrauchtmaschinen verwendet wurden, konnte es zu fehlerhaften URLs kommen.</li>
</ul>
<p>1.3.0:</p>
<ul>
	<li>Berechtigungen angepasst: Neu sind separate Berechtigungen für "Maschinen" und "Kategorien". Die Berechtigungen für "Branchen" gehen in Kategorien auf und die für Stahlverarbeitung und anderen Maschinenerweiterungen in "Maschinen".</li>
	<li>Option zum Erstellen verkürzter URLs hinzugefügt, wenn Artikel nicht Startartikel ist (nur ab URL Addon 2.x oder höher).</li>
	<li>URL Addon 2.x Anpassungen.</li>
	<li>SEO Bilder für URL Addon wurden hinzugefügt.</li>
	<li>YRewrite Multidomain support.</li>
	<li>Stahlverarbeitung Plugin Zu-/Abfuhr Übersichtsliste nun auch mit Titel (nicht nur Name).</li>
	<li>Bugfix Stahlverarbeitung Plugin: speicherte Texte mit einfachen Anführungszeichen nicht.</li>
	<li>Bugfix Stahlverarbeitung Plugin: Materialklassen wurden falsch initiert.</li>
	<li>Modul 90-1 und 90-4 an aktuelles YCom angepasst. Medien werden nur noch zum Download angeboten, wenn Rechte existieren.</li>
	<li>Listen im Backend werden jetzt nicht mehr in Seiten unterteilt.</li>
	<li>Zubehör Plugin: Zubehörgruppen jetzt nach Namen sortiert.</li>
	<li>Bugfix: Zubehör Plugin speicherte Bilder nicht.</li>
	<li>Bugfix: Sprachspezifischer Name von Maschinen kann jetzt auch einfache Anführungszeichen enthalten.</li>
	<li>Konvertierung der Datenbanktabellen zu utf8mb4.</li>
	<li>Bugfix: Videos des D2U Videomanagers werden nur noch hinzugefügt, wenn es entweder in der Sprache oder als sprachübergreifendes Video vorhanden ist.</li>
</ul>
<p>1.2.5:</p>
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
	<li>ycom/media_auth Dateirechte werden bei Downloads abgefragt.</li>
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