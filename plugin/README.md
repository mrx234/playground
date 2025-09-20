# Kirby-Plugin for avalex 

This plugin offers functions to integrate the avalex legal text service into any new or existing Kirby project.

> To make use of the service a paid subscription from https://avalex.de is required!

The plugin calls the avalex API and downloads one of the legal texts that are created and maintained by avalex (depending on your type of subscription, you may not have access to all legal texts avalex generally provides). Once a text is downloaded it will be cached using Kirby’s integrated caching functions. This is a requirement by avalex in order to avoid heavy traffic on the API and must carefully be respected – otherwise, avalex might ban your project for a certain amount of time from further API calls.

All texts will be created and downloaded in plain HTML format and can be styled to match your project’s design by using CSS.

## Compatibility

- Kirby 3
- Kirby 4
- Kirby 5

## Installation

Subscribe to an avalex subscription plan under https://avalex.de and after providing all required information you will be finally directed to their “Installation” page where you can select the Kirby plugin to download as ZIP-file. Unpack and copy it to `/site/plugins/kirby-avalex`.

Alternatively, you may want to install the plugin using composer:

	composer require avalexgmbh/kirby-avalex

## Setup

Extend your project’s config file with your avalex API key as shown on avalex’ “Installation” page:

```php
# /site/config/config.php

<?php

return [
  'avalex.avalex' => [
    'apikey' => 'MySuperSecretApiKey'
  ]
];
```

Your domain will be detected automatically.

## Usage

### Option A: Create A New Legal Text Page

Add or update any appropriate pages section in your blueprints (for instance in `site.yml`) and append the template `avalex`, in order to display and allow the creation of new avalex legal text pages:

```yaml
# /site/blueprints/site.yml

sections:
  pages:
    type: pages
    templates: 
      - avalex
      ...
```

On an avalex legal text page in the panel you will find a `radio` field where you can select the legal text to be displayed on that particular page.

> Please note that for using the legal text types “Conditions” (German “Allgemeine Geschäftsbedingungen”) and “Revocation” (German “Widerrufsbelehrung”) you need an appropriate avalex subscription!

### Option B: Integrate Into An Existing Legal Text Page

#### Insert Helper Function

Instead of creating a new legal text page based on the plugin’s `avalex` template (as shown above) you may also update an existing page by simply inserting a function call into its template where the legal text should display:

```php
# /site/templates/legal.php

<html>
<body>
  <h1><?= $page->title() ?></h1>
  <article class="legal">
    <?= avalex('disclaimer') ?>
  </article>
</body>
</html>
```

The only required parameter for the `avalex()` helper function is the type of legal text you want to display. Chose from one of the following options:

- `imprint`
- `disclaimer`
- `conditions`
- `revocation`

Alternatively, instead of specifying the legal text type as a string you may provide the function a `\Kirby\Content\Field`[^1] instance that prints to one of the options above.

#### Providing Options

Additionally, you may specify your avalex credentials (domain and API key) in the function’s optional second `$options` parameter:

```php
<?= avalex("disclaimer", [
  'domain' => 'example.com',
  'apikey' => 'MySuperSecretApiKey'
]) ?>
```

This can be useful if you have laid out some fields in your custom legal text page’s blueprint where you capture the credential data (instead of putting them into the project config as mentioned above):

```php
<?= avalex("disclaimer", [
  'domain' => $page->avalex_domain(),
  'apikey' => $page->avalex_apikey()
]) ?>
```

In either case, credentials provided in the `$options` array will override those in the project config (if specified).

The `$options` array supports the following entries:

| Key | Type | Value |
| --- | --- | --- |
| `domain` | `string`\|`\Kirby\Content\Field`[^1] | avalex credentials, domain part |
| `apikey` | `string`\|`\Kirby\Content\Field`[^1] | avalex credentials, API key part |
| `language` | `string`\|`\Kirby\Cms\Language` | Language code like `de` or `en`. The plugin will load the legal text in the given language, provided that it is supported by avalex. |
| `force_update` | `boolean` | Force the API call, even if the cached version of the legal text is not outdated yet. **Be careful using this option, since local caching is an avalex requirement and disrespecting it may result in being banned from further API calls!** |

### Languages

