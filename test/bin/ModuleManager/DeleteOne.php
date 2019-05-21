<?php

namespace BFW\Test\Bin\ModuleManager;

use Exception;
use bultonFr\Utils\Cli\BasicMsg;
use BFW\Test\Bin\Ressources\LogLineTesterTrait;

class DeleteOne extends AbstractModuleManagerTests
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

        $cmd       = 'cd '.$this->installDir.' && ./vendor/bin/bfwDelMod -- bfw-test-install';
        $cmdOutput = $this->execCmd($cmd);

        $expectedOutput = ""
            ."\033[0;33m> Delete module bfw-test-install ... \033[0m\033[0;32mDone\033[0m\n"
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
        if (count($this->logRecords) !== 4) {
            BasicMsg::displayMsgNL('Fail : Number of line not equal to 4', 'red', 'bold');
            return false;
        }

        try {
            //Line 0 [2019-05-17 10:18:51] bfw.DEBUG: Module - Read module info {"name":"bfw-test-install","path":"/opt/Projects/bfw/bfw/test/install/app/modules/available/bfw-test-install"} []
            $this->checkLogLineMsg(0, 'Module - Read module info');
            $this->checkLogLineContextKeys(0, ['name', 'path']);
            $this->checkLogLineContextKeyEqual(0, 'name', 'bfw-test-install');
            $this->checkLogLineContextKeyContain(0, 'path', '/test/install/app/modules/available/bfw-test-install');

            //Line 1 [2019-05-17 10:18:51] bfw.DEBUG: FileManager - Remove symlink {"linkFile":"/opt/Projects/bfw/bfw/test/install/app/modules/available/bfw-test-install"} []
            $this->checkLogLineMsg(1, 'FileManager - Remove symlink');
            $this->checkLogLineContextKeys(1, ['linkFile']);
            $this->checkLogLineContextKeyContain(1, 'linkFile', '/test/install/app/modules/available/bfw-test-install');

            //Line 2 [2019-05-17 10:18:51] bfw.DEBUG: Module - Delete config files {"name":"bfw-test-install","configPath":"/opt/Projects/bfw/bfw/test/install/app/config/bfw-test-install"} []
            $this->checkLogLineMsg(2, 'Module - Delete config files');
            $this->checkLogLineContextKeys(2, ['name', 'configPath']);
            $this->checkLogLineContextKeyEqual(2, 'name', 'bfw-test-install');
            $this->checkLogLineContextKeyContain(2, 'configPath', '/test/install/app/config/bfw-test-install');

            //Line 3 [2019-05-17 10:18:51] bfw.DEBUG: FileManager - Remove files and directories {"path":"/opt/Projects/bfw/bfw/test/install/app/config/bfw-test-install"} []
            $this->checkLogLineMsg(3, 'FileManager - Remove files and directories');
            $this->checkLogLineContextKeys(3, ['path']);
            $this->checkLogLineContextKeyContain(3, 'path', '/test/install/app/config/bfw-test-install');
        } catch (Exception $e) {
            BasicMsg::displayMsgNL('Fail : '.$e->getMessage(), 'red', 'bold');
            return false;
        }

        BasicMsg::displayMsgNL('OK', 'green', 'bold');
        return true;
    }
}
