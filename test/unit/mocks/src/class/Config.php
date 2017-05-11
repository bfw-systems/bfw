<?php

namespace BFW\test\unit\mocks;

require_once(__DIR__.'/../../../helpers/Override.php');

/**
 * Mock for Config class
 * To force some configs values
 */
class Config extends \BFW\Config
{
    use \BFW\test\helpers\Override;
    
    /**
     * Define new config value for a file
     * 
     * @param string $file The filename
     * @param array|\stdClass $newConfig The new config values
     * 
     * @return void
     */
    public function forceConfig($file, $newConfig)
    {
        $this->config[$file] = $newConfig;
    }
    
    /**
     * Change the value of a config key
     * 
     * @param string $file The filename
     * @param string $configKey The config key
     * @param mixed $newValue The new value for the key
     * 
     * @return void
     */
    public function updateKey($file, $configKey, $newValue)
    {
        if (is_array($this->config[$file])) {
            $this->config[$file][$configKey] = $newValue;
        } elseif (is_object($this->config[$file])) {
            $this->config[$file]->$configKey = $newValue;
        }
    }
    
    //**** Override all methods ****\\
    /**
     * @see \Config::getComposerLoader
     * Present because the method can be overrided during the test
     */
    public function loadFiles()
    {
        return $this->callOverrideOrParent('loadFiles', []);
    }
    
    /**
     * @see \Config::searchAllConfigFiles
     * Present because the method can be overrided during the test
     */
    protected function searchAllConfigFiles($dirPath, $pathIntoFirstDir = '')
    {
        return $this->callOverrideOrParent(
            'searchAllConfigFiles',
            [$dirPath, $pathIntoFirstDir]
        );
    }
    
    /**
     * @see \Config::loadConfigFile
     * Present because the method can be overrided during the test
     */
    protected function loadConfigFile($fileKey, $filePath)
    {
        return $this->callOverrideOrParent(
            'loadConfigFile',
            [$fileKey, $filePath]
        );
    }
    
    /**
     * @see \Config::loadJsonConfigFile
     * Present because the method can be overrided during the test
     */
    protected function loadJsonConfigFile($fileKey, $filePath)
    {
        return $this->callOverrideOrParent(
            'loadJsonConfigFile',
            [$fileKey, $filePath]
        );
    }
    
    /**
     * @see \Config::loadPhpConfigFile
     * Present because the method can be overrided during the test
     */
    protected function loadPhpConfigFile($fileKey, $filePath)
    {
        return $this->callOverrideOrParent(
            'loadPhpConfigFile',
            [$fileKey, $filePath]
        );
    }
    
    /**
     * @see \Config::getValue
     * Present because the method can be overrided during the test
     */
    public function getValue($key, $file = null)
    {
        return $this->callOverrideOrParent('getValue', [$key, $file]);
    }
    
    /**
     * @see \Config::getConfig
     * Present because the method can be overrided during the test
     */
    public function getConfig()
    {
        return $this->callOverrideOrParent('getConfig', []);
    }
    
    /**
     * @see \Config::getConfigForFile
     * Present because the method can be overrided during the test
     */
    public function getConfigForFile($file)
    {
        return $this->callOverrideOrParent('getConfigForFile', [$file]);
    }
}
