<?php

/**
 * Compile the plugin's translations file from the languages installed in a
 * Kirby instance
 *
 * @var string $k_root Root folder of Kirby instance
 * @var string $plugin_root
 */

require_once __DIR__ . '/../bootstrap_cli.php';

// include Kirby env ...
require_once $k_root . '/kirby/bootstrap.php';



use Kirby\Filesystem\F;
use Kirby\Toolkit\A;
use Kirby\Toolkit\Str;

if (kirby()->languages()->count() === 0) {
    die("No languages installed in Kirby instance: {$k_root}\n");
}

$translations = [];

/** @var \Kirby\Cms\Language $language */
foreach (kirby()->languages() as $language) {
    $code = $language->code();

    echo "Language detected: {$code}";

    if ($vars = $language->translations()) {
        $translations[$code] = $vars;
        echo " (" . count($vars) . " variables)";
    } else {
        echo " - No variables found!";
    }

    echo "\n";
}

createTranslationsFile($translations, $plugin_root);
createCsvFile($translations);



function createTranslationsFile(array $translations, string $plugin_root) {
    $php_tpl = <<<PHP
<?php

/**
 * Translations for multiple languages
 */

return {translations};
PHP;

    $php = Str::template($php_tpl, [
        'translations' => exportArray($translations)
    ]);

    $translations_file = kirby()->root('plugins') . "/{$plugin_root}/include/translations.php";
    F::write($translations_file, $php);

    echo "Translations file updated: {$translations_file}\n";
}

function exportArray(array $array, int $level = 1): string {
    $pad = str_repeat('    ', $level);
    $pad_less = str_repeat('    ', $level > 0 ? $level - 1 : $level);
    $lines = [];

    foreach ($array as $key => $value) {
        if (is_array($value)) {
            $lines[] = Str::template("{{pad}}'{{key}}' => {{entries}},", [
                'pad' => $pad,
                'key' => $key,
                'entries' => exportArray($value, $level + 1),
            ]);

        } else {
            $value = match(gettype($value)) {
                'string' => Str::template("'{{string}}'", [
                    'string' => Str::replace($value, "'", "\'")
                ]),
                'bool', 'boolean' => $value === true ? 'true' : 'false',
                'null' => 'null',
                default => $value,
            };

            $lines[] = Str::template("{{pad}}'{{key}}' => {{value}},", [
                'pad' => $pad,
                'key' => $key,
                'value' => $value,
            ]);
        }
    }

    return Str::template('[{{eol}}{{pad}}{{entries}}{{eol}}{{pad_less}}]', [
        'pad' => $pad,
        'pad_less' => $pad_less,
        'eol' => PHP_EOL,
        'entries' => trim(A::join($lines, PHP_EOL)),
    ]);
}

function createCsvFile(array $translations) {
    $header = ['Key'];
    $data = [];

    // create data structure
    foreach ($translations as $code => $vars) {
        $header[] = Str::upper($code);
        $column = count($header);

        ksort($vars);

        foreach ($vars as $key => $value) {
            $line = A::get($data, $key, []);

            if (!$line) {
                $line[] = $key;
            }

            $line[$column] = $value;

            $data[$key] = $line;
        }
    }

    // write file
    $csv = [];
    $csv[] = createCsvRow($header);

    foreach ($data as $row) {
        $csv[] = createCsvRow($row);
    }

    $now = (new DateTime('now', new DateTimeZone('Europe/Berlin')))->format('Y-m-d_Hi');
    $csv_file = __DIR__ . "/translations_{$now}.csv";
    F::write($csv_file, A::join($csv, "\n"));

    echo "CSV file updated: {$csv_file}\n";
}

function createCsvRow(array $row, string $glue = ';'): string {
    $cells = A::map($row, function($cell) {
        return Str::template('"{cell}"', [
            'cell' => Str::replace($cell, '"', '""')
        ]);
    });

    return A::join($cells, $glue);
}
