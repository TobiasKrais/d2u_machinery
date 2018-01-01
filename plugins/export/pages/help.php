<h1>Facebook Export</h1>
<h2>Einrichtung der Facebook App</h2>
<ol>
	<li>Erstelle eine Facebook App.</li>
	<li>In der App muss unter Einstellungen -> Allgemeines unter App-Domain die
		URL deines Redaxos eingetragen werden. Etwas weiter unten, unter Webseite
		-> "URL der Webseite" bitte die URL deines Redaxos eintragen.</li>
	<li>Unter Rollen müssen alle Facebook Konten als Tester hinzugefügt werden,
		die den Export nutzen wollen.</li>
	<li>Unter Produkte muss ein Facebook-Login hinzugefügt werden. Dort muss nur
		die "Web-OAuth-Anmeldung" aktiviert werden. Außerdem müssen im Feld
		"Gültige OAuth Redirect URIs" die Callback URIs hinzugefügt werden. Diese
		URIs haben folgendes Schema: https://www.example.com/redaxo/index.php?page=d2u_machinery/export/export&func=export&facebook_return=true&provider_id=<span style="color:red">1</span>
		wobei der Wert des Parameters provider_id am Ende der URL mit der
		jeweiligen Portal ID ersetzt werden muss. Für jedes Portal muss diese URL
		hinzugefügt werden.</li>
</ol>
<h2>Einrichtung der Facebook Portaleinstellungen in diesem Plugin</h2>
<ul>
	<li>Um die Facebook Seiten ID herauszufinden, öffne die Seite und klicke
		unterhalb des Headerbildes auf die drei Punkte. Wähle dort "Seiteninfo
		bearbeiten". In dem geöffneten Modal ganz nach unten scrollen und auf
		"Alle Informationen anzeigen" klicken. Recht weit unten im Bereich
		"Weitere Infos" befindet sich die Seiten-ID, die in die Portaleinstellungen
		eingetragen werden muss.</li>
</ul>
<h2>Fehlermeldungen beim Export</h2>
<ul>
	<li><strong>Facebook\Exceptions\FacebookAuthorizationException: (#210) Param id must be a page</strong><br>
		Diese Aktion kann nicht auf der Standardpinwand ausgeführt werden. Es
		muss eine Facebook Seite eingerichtet werden und die Facebook Seiten-ID
		in den Einstellungen des jeweiligen Portals hinterlegt werden.</li>
</ul>
