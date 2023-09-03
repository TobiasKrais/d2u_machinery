# Redaxo 5 Maschinenverwaltung

Mehrsprachig Maschinenverwaltung für Redaxo. Erweiterungen können gerne in Auftrag gegeben werden. Demoseite: <https://test.design-to-use.de/de/addontests/d2u-maschinen/>

## Installation

Nach der Installation in Redaxo sollten folgende Schritte ausgeführt werden:

1. Festlegen der Einstellungen im Addon.
2. Eingabe mindestens eines Kontaktes.
3. Eingabe mindestens einer Kategorie. Diese werden für die Generierung der URLs benötigt.
4. Nun kann mit der Eingabe von Maschinen begonnen werden.
5. Wenn gewünscht, kann unter Setup eines der auf Bootstrap 4 basierenden Beispielmodule installiert werden. Die Templates mit der ID 00-1, 04-1, 04-2 und 04-3 im D2U Helper Addon geben die Reiter der Beispielmodule "90-1 D2U Machinery Addon - Hauptausgabe" und "90-4 D2U Machinery Addon - Gebrauchtmaschinen" aus.

## Plugins

Es exisitieren verschiedene Plugins, die nachfolgend kurz beschrieben werden. Um die Komplexität des Addons so gering wie möglich zu halten, **sollte ein Plugin nur installiert werden, wenn es auch benötigt wird**.

### Kontakte (contacts)

Dieses Plugin ermöglicht einer Maschine oder Gebrauchtmaschine einen Kontakt hinzuzufügen. Maschinenanfragen aus den Beispielmodulen "90-1 D2U Machinery Addon - Hauptausgabe" und "90-4 D2U Machinery Addon - Gebrauchtmaschinen" werden dann an diesen Kontakt geschickt und nicht an die in den Einstellungen hinterlegte allgemeine E-Mail-Adresse. Dieses Plugin ist für Unternehmen mit mehreren Ansprechpartnern gedacht.

### Zubehör (equipment)

Dieses Plugin ermöglicht einer Maschine Zubehör oder Ersatzteile hinzuzufügen. Das Zubehör kann in Gruppen unterteilt werden, um die Übersichtlichkeit zu wahren. Nach der Installation sollten zuerst die Zubehörgruppen eingegeben werden und danach das Zubehör selbst. Im Beispielmodul "90-1 D2U Machinery Addon - Hauptausgabe" erscheint das Zubehör in einem eigenen Reiter.

### Gebrauchtmaschinen Export (export)

Dieses Plugin ermöglicht den Export von Gebrauchtmaschinen auf verschiedene Portale. Getestet sind:

- baupool.com (Portaltyp: mascus)
- machinerypark.com (Portaltyp: machinerypark)
- machineryzone.de (Portaltyp: europemachinery)
- mascus.de (Portaltyp: mascus)
- linkedin.com (Portaltyp: linkedin)

Nach der Installation sollten zuerst die Einstellungen festgelegt und dann die Portale konfiguriert werden. Danach können unter "Export" Gebrauchtmaschinen für den Export als "online" markiert werden. Diese werden beim nächsten Export übertragen. Ein Export kann entweder manuell oder per Cronjob im Cronjob Addon durchgeführt werden. Der Cronjob kann in den Einstellungen installiert werden.

#### Linkedin Export

Zur Einrichtung eines Linkedin Exports sind weitere Schritte nötig. Richten Sie zuerst unter <https://www.linkedin.com/developers/> eine eigene App ein. Legen Sie im Export Plugin unter Portale ein neues Portal vom Typ "linkedin" an. Im Abschnitt "Einstellungen für alle sozialen Netzwerke" tragen Sie dann die App Client ID und das Client Secret der Linkedin App ein. Wird in das Feld "Linkedin ID" kein Wert eingetragen wird bei der Auswahl "Natürliche Person" des Feldes "Linkedin Typ" die URN ID der Person ausgelesen, bzw. bei der Auswahl "Unternehmen" die URN ID des ersten Unternehmens auf das die Person Zugriff hat.

Der Linkedin App muss im Reiter "Auth" im Bereich "OAuth 2.0 settings" unter "Authorized redirect URLs for your app" die URL der Redaxo index.php eingetragen werden, also z.B. "https://www.meine-domain.de/redaxo/index.php". Unter "Products" muss das Recht "Share on LinkedIn" und "Sign In with LinkedIn" und zusätzlich "Advertising API" hinzugefügt werden. Die "Advertising API" erlaubt auf einer Unternehmensseite zu posten.

Auch an der Redaxo Konfiguration muss eine Anpassung vorgenommen werden. In der config.yml muss unter "session:", "backend:", dann "cookie:" der Wert für "samesite:" auf "Lax" eingestellt werden. Wird diese Anpassung nicht vorgenommen, zeigt Redaxo nach der Rückkehr von Linkedin den Redaxo Anmeldebildschirm anstatt die Seite des Export Plugins. Würde man diese Seite einfach nochmals neu laden, erfolgt die Anzeige der gewünschten Seite des Eport Plugins.

