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
    public static function getCsvRowProcessor($header)
    {
        $colNumbers = array_flip($header);

        return function ($row) use ($colNumbers) {
            $res = [];
            $res['dataSizeBytes'] = doubleval($row[$colNumbers['g_projectSizeMB']]);
            $res['rowsCount'] = intval($row[$colNumbers['g_projectRows']]);
            $res['usersCount'] = intval($row[$colNumbers['g_users']]);
            $res['kbc'] = [
                'project' => [
                    'id' => $row[$colNumbers['projectId']],
                    'name' => $row[$colNumbers['projectName']]
                ],
                'configuration' => [
                    'id' => $row[$colNumbers['writerId']],
                    'name' => $row[$colNumbers['g_project_name']]
                ]
            ];
            $res['created'] = date('c');

            return $res;
        };
    }

}