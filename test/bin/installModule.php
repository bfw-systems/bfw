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
                        ." > Run install specific script : \033\n"
                        .">> [1;33mNo specific script declared. Pass\033[0m";

echo 'Test output returned by script : ';
if ($moduleInstallOutput !== $expectedModuleOutput) {
    echo "\033[1;31m[Fail]\033[0m\n";
    fwrite(STDERR, 'Text returned is not equal to expected text.');
    exit;
}

echo "\033[1;32m[OK]\033[0m\n";

echo 'Test structure :'."\n";
testDirectoryOrFile($installDir, 'app/config/bfw-hello-world/hello-world.json');
testDirectoryOrFile($installDir, 'app/modules/bfw-hello-world/helloWorld.php');
testDirectoryOrFile($installDir, 'app/modules/bfw-hello-world/module.json');
