<?php

namespace BFW\Test\Bin\ModuleManager;

use Exception;
use bultonFr\Utils\Cli\BasicMsg;
use BFW\Test\Bin\Ressources\LogLineTesterTrait;

class EnableAll extends AbstractModuleManagerTests
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

        $cmd       = 'cd '.$this->installDir.' && ./vendor/bin/bfwEnMod -a';
        $cmdOutput = $this->execCmd($cmd);

        $expectedOutput = ""
            ."\033[0;33m> Enable module bfw-hello-world ... \033[0m\033[0;32mDone\033[0m\n"
            ."\033[0;33m> Enable module bfw-test-install ... \033[0m\033[0;32mDone\033[0m\n"
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
            //Line 0 [2019-05-17 10:03:03] bfw.DEBUG: Module - Read module info {"name":"bfw-hello-world","path":"/opt/Projects/bfw/bfw/test/install/app/modules/available/bfw-hello-world"} []
            $this->checkLogLineMsg(0, 'Module - Read module info');
            $this->checkLogLineContextKeys(0, ['name', 'path']);
            $this->checkLogLineContextKeyEqual(0, 'name', 'bfw-hello-world');
            $this->checkLogLineContextKeyContain(0, 'path', '/test/install/app/modules/available/bfw-hello-world');

            //Line 1 [2019-05-17 10:03:03] bfw.DEBUG: FileManager - Create symlink {"linkTarget":"/opt/Projects/bfw/bfw/test/install/app/modules/available/bfw-hello-world/src/","linkFile":"/opt/Projects/bfw/bfw/test/install/app/modules/enabled/bfw-hello-world"} []
            $this->checkLogLineMsg(1, 'FileManager - Create symlink');
            $this->checkLogLineContextKeys(1, ['linkTarget', 'linkFile']);
            $this->checkLogLineContextKeyContain(1, 'linkTarget', '/test/install/app/modules/available/bfw-hello-world/src/');
            $this->checkLogLineContextKeyContain(1, 'linkFile', '/test/install/app/modules/enabled/bfw-hello-world');

            //Line 2 [2019-05-17 10:03:03] bfw.DEBUG: Module - Read module info {"name":"bfw-test-install","path":"/opt/Projects/bfw/bfw/test/install/app/modules/available/bfw-test-install"} []
            $this->checkLogLineMsg(2, 'Module - Read module info');
            $this->checkLogLineContextKeys(2, ['name', 'path']);
            $this->checkLogLineContextKeyEqual(2, 'name', 'bfw-test-install');
            $this->checkLogLineContextKeyContain(2, 'path', '/test/install/app/modules/available/bfw-test-install');

            //Line 3 [2019-05-17 10:03:03] bfw.DEBUG: FileManager - Create symlink {"linkTarget":"/opt/Projects/bfw/bfw/test/install/app/modules/available/bfw-test-install/src/","linkFile":"/opt/Projects/bfw/bfw/test/install/app/modules/enabled/bfw-test-install"} []
            $this->checkLogLineMsg(3, 'FileManager - Create symlink');
            $this->checkLogLineContextKeys(3, ['linkTarget', 'linkFile']);
            $this->checkLogLineContextKeyContain(3, 'linkTarget', '/test/install/app/modules/available/bfw-test-install/src/');
            $this->checkLogLineContextKeyContain(3, 'linkFile', '/test/install/app/modules/enabled/bfw-test-install');
        } catch (Exception $e) {
            BasicMsg::displayMsgNL('Fail : '.$e->getMessage(), 'red', 'bold');
            return false;
        }

        BasicMsg::displayMsgNL('OK', 'green', 'bold');
        return true;
    }
}
