# Setzen Sie bitte jetzt diese Installationsanleitung um

## Schritt 1

Bitte geben Sie an, wo die avalex Rechtstexte auf Ihrem Internetauftritt angezeigt werden sollen. Tragen Sie die Webadressen (URLs) Ihrer Rechtstext-Seiten ein. Die Adressen der Seiten sollten diesem Muster entsprechen `www.ihredomain.de/impressum`, `www.ihredomain.de/datenschutzerklaerung` usw.

## Schritt 2

Laden Sie sich das avalex Plugin für Kirby herunter.

[ Download ] -> ...

## Schritt 3

Entpacken Sie das ZIP-Archiv und kopieren Sie den Ordner `kirby-avalex` in den Ordner `/site/plugins` Ihres Internetauftritts. Falls der Ordner `plugins` nicht existiert, legen Sie ihn zuvor bitte an.

/!\ Fortgeschrittene Anwender können das Plugin alternativ auch mit [composer](https://getcomposer.org/) installieren:

	php composer.phar require avalexgmbh/kirby-avalex

## Schritt 4

Tragen Sie nun den folgenden, nur für Ihre Domain gültigen API Key in die Config-Datei Ihres Internetauftritts ein. Sie finden die Datei im Ordner `/site/config/config.php`. Falls die Datei nicht existiert, legen Sie sie bitte zuvor an:

```php
# /site/config/config.php

<?php

return [
	'avalex.avalex' => [
		'apikey' => '...'
	],
];
```

## Schritt 5

Das Plugin lädt die avalex Rechtstexte automatisch in deutsch. Bei mehrsprachigen Internetpräsenzen (deutsch und englisch) erkennt das Plugin die Sprache automatisch. 

Falls Sie Ihre Internetpräsenz einsprachig und nicht in deutsch betreiben, müssen Sie die verwendete Sprache ebenfalls in der Config-Datei angeben:

```php
# /site/config/config.php

<?php

return [
	'avalex.avalex' => [
		'apikey' => '...',
		'language' => 'en', // english!
	],
];
```

Bitte beachten Sie, dass avalex Rechtstexte aktuell nur in den Sprachen Deutsch und Englisch verfügbar sind.

## Schritt 6

Fügen Sie in den Templates Ihrer Seiten an den Stellen, wo die Rechtstexte erscheinen sollen, den jeweiligen Funktionsaufruf nach dem folgenden Muster ein:

| avalex Rechtstext | Funktionsaufruf
| ---
| Impressum | `<?php echo avalex('imprint') ?>`
| Datenschutzerklärung | `<?php echo avalex('disclaimer') ?>`
| Widerrufsbelehrung | `<?php echo avalex('revocation') ?>`
| AGB | `<?php echo avalex('conditions') ?>`

Die Aufrufe sind für alle Sprachen Ihrer Internetpräsenz identisch, das Plugin erkennt die Sprache automatisch.

## Schritt 7

Die Webseiten mit den avalex Rechtstexten müssen von jeder Webseite Ihres Internetauftritts aus direkt erreichbar sein. Wir empfehlen, sie leicht erkennbar in der Hauptnavigation oder im Footer mit den Ankertexten „Impressum“, „Datenschutzerklärung“, „Widerrufsbelehrung“ und „AGB“ zu verlinken. Falls Sie die avalex Rechtstexte in unterschiedlichen Sprachen einsetzen, empfehlen wir, sie jeweils mit Ankertexten in der Zielsprache zu verlinken. Beispiel: „Imprint“, „Privacy“, „Cancellation notice“ sowie „Terms and conditions“ für die englischsprachigen Versionen unserer Rechtstexte.

## Schritt 8

Bitte schließen Sie die Installation durch Klick auf den nachfolgenden Button ab.
