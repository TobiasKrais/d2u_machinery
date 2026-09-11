<?php

/**
 * Backend documentation for the d2u_machinery REST API.
 *
 * @var rex_addon $this
 * @psalm-scope-this rex_addon
 */

use TobiasKrais\D2UMachinery\Api\Schema;

if (!rex_addon::get('api')->isAvailable()) {
    echo rex_view::warning('Das Addon <code>api</code> ist nicht installiert oder aktiviert. Die REST API von d2u_machinery baut darauf auf und ist erst nach Installation des <code>api</code>-Addons verfügbar.');
    return;
}

$describe = Schema::describe();
$tokenUrl = rex_url::backendPage('api/token');
$meUrl = rex_url::backendPage('api/openapi');

$content = '';

$content .= '<p>Diese REST API erlaubt es externen Werkzeugen (z.&nbsp;B. einem KI-gestützten Content-Tool), Daten für dieses Addon auszulesen und einzuspielen: Maschinen, Kategorien und Features inklusive Texten, Bildern und technischen Daten. Sie baut auf dem Addon <code>api</code> auf; alle Endpunkte erscheinen automatisch in dessen OpenAPI/Swagger-Ansicht und unter <code>/api/me</code>.</p>';

$content .= '<h3>Authentifizierung</h3>';
$content .= '<p>Jeder Aufruf benötigt ein Bearer-Token aus dem <code>api</code>-Addon. Lege ein Token unter <a href="'. rex_escape($tokenUrl) .'">API &rsaquo; Token</a> an und weise ihm die benötigten Scopes zu. Beispiel-Header:</p>';
$content .= '<pre><code>Authorization: Bearer DEIN_TOKEN</code></pre>';

$content .= '<h3>Scopes</h3>';
$content .= '<p>Die Scopes folgen dem Schema <code>d2u_machinery/&lt;ressource&gt;/&lt;operation&gt;</code>. Der Schema-Endpunkt benötigt nur ein gültiges Token (kein eigener Scope).</p>';
$content .= '<table class="table table-striped"><thead><tr><th>Zweck</th><th>Scope</th></tr></thead><tbody>';
$content .= '<tr><td>Fähigkeiten &amp; Feldschema</td><td><code>d2u_machinery/schema</code></td></tr>';
foreach ($describe['resources'] as $resource => $info) {
    foreach (['list', 'get', 'create', 'update', 'delete'] as $operation) {
        $content .= '<tr><td>'. rex_escape($resource) .' &ndash; '. rex_escape($operation) .'</td><td><code>d2u_machinery/'. rex_escape($resource) .'/'. rex_escape($operation) .'</code></td></tr>';
    }
}
$content .= '</tbody></table>';

$content .= '<h3>Discovery / Scope-Abfrage</h3>';
$content .= '<p>Damit ein KI-Tool weiß, welche Felder es je nach aktivierten Erweiterungen befüllen darf, liefert der Schema-Endpunkt eine maschinenlesbare Beschreibung (aktive Erweiterungen, Sprachen, Ressourcen und Felder inkl. Typ, Pflichtfeld, Sprachabhängigkeit und Relation):</p>';
$content .= '<pre><code>curl -H "Authorization: Bearer DEIN_TOKEN" \\'. "\n" .'  '. rex_escape(rtrim((string) rex::getServer(), '/')) .'/api/d2u_machinery/schema</code></pre>';

$content .= '<h3>Typischer &bdquo;Exportieren&ldquo;-Ablauf</h3>';
$content .= '<ol>';
$content .= '<li><code>GET /api/d2u_machinery/schema</code> lesen &rarr; verfügbare Ressourcen und Felder ermitteln.</li>';
$content .= '<li>Bilder über <code>POST /api/media</code> (Addon <code>api</code>, multipart) hochladen &rarr; Dateinamen erhalten.</li>';
$content .= '<li>Datensätze anlegen/aktualisieren via <code>POST</code>/<code>PATCH</code> und die Bild-Dateinamen in <code>pics</code>/<code>pic</code> referenzieren.</li>';
$content .= '</ol>';

