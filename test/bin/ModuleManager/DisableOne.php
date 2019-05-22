<?php

namespace BFW\Test\Bin\ModuleManager;

use Exception;
use bultonFr\Utils\Cli\BasicMsg;
use BFW\Test\Bin\Ressources\LogLineTesterTrait;

class DisableOne extends AbstractModuleManagerTests
{
    use LogLineTesterTrait;

    protected function testsList(): array
    {
        return [
            [$this, 'checkCmdOutput'],
            [$this, 'checkMonologRecords']
        ];
    }

    protected function checkCmdOutput(): bool
    {
        BasicMsg::displayMsg('> Check command output : ', 'yellow');

        $cmd       = 'cd '.$this->installDir.' && ./vendor/bin/bfwDisMod -- bfw-test-install';
        $cmdOutput = $this->execCmd($cmd);

        $expectedOutput = ""
            ."\033[0;33m> Disable module bfw-test-install ... \033[0m\033[0;32mDone\033[0m\n"
        ;

        if ($cmdOutput === $expectedOutput) {
            BasicMsg::displayMsgNL('OK', 'green', 'bold');
            return true;
        }

        BasicMsg::displayMsgNL('Fail', 'red', 'bold');
        return false;
    }

    protected function checkMonologRecords(): bool
    {
        BasicMsg::displayMsg('> Check bfw logs : ', 'yellow');

        $this->logRecords = $this->obtainMonologRecords($this->logFilePath);
        if (count($this->logRecords) !== 2) {
            BasicMsg::displayMsgNL('Fail : Number of line not equal to 2', 'red', 'bold');
            return false;
        }

        try {
            //Line 0 [2019-05-17 10:15:49] bfw.DEBUG: Module - Read module info {"name":"bfw-test-install","path":"/opt/Projects/bfw/bfw/test/install/app/modules/available/bfw-test-install"} []
            $lineNb = 0;
            $this->checkLogLineMsg($lineNb, 'Module - Read module info');
            $this->checkLogLineContextKeys($lineNb, ['name', 'path']);
            $this->checkLogLineContextKeyEqual($lineNb, 'name', 'bfw-test-install');
            $this->checkLogLineContextKeyContain($lineNb, 'path', '/test/install/app/modules/available/bfw-test-install');

            //Line 1 [2019-05-17 10:15:49] bfw.DEBUG: FileManager - Remove symlink {"linkFile":"/opt/Projects/bfw/bfw/test/install/app/modules/enabled/bfw-test-install"} []
            $lineNb++;
            $this->checkLogLineMsg($lineNb, 'FileManager - Remove symlink');
            $this->checkLogLineContextKeys($lineNb, ['linkFile']);
            $this->checkLogLineContextKeyContain($lineNb, 'linkFile', '/test/install/app/modules/enabled/bfw-test-install');
        } catch (Exception $e) {
            BasicMsg::displayMsgNL('Fail : '.$e->getMessage(), 'red', 'bold');
            return false;
        }

        BasicMsg::displayMsgNL('OK', 'green', 'bold');
        return true;
    }
}
