<?php

use Slim\Container;

if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

require __DIR__ . '/../vendor/autoload.php';

$settings = require_once __DIR__ . '/../src/settings.php';
// Instantiate the app
$container = new Container(['settings' => $settings]);
$serviceProvider = new GeoIP\ServiceProvider();
$serviceProvider->register($container);
$app = new \Slim\App($container);

// Register routes
require __DIR__ . '/../src/routes.php';

// Run app
$app->run();
