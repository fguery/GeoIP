<?php

namespace Tests\Unit;

use GeoIP\Commands\Import;
use GeoIP\Models\GeoIp;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class ImportTest
 *
 * @author Fabrice Guery <fabrice@workdigital.co.uk>
 */
class ImportTest extends \PHPUnit_Framework_TestCase
{
    /** @var CommandTester */
    protected $commandTester;
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $mockedGeoIp;

    public function setUp()
    {
        $this->mockedGeoIp = $this->getMockBuilder(GeoIp::class)
            ->disableOriginalConstructor()
            ->setMethods(['insertMany', 'createDatabase'])
            ->getMock();
        $app = new Application();
        $import = new Import();
        $import->setGeoIpModel($this->mockedGeoIp);
        $app->add($import);
        $command = $app->find('import');
        $this->commandTester = new CommandTester($command);
    }

    public function testWrongFileErrorsOut()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->commandTester->execute(
            [
                '--file' => 'toto'
            ]
        );
    }

    public function testImportCreatesTheDatabase()
    {
        $this->mockedGeoIp->expects($this->once())
            ->method('createDatabase');

        $this->commandTester->execute(
            [
                '--file' => __DIR__ . '/../fixtures/geoIp.csv'
            ]
        );
    }

    public function testCSVUpdatesTheDB()
    {
        $this->mockedGeoIp->expects($this->atLeastOnce())
            ->method('insertMany');
        $this->commandTester->execute(
            [
                '--file' => __DIR__ . '/../fixtures/geoIp.csv'
            ]
        );
    }

    public function testGZippedCSVUpdatesTheDB()
    {
        $this->mockedGeoIp->expects($this->atLeastOnce())
            ->method('insertMany');
        $this->commandTester->execute(
            [
                '--file' => __DIR__ . '/../fixtures/geoIp.csv.gz'
            ]
        );
    }
}
