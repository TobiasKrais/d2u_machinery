# REST API

Das Addon d2u_machinery stellt eine REST API bereit, mit der externe Werkzeuge (z. B. ein KI-gestütztes Content-Tool) Daten auslesen und einspielen können: Maschinen, Kategorien, Features und weitere Datentypen inklusive Texten, Bildern und technischen Daten.

Die API baut auf dem Addon [`api`](https://github.com/FriendsOfREDAXO/api) auf. Alle Endpunkte erscheinen automatisch in dessen OpenAPI-/Swagger-Ansicht und unter `/api/me`. Im Backend gibt es unter **D2U Maschinen › Hilfe › REST API** eine Seite, die das aktuell verfügbare Feldschema live anzeigt.

## Voraussetzungen

- Addon `api` (FriendsOfREDAXO) installiert und aktiviert.
- Ein API-Token mit den benötigten Scopes (siehe unten).

### Authorization-Header

Manche Apache-Konfigurationen entfernen den `Authorization`-Header. Falls Aufrufe trotz gültigem Token mit `401` beantwortet werden, muss der Header durchgereicht werden. Dazu in der `.htaccess` im Projektstamm direkt nach `RewriteEngine On` ergänzen:

```apache
RewriteCond %{HTTP:Authorization} .
RewriteRule ^ - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
```

## Authentifizierung

Jeder Aufruf benötigt ein Bearer-Token aus dem `api`-Addon. Das Token wird im Backend unter **API › Token** angelegt; dort werden ihm die benötigten Scopes zugewiesen.

```
Authorization: Bearer DEIN_TOKEN
```

## Basis-URL

```
https://deine-domain.tld/api/d2u_machinery/...
```

## Scopes

Die Scopes folgen dem Schema `d2u_machinery/<ressource>/<operation>`. Der Schema-Endpunkt benötigt nur ein gültiges Token (keinen eigenen Scope).

| Operation | Scope |
| --- | --- |
| Schema/Discovery | `d2u_machinery/schema` (kein Scope nötig) |
| Liste | `d2u_machinery/<ressource>/list` |
| Einzeln lesen | `d2u_machinery/<ressource>/get` |
| Anlegen | `d2u_machinery/<ressource>/create` |
| Ändern | `d2u_machinery/<ressource>/update` |
| Löschen | `d2u_machinery/<ressource>/delete` |

## Discovery / Scope-Abfrage

Damit ein externes Tool weiß, welche Felder es je nach aktivierten Erweiterungen befüllen darf, liefert der Schema-Endpunkt eine maschinenlesbare Beschreibung: Addon-Version, Sprachen, aktive Erweiterungen sowie pro Ressource die verfügbaren Felder mit Typ, Pflichtangabe, Sprachabhängigkeit und Relation.

```bash
curl -H "Authorization: Bearer DEIN_TOKEN" \
  https://deine-domain.tld/api/d2u_machinery/schema
```

Nur Felder, deren zugehörige Erweiterung aktiv ist, werden gelistet. Ist z. B. die Erweiterung „Technische Basisdaten" (`basic_tech_data`) aktiv, enthält die Ressource `machines` unter anderem `engine_power`, `length` usw.; ist die Stahlbearbeitung inaktiv, fehlen deren Felder.

## Ressourcen

Jede Ressource ist an eine Erweiterung gekoppelt und nur verfügbar, wenn diese aktiv ist (Ausnahme: `machines` und `categories` sind immer verfügbar).

| Ressource | ID-Feld | Erweiterung |
| --- | --- | --- |
| `machines` | `machine_id` | – |
| `categories` | `category_id` | – |
| `features` | `feature_id` | `machine_features_extension` |
| `options` | `option_id` | `machine_options_extension` |
| `certificates` | `certificate_id` | `machine_certificates_extension` |
| `equipment` | `equipment_id` | `equipment` |
| `equipment_groups` | `group_id` | `equipment` |
| `agitators` | `agitator_id` | `machine_agitator_extension` |
| `service_options` | `service_option_id` | `service_options` |
| `contacts` | `contact_id` | `contacts` |
| `industry_sectors` | `industry_sector_id` | `industry_sectors` |
| `used_machines` | `used_machine_id` | `used_machines` |
| `production_lines` | `production_line_id` | `production_lines` |

## Endpunkte

Pro Ressource stehen die folgenden Endpunkte bereit:

| Methode | Pfad | Beschreibung |
| --- | --- | --- |
| `GET` | `/api/d2u_machinery/<ressource>` | Liste (Query: `clang_id`, `page`, `per_page`) |
| `GET` | `/api/d2u_machinery/<ressource>/{id}` | Einzelnen Datensatz inkl. Übersetzungen lesen |
| `POST` | `/api/d2u_machinery/<ressource>` | Datensatz anlegen |
| `PUT`/`PATCH` | `/api/d2u_machinery/<ressource>/{id}` | Datensatz ändern |
| `DELETE` | `/api/d2u_machinery/<ressource>/{id}` | Datensatz löschen |

> Hinweis: `PUT`, `PATCH` und `DELETE` müssen serverseitig erlaubt sein. Manche Apache-Konfigurationen blockieren diese Methoden (Antwort: HTTP 403 als HTML).

## Payload-Aufbau

Nicht sprachabhängige Felder liegen unter `fields`, sprachabhängige je Sprach-ID (clang) unter `translations`. Unbekannte oder inaktive Felder werden mit `HTTP 400` abgelehnt.

```json
{
  "fields": {
    "name": "CNC 5000",
    "product_number": "ABC-123",
    "online_status": "online",
    "category_id": 1,
    "pics": ["cnc5000_front.jpg"],
    "engine_power": "15"
  },
  "translations": {
    "1": { "lang_name": "CNC 5000", "teaser": "Kurztext", "description": "<p>Beschreibung</p>" },
    "2": { "lang_name": "CNC 5000" }
  }
}
```

Ressourcen ohne Sprachtabelle (z. B. `contacts`) besitzen keine sprachabhängigen Felder. Für sie entfällt `translations`; alle Werte liegen unter `fields`.

### Feldtypen

| Typ | Bedeutung |
| --- | --- |
| `string` | Zeichenkette |
| `html` | HTML-Text (z. B. Beschreibung) |
| `int` | Ganzzahl |
| `bool` | Wahrheitswert |
| `int[]` | Liste von IDs (z. B. Relationen) |
| `media` | Dateiname aus dem Medienpool |
| `media[]` | Liste von Medienpool-Dateinamen |
| `enum:a,b` | Fester Wertebereich (z. B. `online,offline`) |

## Bilder hochladen

Bilder werden zuerst über den Medien-Endpunkt des `api`-Addons hochgeladen und anschließend per Dateiname referenziert:

1. `POST /api/media` (multipart) im `api`-Addon → liefert den Dateinamen.
2. Den Dateinamen in `pics` (Liste) bzw. `pic`/`picture` (Einzelbild) der jeweiligen Ressource eintragen.

## Typischer „Exportieren"-Ablauf

1. `GET /api/d2u_machinery/schema` lesen → verfügbare Ressourcen und Felder ermitteln.
2. Bilder über `POST /api/media` hochladen → Dateinamen erhalten.
3. Datensätze via `POST`/`PATCH` anlegen bzw. aktualisieren und die Bild-Dateinamen referenzieren.

## Beispiele

Schema abrufen:

```bash
curl -H "Authorization: Bearer DEIN_TOKEN" \
  https://deine-domain.tld/api/d2u_machinery/schema
```

Kategorien auflisten:

```bash
curl -H "Authorization: Bearer DEIN_TOKEN" \
  "https://deine-domain.tld/api/d2u_machinery/categories?per_page=20"
```

Maschine anlegen:

```bash
curl -X POST \
  -H "Authorization: Bearer DEIN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"fields":{"name":"CNC 5000","online_status":"offline","category_id":1},"translations":{"1":{"lang_name":"CNC 5000"}}}' \
  https://deine-domain.tld/api/d2u_machinery/machines
```

## Fehlercodes

| Code | Bedeutung |
| --- | --- |
| `400` | Ungültiger Payload, unbekanntes/inaktives Feld oder fehlendes Pflichtfeld |
| `401` | Kein oder ungültiges Token bzw. fehlender Scope |
| `404` | Ressource nicht verfügbar oder Datensatz nicht gefunden |
| `500` | Interner Fehler (Details im REDAXO-Systemlog) |
