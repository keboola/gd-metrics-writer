<?php
/**
 * Created by PhpStorm.
 * User: miroslavcillik
 * Date: 27/11/15
 * Time: 16:25
 */

namespace Keboola\MetricsWriter;

use Keboola\Csv\CsvFile;
use Keboola\Juicer\Common\Logger;
use KeenIO\Client\KeenIOClient;

class Writer
{
    /** @var KeenIOClient */
    private $client;

    private $processorConfig;

    private $dataFolder;

    public function __construct(array $params)
    {
        $this->client = KeenIOClient::factory([
            'projectId' => $params['keenio']['projectId'],
            'writeKey'  => $params['keenio']['writeKey'],
            'readKey'   => $params['keenio']['readKey']
        ]);

        $this->processorConfig = $params['processor'];
    }

    /**
     * @param $table
     * @throws \Keboola\Juicer\Exception\ApplicationException
     */
    public function write($table)
    {
        $tableName = $this->getTableName($table);
        $csv = new CsvFile($this->getSourceFileName($table));

        $csv->next();
        $header = $csv->current();

        $processorFactory = new Processor($this->processorConfig[$tableName]);
        $processor = $processorFactory->getProcessor($header);

        $csv->next();
        $eventsCnt = 0;
        while ($csv->current() != null) {
            $batch = [];
            for ($i=0; $i<1000 && $csv->current() != null; $i++) {
                $batch[] = $processor($csv->current());
                $csv->next();
            }

            $result = $this->client->addEvents([$tableName => $batch]);
            $eventsCnt += count($result[$tableName]);
        }

        Logger::log('info', sprintf('Created %s events.', $eventsCnt));
    }

    private function getSourceFileName($tableConfig)
    {
        $dataFolder = $this->dataFolder . '/in/tables/';
        if (isset($tableConfig['destination'])) {
            return $dataFolder . $tableConfig['destination'];
        }
        return $dataFolder . $tableConfig['source'] . '.csv';
    }

    private function getTableName($tableConfig)
    {
        $tableArr = explode('.', $tableConfig['source']);
        return array_pop($tableArr);
    }
}