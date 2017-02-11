<?php

require_once(__DIR__.'/functions.php');

$installDir  = realpath(__DIR__.'/../install');

echo "\033[0;33mCheck hello-world module install\033[0m\n";

$moduleInstallOutput = [];
exec('cd '.$installDir.' && ./vendor/bin/bfw_installModules', $moduleInstallOutput);

$moduleInstallOutput  = implode("\n", $moduleInstallOutput);
$expectedModuleOutput = "bfw-hello-world : Run install.\n"
    ." > Create symbolic link ... \033[1;32mDone\033[0m\n"
    ." > Copy config files :\n"
    ." >> Create config directory for this module ... \033[1;32mCreated.\033[0m\n"
    ." >> Copy hello-world.json ... \033[1;32mDone\033[0m\n"
    ." > Check install specific script :\n"
    ." >> \033[1;33mNo specific script declared. Pass\033[0m\n"
    ."bfw-test-install : Run install.\n"
    ." > Create symbolic link ... \033[1;32mDone\033[0m\n"
    ." > Copy config files :\n"
    ." >> Create config directory for this module ... \033[1;32mCreated.\033[0m\n"
    ." >> Copy test-install.json ... \033[1;32mDone\033[0m\n"
    ." > Check install specific script :\n"
    ." >> \033[1;33mScripts find. Add to list to execute.\033[0m\n"
    ."Read all modules to run install script :\n"
    ." > Read for module bfw-hello-world\n"
    ." >> No script to run.\n"
    ." > Read for module bfw-test-install\n"
    ." >> \033[1;33mExecute script install.php\033[0m\n"
    ."  \033[1;33mCreate install_test.php file into web directory\033[0m\n";

echo 'Test output returned by script : ';

echo "\n[TRAVIS DEBUG]:\n--------------------------\n";
print_r($moduleInstallOutput);
echo "\n--------------------------\n";
print_r($expectedModuleOutput);
echo "\n--------------------------\n";

if ($moduleInstallOutput !== $expectedModuleOutput) {
    echo "\033[1;31m[Fail]\033[0m\n";
    fwrite(STDERR, 'Text returned is not equal to expected text.');
    exit(1);
}

echo "\033[1;32m[OK]\033[0m\n";

echo 'Test structure :'."\n";

testDirectoryOrFile($installDir, 'app/config/bfw-hello-world/hello-world.json');
testDirectoryOrFile($installDir, 'app/modules/bfw-hello-world/helloWorld.php');
testDirectoryOrFile($installDir, 'app/modules/bfw-hello-world/module.json');

testDirectoryOrFile($installDir, 'app/config/bfw-test-install/test-install.json');
testDirectoryOrFile($installDir, 'app/modules/bfw-test-install/runner.php');
testDirectoryOrFile($installDir, 'app/modules/bfw-test-install/module.json');
testDirectoryOrFile($installDir, 'web/install_test.php');
