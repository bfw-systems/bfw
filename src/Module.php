<?php

namespace BFW;

use \Exception;

/**
 * Class to manage a module
 */
class Module
{
    /**
     * @const ERR_FILE_NOT_FOUND Exception code if the file is not found.
     */
    const ERR_FILE_NOT_FOUND = 1104001;
    
    /**
     * @const ERR_JSON_PARSE Exception code if the parse of a json file fail.
     */
    const ERR_JSON_PARSE = 1104002;
    
    /**
     * @const ERR_RUNNER_FILE_NOT_FOUND Exception code if the runner file to
     * execute is not found.
     */
    const ERR_RUNNER_FILE_NOT_FOUND = 1104003;
    
    /**
     * @const ERR_METHOD_NOT_EXIST Exception code if the use call an unexisting
     * method.
     */
    const ERR_METHOD_NOT_EXIST = 1104004;

    /**
     * @var string $name Module's name
     */
    protected $name = '';

    /**
     * @var \BFW\Config|null $config Config object for this module
     */
    protected $config;

    /**
     * @var \stdClass|null $loadInfos All informations about how to run the module
     */
    protected $loadInfos;

    /**
     * @var object $status Load and run status
     */
    protected $status;

    /**
     * Constructor
     * 
     * @param string $name Module name
     */
    public function __construct(string $name)
    {
        \BFW\Application::getInstance()
            ->getMonolog()
            ->getLogger()
            ->debug('New module declared', ['name' => $name])
        ;
        
        $this->name   = $name;
        $this->status = new class {
            public $load = false;
            public $run  = false;
        };
    }
    
    /**
     * PHP Magic method, called when we call an unexisting method
     * This method allow the module to add dynamic method on fly (issue #88)
     * 
     * @param string $name The method name
     * @param array $arguments The argument passed to the method
     * 
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        if (
            property_exists($this, $name) === true &&
            is_callable($this->$name) === true
        ) {
            //Temp var because $this->$name(...$arguments) create ininite loop
            $fct = $this->$name;
            return $fct(...$arguments);
        }
        
        throw new Exception(
            'The method '.$name.' not exist in module class for '.$this->name,
            self::ERR_METHOD_NOT_EXIST
        );
    }
    
    /**
     * Load informations about the module
     * 
     * @return void
     */
    public function loadModule()
    {
        \BFW\Application::getInstance()
            ->getMonolog()
            ->getLogger()
            ->debug('Load module', ['name' => $this->name])
        ;
        
        $this->loadConfig();
        $this->obtainLoadInfos();

        $this->status->load = true;
    }

    /**
     * Get installation informations
     * 
     * @param string $sourceFiles Path to module source (in vendor)
     * 
     * @return \stdClass
     */
    public static function installInfo(string $sourceFiles): \stdClass
    {
        $currentClass = get_called_class(); //Allow extends
        return $currentClass::readJsonFile(
            $sourceFiles.'/bfwModulesInfos.json'
        );
    }

    /**
     * Get the module's name
     * 
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the Config object which have config for this module
     * 
     * @return \BFW\Config|null
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Get the load informations
     * 
     * @return \stdClass|null
     */
    public function getLoadInfos()
    {
        return $this->loadInfos;
    }

    /**
     * Get the status object for this module
     * 
     * @return object
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
    public function isLoaded(): bool
    {
        return $this->status->load;
    }

    /**
     * Return the run status
     * 
     * @return boolean
     */
    public function isRun(): bool
    {
        return $this->status->run;
    }

    /**
     * Instantiate the Config object to obtains module's configuration
     * 
     * @return void
     */
    protected function loadConfig()
    {
        if (!file_exists(CONFIG_DIR.$this->name)) {
            return;
        }

        $this->config = new \BFW\Config($this->name);
        $this->config->loadFiles();
    }

    /**
     * Save loaded informations from json file into the loadInfos property
     * 
     * @return void
     */
    protected function obtainLoadInfos()
    {
        $currentClass = get_called_class(); //Allow extends
        
        $this->loadInfos = $currentClass::readJsonFile(
            MODULES_DIR.$this->name
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
     * @throws \Exception If the file is not found or for a json parser error
     */
    protected static function readJsonFile(string $jsonFilePath)
    {
        if (!file_exists($jsonFilePath)) {
            throw new Exception(
                'File '.$jsonFilePath.' not found.',
                self::ERR_FILE_NOT_FOUND
            );
        }

        $infos = json_decode(file_get_contents($jsonFilePath));
        if ($infos === null) {
            throw new Exception(
                json_last_error_msg(),
                self::ERR_JSON_PARSE
            );
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
    public function addDependency(string $dependencyName): self
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
     * @throws \Exception If the file not exists
     */
    protected function obtainRunnerFile(): string
    {
        $moduleInfos = $this->loadInfos;
        $runnerFile  = '';

        if (property_exists($moduleInfos, 'runner')) {
            $runnerFile = (string) $moduleInfos->runner;
        }

        if (empty($runnerFile)) {
            return '';
        }

        $runnerFile = MODULES_DIR.$this->name.'/'.$runnerFile;
        if (!file_exists($runnerFile)) {
            throw new Exception(
                'Runner file for module '.$this->name.' not found.',
                $this::ERR_RUNNER_FILE_NOT_FOUND
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
        if ($this->status->run === true) {
            return;
        }
        
        $runnerFile   = $this->obtainRunnerFile();
        $initFunction = function() use ($runnerFile) {
            if (empty($runnerFile)) {
                return;
            }
            
            require(realpath($runnerFile));
        };

        $this->status->run = true;
        $initFunction();
    }
}
