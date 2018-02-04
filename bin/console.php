#!/usr/local/bin/php
<?php
declare(strict_types=1);
set_time_limit(0);

require_once __DIR__ . '/../vendor/autoload.php';

use GeoIP\Commands\Import;
use Symfony\Component\Console\Application;

$application = new Application("GeoIP command line app");

$application->addCommands([
    new Import()
]);

$application->run();
