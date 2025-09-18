<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/vendor/autoload.php';
/** @var Application $app */
$app = require_once __DIR__.'/bootstrap/app.php';

//  Tell Laravel public folder is still /public
$app->bind('path.public', function () {
    return __DIR__ . '/public';
});

$app->handleRequest(Request::capture());
