<?php

namespace BFW\Install\ModuleManager;

class ModuleInfo
{
    /**
     * Info extracted from file bfwModulesInfos.json (json_decode)
     *
     * @var array|object
     */
    protected $info;

    /**
     * The src path into the module.
     *
     * @var string
     */
    protected $srcPath = '';

    /**
     * The list of all config files declared.
     * If the value in $info is not an array, it will be converted.
     *
     * @var array
     */
    protected $configFiles = [];

    /**
     * The path to config files
     *
     * @var string
     */
    protected $configPath = '';

    /**
     * The path to the install script.
     * If the value in $info is not a string, it will be converted.
     *
     * @var string
     */
    protected $installScript = '';
    
    /**
     * Constructor
     *
     * Read all properties in $moduleInfo and populate class properties with it.
     * If a property is in $moduleInfo but not exist in the class, it will be
     * create "on the fly", so it will be public.
     * After that, call the method to convert somes values.
     *
     * @param array|object $moduleInfo
     */
    public function __construct($moduleInfo)
    {
        $this->info = $moduleInfo;

        foreach ($this as $propName => $propDefaultValue) {
            if (property_exists($moduleInfo, $propName)) {
                $this->{$propName} = $moduleInfo->{$propName};
            }
        }
        
        $this->convertValues();
    }

    /**
     * Get the value of info (return of json_decode)
     *
     * @return array|object
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * Get the value of srcPath
     *
     * @return string
     */
    public function getSrcPath(): string
    {
        return $this->srcPath;
    }

    /**
     * Get the value of configFiles
     *
     * @return array
     */
    public function getConfigFiles(): array
    {
        return $this->configFiles;
    }

    /**
     * Get the value of configPath
     *
     * @return string
     */
    public function getConfigPath(): string
    {
        return $this->configPath;
    }

    /**
     * Get the value of installScript
     *
     * @return string
     */
    public function getInstallScript(): string
    {
        return $this->installScript;
    }
    
    /**
     * Call methods to convert values for some properties
     *
     * @return void
     */
    protected function convertValues()
    {
        $this->convertConfigFiles();
        $this->convertInstallScript();
    }

    /**
     * Convert the value of $configFiles
     *
     * @return void
     */
    protected function convertConfigFiles()
    {
        if (is_array($this->configFiles)) {
            return;
        }

        if (is_string($this->configFiles) === false) {
            $this->configFiles = [];
        } else {
            $this->configFiles = (array) $this->configFiles;
        }
    }

    /**
     * Convert the value of $installScript
     *
     * @return void
     */
    protected function convertInstallScript()
    {
        if ($this->installScript === true) {
            $this->installScript = 'runInstallModule.php';
        }

        if (is_string($this->installScript) === false) {
            $this->installScript = '';
        }
    }
}
