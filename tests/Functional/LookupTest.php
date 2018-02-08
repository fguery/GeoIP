<?php

namespace Tests\Functional;

use GeoIP\Commands\Import;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;


/**
 * Class LookupTest
 *
 * @author Fabrice Guery <fabrice@workdigital.co.uk>
 */
class LookupTest extends BaseApiTestCase
{
    public function setUp()
    {
        $application = new Application("GeoIP command line app");

        $application->addCommands([
            new Import()
        ]);
        $application->find('import')->run(
            new ArrayInput([
                'name' => 'input',
                'file' => __DIR__ . '/../fixtures/geoIp.csv'
            ]),
            new NullOutput()
        );
    }



    public function testLookupByValidIpV4ReturnsLocation()
    {
        $response = $this->runApp(
            'GET',
            'lookup?ip=1.2.3.4'
        );
        $body = $response->getBody()->getContents();
        $body = json_decode($body, true);
        $this->assertEquals(
            [
                'city' => 'Brisbane',
                'region' =>'Queensland',
                'ip' => '1.2.3.4',
                'rangeStart' =>'1.2.3.0',
                'rangeEnd' =>'1.2.3.255',
            ],
            $body
        );
    }

    public function testLookupByValidIpV6ReturnsLocation()
    {
        $response = $this->runApp(
            'GET',
            'lookup?ip=2c0f:ffb8:1a2c'
        );
        $body = $response->getBody()->getContents();
        $body = json_decode($body, true);
        $this->assertEquals(
            [
                'city' => 'Khartoum',
                'region' =>'Khartoum',
                'ip' => '2c0f:ffb8:1a2c::',
                'rangeStart' =>'2c0f:ffb8::',
                'rangeEnd' =>'2c0f:ffb8:ffff:ffff:ffff:ffff:ffff:ffff',
            ],
            $body
        );
    }


    public function testLookupByShortenedIpV6ReturnsLocation()
    {
        $response = $this->runApp(
            'GET',
            'lookup?ip=2c0f:ffb8::1a2c'
        );
        $body = $response->getBody()->getContents();
        $body = json_decode($body, true);
        $this->assertEquals(
            [
                'city' => 'Khartoum',
                'region' =>'Khartoum',
                'ip' => '2c0f:ffb8:1a2c::',
                'rangeStart' =>'2c0f:ffb8::',
                'rangeEnd' =>'2c0f:ffb8:ffff:ffff:ffff:ffff:ffff:ffff',
            ],
            $body
        );
    }

    public function testInvalidIp()
    {
        $response = $this->runApp(
            'GET',
            'lookup?ip=zzzz'
        );
        $this->assertEquals(
            400,
            $response->getStatusCode()
        );
    }


    public function testNotSufficientIp()
    {
        $response = $this->runApp(
            'GET',
            'lookup?ip=2c0f:'
        );
        $this->assertEquals(
            400,
            $response->getStatusCode()
        );
        $this->assertEquals(
            'IP too broad for resolving',
            $response->getReasonPhrase()
        );
    }

    public function testNotFoundIp()
    {
        $response = $this->runApp(
            'GET',
            'lookup?ip=2c0f:2c0f:2c0f:2c0f:2c0f:2c0f:2c0f:2c0f'
        );
        $this->assertEquals(
            404,
            $response->getStatusCode()
        );
    }
}
