<?php

/**
 * Created by PhpStorm.
 * User: miroslavcillik
 * Date: 27/11/15
 * Time: 16:41
 */

namespace Keboola\MetricsWriter;

class Processor
{
    private $type;

    public function __construct($type)
    {
        $this->type = $type;
    }

    public function getProcessor($header)
    {
        $functionName = 'get' . ucfirst($this->type) . 'Processor';
        return $this->$functionName($header);
    }

    public static function getMainProcessor($header)
    {
        $colNumbers = array_flip($header);

        return function ($row) use ($colNumbers) {
            $res = [];
            $res['dataSizeBytes'] = doubleval($row[$colNumbers['g_projectSizeMB']] * 1024 * 1024);
            $res['rowsCount'] = intval($row[$colNumbers['g_projectRows']]);
            $res['usersCount'] = intval($row[$colNumbers['g_users']]);
            $res['kbc'] = [
                'project' => [
                    'id' => $row[$colNumbers['projectId']],
                    'name' => $row[$colNumbers['projectName']]
                ],
                'configuration' => [
                    'id' => $row[$colNumbers['writerId']]
                ]
            ];
            $res['created'] = date('c');

            return $res;
        };
    }

    public static function getUsersProcessor($header)
    {
        $colNumbers = array_flip($header);

        return function ($row) use ($colNumbers) {
            $res = [];
            $res['usersCount'] = intval($row[$colNumbers['gdUsersCount']]);
            $res['kbc'] = [
                'project' => [
                    'id' => $row[$colNumbers['projectId']],
                    'name' => $row[$colNumbers['projectName']]
                ]
            ];
            $res['created'] = date('c');

            return $res;
        };
    }

}