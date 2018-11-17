<?php

require_once(__DIR__.'/functions.php');
require_once(__DIR__.'/ExpectedModuleInstall.php');

$installDir = realpath(__DIR__.'/../install');

$moduleToInstall   = [];
$moduleToInstall[] = new \BFW\Test\Bin\ExpectedModuleInstall(
    'bfw-hello-world',
    ['hello-world.json']
);
$moduleToInstall[] = new \BFW\Test\Bin\ExpectedModuleInstall(
    'bfw-test-install',
    ['test-install.json'],
    [
        'install.php' => "  \033[1;33mCreate install_test.php file into web directory\033[0m\n"
    ]
);

echo "\033[0;33mCheck hello-world module install\033[0m\n";

$moduleInstallOutput = [];
exec('cd '.$installDir.' && ./vendor/bin/bfwInstallModules', $moduleInstallOutput);

$moduleInstallOutput = implode("\n", $moduleInstallOutput)."\n";

$expectedStart     = "\033[0;33mRun BFW Modules Install\033[0m\n";
$expectedRunScript = "Read all modules to run install script...\n";
$expectedEndScript = "All modules have been read.\n";

$matrix = [];
foreach ($moduleToInstall as $mod1) {
	foreach ($moduleToInstall as $mod2) {
		if ($mod1 === $mod2) {
			continue;
		}
		
		$matrix[] = [$mod1, $mod2];
	}
}

$expectedModuleOutput = [];

foreach ($matrix as $modComb1) {
    foreach ($matrix as $modComb2) {
        $expectedModuleOutput[] = $expectedStart
            .$modComb1[0]->generateInstallOutput()
            .$modComb1[1]->generateInstallOutput()
            .$expectedRunScript
            .$modComb2[0]->generateScriptOutput()
            .$modComb2[1]->generateScriptOutput()
            .$expectedEndScript
        ;
    }
}

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
