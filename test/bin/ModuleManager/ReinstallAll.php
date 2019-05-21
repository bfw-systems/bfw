<?php

namespace BFW\Test\Bin\ModuleManager;

use Exception;
use bultonFr\Utils\Cli\BasicMsg;
use BFW\Test\Bin\Ressources\LogLineTesterTrait;

class ReinstallAll extends AbstractModuleManagerTests
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

        $cmd       = 'cd '.$this->installDir.' && ./vendor/bin/bfwAddMod -a -r';
        $cmdOutput = $this->execCmd($cmd);

        $expectedOutput = ""
            ."\033[0;33m> Delete module bfw-hello-world ... \033[0m\033[0;32mDone\033[0m\n"
            ."\033[0;33m> Delete module bfw-test-install ... \033[0m\033[0;32mDone\033[0m\n"
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
        if (count($this->logRecords) !== 21) {
            BasicMsg::displayMsgNL('Fail : Number of line not equal to 21', 'red', 'bold');
            return false;
        }

        try {
            //Line 0 [2019-05-17 10:33:26] bfw.DEBUG: Module - Read module info {"name":"bfw-hello-world","path":"/opt/Projects/bfw/bfw/test/install/app/modules/available/bfw-hello-world"} []
            $this->checkLogLineMsg(0, 'Module - Read module info');
            $this->checkLogLineContextKeys(0, ['name', 'path']);
            $this->checkLogLineContextKeyEqual(0, 'name', 'bfw-hello-world');
            $this->checkLogLineContextKeyContain(0, 'path', '/test/install/app/modules/available/bfw-hello-world');

            //Line 1 [2019-05-17 10:33:26] bfw.DEBUG: FileManager - Remove symlink {"linkFile":"/opt/Projects/bfw/bfw/test/install/app/modules/available/bfw-hello-world"} []
            $this->checkLogLineMsg(1, 'FileManager - Remove symlink');
            $this->checkLogLineContextKeys(1, ['linkFile']);
            $this->checkLogLineContextKeyContain(1, 'linkFile', '/test/install/app/modules/available/bfw-hello-world');

            //Line 2 [2019-05-17 10:33:26] bfw.DEBUG: Module - Delete config files {"name":"bfw-hello-world","configPath":"/opt/Projects/bfw/bfw/test/install/app/config/bfw-hello-world"} []
            $this->checkLogLineMsg(2, 'Module - Delete config files');
            $this->checkLogLineContextKeys(2, ['name', 'configPath']);
            $this->checkLogLineContextKeyEqual(2, 'name', 'bfw-hello-world');
            $this->checkLogLineContextKeyContain(2, 'configPath', '/test/install/app/config/bfw-hello-world');

            //Line 3 [2019-05-17 10:33:26] bfw.DEBUG: FileManager - Remove files and directories {"path":"/opt/Projects/bfw/bfw/test/install/app/config/bfw-hello-world"} []
            $this->checkLogLineMsg(3, 'FileManager - Remove files and directories');
            $this->checkLogLineContextKeys(3, ['path']);
            $this->checkLogLineContextKeyContain(3, 'path', '/test/install/app/config/bfw-hello-world');

            //Line 4 [2019-05-17 10:33:26] bfw.DEBUG: Module - Read module info {"name":"bfw-test-install","path":"/opt/Projects/bfw/bfw/test/install/app/modules/available/bfw-test-install"} []
            $this->checkLogLineMsg(4, 'Module - Read module info');
            $this->checkLogLineContextKeys(4, ['name', 'path']);
            $this->checkLogLineContextKeyEqual(4, 'name', 'bfw-test-install');
            $this->checkLogLineContextKeyContain(4, 'path', '/test/install/app/modules/available/bfw-test-install');

            //Line 5 [2019-05-17 10:33:26] bfw.DEBUG: FileManager - Remove symlink {"linkFile":"/opt/Projects/bfw/bfw/test/install/app/modules/available/bfw-test-install"} []
            $this->checkLogLineMsg(5, 'FileManager - Remove symlink');
            $this->checkLogLineContextKeys(5, ['linkFile']);
            $this->checkLogLineContextKeyContain(5, 'linkFile', '/test/install/app/modules/available/bfw-test-install');

            //Line 6 [2019-05-17 10:33:26] bfw.DEBUG: Module - Delete config files {"name":"bfw-test-install","configPath":"/opt/Projects/bfw/bfw/test/install/app/config/bfw-test-install"} []
            $this->checkLogLineMsg(6, 'Module - Delete config files');
            $this->checkLogLineContextKeys(6, ['name', 'configPath']);
            $this->checkLogLineContextKeyEqual(6, 'name', 'bfw-test-install');
            $this->checkLogLineContextKeyContain(6, 'configPath', '/test/install/app/config/bfw-test-install');

            //Line 7 [2019-05-17 10:33:26] bfw.DEBUG: FileManager - Remove files and directories {"path":"/opt/Projects/bfw/bfw/test/install/app/config/bfw-test-install"} []
            $this->checkLogLineMsg(7, 'FileManager - Remove files and directories');
            $this->checkLogLineContextKeys(7, ['path']);
            $this->checkLogLineContextKeyContain(7, 'path', '/test/install/app/config/bfw-test-install');

            //Line 8 [2019-05-17 09:00:49] bfw.DEBUG: Module - Read module info {"name":"bfw-hello-world","path":"/opt/Projects/bfw/bfw/test/install/vendor//bulton-fr/bfw-hello-world"} []
            $this->checkLogLineMsg(8, 'Module - Read module info');
            $this->checkLogLineContextKeys(8, ['name', 'path']);
            $this->checkLogLineContextKeyEqual(8, 'name', 'bfw-hello-world');
            $this->checkLogLineContextKeyContain(8, 'path', '/test/install/vendor//bulton-fr/bfw-hello-world');

            //Line 9 [2019-05-17 09:00:49] bfw.DEBUG: FileManager - Create symlink {"linkTarget":"/opt/Projects/bfw/bfw/test/install/vendor//bulton-fr/bfw-hello-world","linkFile":"/opt/Projects/bfw/bfw/test/install/app/modules/available/bfw-hello-world"} []
            $this->checkLogLineMsg(9, 'FileManager - Create symlink');
            $this->checkLogLineContextKeys(9, ['linkTarget', 'linkFile']);
            $this->checkLogLineContextKeyContain(9, 'linkTarget', '/test/install/vendor//bulton-fr/bfw-hello-world');
            $this->checkLogLineContextKeyContain(9, 'linkFile', '/test/install/app/modules/available/bfw-hello-world');

            //Line 10 [2019-05-17 09:00:49] bfw.DEBUG: Module - Copy config files {"name":"bfw-hello-world","configPath":"/opt/Projects/bfw/bfw/test/install/app/config/bfw-hello-world","sourceConfigPath":"/opt/Projects/bfw/bfw/test/install/app/modules/available/bfw-hello-world/config/","configFiles":["hello-world.json"]} []
            $this->checkLogLineMsg(10, 'Module - Copy config files');
            $this->checkLogLineContextKeys(10, ['name', 'configPath', 'sourceConfigPath', 'configFiles']);
            $this->checkLogLineContextKeyEqual(10, 'name', 'bfw-hello-world');
            $this->checkLogLineContextKeyContain(10, 'configPath', '/test/install/app/config/bfw-hello-world');
            $this->checkLogLineContextKeyContain(10, 'sourceConfigPath', '/test/install/app/modules/available/bfw-hello-world/config/');
            $this->checkLogLineContextKeyEqual(10, 'configFiles', ["hello-world.json"]);

            //Line 11 [2019-05-17 09:00:49] bfw.DEBUG: FileManager - Create directory {"path":"/opt/Projects/bfw/bfw/test/install/app/config/bfw-hello-world"} []
            $this->checkLogLineMsg(11, 'FileManager - Create directory');
            $this->checkLogLineContextKeys(11, ['path']);
            $this->checkLogLineContextKeyContain(11, 'path', '/test/install/app/config/bfw-hello-world');

            //Line 12 [2019-05-17 09:00:49] bfw.DEBUG: FileManager - Copy file {"source":"/opt/Projects/bfw/bfw/test/install/app/modules/available/bfw-hello-world/config/manifest.json","target":"/opt/Projects/bfw/bfw/test/install/app/config/bfw-hello-world/manifest.json"} []
            $this->checkLogLineMsg(12, 'FileManager - Copy file');
            $this->checkLogLineContextKeys(12, ['source', 'target']);
            $this->checkLogLineContextKeyContain(12, 'source', '/test/install/app/modules/available/bfw-hello-world/config/manifest.json');
            $this->checkLogLineContextKeyContain(12, 'target', '/test/install/app/config/bfw-hello-world/manifest.json');

            //Line 13 [2019-05-17 09:00:49] bfw.DEBUG: FileManager - Copy file {"source":"/opt/Projects/bfw/bfw/test/install/app/modules/available/bfw-hello-world/config/hello-world.json","target":"/opt/Projects/bfw/bfw/test/install/app/config/bfw-hello-world/hello-world.json"} []
            $this->checkLogLineMsg(13, 'FileManager - Copy file');
            $this->checkLogLineContextKeys(13, ['source', 'target']);
            $this->checkLogLineContextKeyContain(13, 'source', '/test/install/app/modules/available/bfw-hello-world/config/hello-world.json');
            $this->checkLogLineContextKeyContain(13, 'target', '/test/install/app/config/bfw-hello-world/hello-world.json');

            //Line 14 [2019-05-17 09:00:49] bfw.DEBUG: Module - Read module info {"name":"bfw-test-install","path":"/opt/Projects/bfw/bfw/test/install/vendor//bulton-fr/bfw-test-install"} []
            $this->checkLogLineMsg(14, 'Module - Read module info');
            $this->checkLogLineContextKeys(14, ['name', 'path']);
            $this->checkLogLineContextKeyEqual(14, 'name', 'bfw-test-install');
            $this->checkLogLineContextKeyContain(14, 'path', '/test/install/vendor//bulton-fr/bfw-test-install');

            //Line 15 [2019-05-17 09:00:49] bfw.DEBUG: FileManager - Create symlink {"linkTarget":"/opt/Projects/bfw/bfw/test/install/vendor//bulton-fr/bfw-test-install","linkFile":"/opt/Projects/bfw/bfw/test/install/app/modules/available/bfw-test-install"} []
            $this->checkLogLineMsg(15, 'FileManager - Create symlink');
            $this->checkLogLineContextKeys(15, ['linkTarget', 'linkFile']);
            $this->checkLogLineContextKeyContain(15, 'linkTarget', '/test/install/vendor//bulton-fr/bfw-test-install');
            $this->checkLogLineContextKeyContain(15, 'linkFile', '/test/install/app/modules/available/bfw-test-install');

            //Line 16 [2019-05-17 09:00:49] bfw.DEBUG: Module - Copy config files {"name":"bfw-test-install","configPath":"/opt/Projects/bfw/bfw/test/install/app/config/bfw-test-install","sourceConfigPath":"/opt/Projects/bfw/bfw/test/install/app/modules/available/bfw-test-install/config/","configFiles":["test-install.json"]} []
            $this->checkLogLineMsg(16, 'Module - Copy config files');
            $this->checkLogLineContextKeys(16, ['name', 'configPath', 'sourceConfigPath', 'configFiles']);
            $this->checkLogLineContextKeyEqual(16, 'name', 'bfw-test-install');
            $this->checkLogLineContextKeyContain(16, 'configPath', '/test/install/app/config/bfw-test-install');
            $this->checkLogLineContextKeyContain(16, 'sourceConfigPath', '/test/install/app/modules/available/bfw-test-install/config/');
            $this->checkLogLineContextKeyEqual(16, 'configFiles', ["test-install.json"]);

            //Line 17 [2019-05-17 09:00:49] bfw.DEBUG: FileManager - Create directory {"path":"/opt/Projects/bfw/bfw/test/install/app/config/bfw-test-install"} []
            $this->checkLogLineMsg(17, 'FileManager - Create directory');
            $this->checkLogLineContextKeys(17, ['path']);
            $this->checkLogLineContextKeyContain(17, 'path', '/test/install/app/config/bfw-test-install');

            //Line 18 [2019-05-17 09:00:49] bfw.DEBUG: FileManager - Copy file {"source":"/opt/Projects/bfw/bfw/test/install/app/modules/available/bfw-test-install/config/manifest.json","target":"/opt/Projects/bfw/bfw/test/install/app/config/bfw-test-install/manifest.json"} []
            $this->checkLogLineMsg(18, 'FileManager - Copy file');
            $this->checkLogLineContextKeys(18, ['source', 'target']);
            $this->checkLogLineContextKeyContain(18, 'source', '/test/install/app/modules/available/bfw-test-install/config/manifest.json');
            $this->checkLogLineContextKeyContain(18, 'target', '/test/install/app/config/bfw-test-install/manifest.json');

            //Line 19 [2019-05-17 09:00:49] bfw.DEBUG: FileManager - Copy file {"source":"/opt/Projects/bfw/bfw/test/install/app/modules/available/bfw-test-install/config/test-install.json","target":"/opt/Projects/bfw/bfw/test/install/app/config/bfw-test-install/test-install.json"} []
            $this->checkLogLineMsg(19, 'FileManager - Copy file');
            $this->checkLogLineContextKeys(19, ['source', 'target']);
            $this->checkLogLineContextKeyContain(19, 'source', '/test/install/app/modules/available/bfw-test-install/config/test-install.json');
            $this->checkLogLineContextKeyContain(19, 'target', '/test/install/app/config/bfw-test-install/test-install.json');

            //Line 20 [2019-05-17 09:00:49] bfw.DEBUG: Module - Run install script {"name":"bfw-test-install","installScript":"install.php"} []
            $this->checkLogLineMsg(20, 'Module - Run install script');
            $this->checkLogLineContextKeys(20, ['name', 'installScript']);
            $this->checkLogLineContextKeyEqual(20, 'name', 'bfw-test-install');
            $this->checkLogLineContextKeyEqual(20, 'installScript', 'install.php');
        } catch (Exception $e) {
            BasicMsg::displayMsgNL('Fail : '.$e->getMessage(), 'red', 'bold');
            return false;
        }

        BasicMsg::displayMsgNL('OK', 'green', 'bold');
        return true;
    }
}