$content .= '<h3>Payload-Aufbau (Anlegen/Aktualisieren)</h3>';
$content .= '<p>Nicht-sprachabhängige Felder liegen unter <code>fields</code>, sprachabhängige je Sprach-ID unter <code>translations</code>. Unbekannte oder inaktive Felder werden mit <code>HTTP 400</code> abgelehnt.</p>';
$machineExample = [
    'fields' => [
        'name' => 'CNC 5000',
        'product_number' => 'ABC-123',
        'online_status' => 'online',
        'category_id' => 1,
        'pics' => ['cnc5000_front.jpg'],
        'engine_power' => '15',
    ],
    'translations' => [
        '1' => ['lang_name' => 'CNC 5000', 'teaser' => 'Kurztext', 'description' => '<p>Beschreibung</p>'],
    ],
];
$content .= '<pre><code>curl -X POST -H "Authorization: Bearer DEIN_TOKEN" -H "Content-Type: application/json" \\'. "\n" .'  -d \''. rex_escape((string) json_encode($machineExample, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)) .'\' \\'. "\n" .'  '. rex_escape(rtrim((string) rex::getServer(), '/')) .'/api/d2u_machinery/machines</code></pre>';

$content .= '<h3>Bilder hochladen</h3>';
$content .= '<p>Bilder werden zuerst über den Medien-Endpunkt des <code>api</code>-Addons hochgeladen und anschließend per Dateiname referenziert:</p>';
$content .= '<ol>';
$content .= '<li><code>POST /api/media</code> (multipart) im <code>api</code>-Addon &rarr; liefert den Dateinamen.</li>';
$content .= '<li>Den Dateinamen in <code>pics</code> (Liste) bzw. <code>pic</code>/<code>picture</code> (Einzelbild) der jeweiligen Ressource eintragen.</li>';
$content .= '</ol>';

$content .= '<h3>Fehlercodes</h3>';
$content .= '<table class="table table-striped"><thead><tr><th>Code</th><th>Bedeutung</th></tr></thead><tbody>';
$content .= '<tr><td><code>400</code></td><td>Ungültiger Payload, unbekanntes/inaktives Feld oder fehlendes Pflichtfeld</td></tr>';
$content .= '<tr><td><code>401</code></td><td>Kein oder ungültiges Token bzw. fehlender Scope</td></tr>';
$content .= '<tr><td><code>404</code></td><td>Ressource nicht verfügbar oder Datensatz nicht gefunden</td></tr>';
$content .= '<tr><td><code>500</code></td><td>Interner Fehler (Details im REDAXO-Systemlog)</td></tr>';
$content .= '</tbody></table>';
$content .= '<p><small>Hinweis: <code>PUT</code>, <code>PATCH</code> und <code>DELETE</code> müssen serverseitig erlaubt sein; manche Apache-Konfigurationen blockieren diese Methoden (Antwort: HTTP 403 als HTML).</small></p>';

$content .= '<h3>Aktuell verfügbare Ressourcen und Felder</h3>';
$content .= '<p>Die folgende Übersicht spiegelt die aktuell aktivierten Erweiterungen wider. Aktive Erweiterungen: <code>'. rex_escape(implode(', ', $describe['active_extensions'])) .'</code>.</p>';

foreach ($describe['resources'] as $resource => $info) {
    $content .= '<h4>'. rex_escape($resource) .' <small>(ID-Feld: <code>'. rex_escape($info['id_field']) .'</code>)</small></h4>';
    $content .= '<table class="table table-striped"><thead><tr><th>Feld</th><th>Typ</th><th>Pflicht</th><th>Sprachabhängig</th><th>Relation</th></tr></thead><tbody>';
    foreach ($info['fields'] as $field) {
        $content .= '<tr>'
            .'<td><code>'. rex_escape($field['name']) .'</code></td>'
            .'<td>'. rex_escape($field['type']) .'</td>'
            .'<td>'. ($field['required'] ? 'ja' : '&ndash;') .'</td>'
            .'<td>'. ($field['language'] ? 'ja' : '&ndash;') .'</td>'
            .'<td>'. (null !== $field['relation'] ? '<code>'. rex_escape((string) $field['relation']) .'</code>' : '&ndash;') .'</td>'
            .'</tr>';
    }
    $content .= '</tbody></table>';
}

$fragment = new rex_fragment();
$fragment->setVar('title', rex_i18n::msg('d2u_machinery_help_chapter_api'), false);
$fragment->setVar('body', $content, false);
echo $fragment->parse('core/page/section.php');
