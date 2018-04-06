<?php

require_once(__DIR__.'/functions.php');

$installDir = realpath(__DIR__.'/../install');

$composerBin     = 'composer';
$composerWhereIs = `whereis composer`;

if ($composerWhereIs === 'composer:'."\n") {
    echo "\033[0;33mDownload composer \033[0m";
    `cd $installDir && curl -sS https://getcomposer.org/installer | php`;
    echo "\033[1;32mOK\033[0m\n";
    
    $composerBin = 'php composer.phar';
}

`cd $installDir && $composerBin install`;
echo "\n";

$bfwVendorPath = realpath($installDir.'/vendor/bulton-fr/bfw/');

$outputTitle = "\033[0;33mRun BFW Install\033[0m\n";

$outputForceReInstall = "\n"
    ."\033[0;33mForce option : Create directory reinstallBackup\033[0m ... "
    ."\033[1;32mDone\033[0m\n"
    ."\033[0;33mForce option : Copy current config directory to reinstallBackup/config\033[0m ... "
    ."\033[1;32mDone\033[0m\n"
    ."\033[0;33mForce option : Remove bfw directories\033[0m ... "
    ."\033[1;32mDone\033[0m\n"
;

$outputCreateDirectories = "\n"
    ."> Create app directory ...\033[1;32m Done\033[0m\n"
    ."> Create app/config directory ...\033[1;32m Done\033[0m\n"
    ."> Create app/config/bfw directory ...\033[1;32m Done\033[0m\n"
    ."> Create app/modules directory ...\033[1;32m Done\033[0m\n"
    ."> Create src directory ...\033[1;32m Done\033[0m\n"
    ."> Create src/cli directory ...\033[1;32m Done\033[0m\n"
    ."> Create src/controllers directory ...\033[1;32m Done\033[0m\n"
    ."> Create src/modeles directory ...\033[1;32m Done\033[0m\n"
    ."> Create src/view directory ...\033[1;32m Done\033[0m\n"
    ."> Create web directory ...\033[1;32m Done\033[0m\n";

$outputExistsDirectories = "\n"
    ."> Create app directory ...\033[1;33m Directory exist\033[0m\n"
    ."> Create app/config directory ...\033[1;33m Directory exist\033[0m\n"
    ."> Create app/config/bfw directory ...\033[1;33m Directory exist\033[0m\n"
    ."> Create app/modules directory ...\033[1;33m Directory exist\033[0m\n"
    ."> Create src directory ...\033[1;33m Directory exist\033[0m\n"
    ."> Create src/cli directory ...\033[1;33m Directory exist\033[0m\n"
    ."> Create src/controllers directory ...\033[1;33m Directory exist\033[0m\n"
    ."> Create src/modeles directory ...\033[1;33m Directory exist\033[0m\n"
    ."> Create src/view directory ...\033[1;33m Directory exist\033[0m\n"
    ."> Create web directory ...\033[1;33m Directory exist\033[0m\n";

$outputSearchPath = "\n"
    ."> Search BFW vendor directory path ...\033[1;32m Found\033[0m\n"
    ."\033[0;33mBFW path : ".$bfwVendorPath."\033[0m\n";

$outputCreateFiles = "\n"
    ."> Copy skel/cli.php file to cli.php ...\033[1;32m Done\033[0m\n"
    ."> Copy skel/app/config/bfw/config.php file to app/config/bfw/config.php ...\033[1;32m Done\033[0m\n"
    ."> Copy skel/src/cli/exemple.php file to src/cli/exemple.php ...\033[1;32m Done\033[0m\n"
    ."> Copy skel/web/.htaccess file to web/.htaccess ...\033[1;32m Done\033[0m\n"
    ."> Copy skel/web/index.php file to web/index.php ...\033[1;32m Done\033[0m\n"
;

$outputExistsFiles = "\n"
    ."> Copy skel/cli.php file to cli.php ...\033[1;33m File exist\033[0m\n"
    ."> Copy skel/app/config/bfw/config.php file to app/config/bfw/config.php ...\033[1;33m File exist\033[0m\n"
    ."> Copy skel/src/cli/exemple.php file to src/cli/exemple.php ...\033[1;33m File exist\033[0m\n"
    ."> Copy skel/web/.htaccess file to web/.htaccess ...\033[1;33m File exist\033[0m\n"
    ."> Copy skel/web/index.php file to web/index.php ...\033[1;33m File exist\033[0m\n"
;
    
$outputInstallStatus = "\n\033[0;33mBFW install status : \033[1;32mSuccess\033[0m";

$outputFirstInstall = ""
    .$outputTitle
    .$outputCreateDirectories
    .$outputSearchPath
    .$outputCreateFiles
    .$outputInstallStatus
;

$outputSecondInstall = ""
    .$outputTitle
    .$outputExistsDirectories
    .$outputSearchPath
    .$outputExistsFiles
    .$outputInstallStatus
;

$outputThirdInstall = ""
    .$outputTitle
    .$outputForceReInstall
    .$outputCreateDirectories
    .$outputSearchPath
    .$outputCreateFiles
    .$outputInstallStatus
;

$expectedOutput = [
    $outputFirstInstall,
    $outputSecondInstall,
    $outputThirdInstall
];

for ($installIndex = 0; $installIndex < 3; $installIndex++) {
    
    if ($installIndex === 0) {
        echo "\033[0;33mCheck first install\033[0m\n";
    } elseif ($installIndex === 1) {
        echo "\n\n\033[0;33mCheck re-install without force option\033[0m\n";
    } elseif ($installIndex === 2) {
        echo "\n\n\033[0;33mCheck re-install with force option\033[0m\n";
    }
    
    $installCmd = './vendor/bin/bfwInstall';
    if ($installIndex === 2) {
        $installCmd .= ' -f';
    }
    
    $installOutput = [];
    exec('cd '.$installDir.' && '.$installCmd, $installOutput);
    $installOutput = implode("\n", $installOutput);
    
    echo $installOutput;
    
    echo "\n";
    //echo `cd $installDir && ls -al *`;
    
    echo 'Test output returned by script : ';
    if ($installOutput !== $expectedOutput[$installIndex]) {
        echo "\033[1;31m[Fail]\033[0m\n";
        fwrite(STDERR, "Text returned is not equal to expected text.\n");
        exit(1);
    }

    echo "\033[1;32m[OK]\033[0m\n";

    echo 'Test structure :'."\n";

    testDirectoryOrFile($installDir, 'app');
    testDirectoryOrFile($installDir, 'app/config');
    testDirectoryOrFile($installDir, 'app/config/bfw');
    testDirectoryOrFile($installDir, 'app/modules');

    testDirectoryOrFile($installDir, 'src');
    testDirectoryOrFile($installDir, 'src/cli');
    testDirectoryOrFile($installDir, 'src/controllers');
    testDirectoryOrFile($installDir, 'src/modeles');
    testDirectoryOrFile($installDir, 'src/view');

    testDirectoryOrFile($installDir, 'web');

    testDirectoryOrFile($installDir, 'app/config/bfw/config.php');
    testDirectoryOrFile($installDir, 'src/cli/exemple.php');
    testDirectoryOrFile($installDir, 'web/index.php');
    testDirectoryOrFile($installDir, 'web/.htaccess');
    testDirectoryOrFile($installDir, 'cli.php');
}
