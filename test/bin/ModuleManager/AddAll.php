<?php

namespace BFW\Test\Bin\ModuleManager;

use Exception;
use bultonFr\Utils\Cli\BasicMsg;
use BFW\Test\Bin\Ressources\LogLineTesterTrait;

class AddAll extends AbstractModuleManagerTests
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

        $cmd       = 'cd '.$this->installDir.' && ./vendor/bin/bfwAddMod -a';
        $cmdOutput = $this->execCmd($cmd);

        $expectedOutput = ""
            ."\033[0;33m> Add module bfw-hello-world ... \033[0m\033[0;32mDone\033[0m\n"
            ."\033[0;33m> Add module bfw-test-install ... \033[0m\033[0;32mDone\033[0m\n"
            ."\033[0;33m> Execute install script for bfw-hello-world ... \033[0m\033[0;33mNo script, pass.\033[0m\n"
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
        if (count($this->logRecords) !== 13) {
            BasicMsg::displayMsgNL('Fail : Number of line not equal to 13', 'red', 'bold');
            return false;
        }

        try {
            //Line 0 [2019-05-17 09:00:49] bfw.DEBUG: Module - Read module info {"name":"bfw-hello-world","path":"/opt/Projects/bfw/bfw/test/install/vendor//bulton-fr/bfw-hello-world"} []
            $this->checkLogLineMsg(0, 'Module - Read module info');
            $this->checkLogLineContextKeys(0, ['name', 'path']);
            $this->checkLogLineContextKeyEqual(0, 'name', 'bfw-hello-world');
            $this->checkLogLineContextKeyContain(0, 'path', '/test/install/vendor//bulton-fr/bfw-hello-world');

            //Line 1 [2019-05-17 09:00:49] bfw.DEBUG: FileManager - Create symlink {"linkTarget":"/opt/Projects/bfw/bfw/test/install/vendor//bulton-fr/bfw-hello-world","linkFile":"/opt/Projects/bfw/bfw/test/install/app/modules/available/bfw-hello-world"} []
            $this->checkLogLineMsg(1, 'FileManager - Create symlink');
            $this->checkLogLineContextKeys(1, ['linkTarget', 'linkFile']);
            $this->checkLogLineContextKeyContain(1, 'linkTarget', '/test/install/vendor//bulton-fr/bfw-hello-world');
            $this->checkLogLineContextKeyContain(1, 'linkFile', '/test/install/app/modules/available/bfw-hello-world');

            //Line 2 [2019-05-17 09:00:49] bfw.DEBUG: Module - Copy config files {"name":"bfw-hello-world","configPath":"/opt/Projects/bfw/bfw/test/install/app/config/bfw-hello-world","sourceConfigPath":"/opt/Projects/bfw/bfw/test/install/app/modules/available/bfw-hello-world/config/","configFiles":["hello-world.json"]} []
            $this->checkLogLineMsg(2, 'Module - Copy config files');
            $this->checkLogLineContextKeys(2, ['name', 'configPath', 'sourceConfigPath', 'configFiles']);
            $this->checkLogLineContextKeyEqual(2, 'name', 'bfw-hello-world');
            $this->checkLogLineContextKeyContain(2, 'configPath', '/test/install/app/config/bfw-hello-world');
            $this->checkLogLineContextKeyContain(2, 'sourceConfigPath', '/test/install/app/modules/available/bfw-hello-world/config/');
            $this->checkLogLineContextKeyEqual(2, 'configFiles', ["hello-world.json"]);

            //Line 3 [2019-05-17 09:00:49] bfw.DEBUG: FileManager - Create directory {"path":"/opt/Projects/bfw/bfw/test/install/app/config/bfw-hello-world"} []
            $this->checkLogLineMsg(3, 'FileManager - Create directory');
            $this->checkLogLineContextKeys(3, ['path']);
            $this->checkLogLineContextKeyContain(3, 'path', '/test/install/app/config/bfw-hello-world');

            //Line 4 [2019-05-17 09:00:49] bfw.DEBUG: FileManager - Copy file {"source":"/opt/Projects/bfw/bfw/test/install/app/modules/available/bfw-hello-world/config/manifest.json","target":"/opt/Projects/bfw/bfw/test/install/app/config/bfw-hello-world/manifest.json"} []
            $this->checkLogLineMsg(4, 'FileManager - Copy file');
            $this->checkLogLineContextKeys(4, ['source', 'target']);
            $this->checkLogLineContextKeyContain(4, 'source', '/test/install/app/modules/available/bfw-hello-world/config/manifest.json');
            $this->checkLogLineContextKeyContain(4, 'target', '/test/install/app/config/bfw-hello-world/manifest.json');

            //Line 5 [2019-05-17 09:00:49] bfw.DEBUG: FileManager - Copy file {"source":"/opt/Projects/bfw/bfw/test/install/app/modules/available/bfw-hello-world/config/hello-world.json","target":"/opt/Projects/bfw/bfw/test/install/app/config/bfw-hello-world/hello-world.json"} []
            $this->checkLogLineMsg(5, 'FileManager - Copy file');
            $this->checkLogLineContextKeys(5, ['source', 'target']);
            $this->checkLogLineContextKeyContain(5, 'source', '/test/install/app/modules/available/bfw-hello-world/config/hello-world.json');
            $this->checkLogLineContextKeyContain(5, 'target', '/test/install/app/config/bfw-hello-world/hello-world.json');

            //Line 6 [2019-05-17 09:00:49] bfw.DEBUG: Module - Read module info {"name":"bfw-test-install","path":"/opt/Projects/bfw/bfw/test/install/vendor//bulton-fr/bfw-test-install"} []
            $this->checkLogLineMsg(6, 'Module - Read module info');
            $this->checkLogLineContextKeys(6, ['name', 'path']);
            $this->checkLogLineContextKeyEqual(6, 'name', 'bfw-test-install');
            $this->checkLogLineContextKeyContain(6, 'path', '/test/install/vendor//bulton-fr/bfw-test-install');

            //Line 7 [2019-05-17 09:00:49] bfw.DEBUG: FileManager - Create symlink {"linkTarget":"/opt/Projects/bfw/bfw/test/install/vendor//bulton-fr/bfw-test-install","linkFile":"/opt/Projects/bfw/bfw/test/install/app/modules/available/bfw-test-install"} []
            $this->checkLogLineMsg(7, 'FileManager - Create symlink');
            $this->checkLogLineContextKeys(7, ['linkTarget', 'linkFile']);
            $this->checkLogLineContextKeyContain(7, 'linkTarget', '/test/install/vendor//bulton-fr/bfw-test-install');
            $this->checkLogLineContextKeyContain(7, 'linkFile', '/test/install/app/modules/available/bfw-test-install');

            //Line 8 [2019-05-17 09:00:49] bfw.DEBUG: Module - Copy config files {"name":"bfw-test-install","configPath":"/opt/Projects/bfw/bfw/test/install/app/config/bfw-test-install","sourceConfigPath":"/opt/Projects/bfw/bfw/test/install/app/modules/available/bfw-test-install/config/","configFiles":["test-install.json"]} []
            $this->checkLogLineMsg(8, 'Module - Copy config files');
            $this->checkLogLineContextKeys(8, ['name', 'configPath', 'sourceConfigPath', 'configFiles']);
            $this->checkLogLineContextKeyEqual(8, 'name', 'bfw-test-install');
            $this->checkLogLineContextKeyContain(8, 'configPath', '/test/install/app/config/bfw-test-install');
            $this->checkLogLineContextKeyContain(8, 'sourceConfigPath', '/test/install/app/modules/available/bfw-test-install/config/');
            $this->checkLogLineContextKeyEqual(8, 'configFiles', ["test-install.json"]);

            //Line 9 [2019-05-17 09:00:49] bfw.DEBUG: FileManager - Create directory {"path":"/opt/Projects/bfw/bfw/test/install/app/config/bfw-test-install"} []
            $this->checkLogLineMsg(9, 'FileManager - Create directory');
            $this->checkLogLineContextKeys(9, ['path']);
            $this->checkLogLineContextKeyContain(9, 'path', '/test/install/app/config/bfw-test-install');

            //Line 10 [2019-05-17 09:00:49] bfw.DEBUG: FileManager - Copy file {"source":"/opt/Projects/bfw/bfw/test/install/app/modules/available/bfw-test-install/config/manifest.json","target":"/opt/Projects/bfw/bfw/test/install/app/config/bfw-test-install/manifest.json"} []
            $this->checkLogLineMsg(10, 'FileManager - Copy file');
            $this->checkLogLineContextKeys(10, ['source', 'target']);
            $this->checkLogLineContextKeyContain(10, 'source', '/test/install/app/modules/available/bfw-test-install/config/manifest.json');
            $this->checkLogLineContextKeyContain(10, 'target', '/test/install/app/config/bfw-test-install/manifest.json');

            //Line 11 [2019-05-17 09:00:49] bfw.DEBUG: FileManager - Copy file {"source":"/opt/Projects/bfw/bfw/test/install/app/modules/available/bfw-test-install/config/test-install.json","target":"/opt/Projects/bfw/bfw/test/install/app/config/bfw-test-install/test-install.json"} []
            $this->checkLogLineMsg(11, 'FileManager - Copy file');
            $this->checkLogLineContextKeys(11, ['source', 'target']);
            $this->checkLogLineContextKeyContain(11, 'source', '/test/install/app/modules/available/bfw-test-install/config/test-install.json');
            $this->checkLogLineContextKeyContain(11, 'target', '/test/install/app/config/bfw-test-install/test-install.json');

            //Line 12 [2019-05-17 09:00:49] bfw.DEBUG: Module - Run install script {"name":"bfw-test-install","installScript":"install.php"} []
            $this->checkLogLineMsg(12, 'Module - Run install script');
            $this->checkLogLineContextKeys(12, ['name', 'installScript']);
            $this->checkLogLineContextKeyEqual(12, 'name', 'bfw-test-install');
            $this->checkLogLineContextKeyEqual(12, 'installScript', 'install.php');
        } catch (Exception $e) {
            BasicMsg::displayMsgNL('Fail : '.$e->getMessage(), 'red', 'bold');
            return false;
        }

        BasicMsg::displayMsgNL('OK', 'green', 'bold');
        return true;
    }
}
