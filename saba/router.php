<?php
// Simple router for PHP built-in server.
// Usage: php -S 0.0.0.0:8080 router.php

// Decode the requested path
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// If the request maps to an existing file, let the server handle it
$file = __DIR__ . $uri;
if ($uri !== '/' && file_exists($file)) {
    return false;
}

// Route /article/{slug} to article.php?slug={slug}
if (preg_match('#^/article/([^/]+)/?$#', $uri, $m)) {
    $_GET['slug'] = $m[1];
    require __DIR__ . '/article.php';
    exit;
}

// Route /product/{slug} to product.php?slug={slug}
if (preg_match('#^/product/([^/]+)/?$#', $uri, $m)) {
    $_GET['slug'] = $m[1];
    require __DIR__ . '/product.php';
    exit;
}

// Route /blog or /blog.php to blog.php
if ($uri === '/blog' || $uri === '/blog.php') {
    require __DIR__ . '/blog.php';
    exit;
}

// Route /faq or /faq.php to faq.php
if ($uri === '/faq' || $uri === '/faq.php') {
    require __DIR__ . '/faq.php';
    exit;
}

// Fallback to index.php for all other requests
require __DIR__ . '/index.php';
