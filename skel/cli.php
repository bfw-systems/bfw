<?php
/**
 * Run application in cli mode
 */

//Define cli mode to true
define('CLI_MODE', true);

//If this file is not call from cli, we display an error
if (PHP_SAPI !== 'cli') {
    echo 'cli.php should be used only from the command.';
    exit;
}

//Get path of root and vendor directories
$rootDir   = realpath(__DIR__);
$vendorDir = realpath($rootDir.'/vendor');

//Load composer autoloader
require_once($vendorDir.'/autoload.php');

//Initialise BFW application
$app = \BFW\Application::getInstance();
$app->initSystems([
    'rootDir'   => $rootDir,
    'vendorDir' => $vendorDir
]);

//Run BFW application
$app->run();
