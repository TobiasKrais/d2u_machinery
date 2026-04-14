# Redaxo 5 Maschinenverwaltung

Mehrsprachig Maschinenverwaltung für Redaxo. Erweiterungen können gerne in Auftrag gegeben werden. Demoseite: <https://test.design-to-use.de/de/addontests/d2u-maschinen/>

## Installation

Nach der Installation in Redaxo sollten folgende Schritte ausgeführt werden:

1. Festlegen der Einstellungen im Addon.
2. Eingabe mindestens einer Kategorie. Diese werden für die Generierung der URLs benötigt.
3. Nun kann mit der Eingabe von Maschinen begonnen werden.
4. Falls die Erweiterung Kontakte aktiviert wird, sollte mindestens ein Kontakt angelegt werden.
5. Wenn gewünscht, kann unter Setup eines der Beispielmodule in Bootstrap 4 oder Bootstrap 5 installiert werden. Die Templates mit der ID 00-1, 04-1, 04-2 und 04-3 im D2U Helper Addon geben die Reiter der Beispielmodule "90-1 D2U Machinery Addon - Hauptausgabe (BS4, deprecated)", "90-4 D2U Machinery Addon - Gebrauchtmaschinen (BS4, deprecated)", "90-7 D2U Machinery Addon - Hauptausgabe (BS5)" und "90-10 D2U Machinery Addon - Gebrauchtmaschinen (BS5)" aus.

## Erweiterungen

Seit Version 1.6 werden die frueheren Plugins zentral als Erweiterungen innerhalb des Addons verwaltet. Die Fachlogik, Backend-Seiten sowie Installations- und Update-Routinen liegen direkt im Hauptaddon. Um die Komplexitaet des Addons so gering wie moeglich zu halten, **sollte eine Erweiterung nur aktiviert werden, wenn sie auch benoetigt wird**.

### Abfrage von Erweiterungen

Bis Version 1.5 wurde in der Regel das jeweilige Plugin direkt abgefragt. Typische Altaufrufe sehen so aus:

```php
if (rex_plugin::get('d2u_machinery', 'used_machines')->isAvailable()) {
    // Funktion der frueheren used_machines-Erweiterung verwenden
}
```

Seit Version 1.6 wird nicht mehr das Plugin, sondern die integrierte Erweiterung des Hauptaddons abgefragt. Dafuer gibt es die zentrale Extension-Verwaltung:

```php
use TobiasKrais\D2UMachinery\Extension;

if (Extension::isActive('used_machines')) {
    // Funktion der integrierten Erweiterung verwenden
}
```

Die Schluessel entsprechen in der Regel den frueheren Plugin-Namen, zum Beispiel `contacts`, `equipment`, `industry_sectors`, `production_lines`, `service_options`, `used_machines` oder `export`.

Fuer Altinstallationen bleiben auch Wrapper fuer ehemalige Plugins ohne eigene Backend-Seite erhalten, zum Beispiel `machine_construction_equipment_extension`.

### Kontakte (contacts)

Diese Erweiterung ermöglicht einer Maschine oder Gebrauchtmaschine einen Kontakt hinzuzufügen. Maschinenanfragen aus den Hauptausgabe- und Gebrauchtmaschinen-Beispielmodulen in Bootstrap 5 oder den veralteten Bootstrap-4-Varianten werden dann an diesen Kontakt geschickt und nicht an die in den Einstellungen hinterlegte allgemeine E-Mail-Adresse. Diese Erweiterung ist für Unternehmen mit mehreren Ansprechpartnern gedacht.

### Zubehör (equipment)

Diese Erweiterung ermöglicht einer Maschine Zubehör oder Ersatzteile hinzuzufügen. Das Zubehör kann in Gruppen unterteilt werden, um die Übersichtlichkeit zu wahren. Nach der Installation sollten zuerst die Zubehörgruppen eingegeben werden und danach das Zubehör selbst. In den Hauptausgabe-Beispielmodulen erscheint das Zubehör in einem eigenen Reiter.

### Gebrauchtmaschinen Export (export)

Diese Erweiterung ermöglicht den Export von Gebrauchtmaschinen auf verschiedene Portale. Getestet sind:

