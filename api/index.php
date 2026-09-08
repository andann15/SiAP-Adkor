<?php

// On Vercel, the filesystem is read-only except /tmp
// We need to set up writable directories in /tmp before the app boots

$tmpStorage = '/tmp/storage';
$dirs = [
    $tmpStorage,
    $tmpStorage . '/app',
    $tmpStorage . '/app/public',
    $tmpStorage . '/framework',
    $tmpStorage . '/framework/cache',
    $tmpStorage . '/framework/cache/data',
    $tmpStorage . '/framework/sessions',
    $tmpStorage . '/framework/testing',
    $tmpStorage . '/framework/views',
    $tmpStorage . '/logs',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
}

// Also make bootstrap/cache writable by pointing to /tmp
$tmpBootstrapCache = '/tmp/bootstrap/cache';
if (!is_dir($tmpBootstrapCache)) {
    mkdir($tmpBootstrapCache, 0775, true);
}

// Copy existing cache files to /tmp if they exist in the project
$projectBootstrapCache = __DIR__ . '/../bootstrap/cache';
if (is_dir($projectBootstrapCache)) {
    foreach (['packages.php', 'services.php'] as $cacheFile) {
        $src = $projectBootstrapCache . '/' . $cacheFile;
        $dst = $tmpBootstrapCache . '/' . $cacheFile;
        if (file_exists($src) && !file_exists($dst)) {
            copy($src, $dst);
        }
    }
}

require __DIR__ . '/../public/index.php';
