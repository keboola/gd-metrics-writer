<?php
/**
 * Created by PhpStorm.
 * User: miroslavcillik
 * Date: 27/11/15
 * Time: 16:47
 */

namespace Keboola\MetricsWriter;

use Keboola\Csv\CsvFile;

class Application
{
    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function run()
    {
        $writer = new Writer($this->config['parameters']);

        $tables = $this->config['storage']['input']['tables'];

        foreach ($tables as $table) {

            $csv = new CsvFile($this->getSourceFileName($table));
            $csv->next();

            $header = $csv->current();
            $processor = Processor::getCsvRowProcessor($header);

            $csv->next();

            while ($csv->current() != null) {
                $writer->write($processor($csv->current()));
                $csv->next();
            }
        }
    }

    private function getSourceFileName($tableConfig)
    {
        $dataFolder = $this->config['dataFolder'] . '/in/tables/';
        if (isset($tableConfig['destination'])) {
            return $dataFolder . $tableConfig['destination'];
        }
        return $dataFolder . $tableConfig['source'] . '.csv';
    }

}