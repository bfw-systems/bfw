<?php

namespace BFW\Test\Bin\ModuleManager;

use Exception;
use bultonFr\Utils\Cli\BasicMsg;
use Dubture\Monolog\Reader\LogReader;

class LoadedAll extends AbstractModuleManagerTests
{
    protected function testsList(): array
    {
        return [
            [$this, 'checkMonologRecords']
        ];
    }

    protected function checkMonologRecords(): bool
    {
        BasicMsg::displayMsg('> Check bfw logs : ', 'yellow');

        exec('curl -s -I http://localhost:8000');

        $logRecords = new LogReader($this->logFilePath);
        if (count($logRecords) === 0) {
            BasicMsg::displayMsgNL('Fail : No log to read', 'red', 'bold');
            return false;
        }

        $logNewModule  = [];
        $logLoadModule = [];

        foreach ($logRecords as $record) {
            if (empty($record)) {
                continue;
            }

            if ($record['message'] === 'New module declared') {
                $logNewModule[] = $record;
            }

            if ($record['message'] === 'Load module') {
                $logLoadModule[] = $record;
            }
        }

        if (count($logNewModule) !== 2) {
            BasicMsg::displayMsgNL('Fail : One or all modules has not been found', 'red', 'bold');
            return false;
        }

        if (count($logLoadModule) !== 2) {
            BasicMsg::displayMsgNL('Fail : One or all modules has not been loaded', 'red', 'bold');
            return false;
        }

        BasicMsg::displayMsgNL('OK', 'green', 'bold');
        return true;
    }
}
