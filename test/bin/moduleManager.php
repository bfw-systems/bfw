<?php

require_once(__DIR__.'/../../vendor/autoload.php');

use BFW\Test\Bin\ModuleManager;
use bultonFr\Utils\Cli\BasicMsg;

BasicMsg::displayMsgNL('Prepare tests : Remove existing modules and modules configs', 'yellow');
$installDir = realpath(__DIR__.'/../install');
exec(
    'cd '.$installDir
    .' && rm -rf'
        .' app/modules/available/*'
        .' app/modules/enabled/*'
        .' app/config/bfw-*'
        .' web/install_test.php'
);

BasicMsg::displayMsgNL('Prepare tests : Execute php server', 'yellow');
exec('cd '.$installDir.' && mkdir -p app/logs && echo "" > app/logs/server.log');
exec('cd '.$installDir.' && php -S localhost:8000 -t web web/index.php > app/logs/server.log 2>&1 &');

BasicMsg::displayMsgNL('Prepare tests : Waiting 5sec for server take the time to start', 'yellow');
sleep(5); //Waiting server loaded

BasicMsg::displayMsgNL('Prepare tests : Server status :', 'yellow');
echo file_get_contents($installDir.'/app/logs/server.log')."\n";

BasicMsg::displayMsgNL('test #1 Add all : ', 'yellow');
$testAddAll = new ModuleManager\AddAll;
$testAddAll->runTests();

echo "\n";
BasicMsg::displayMsgNL('test #2 Reinstall all : ', 'yellow');
$testReinstallAll = new ModuleManager\ReinstallAll;
$testReinstallAll->runTests();

echo "\n";
BasicMsg::displayMsgNL('test #3 Enable all : ', 'yellow');
$testEnableAll = new ModuleManager\EnableAll;
$testEnableAll->runTests();

echo "\n";
BasicMsg::displayMsgNL('test #4 Check all module are loaded by framework : ', 'yellow');
$testLoadedAll = new ModuleManager\LoadedAll;
$testLoadedAll->runTests();

echo "\n";
BasicMsg::displayMsgNL('test #5 Disable one : ', 'yellow');
$testDisableOne = new ModuleManager\DisableOne;
$testDisableOne->runTests();

echo "\n";
BasicMsg::displayMsgNL('test #6 Delete one : ', 'yellow');
$testDeleteOne = new ModuleManager\DeleteOne;
$testDeleteOne->runTests();

echo "\n";
BasicMsg::displayMsgNL('test #7 Add one : ', 'yellow');
$testAddOne = new ModuleManager\AddOne;
$testAddOne->runTests();

echo "\n";
BasicMsg::displayMsgNL('test #8 Reinstall one : ', 'yellow');
$testReinstallOne = new ModuleManager\ReinstallOne;
$testReinstallOne->runTests();

echo "\n";
BasicMsg::displayMsgNL('test #9 Enable one : ', 'yellow');
$testEnableOne = new ModuleManager\EnableOne;
$testEnableOne->runTests();

echo "\n";
BasicMsg::displayMsgNL('test #10 Disable all : ', 'yellow');
$testDisableAll = new ModuleManager\DisableAll;
$testDisableAll->runTests();

echo "\n";
BasicMsg::displayMsgNL('test #11 Delete all : ', 'yellow');
$testDeleteAll = new ModuleManager\DeleteAll;
$testDeleteAll->runTests();
