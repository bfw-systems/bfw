<?php

namespace BFW;

use \Exception;
use \stdClass;

/**
 * Class to manage a module
 */
class Module
{
    /**
     * @var string $pathName Module's name
     */
    protected $pathName = '';

    /**
     * @var \BFW\Config $config Config object for this module
     */
    protected $config;

    /**
     * @var \stdClass $loadInfos All informations about how to run the module
     */
    protected $loadInfos;

    /**
     * @var \stdClass $status Load and run status
     */
    protected $status;

    /**
     * Constructor
     * Load all informations if $loadModule is true
     * 
     * @param string $pathName Module name
     * @param boolean $loadModule (default true) If run load information
     */
    public function __construct($pathName, $loadModule = true)
    {
        $this->pathName = $pathName;
        
        if ($loadModule === true) {
            $this->loadModule();
        }
    }
    
    /**
     * Load informations about the module
     * 
     * @return void
     */
    public function loadModule()
    {
        $this->status       = new stdClass();
        $this->status->load = false;
        $this->status->run  = false;

        $this->loadConfig();
        $this->loadModuleInfos();

        $this->status->load = true;
    }

    /**
     * Get installation informations
     * 
     * @param string $sourceFiles Path to module source (in vendor)
     * 
     * @return \stdClass
     */
    public static function installInfos($sourceFiles)
    {
        $currentClass = get_called_class(); //Allow extends
        return $currentClass::loadJsonFile(
            $sourceFiles.'/bfwModulesInfos.json'
        );
    }

    /**
     * Get the module's name
     * 
     * @return string
     */
    public function getPathName()
    {
        return $this->pathName;
    }

    /**
     * Get the Config object which have config for this module
     * 
     * @return \BFW\Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Get the load informations
     * 
     * @return \stdClass
     */
    public function getLoadInfos()
    {
        return $this->loadInfos;
    }

    /**
     * Get the status object for this module
     * 
     * @return \stdClass
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Return the load status
     * 
     * @return boolean
     */
    public function isLoaded()
    {
        return $this->status->load;
    }

    /**
     * Return the run status
     * 
     * @return boolean
     */
    public function isRun()
    {
        return $this->status->run;
    }

    /**
     * Instantiate the Config object to obtains module's configuration
     * 
     * @return void
     */
    public function loadConfig()
    {
        if (!file_exists(CONFIG_DIR.$this->pathName)) {
            return;
        }

        $this->config = new \BFW\Config($this->pathName);
        $this->config->loadFiles();
    }

    /**
     * Save loaded informations from json file into the loadInfos property
     * 
     * @return void
     */
    public function loadModuleInfos()
    {
        $currentClass = get_called_class(); //Allow extends
        
        $this->loadInfos = $currentClass::loadJsonFile(
            MODULES_DIR.$this->pathName
            .'/module.json'
        );
    }

    /**
     * Read and parse a json file
     * 
     * @param string $jsonFilePath : The path to the file to read
     * 
     * @return mixed Json parsed datas
     * 
     * @throws Exception If the file is not found or for a json parser error
     */
    protected static function loadJsonFile($jsonFilePath)
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
    
    /**
     * Add a dependency to the module
     * Used for needMe property in module infos
     * 
     * @param string $dependencyName The dependency name to add
     * 
     * @return $this
     */
    public function addDependency($dependencyName)
    {
        if (!property_exists($this->loadInfos, 'require')) {
            $this->loadInfos->require = [];
        }
        
        if (!is_array($this->loadInfos->require)) {
            $this->loadInfos->require = [$this->loadInfos->require];
        }
        
        $this->loadInfos->require[] = $dependencyName;
        
        return $this;
    }

    /**
     * Get path to the runner file
     * 
     * @return string
     * 
     * @throws Exception If the file not exists
     */
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

        $runnerFile = MODULES_DIR.$this->pathName.'/'.$runnerFile;
        if (!file_exists($runnerFile)) {
            throw new Exception(
                'Runner file for module '.$this->pathName.' not found.'
            );
        }

        return $runnerFile;
    }

    /**
     * Run the module in a closure
     * 
     * @return void
     */
    public function runModule()
    {
        $runnerFile   = $this->getRunnerFile();
        $initFunction = function() use ($runnerFile) {
            if ($runnerFile === null) {
                return;
            }
            
            require(realpath($runnerFile));
        };

        $this->status->run = true;
        $initFunction();
    }
}
