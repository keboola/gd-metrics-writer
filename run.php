<?php

use	Keboola\Juicer\Common\Logger;
use Keboola\Juicer\Exception\ApplicationException;
use Keboola\Juicer\Exception\UserException;
use Keboola\MetricsWriter\Application;
use Symfony\Component\Yaml\Yaml;

require_once(dirname(__FILE__) . "/bootstrap.php");

Logger::initLogger(APP_NAME);

try {
    $arguments = getopt("d::", ["data::"]);
    if (!isset($arguments["data"])) {
        throw new UserException('Data folder not set.');
    }

    $config = Yaml::parse(file_get_contents($arguments["data"] . "/config.yml"));
    $config['dataFolder'] = $arguments['data'];

    $app = new Application($config);
    $app->run();

} catch(UserException $e) {

    Logger::log('error', $e->getMessage(), (array) $e->getData());
    exit(1);

} catch(ApplicationException $e) {
    Logger::log('error', $e->getMessage(), (array) $e->getData());
    exit($e->getCode() > 1 ? $e->getCode(): 2);

} catch(\Exception $e) {

    Logger::log('error', $e->getMessage(), [
        'errFile' => $e->getFile(),
        'errLine' => $e->getLine(),
        'trace' => $e->getTrace()
    ]);
    exit(2);
}

Logger::log('info', "Writer finished successfully.");
exit(0);