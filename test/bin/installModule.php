<?php

require_once(__DIR__.'/functions.php');
require_once(__DIR__.'/ExpectedModuleInstall.php');

$installDir = realpath(__DIR__.'/../install');

echo "\033[0;33mCheck hello-world module install\033[0m\n";

$moduleInstallOutput = [];
exec('cd '.$installDir.' && ./vendor/bin/bfwInstallModules', $moduleInstallOutput);

$moduleInstallOutput = implode("\n", $moduleInstallOutput)."\n";

$bfwHelloWorld = new \BFW\Test\Bin\ExpectedModuleInstall(
    'bfw-hello-world',
    ['hello-world.json']
);
$bfwTestInstall = new \BFW\Test\Bin\ExpectedModuleInstall(
    'bfw-test-install',
    ['test-install.json'],
    [
        'install.php' => "  \033[1;33mCreate install_test.php file into web directory\033[0m\n"
    ]
);

$expectedStart     = "\033[0;33mRun BFW Modules Install\033[0m\n";
$expectedRunScript = "Read all modules to run install script...\n";
$expectedEndScript = "All modules have been read.\n";

$expectedModuleOutput = [
    $expectedStart
    .$bfwHelloWorld->generateInstallOutput()
    .$bfwTestInstall->generateInstallOutput()
    .$expectedRunScript
    .$bfwHelloWorld->generateScriptOutput()
    .$bfwTestInstall->generateScriptOutput()
    .$expectedEndScript,
    
    $expectedStart
    .$bfwTestInstall->generateInstallOutput()
    .$bfwHelloWorld->generateInstallOutput()
    .$expectedRunScript
    .$bfwHelloWorld->generateScriptOutput()
    .$bfwTestInstall->generateScriptOutput()
    .$expectedEndScript,
    
    $expectedStart
    .$bfwHelloWorld->generateInstallOutput()
    .$bfwTestInstall->generateInstallOutput()
    .$expectedRunScript
    .$bfwTestInstall->generateScriptOutput()
    .$bfwHelloWorld->generateScriptOutput()
    .$expectedEndScript,
    
    $expectedStart
    .$bfwTestInstall->generateInstallOutput()
    .$bfwHelloWorld->generateInstallOutput()
    .$expectedRunScript
    .$bfwTestInstall->generateScriptOutput()
    .$bfwHelloWorld->generateScriptOutput()
    .$expectedEndScript,
];

echo 'Test output returned by script : ';

/*
echo "\n[TRAVIS DEBUG]\n--------------------------\n";
print_r($moduleInstallOutput);
echo "\n--------------------------\n";
print_r($expectedModuleOutput);
echo "\n--------------------------\n";
*/

$expectedIsFound = false;
foreach ($expectedModuleOutput as $expectedOutput) {
    if ($expectedOutput === $moduleInstallOutput) {
        $expectedIsFound = true;
        break;
    }
}

if ($expectedIsFound === false) {
    echo "\033[1;31m[Fail]\033[0m\n";
    fwrite(STDERR, 'Text returned is not equal to expected text.');
    exit(1);
}

echo "\033[1;32m[OK]\033[0m\n";

echo 'Test structure :'."\n";

testDirectoryOrFile($installDir, 'app/config/bfw-hello-world/hello-world.json');
testDirectoryOrFile($installDir, 'app/config/bfw-hello-world/manifest.json');
testDirectoryOrFile($installDir, 'app/modules/bfw-hello-world/helloWorld.php');
testDirectoryOrFile($installDir, 'app/modules/bfw-hello-world/module.json');

testDirectoryOrFile($installDir, 'app/config/bfw-test-install/test-install.json');
testDirectoryOrFile($installDir, 'app/config/bfw-test-install/manifest.json');
testDirectoryOrFile($installDir, 'app/modules/bfw-test-install/runner.php');
testDirectoryOrFile($installDir, 'app/modules/bfw-test-install/module.json');
testDirectoryOrFile($installDir, 'web/install_test.php');
