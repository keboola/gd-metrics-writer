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

    private $collectionName;

    public function __construct(array $params)
    {
        $this->collectionName = $params['keenio']['collectionName'];

        $this->client = KeenIOClient::factory([
            'projectId' => $params['keenio']['projectId'],
            'writeKey'  => $params['keenio']['writeKey'],
            'readKey'   => $params['keenio']['readKey']
        ]);
    }

    public function write(CsvFile $csv)
    {
        $csv->next();
        $header = $csv->current();
        $processor = Processor::getCsvRowProcessor($header);
        $csv->next();

        $eventsCnt = 0;
        while ($csv->current() != null) {
            $batch = [];
            for ($i=0; $i<1000 && $csv->current() != null; $i++) {
                $batch[] = $processor($csv->current());
                $csv->next();
            }

            $result = $this->client->addEvents([$this->collectionName => $batch]);
            $eventsCnt += count($result['gooddata-metrics']);
        }

        Logger::log('info', sprintf('Created %s events.', $eventsCnt));
    }
}