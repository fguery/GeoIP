<?php

use GeoIP\Models\GeoIp;
use Slim\Http\Request;
use Slim\Http\Response;

// Routes

/** @var GeoIp $geoIpModel */
$geoIpModel = $app->getContainer()['geoIpModel'];

$app->get('/lookup', function (Request $request, Response $response) use ($geoIpModel) {

    $ip = $request->getParam('ip');
    if (!filter_var($ip, FILTER_VALIDATE_IP)) {
        return $response->withStatus(400, 'Invalid IP address');
    }
    $result = $geoIpModel->findOneByIp($ip);
    if (is_array($result)) {
        $response = $response->withJson([
            'city' => $result['city'],
            'region' => $result['region'],
            'ip' => $ip,
            'rangeStart' => $result['range_start'],
            'rangeEnd' => $result['range_end'],
        ]);
    } elseif ($result === GeoIp::NOT_FOUND) {
        $response = $response->withStatus(404, 'Unable to find correct geolocation');
    } elseif ($result === GeoIp::UNKNOWN_ERROR) {
        $response = $response->withStatus(500, 'Error querying the database');
    }

    return $response;
});
