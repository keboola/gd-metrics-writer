<?php
/**
 * Created by PhpStorm.
 * User: miroslavcillik
 * Date: 27/11/15
 * Time: 16:25
 */

namespace Keboola\MetricsWriter;

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

    public function write($data)
    {
        $this->client->addEvent($this->collectionName, $data);
    }

}