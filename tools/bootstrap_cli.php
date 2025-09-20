<?php

/**
 * Tools bootstrap file for usage in CLI environment
 */

if (PHP_SAPI !== 'cli') {
    die("*** CLI required! ***\n");
}

$k_root = $argv[1] ?? '';

if (($plugin_root = getPluginRoot()) === false) {
    die("Please check!\n");
}

if (!$k_root || ($k_root = realpath($k_root)) === false) {
    die("Please specify an existing Kirby installation root folder as argument: {$argv[0]} path/to/kirby\n");
}

if (!verifyKirby($k_root)) {
    die("Please check!\n");
}



function verifyKirby(string $root = ''): bool {
    $composer = $root . '/composer.json';

    if (!file_exists($composer)) {
        echo "Error: composer.json is required in folder {$root}\n";
        return false;
    }

    $data = json_decode(file_get_contents($composer), true);

    if (!array_key_exists('require', $data)
        || ($version = $data['require']['getkirby/cms'] ?? '') === '') {
        echo "Error: composer.json does not contain requirement for 'getkirby/cms'\n";
        return false;
    }

    return true;
}

function getPluginRoot(): string|false {
    $composer = __DIR__ . '/../kirby/composer.json';

    if (!file_exists($composer)) {
        echo "Error: composer.json is required in plugin directory\n";
        return false;
    }

    $data = json_decode(file_get_contents($composer), true);

    $name = $data['extra']['installer-name']
        ?? $data['name']
        ?? '';

    return preg_replace('`^[a-z0-9-_]+/`', '', $name);
}
