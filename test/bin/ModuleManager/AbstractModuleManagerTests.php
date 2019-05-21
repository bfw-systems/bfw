<?php

namespace BFW\Test\Bin\ModuleManager;

use Exception;

abstract class AbstractModuleManagerTests
{
    protected $installDir = '';
    protected $logFilePath = '';

    protected static $monologConfigFileCopyStatus = false;

    public function __construct()
    {
        $this->installDir  = realpath(__DIR__.'/../../install');
        $this->logFilePath = $this->installDir.'/app/logs/bfw/bfw.log';

        $this->checkBfwInstalled();
        static::copyMonologConfigFile($this->installDir);
        $this->removeMonologLog();
    }

    protected function checkBfwInstalled()
    {
        if (!file_exists($this->installDir.'/app')) {
            throw new Exception('BFW seem to not be installed in /test/install directory.');
        }
    }

    protected static function copyMonologConfigFile($installDir)
    {
        if (static::$monologConfigFileCopyStatus === true) {
            return;
        }

        static::$monologConfigFileCopyStatus = true;

        copy($installDir.'/app/config/bfw/monolog.php', $installDir.'/app/config/bfw/monolog.php.bak');
        copy(__DIR__.'/../Ressources/monolog.config.php', $installDir.'/app/config/bfw/monolog.php');
    }

    protected function removeMonologLog()
    {
        if (file_exists($this->installDir.'/app/logs/bfw/bfw.log')) {
            unlink($this->installDir.'/app/logs/bfw/bfw.log');
        }
    }

    abstract protected function testsList(): array;

    public function runTests()
    {
        $list = $this->testsList();

        foreach ($list as $fctToCall) {
            $fctReturn = $fctToCall();

            if ($fctReturn === false) {
                exit(1);
            }
        }
    }

    protected function execCmd(string $cmd): string
    {
        $cmdOutput = [];
        exec($cmd, $cmdOutput);
        
        return implode("\n", $cmdOutput)."\n";
    }
}
