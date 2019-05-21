<?php

namespace BFW\Test\Bin\ModuleManager;

use Exception;
use bultonFr\Utils\Cli\BasicMsg;
use BFW\Test\Bin\Ressources\LogLineTesterTrait;

class ReinstallOne extends AbstractModuleManagerTests
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

        $cmd       = 'cd '.$this->installDir.' && ./vendor/bin/bfwAddMod -r -- bfw-test-install';
        $cmdOutput = $this->execCmd($cmd);

        $expectedOutput = ""
            ."\033[0;33m> Delete module bfw-test-install ... \033[0m\033[0;32mDone\033[0m\n"
            ."\033[0;33m> Add module bfw-test-install ... \033[0m\033[0;32mDone\033[0m\n"
            ."\033[0;33m> Execute install script for bfw-test-install ... \033[0m"
                ."  \033[1;33mCreate install_test.php file into web directory\033[0m\n"
            ."\033[0;32mDone\033[0m\n"
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
        if (count($this->logRecords) !== 11) {
            BasicMsg::displayMsgNL('Fail : Number of line not equal to 11', 'red', 'bold');
            return false;
        }

        try {
            //Line 0 [2019-05-17 10:33:26] bfw.DEBUG: Module - Read module info {"name":"bfw-test-install","path":"/opt/Projects/bfw/bfw/test/install/app/modules/available/bfw-test-install"} []
            $this->checkLogLineMsg(0, 'Module - Read module info');
            $this->checkLogLineContextKeys(0, ['name', 'path']);
            $this->checkLogLineContextKeyEqual(0, 'name', 'bfw-test-install');
            $this->checkLogLineContextKeyContain(0, 'path', '/test/install/app/modules/available/bfw-test-install');

            //Line 1 [2019-05-17 10:33:26] bfw.DEBUG: FileManager - Remove symlink {"linkFile":"/opt/Projects/bfw/bfw/test/install/app/modules/available/bfw-test-install"} []
            $this->checkLogLineMsg(1, 'FileManager - Remove symlink');
            $this->checkLogLineContextKeys(1, ['linkFile']);
            $this->checkLogLineContextKeyContain(1, 'linkFile', '/test/install/app/modules/available/bfw-test-install');

            //Line 2 [2019-05-17 10:33:26] bfw.DEBUG: Module - Delete config files {"name":"bfw-test-install","configPath":"/opt/Projects/bfw/bfw/test/install/app/config/bfw-test-install"} []
            $this->checkLogLineMsg(2, 'Module - Delete config files');
            $this->checkLogLineContextKeys(2, ['name', 'configPath']);
            $this->checkLogLineContextKeyEqual(2, 'name', 'bfw-test-install');
            $this->checkLogLineContextKeyContain(2, 'configPath', '/test/install/app/config/bfw-test-install');

            //Line 3 [2019-05-17 10:33:26] bfw.DEBUG: FileManager - Remove files and directories {"path":"/opt/Projects/bfw/bfw/test/install/app/config/bfw-test-install"} []
            $this->checkLogLineMsg(3, 'FileManager - Remove files and directories');
            $this->checkLogLineContextKeys(3, ['path']);
            $this->checkLogLineContextKeyContain(3, 'path', '/test/install/app/config/bfw-test-install');

            //Line 4 [2019-05-17 09:00:49] bfw.DEBUG: Module - Read module info {"name":"bfw-test-install","path":"/opt/Projects/bfw/bfw/test/install/vendor//bulton-fr/bfw-test-install"} []
            $this->checkLogLineMsg(4, 'Module - Read module info');
            $this->checkLogLineContextKeys(4, ['name', 'path']);
            $this->checkLogLineContextKeyEqual(4, 'name', 'bfw-test-install');
            $this->checkLogLineContextKeyContain(4, 'path', '/test/install/vendor//bulton-fr/bfw-test-install');

            //Line 5 [2019-05-17 09:00:49] bfw.DEBUG: FileManager - Create symlink {"linkTarget":"/opt/Projects/bfw/bfw/test/install/vendor//bulton-fr/bfw-test-install","linkFile":"/opt/Projects/bfw/bfw/test/install/app/modules/available/bfw-test-install"} []
            $this->checkLogLineMsg(5, 'FileManager - Create symlink');
            $this->checkLogLineContextKeys(5, ['linkTarget', 'linkFile']);
            $this->checkLogLineContextKeyContain(5, 'linkTarget', '/test/install/vendor//bulton-fr/bfw-test-install');
            $this->checkLogLineContextKeyContain(5, 'linkFile', '/test/install/app/modules/available/bfw-test-install');

            //Line 6 [2019-05-17 09:00:49] bfw.DEBUG: Module - Copy config files {"name":"bfw-test-install","configPath":"/opt/Projects/bfw/bfw/test/install/app/config/bfw-test-install","sourceConfigPath":"/opt/Projects/bfw/bfw/test/install/app/modules/available/bfw-test-install/config/","configFiles":["test-install.json"]} []
            $this->checkLogLineMsg(6, 'Module - Copy config files');
            $this->checkLogLineContextKeys(6, ['name', 'configPath', 'sourceConfigPath', 'configFiles']);
            $this->checkLogLineContextKeyEqual(6, 'name', 'bfw-test-install');
            $this->checkLogLineContextKeyContain(6, 'configPath', '/test/install/app/config/bfw-test-install');
            $this->checkLogLineContextKeyContain(6, 'sourceConfigPath', '/test/install/app/modules/available/bfw-test-install/config/');
            $this->checkLogLineContextKeyEqual(6, 'configFiles', ["test-install.json"]);

            //Line 7 [2019-05-17 09:00:49] bfw.DEBUG: FileManager - Create directory {"path":"/opt/Projects/bfw/bfw/test/install/app/config/bfw-test-install"} []
            $this->checkLogLineMsg(7, 'FileManager - Create directory');
            $this->checkLogLineContextKeys(7, ['path']);
            $this->checkLogLineContextKeyContain(7, 'path', '/test/install/app/config/bfw-test-install');

            //Line 8 [2019-05-17 09:00:49] bfw.DEBUG: FileManager - Copy file {"source":"/opt/Projects/bfw/bfw/test/install/app/modules/available/bfw-test-install/config/manifest.json","target":"/opt/Projects/bfw/bfw/test/install/app/config/bfw-test-install/manifest.json"} []
            $this->checkLogLineMsg(8, 'FileManager - Copy file');
            $this->checkLogLineContextKeys(8, ['source', 'target']);
            $this->checkLogLineContextKeyContain(8, 'source', '/test/install/app/modules/available/bfw-test-install/config/manifest.json');
            $this->checkLogLineContextKeyContain(8, 'target', '/test/install/app/config/bfw-test-install/manifest.json');

            //Line 9 [2019-05-17 09:00:49] bfw.DEBUG: FileManager - Copy file {"source":"/opt/Projects/bfw/bfw/test/install/app/modules/available/bfw-test-install/config/test-install.json","target":"/opt/Projects/bfw/bfw/test/install/app/config/bfw-test-install/test-install.json"} []
            $this->checkLogLineMsg(9, 'FileManager - Copy file');
            $this->checkLogLineContextKeys(9, ['source', 'target']);
            $this->checkLogLineContextKeyContain(9, 'source', '/test/install/app/modules/available/bfw-test-install/config/test-install.json');
            $this->checkLogLineContextKeyContain(9, 'target', '/test/install/app/config/bfw-test-install/test-install.json');

            //Line 10 [2019-05-17 09:00:49] bfw.DEBUG: Module - Run install script {"name":"bfw-test-install","installScript":"install.php"} []
            $this->checkLogLineMsg(10, 'Module - Run install script');
            $this->checkLogLineContextKeys(10, ['name', 'installScript']);
            $this->checkLogLineContextKeyEqual(10, 'name', 'bfw-test-install');
            $this->checkLogLineContextKeyEqual(10, 'installScript', 'install.php');
        } catch (Exception $e) {
            BasicMsg::displayMsgNL('Fail : '.$e->getMessage(), 'red', 'bold');
            return false;
        }

        BasicMsg::displayMsgNL('OK', 'green', 'bold');
        return true;
    }
}
