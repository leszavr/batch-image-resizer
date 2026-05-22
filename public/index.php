<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Check if installation is complete
$vendorExists = file_exists(__DIR__.'/../vendor/autoload.php');
$installedFile = __DIR__.'/.installed';

// If not installed, redirect to installer
if (!$vendorExists || !file_exists($installedFile)) {
    // Don't redirect if already on install page
    $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
    if (strpos($requestUri, '/install') !== 0) {
        if (!headers_sent()) {
            header('Location: /install/');
            exit;
        }
        // Fallback if headers already sent
        echo '<script>window.location.href="/install/";</script>';
        echo '<p>Application requires installation. <a href="/install/">Go to installer</a></p>';
        exit;
    }
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