- baupool.com (Portaltyp: mascus)
- machinerypark.com (Portaltyp: machinerypark)
- machineryzone.de (Portaltyp: europemachinery)
- mascus.de (Portaltyp: mascus)
- linkedin.com (Portaltyp: linkedin)

Nach der Installation sollten zuerst die Einstellungen festgelegt und dann die Portale konfiguriert werden. Danach können unter "Export" Gebrauchtmaschinen für den Export als "online" markiert werden. Diese werden beim nächsten Export übertragen. Ein Export kann entweder manuell oder per Cronjob im Cronjob Addon durchgeführt werden. Der Cronjob kann in den Einstellungen installiert werden.

#### Linkedin Export

Zur Einrichtung eines Linkedin Exports sind weitere Schritte nötig. Richten Sie zuerst unter <https://www.linkedin.com/developers/> eine eigene App ein. Legen Sie in der Export-Verwaltung unter Portale ein neues Portal vom Typ "linkedin" an. Im Abschnitt "Einstellungen für alle sozialen Netzwerke" tragen Sie dann die App Client ID und das Client Secret der Linkedin App ein. Wird in das Feld "Linkedin ID" kein Wert eingetragen wird bei der Auswahl "Natürliche Person" des Feldes "Linkedin Typ" die URN ID der Person ausgelesen, bzw. bei der Auswahl "Unternehmen" die URN ID des ersten Unternehmens auf das die Person Zugriff hat.

Der Linkedin App muss im Reiter "Auth" im Bereich "OAuth 2.0 settings" unter "Authorized redirect URLs for your app" die URL der Redaxo index.php eingetragen werden, also z.B. <https://www.meine-domain.de/redaxo/index.php>. Unter "Products" muss das Recht "Share on LinkedIn" und "Sign In with LinkedIn" und zusätzlich "Advertising API" hinzugefügt werden. Die "Advertising API" erlaubt auf einer Unternehmensseite zu posten.

Auch an der Redaxo Konfiguration muss eine Anpassung vorgenommen werden. In der config.yml muss unter "session:", "backend:", dann "cookie:" der Wert für "samesite:" auf "Lax" eingestellt werden. Wird diese Anpassung nicht vorgenommen, zeigt Redaxo nach der Rückkehr von Linkedin den Redaxo Anmeldebildschirm anstatt die Export-Seite des Addons. Würde man diese Seite einfach nochmals neu laden, erfolgt die Anzeige der gewünschten Seite.

### Branchen (industry_sectors)

Diese Erweiterung ermöglicht Branchen zu erstellen, in denen Maschinen eingesetzt werden. Eine Maschine kann Branchen zugeordnet werden. Für jede Branche wird eine eigene URL generiert. Nach der Installation sollten zuerst die Einstellungen festegelegt, danach die Branchen eingegeben und zuletzt die Maschinen den Branchen zugeordnet werden. Die Erweiterung stellt das Beispielmodul "90-2 D2U Machinery Addon - Branchen" zur Verfügung.

### Rührwerke (machine_agitator_extension)

Diese Erweiterung erweitert die Eigenschaften der Maschinen um Rührwerke. Nach der Installation sollten zuerst Rührorgane eingegeben werden. Danach Rührwerkstypen. Die Organe können diesen zugeordnet werden. Die eingegebenen Rührwerkstypen stehen dann in den Maschinen zur Zuordnung zur Verfügung.

### Zertifikate (machine_certificates_extension)

Diese Erweiterung fügt den Maschinen Zertifikate hinzu. Nach der Installation kann ein Zertifikat erstellt werden. Dieses kann dann in einer Maschine im Abschnitt "Zertifikate" zugeordnet werden.

### Baustellenausrüstung (machine_construction_equipment_extension)

Diese Erweiterung hat keine eigene Seite. Sie erweitert lediglich die Eingabefelder der Maschinen um Felder für technische Daten, die den Spezialbereichen Airlessgeräte, Container, Schneidegeräte, Bodenbearbeitung, Spezial-Schleifgeräte, Pumpen / Einhandpistolen und Abwasseranlagen zugeordnet sind. In den Hauptausgabe-Beispielmodulen werden diese Felder in der Kategorieübersicht als Vergleich mit anderen Maschinen der Kategorie und einzeln in der Maschine im Reiter "Technische Daten" ausgegeben.