### Branchen (industry_sectors)

Dieses Plugin ermöglicht Branchen zu erstellen, in denen Maschinen eingesetzt werden. Eine Maschine kann Branchen zugeordnet werden. Für jede Branche wird eine eigene URL generiert. Nach der Installation sollten zuerst die Einstellungen festegelegt, danach die Branchen eingegeben und zuletzt die Maschinen den Branchen zugeordnet werden. Das Plugin stellt das Beispielmodul "90-2 D2U Machinery Addon - Branchen" zur Verfügung.

### Rührwerke (machine_agitator_extension)

Dieses Plugin erweitert die Eigenschaften der Maschinen um Rührwerke. Nach der Installation sollten zuerst Rührorgane eingegeben werden. Danach Rührwerkstypen. Die Organe können diesen zugeordnet werden. Die eingegebenen Rührwerkstypen stehen dann in den Maschinen zur Zuordnung zur Verfügung.

### Zertifikate (machine_certificates_extension)

Dieses Plugin fügt den Maschinen Zertifikate hinzu. Nach der Installation kann ein Zertifikat erstellt werden. Dieses kann dann in einer Maschine im Abschnitt "Zertifikate" zugeordnet werden.

### Baustellenausrüstung (machine_construction_equipment_extension)

Dieses Plugin hat keine eigene Seite. Es erweitert lediglich die Eingabefelder der Maschinen um Felder für technische Daten, die den Spezialbereichen Airlessgeräte, Container, Schneidegeräte, Bodenbearbeitung, Spezial-Schleifgeräte, Pumpen / Einhandpistolen und Abwasseranlagen zugeordnet sind. Im Beispielmodul "90-1 D2U Machinery Addon - Hauptausgabe" werden diese Felder in der Kategorieübersicht als Vergleich mit anderen Maschinen der Kategorie und einzeln in der Maschine im Reiter "Technische Daten" ausgegeben.

### Features (machine_features_extension)

Dieses Plugin fügt den Maschinen hervorhebenswerte Features hinzu. Nach der Installation kann ein Feature erstellt werden. Dieses kann dann in einer Maschine im Abschnitt "Features" zugeordnet werden. Im Beispielmodul "90-1 D2U Machinery Addon - Hauptausgabe" werden Features im Reiter "Features" ausgegeben.

### Optionen (machine_options_extension)

Wie Features, fügt dieses Plugin den Maschinen zusätzliche Optionen hinzu. Im Unterschied zu den Features sind Optionen nicht fest in der Maschine eingebaut, sondern können optional gebucht werden. Nach der Installation kann eine Option erstellt werden. Dieses kann dann in einer Maschine im Abschnitt "Optionen" zugeordnet werden. Im Beispielmodul "90-1 D2U Machinery Addon - Hauptausgabe" werden Optionen derzeit noch nicht ausgegeben.

### Stahlverarbeitung (machine_steel_processing_extension)

Dieses Plugin erweitert die Eingabefelder der Maschinen um Felder für technische Daten, die den Spezialbereichen Automatisierung, Blech- / Stahlverarbeitung, Bohren, Sägen, Stanz- / Ausklink- / Schweißmaschinen, Strahlanlagen zugeordnet sind. Einzelne Auswahlfelder dieser Bereiche können im Reiter "Stahlverarbeitung" definiert werden. Im Beispielmodul "90-1 D2U Machinery Addon - Hauptausgabe" werden diese Felder nicht ausgegeben.

### Anwendungsgebiete (machine_usage_area_extension)

Dieses Plugin erweitert die Eingabefelder der Maschinen um Anwendungsgebiete. Der Übersichtlichkeit halber können Anwendungsgebiete auf bestimmte Kategorien beschränkt werden. Im Beispielmodul "90-1 D2U Machinery Addon - Hauptausgabe" werden diese Felder in der Kategorieübersicht als Vergleich mit anderen Maschinen der Kategorie und einzeln in der Maschine im Reiter "Anwendungsgebiete" ausgegeben.

### Produktionslinien (production_lines)

Mit diesem Plugin lassen Produktionslinien erstellen. Hierzu werden verschiedene Maschinen miteinander verbunden. Es gibt kein Beispielmodul für dieses Plugin.

### Service (service_options)

Dieses Plugin erweitert die Eingabefelder der Maschinen um Serviceoptionen. Im Beispielmodul "90-1 D2U Machinery Addon - Hauptausgabe" werden diese Felder in der Maschine im Reiter "Service" ausgegeben.

### Gebrauchtmaschinen (used_machines)

Dieses Plugin bietet die Möglichkeit zusätzlich zu Maschinen auch Gebrauchtmaschinen anzubieten. Diese können als Miet- oder Verkaufsangebot definiert werden. Als Beispielmodul steht das Modul "90-4 D2U Machinery Addon - Gebrauchtmaschinen zur Verfügung. Wenn das Export Plugin installiert ist, können Gebrauchtmaschinen auch auf Onlineportalen eingestellt werden.
