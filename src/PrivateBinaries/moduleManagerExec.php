<?php
/*
 * Manager BFW Modules
 *
 * @created : 2019-05-03
 * @version : 3.0.0
 * @author : bulton-fr <bulton.fr@gmail.com>
 */

$cliArgs = getopt(
    '',
    [
        'bfw-path:',
        'vendor-path::',
        'action:',
        'reinstall',
        'all',
        'module:'
    ]
);

$bfwPath = realpath(__DIR__.'/../../../../../');
if (isset($cliArgs['bfw-path'])) {
    $bfwPath = realpath($cliArgs['bfw-path']);
}

$vendorPath = $bfwPath.'/vendor';
if (isset($cliArgs['vendor-path'])) {
    $vendorPath = realpath($cliArgs['vendor-path']);
}

if (!file_exists($vendorPath.'/autoload.php')) {
    echo "\033[1;31mUnable to load autoload file from $vendorPath/autoload.php\033[0m\n";
    exit;
}

require_once($vendorPath.'/autoload.php');
use \bultonFr\Utils\Cli\BasicMsg;

$action = null;
if (isset($cliArgs['action'])) {
    $action = $cliArgs['action'];
}

$reinstall = false;
if (isset($cliArgs['reinstall'])) {
    $reinstall = true;
}

$allModules = false;
if (isset($cliArgs['all'])) {
    $allModules = true;
}

$specificModule = '';
if (isset($cliArgs['module'])) {
    $specificModule = $cliArgs['module'];
}

if ($allModules === false && empty($specificModule)) {
    $msg = 'A module should be specified if the option for all module is not declared.';
    BasicMsg::displayMsgNL($msg, 'red', 'bold');
    exit;
} elseif ($allModules === true && empty($specificModule) === false) {
    $msg = 'A module is specified but the option for all module is also declared.';
    BasicMsg::displayMsgNL($msg, 'red', 'bold');
    exit;
}

$reinstallTxt      = $reinstall ? 'Yes' : 'No';
$allModulesTxt     = $allModules ? 'Yes' : 'No';
$specificModuleTxt = empty($specificModule) ? 'Not declared' : $specificModule;

/*
echo 'BFW Application path : '.$bfwPath."\n";
echo 'Vendor path : '.$vendorPath."\n";
echo 'Action todo : '.$action."\n";
echo 'Reinstall option : '.$reinstallTxt."\n";
echo 'Work on all modules : '.$allModulesTxt."\n";
echo 'Work only on the module : '.$specificModuleTxt."\n";
//*/

$app = \BFW\Install\Application::getInstance();
$app->initSystems([
    'rootDir'    => $bfwPath,
    'vendorDir'  => $vendorPath,
    'runSession' => false
]);

$manager = $app->getModuleManager();
$manager
    ->setAction($action)
    ->setReinstall($reinstall)
    ->setAllModules($allModules)
    ->setSpecificModule($specificModule)
;

$app->run();