### Features (machine_features_extension)

Diese Erweiterung fügt den Maschinen hervorhebenswerte Features hinzu. Nach der Installation kann ein Feature erstellt werden. Dieses kann dann in einer Maschine im Abschnitt "Features" zugeordnet werden. In den Hauptausgabe-Beispielmodulen werden Features im Reiter "Features" ausgegeben.

### Optionen (machine_options_extension)

Wie Features, fügt diese Erweiterung den Maschinen zusätzliche Optionen hinzu. Im Unterschied zu den Features sind Optionen nicht fest in der Maschine eingebaut, sondern können optional gebucht werden. Nach der Installation kann eine Option erstellt werden. Diese kann dann in einer Maschine im Abschnitt "Optionen" zugeordnet werden. In den Hauptausgabe-Beispielmodulen werden Optionen derzeit noch nicht ausgegeben.

### Stahlverarbeitung (machine_steel_processing_extension)

Diese Erweiterung erweitert die Eingabefelder der Maschinen um Felder für technische Daten, die den Spezialbereichen Automatisierung, Blech- / Stahlverarbeitung, Bohren, Sägen, Stanz- / Ausklink- / Schweißmaschinen, Strahlanlagen zugeordnet sind. Einzelne Auswahlfelder dieser Bereiche können im Reiter "Stahlverarbeitung" definiert werden. In den Hauptausgabe-Beispielmodulen werden diese Felder nicht ausgegeben.

### Anwendungsgebiete (machine_usage_area_extension)

Diese Erweiterung erweitert die Eingabefelder der Maschinen um Anwendungsgebiete. Der Übersichtlichkeit halber können Anwendungsgebiete auf bestimmte Kategorien beschränkt werden. In den Hauptausgabe-Beispielmodulen werden diese Felder in der Kategorieübersicht als Vergleich mit anderen Maschinen der Kategorie und einzeln in der Maschine im Reiter "Anwendungsgebiete" ausgegeben.

### Produktionslinien (production_lines)

Mit dieser Erweiterung lassen Produktionslinien erstellen. Hierzu werden verschiedene Maschinen miteinander verbunden. Es gibt kein Beispielmodul für diese Erweiterung.

### Service (service_options)

Diese Erweiterung erweitert die Eingabefelder der Maschinen um Serviceoptionen. In den Hauptausgabe-Beispielmodulen werden diese Felder in der Maschine im Reiter "Service" ausgegeben.

### Gebrauchtmaschinen (used_machines)

Diese Erweiterung bietet die Möglichkeit zusätzlich zu Maschinen auch Gebrauchtmaschinen anzubieten. Diese können als Miet- oder Verkaufsangebot definiert werden. Als Beispielmodule stehen die Gebrauchtmaschinen-Varianten für Bootstrap 5 und das veraltete Bootstrap 4 zur Verfügung. Wenn die Erweiterung Export aktiviert ist, können Gebrauchtmaschinen auch auf Onlineportalen eingestellt werden.

## Beispielmodule

- 90-1 D2U Machinery Addon - Hauptausgabe (BS4, deprecated)
- 90-2 D2U Machinery Addon - Branchen (BS4, deprecated)
- 90-3 D2U Machinery Addon - Kategorien (BS4, deprecated)
- 90-4 D2U Machinery Addon - Gebrauchtmaschinen (BS4, deprecated)
- 90-5 D2U Machinery Addon - Box Beratungshinweis (BS4, deprecated)
- 90-6 D2U Machinery Addon - Gebrauchtmaschinen Topangebote (BS4, deprecated)
- 90-7 D2U Machinery Addon - Hauptausgabe (BS5)
- 90-8 D2U Machinery Addon - Branchen (BS5)
- 90-9 D2U Machinery Addon - Kategorien (BS5)
- 90-10 D2U Machinery Addon - Gebrauchtmaschinen (BS5)
- 90-11 D2U Machinery Addon - Box Beratungshinweis (BS5)
- 90-12 D2U Machinery Addon - Gebrauchtmaschinen Topangebote (BS5)
