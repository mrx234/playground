<?php

/**
 * Pull content from Avalex by requesting URLs to pages that show Avalex legal texts
 *
 * Usage:
 * $ php pull.php
 * $ http://example.com/pull.php
 */

$is_cli = (PHP_SAPI === 'cli');

if (!$is_cli) {
    header('Content-type: text/plain; charset=utf-8');
}

$urls_file = __DIR__ . '/urls.json';
if (!file_exists($urls_file)) {
    if ($is_cli) {
        die("File missing: urls.json\n");
    } else {
        // die silently on web ...
        exit;
    }
}

$urls = json_decode(file_get_contents($urls_file), true);

foreach ($urls as $url) {
    echo "Pulling URL: {$url} ...";

    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_SSL_VERIFYPEER => false,
    ]);

    $html = curl_exec($ch);
    $status = curl_getinfo($ch)['http_code'];

    if ($status == 200) {
        echo " OK\n";

    } else {
        echo " ERROR!\n";
        echo "*** Status: {$status} ***\n";
        echo "*** Returned: {$html} ***\n";
    }
}
