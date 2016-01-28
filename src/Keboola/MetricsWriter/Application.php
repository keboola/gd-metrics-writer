<?php
/**
 * Created by PhpStorm.
 * User: miroslavcillik
 * Date: 27/11/15
 * Time: 16:47
 */

namespace Keboola\MetricsWriter;

class Application
{
    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function run()
    {
        $writer = new Writer($this->config['parameters'], $this->config['dataFolder']);
        $tables = $this->config['storage']['input']['tables'];

        foreach ($tables as $table) {
            $writer->write($table);
        }
    }
}