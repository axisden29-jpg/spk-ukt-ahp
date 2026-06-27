<?php
# Pindahkan bootstrap cache ke /tmp agar tidak error di Vercel
$appContent = $_SERVER['DOCUMENT_ROOT'] ?? __DIR__;
if (env('APP_ENV') === 'production') {
    config(['app.manifest' => '/tmp/manifest']);
    config(['view.compiled' => '/tmp/views']);
    config(['cache.stores.file.path' => '/tmp/cache']);
    config(['session.files' => '/tmp/sessions']);
}

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

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
