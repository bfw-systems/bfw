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
    
    public function __construct($pathName)
    {
        $this->pathName = $pathName;
        
        $this->status = new stdClass();
        $this->status->load = false;
        $this->status->run  = false;
        
        $this->loadConfig();
        $this->loadModulesInstallInfos();
        $this->loadModulesInfos();
        
        $this->status->load = true;
    }
    
    public static function installInfos($pathName)
    {
        $this->loadModulesInstallInfos();
        return $this->installInfos;
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
    
    protected function loadConfig()
    {
        if(!file_exists(CONFIG_DIR.$this->pathName)) {
            return;
        }
        
        $this->config = new \BFW\Config($this->pathName);
    }
    
    protected function loadModulesInstallInfos()
    {
        $this->installInfos = $this->loadJsonFile(
            MODULES_DIR.$this->pathName.'/bfwModuleInstall.json'
        );
    }
    
    protected function loadModulesInfos()
    {
        $this->loadInfos = $this->loadJsonFile(
            MODULES_DIR.$this->pathName
            .'/'.$this->installInfos->srcPath
            .'/modules.json'
        );
    }
    
    protected function loadJsonFile($jsonFilePath)
    {
        if(!file_exists($jsonFilePath)) {
            throw new Exception('File '.$jsonFilePath.' not found.');
        }
        
        $infos = json_decode(file_get_contents($jsonFilePath));
        if($infos === null) {
            throw new Exception(json_last_error_msg());
        }
        
        return $infos;
    }
    
    protected function getRunnerFile()
    {
        $moduleInfos = $this->loadInfos;
        $runnerFile  = '';
        
        if(property_exists($moduleInfos, 'runner')) {
            $runnerFile = (string) $moduleInfos->runner;
        }
        
        if($runnerFile === '') {
            return;
        }
        
        $runnerFile = MODULES_DIR.$this->pathName
            .'/'.$this->installInfos->srcPath
            .'/'.$runnerFile
        ;
        
        if(!file_exists($runnerFile)) {
            throw new Exception(
                'Runner file for module '.$this->pathName.' not found.'
            );
        }
        
        return $runnerFile;
    }
    
    public function initModule()
    {
        $runnerFile = $this->getRunnerFile();
        
        $initFunction = function() use ($runnerFile) {
            require(realpath($runnerFile));
        };
        
        $this->status->run = true;
        $initFunction();
    }
}
