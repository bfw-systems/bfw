<?php

namespace BFW;

use \Exception;
use \stdClass;

class Module
{
    protected $pathName = '';

    protected $config;

    protected $installInfos;

    protected $loadInfos;

    protected $status;

    public function __construct($pathName, $loadModule = true)
    {
        $this->pathName = $pathName;
        
        if ($loadModule === true) {
            $this->loadModule();
        }
    }
    
    public function loadModule()
    {
        $this->status       = new stdClass();
        $this->status->load = false;
        $this->status->run  = false;

        $this->loadConfig();
        $this->loadModuleInstallInfos();
        $this->loadModuleInfos();

        $this->status->load = true;
    }

    public static function installInfos($pathName)
    {
        $module = new self($pathName, false);
        $module->loadModuleInstallInfos();
        
        return $module->getInstallInfos();
    }

    public function getPathName()
    {
        return $this->pathName;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function getInstallInfos()
    {
        return $this->installInfos;
    }

    public function getLoadInfos()
    {
        return $this->loadInfos;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function isLoaded()
    {
        return $this->status->load;
    }

    public function isRun()
    {
        return $this->status->run;
    }

    public function loadConfig()
    {
        if (!file_exists(CONFIG_DIR.$this->pathName)) {
            return;
        }

        $this->config = new \BFW\Config($this->pathName);
    }

    public function loadModuleInstallInfos()
    {
        $this->installInfos = $this->loadJsonFile(
            MODULES_DIR.$this->pathName.'/bfwModuleInstall.json'
        );
    }

    public function loadModuleInfos()
    {
        $this->loadInfos = $this->loadJsonFile(
            MODULES_DIR.$this->pathName
            .'/'.$this->installInfos->srcPath
            .'/module.json'
        );
    }

    protected function loadJsonFile($jsonFilePath)
    {
        if (!file_exists($jsonFilePath)) {
            throw new Exception('File '.$jsonFilePath.' not found.');
        }

        $infos = json_decode(file_get_contents($jsonFilePath));
        if ($infos === null) {
            throw new Exception(json_last_error_msg());
        }

        return $infos;
    }

    protected function getRunnerFile()
    {
        $moduleInfos = $this->loadInfos;
        $runnerFile  = '';

        if (property_exists($moduleInfos, 'runner')) {
            $runnerFile = (string) $moduleInfos->runner;
        }

        if ($runnerFile === '') {
            return;
        }

        $runnerFile = MODULES_DIR.$this->pathName
            .'/'.$this->installInfos->srcPath
            .'/'.$runnerFile
        ;

        if (!file_exists($runnerFile)) {
            throw new Exception(
                'Runner file for module '.$this->pathName.' not found.'
            );
        }

        return $runnerFile;
    }

    public function runModule()
    {
        $runnerFile = $this->getRunnerFile();

        $initFunction = function() use ($runnerFile) {
            if($runnerFile === null) {
                return;
            }
            
            require(realpath($runnerFile));
        };

        $this->status->run = true;
        $initFunction();
    }
}
