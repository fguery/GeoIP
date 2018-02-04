<?php
namespace GeoIP\Commands;

use GeoIP\Models\GeoIp;
use GeoIP\ServiceProvider;
use Pimple\Container;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Import
 */
class Import extends Command
{
    /** @var GeoIp */
    private $geoIpModel;

    public function configure()
    {
        $this->setName('import');
        $this->setDescription('Import the data from the passed file into the DB');
        $this->addOption(
            'file',
            null,
            InputOption::VALUE_REQUIRED,
            'File containing CSV geolocation information',
            'http://download.db-ip.com/free/dbip-city-2018-02.csv.gz'
        );
    }

    public function initialize(InputInterface $input, OutputInterface $output)
    {
        $container = new Container(
            ['settings' => include __DIR__ . '/../settings.php']
        );
        $serviceProvider = new ServiceProvider();
        $container->register($serviceProvider);
        $this->geoIpModel = $container['geoIpModel'];
        $this->geoIpModel->prepareCollection();
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $fileName = $input->getOption('file');
        if (empty($fileName)) {
            throw new \InvalidArgumentException('File cannot be empty');
        }
        $fileName = $this->decompressFile($fileName);
        $file = fopen($fileName, 'r');
        $data = [];
        $i = 0;
        while (false !== ($line = fgetcsv($file))) {
            $data[] = [
                'min' => inet_pton($line[0]),
                'max' => inet_pton($line[1]),
                'country' => $line[2],
                'state' => $line[3],
                'city' => $line[4]
            ];
            if ($i % 1000 === 0) {
                $this->geoIpModel->insertMany($data);
                $data = [];
            }
            $i++;
        }
        $this->geoIpModel->insertMany($data);
    }

    protected function ipToInt($ip)
    {


    }

    /**
     * Decompress the passed file, if required. Works on HTTP or on local files.
     *
     * @param string $fileName
     * @return string
     */
    protected function decompressFile($fileName)
    {
        $outputFileName = sys_get_temp_dir() . '/geoIpData.csv';
        if (!preg_match('/^http/i', $fileName)) {
            if (!is_file($fileName) || !is_readable($fileName)) {
                throw new \InvalidArgumentException('File must be readable.');
            }
        }
        if (substr($fileName, -3) === '.gz') {
            $fileName = 'compress.zlib://' . $fileName;
        }

        $file = fopen($fileName, 'rb');
        $outputFile = fopen($outputFileName, 'w');

        while (!feof($file)) {
            $data = fread($file, 1024*1024);
            fwrite($outputFile, $data);
        }
        fclose($file);
        fclose($outputFile);
        return $outputFileName;
    }
}
