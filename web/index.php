<?php
/**
 * Run application in www (apache/nginx/...) mode
 */

//Get path of root and vendor directories
$rootDir   = realpath(__DIR__.'/../');
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