See the [Kirby guide](https://getkirby.com/docs/guide/languages) for details about languages and multi language site setups.

In a single language setup – i.e. Kirby’s `languages` option is either not set, or set to `false`, or no languages have been activated yet in the panel – the plugin loads all legal texts from the API in German language (code `"de"`) by default. If your project’s language is different you have to specify the appropriate language code in the project configuration:

```php
# /site/config/config.php

<?php

return [
  'avalex.avalex' => [
    'language' => 'en'
  ]
];
```

> Please note that avalex supports only a limited set of languages. Please refer to [avalex](https://avalex.de) for supported languages.

If your language is not supported by avalex it will return an HTML structure consisting of a `<div>` container only with no visible content.

In a Kirby multi language setup the plugin asks the Kirby core for the current language and uses it for loading the texts. The plugin’s `language` option mentioned above will be ignored in this case.

Nevertheless, in either case (single or multi language) you may provide a custom language code in the `avalex()` function’s `$options` parameter which will override any of the plugin’s other language options:

```php
<?= avalex("disclaimer", [
  'language' => 'fr' // French version required!
]) ?>
```

### Blueprints & Templates

The plugin provides some Kirby blueprints and templates which you can use for your convenience to create your custom legal text pages.

#### /blueprints/pages/avalex.yml

A simple legal text page blueprint that provides a `radio` field to select one of the legal text types to be displayed on the page (see [Option A](#option-a-create-new-legal-text-page) above). There is also a `stats` section telling you some details about your current avalex configuration, ie. if the API key is entered correctly, and a preview button (which works similar to Kirby’s page preview).

#### /blueprints/fields/avx_select_resource.yml

A `radio` field blueprint to select the legal text type (actually, this component is used in the above page blueprint).

```yaml
# /site/blueprints/pages/mylegalpage.yml

title: My Legal Page
fields:
  legal_text:
    extends: fields/avx_select_resource
    label: Select Legal Text
```

```php
# /site/templates/mylegalpage.php

<html>
<body>
  <h1><?= $page->title() ?></h1>
  <article class="legal">
    <?= avalex($page->legal_text()) ?>
  </article>
</body>
</html>
```

#### /blueprints/fields/avx_select_resource_multiple.yml

A `structure` field to select multiple legal text types to be displayed on a single page – if adequate for your particular use case. As with the page blueprint this also incorporates the `avx_select_resource.yml` field.

```yaml
# /site/blueprints/pages/mylegalpage.yml

title: My Legal Page
fields:
  legal_texts:
    extends: fields/avx_select_resource_multiple
    label: Select Legal Texts
```

```php
# /site/templates/mylegalpage.php

<html>
<body>
  <h1><?= $page->title() ?></h1>
  <?php foreach($page->legal_texts()->toStructure() as $legal): ?>
    <article class="legal">
      <?= avalex($legal->resource()) ?>
    </article>	  
  <?php endforeach ?>
</body>
</html>
```

#### /templates/avalex.php

This template is used for rendering legal text pages by default using the above-mentioned page blueprint. The basic template layout is inspired by [Kirby’s Starterkit](https://github.com/getkirby/starterkit).

You can provide a custom template with the same name (under `/site/templates/avalex.php`) in order to override the plugin’s template with your own.

## Options

The plugin provides several configuration options which you may override in your project’s config:

| Option | Default | Description |
| --- | --- | --- |
| `domain` | `""` | avalex credentials, domain part |
| `apikey` | `""` | avalex credentials, API key part |
| `language` | `"de"` | Default language code |
| `active` | `true` | Activate loading legal texts from the avalex API |
| `replace-references` | `true` | Parse the legal text loaded from the API and replace image URLs referencing avalex.de with their binary data (in form of Data-URLs) in order to avoid cross site cookie issues. |
| `log` | `false` | Log certain events under `/site/logs/avalex.log` (or the custom `logs` root you may have configured for your project). |
| `logfile` | `avalex.log` | Name of the log file. If you include a path in this setting, e.g. `some/path/avalex.log`, it will automatically be created under your project’s `logs` root as such: `/site/logs/some/path/avalex.log`. |

## Troubleshooting

Please activate logging by setting the `log` option to `true`:

```php
# /site/config/env.php

<?php
return [
  'avalex.avalex' => [
    'log' => true
  ]
]
```

In most cases, the information in the log file should be sufficient to resolve the respective problem.



[^1]: In Kirby 3 you have to use a `\Kirby\Cms\Field` instance instead!
