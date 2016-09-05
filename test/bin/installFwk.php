<?php

require_once(__DIR__.'/functions.php');

$installDir  = realpath(__DIR__.'/../install');

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

$outputFirstInstall = "\033[0;33mBFW Installation :\033[0m\n"
    ."  > Create app directory ...\033[1;32m Done\033[0m\n"
    ."  > Create app/config directory ...\033[1;32m Done\033[0m\n"
    ."  > Create app/config/bfw directory ...\033[1;32m Done\033[0m\n"
    ."  > Create app/modules directory ...\033[1;32m Done\033[0m\n"
    ."  > Create src directory ...\033[1;32m Done\033[0m\n"
    ."  > Create src/cli directory ...\033[1;32m Done\033[0m\n"
    ."  > Create src/controllers directory ...\033[1;32m Done\033[0m\n"
    ."  > Create src/modeles directory ...\033[1;32m Done\033[0m\n"
    ."  > Create src/view directory ...\033[1;32m Done\033[0m\n"
    ."  > Create web directory ...\033[1;32m Done\033[0m\n"
    ."  > Search skeleton directory path ...\033[1;32m Done\033[0m\n"
    ."  > Copy .htaccess file ...\033[1;32m Done\033[0m\n"
    ."  > Copy config file ...\033[1;32m Done\033[0m\n"
    ."  > Copy example index file into web directory ...\033[1;32m Done\033[0m\n"
    ."  > Copy cli.php file to root project directory ...\033[1;32m Done\033[0m\n"
    ."  > Copy example cli file into app/cli directory ...\033[1;32m Done\033[0m\n"
    ."\n"
    ."\033[0;33mBFW installation status : \033[1;32msuccess\033[0m"
;

$outputSecondInstall = "\033[0;33mBFW Installation :\033[0m\n"
    ."  > Create app directory ...\033[1;32m Done\033[0m\n"
    ."  > Create app/config directory ...\033[1;32m Done\033[0m\n"
    ."  > Create app/config/bfw directory ...\033[1;32m Done\033[0m\n"
    ."  > Create app/modules directory ...\033[1;32m Done\033[0m\n"
    ."  > Create src directory ...\033[1;32m Done\033[0m\n"
    ."  > Create src/cli directory ...\033[1;32m Done\033[0m\n"
    ."  > Create src/controllers directory ...\033[1;32m Done\033[0m\n"
    ."  > Create src/modeles directory ...\033[1;32m Done\033[0m\n"
    ."  > Create src/view directory ...\033[1;32m Done\033[0m\n"
    ."  > Create web directory ...\033[1;32m Done\033[0m\n"
    ."  > Search skeleton directory path ...\033[1;32m Done\033[0m\n"
    ."  > Copy .htaccess file ...\033[1;32m Done\033[0m\n"
    ."  > Copy config file ...\033[1;32m Done\033[0m\n"
    ."  > Copy example index file into web directory ...\033[1;32m Done\033[0m\n"
    ."  > Copy cli.php file to root project directory ...\033[1;32m Done\033[0m\n"
    ."  > Copy example cli file into app/cli directory ...\033[1;32m Done\033[0m\n"
    ."\n"
    ."\033[0;33mBFW installation status : \033[1;32msuccess\033[0m"
;

$exceptedOutput = [
    $outputFirstInstall,
    $outputSecondInstall
];

for ($installIndex = 0; $installIndex < 2; $installIndex++) {
    
    if ($installIndex === 0) {
        echo "\033[0;33mCheck first install\033[0m\n";
    } else {
        echo "\n\n\033[0;33mCheck re-install\033[0m\n";
    }
    
    $installOutput = [];
    exec('cd '.$installDir.' && ./vendor/bin/bfw_install', $installOutput);
    $installOutput = implode("\n", $installOutput);
    
    echo $installOutput;
    
    echo `cd $installDir && ls -al`;
    
    echo 'Test output returned by script : ';
    if ($installOutput !== $exceptedOutput[$installIndex]) {
        echo "\033[1;31m[Fail]\033[0m\n";
        fwrite(STDERR, 'Text returned is not equal to expected text.');
        exit;
    }

    echo "\033[1;32m[OK]\033[0m\n";

    echo 'Test structure :'."\n";

    testDirectoryOrFile('app');
    testDirectoryOrFile('app/config');
    testDirectoryOrFile('app/config/bfw');
    testDirectoryOrFile('app/modules');

    testDirectoryOrFile('src');
    testDirectoryOrFile('src/cli');
    testDirectoryOrFile('src/controllers');
    testDirectoryOrFile('src/modeles');
    testDirectoryOrFile('src/view');

    testDirectoryOrFile('web');

    testDirectoryOrFile('app/config/bfw/config.php');
    testDirectoryOrFile('src/cli/exemple.php');
    testDirectoryOrFile('web/index.php');
    testDirectoryOrFile('.htaccess');
    testDirectoryOrFile('cli.php');
}
