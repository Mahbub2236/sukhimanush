<?php

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * This file allows us to emulate Apache's "mod_rewrite" functionality from the
 * built-in PHP web server. This provides a convenient way to test a Laravel
 * application without having installed a "real" web server software here.
 */
$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? ''
);

// This file allows us to emulate Apache's "mod_rewrite" functionality from the
// built-in PHP web server. Only serve actual static files, not directory collisions.
if ($uri !== '/' && file_exists(__DIR__.'/public'.$uri) && ! is_dir(__DIR__.'/public'.$uri)) {
    $filePath = __DIR__.'/public'.$uri;
    $mimeType = match (pathinfo($filePath, PATHINFO_EXTENSION)) {
        'jpg', 'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'webp' => 'image/webp',
        'svg' => 'image/svg+xml',
        'css' => 'text/css',
        'js' => 'application/javascript',
        default => mime_content_type($filePath) ?: 'application/octet-stream',
    };
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, OPTIONS');
    header('Access-Control-Allow-Headers: *');
    header('Content-Type: '.$mimeType);
    header('Content-Length: '.filesize($filePath));
    readfile($filePath);
    exit;
}

require_once __DIR__.'/public/index.php';
