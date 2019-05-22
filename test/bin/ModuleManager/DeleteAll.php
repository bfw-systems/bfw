<?php

namespace BFW\Test\Bin\ModuleManager;

use Exception;
use bultonFr\Utils\Cli\BasicMsg;
use BFW\Test\Bin\Ressources\LogLineTesterTrait;

class DeleteAll extends AbstractModuleManagerTests
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

        $cmd       = 'cd '.$this->installDir.' && ./vendor/bin/bfwDelMod -a';
        $cmdOutput = $this->execCmd($cmd);

        $expectedOutput = ""
            ."\033[0;33m> Delete module bfw-hello-world ... \033[0m\033[0;32mDone\033[0m\n"
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
        if (count($this->logRecords) !== 8) {
            BasicMsg::displayMsgNL('Fail : Number of line not equal to 8', 'red', 'bold');
            return false;
        }

        try {
            //Line 0 [2019-05-17 10:33:26] bfw.DEBUG: Module - Read module info {"name":"bfw-hello-world","path":"/opt/Projects/bfw/bfw/test/install/app/modules/available/bfw-hello-world"} []
            $lineNb = 0;
            $this->checkLogLineMsg($lineNb, 'Module - Read module info');
            $this->checkLogLineContextKeys($lineNb, ['name', 'path']);
            $this->checkLogLineContextKeyEqual($lineNb, 'name', 'bfw-hello-world');
            $this->checkLogLineContextKeyContain($lineNb, 'path', '/test/install/app/modules/available/bfw-hello-world');

            //Line 1 [2019-05-17 10:33:26] bfw.DEBUG: FileManager - Remove symlink {"linkFile":"/opt/Projects/bfw/bfw/test/install/app/modules/available/bfw-hello-world"} []
            $lineNb++;
            $this->checkLogLineMsg($lineNb, 'FileManager - Remove symlink');
            $this->checkLogLineContextKeys($lineNb, ['linkFile']);
            $this->checkLogLineContextKeyContain($lineNb, 'linkFile', '/test/install/app/modules/available/bfw-hello-world');

            //Line 2 [2019-05-17 10:33:26] bfw.DEBUG: Module - Delete config files {"name":"bfw-hello-world","configPath":"/opt/Projects/bfw/bfw/test/install/app/config/bfw-hello-world"} []
            $lineNb++;
            $this->checkLogLineMsg($lineNb, 'Module - Delete config files');
            $this->checkLogLineContextKeys($lineNb, ['name', 'configPath']);
            $this->checkLogLineContextKeyEqual($lineNb, 'name', 'bfw-hello-world');
            $this->checkLogLineContextKeyContain($lineNb, 'configPath', '/test/install/app/config/bfw-hello-world');

            //Line 3 [2019-05-17 10:33:26] bfw.DEBUG: FileManager - Remove files and directories {"path":"/opt/Projects/bfw/bfw/test/install/app/config/bfw-hello-world"} []
            $lineNb++;
            $this->checkLogLineMsg($lineNb, 'FileManager - Remove files and directories');
            $this->checkLogLineContextKeys($lineNb, ['path']);
            $this->checkLogLineContextKeyContain($lineNb, 'path', '/test/install/app/config/bfw-hello-world');

            //Line 4 [2019-05-17 10:33:26] bfw.DEBUG: Module - Read module info {"name":"bfw-test-install","path":"/opt/Projects/bfw/bfw/test/install/app/modules/available/bfw-test-install"} []
            $lineNb++;
            $this->checkLogLineMsg($lineNb, 'Module - Read module info');
            $this->checkLogLineContextKeys($lineNb, ['name', 'path']);
            $this->checkLogLineContextKeyEqual($lineNb, 'name', 'bfw-test-install');
            $this->checkLogLineContextKeyContain($lineNb, 'path', '/test/install/app/modules/available/bfw-test-install');

            //Line 5 [2019-05-17 10:33:26] bfw.DEBUG: FileManager - Remove symlink {"linkFile":"/opt/Projects/bfw/bfw/test/install/app/modules/available/bfw-test-install"} []
            $lineNb++;
            $this->checkLogLineMsg($lineNb, 'FileManager - Remove symlink');
            $this->checkLogLineContextKeys($lineNb, ['linkFile']);
            $this->checkLogLineContextKeyContain($lineNb, 'linkFile', '/test/install/app/modules/available/bfw-test-install');

            //Line 6 [2019-05-17 10:33:26] bfw.DEBUG: Module - Delete config files {"name":"bfw-test-install","configPath":"/opt/Projects/bfw/bfw/test/install/app/config/bfw-test-install"} []
            $lineNb++;
            $this->checkLogLineMsg($lineNb, 'Module - Delete config files');
            $this->checkLogLineContextKeys($lineNb, ['name', 'configPath']);
            $this->checkLogLineContextKeyEqual($lineNb, 'name', 'bfw-test-install');
            $this->checkLogLineContextKeyContain($lineNb, 'configPath', '/test/install/app/config/bfw-test-install');

            //Line 7 [2019-05-17 10:33:26] bfw.DEBUG: FileManager - Remove files and directories {"path":"/opt/Projects/bfw/bfw/test/install/app/config/bfw-test-install"} []
            $lineNb++;
            $this->checkLogLineMsg($lineNb, 'FileManager - Remove files and directories');
            $this->checkLogLineContextKeys($lineNb, ['path']);
            $this->checkLogLineContextKeyContain($lineNb, 'path', '/test/install/app/config/bfw-test-install');
        } catch (Exception $e) {
            BasicMsg::displayMsgNL('Fail : '.$e->getMessage(), 'red', 'bold');
            return false;
        }

        BasicMsg::displayMsgNL('OK', 'green', 'bold');
        return true;
    }
}
