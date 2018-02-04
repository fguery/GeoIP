<?php

namespace GeoIP;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use MongoDB\Client as MongoClient;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Monolog\Handler\StreamHandler;

/**
 * Class ServiceProvider
 *
 * @author Fabrice Guery <fabrice@workdigital.co.uk>
 */
class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $c)
    {
        // Duplicate of Slim level 'settings' so they don't conflict
        $c['config'] = function () use ($c) {
            return include __DIR__ . '/settings.php';
        };

        $c['logger'] = function ($c) {
            $settings = $c['config']['logger'];
            $logger = new Logger($settings['name']);
            $logger->pushProcessor(new UidProcessor());
            $logger->pushHandler(new StreamHandler($settings['path'], $settings['level']));
            return $logger;
        };

        $c['mongo'] = function () use ($c) {
            return new MongoClient(
                $c['config']['db'],
                [
                    'w' => 1,
                    'serverSelectionTryOnce' => false,
                ]
            );
        };
    }
}
