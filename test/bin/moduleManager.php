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

BasicMsg::displayMsgNL('test #1 Add all : ', 'yellow');
$testAddAll = new ModuleManager\AddAll;
$testAddAll->runTests();

echo "\n";
BasicMsg::displayMsgNL('test #2 Reinstall all : ', 'yellow');
$testReinstallAll = new ModuleManager\ReinstallAll;
$testReinstallAll->runTests();

echo "\n";
BasicMsg::displayMsgNL('test #2 Enable all : ', 'yellow');
$testEnableAll = new ModuleManager\EnableAll;
$testEnableAll->runTests();

echo "\n";
BasicMsg::displayMsgNL('test #3 Disable one : ', 'yellow');
$testDisableOne = new ModuleManager\DisableOne;
$testDisableOne->runTests();

echo "\n";
BasicMsg::displayMsgNL('test #4 Delete one : ', 'yellow');
$testDeleteOne = new ModuleManager\DeleteOne;
$testDeleteOne->runTests();

echo "\n";
BasicMsg::displayMsgNL('test #5 Add one : ', 'yellow');
$testAddOne = new ModuleManager\AddOne;
$testAddOne->runTests();

echo "\n";
BasicMsg::displayMsgNL('test #2 Reinstall one : ', 'yellow');
$testReinstallOne = new ModuleManager\ReinstallOne;
$testReinstallOne->runTests();

echo "\n";
BasicMsg::displayMsgNL('test #6 Enable one : ', 'yellow');
$testEnableOne = new ModuleManager\EnableOne;
$testEnableOne->runTests();

echo "\n";
BasicMsg::displayMsgNL('test #7 Disable all : ', 'yellow');
$testDisableAll = new ModuleManager\DisableAll;
$testDisableAll->runTests();

echo "\n";
BasicMsg::displayMsgNL('test #8 Delete all : ', 'yellow');
$testDeleteAll = new ModuleManager\DeleteAll;
$testDeleteAll->runTests();
