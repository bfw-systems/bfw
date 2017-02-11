<?php

namespace BFW;

use \Exception;

/**
 * Class to load all files from a directory in config dir.
 */
class Config
{
    /**
     * @var string $configDirName Directory's name in config dir
     */
    protected $configDirName = '';

    /**
     * @var string $configDir Complete path to the reading directory
     */
    protected $configDir = '';

    /**
     * @var string[] $configFiles List of file to read
     */
    protected $configFiles = [];

    /**
     * @var array $config List of config value found
     */
    protected $config = [];

    /**
     * Constructor
     * Define attributes configDirName and configDir
     * 
     * @param string $configDirName Directory's name in config dir
     */
    public function __construct($configDirName)
    {
        $this->configDirName = $configDirName;
        $this->configDir     = CONFIG_DIR.$this->configDirName;
    }
    
    /**
     * Search and load all config files which has been found
     * 
     * @return void
     */
    public function loadFiles()
    {
        $this->searchAllConfigsFiles($this->configDir);
        
        foreach ($this->configFiles as $fileKey => $filePath) {
            $this->loadConfigFile($fileKey, $filePath);
        }
    }

    /**
     * Search all config files in a directory
     * Search also in sub-directory (2nd parameter)
     * 
     * @param string $dirPath The directory path where is the search
     * @param string $pathFromRoot (default '') The path to the dir where we
     *      read from the system dir in config dir.
     * 
     * @return void
     */
    protected function searchAllConfigsFiles($dirPath, $pathFromRoot = '')
    {
        if (!file_exists($dirPath)) {
            return;
        }

        //Remove some value in list of file
        $listFiles = array_diff(scandir($dirPath), ['.', '..']);

        foreach ($listFiles as $file) {
            $keyFile  = $pathFromRoot.$file;
            $readPath = $dirPath.'/'.$file;

            if (is_file($readPath)) {
                $this->configFiles[$keyFile] = $readPath;
                continue;
            }

            if (is_link($readPath)) {
                $this->configFiles[$keyFile] = realpath($readPath);
                continue;
            }

            if (is_dir($readPath)) {
                $this->searchAllConfigsFiles($readPath, $pathFromRoot.$file.'/');
                continue;
            }
        }
    }

    /**
     * Load a config file.
     * Find the file's extension and call the method to parse the file
     * 
     * @param string $fileKey The key of file in configFiles array
     * @param string $filePath The path to the file
     * 
     * @return void
     */
    protected function loadConfigFile($fileKey, $filePath)
    {
        $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);

        if ($fileExtension === 'json') {
            $this->loadJsonConfigFile($fileKey, $filePath);
            return;
        }

        if ($fileExtension === 'php') {
            $this->loadPhpConfigFile($fileKey, $filePath);
            return;
        }

        //@TODO : YAML
    }

    /**
     * Load a json config file
     * 
     * @param string $fileKey The key of file in configFiles array
     * @param string $filePath The path to the file
     * 
     * @return void
     * 
     * @throws Exception If json parser error
     */
    protected function loadJsonConfigFile($fileKey, $filePath)
    {
        $json   = file_get_contents($filePath);
        $config = json_decode($json);

        if ($config === null) {
            throw new Exception(json_last_error_msg());
        }

        $this->config[$fileKey] = $config;
    }

    /**
     * Load a php config file
     * 
     * @param string $fileKey The key of file in configFiles array
     * @param string $filePath The path to the file
     * 
     * @return void
     */
    protected function loadPhpConfigFile($fileKey, $filePath)
    {
        $this->config[$fileKey] = require($filePath);
    }

    /**
     * Return a config value for a key
     * 
     * @param string $key The key for the value
     * @param string $file (default null) If many file is loaded, the file name
     *      where is the key 
     * 
     * @return mixed
     * 
     * @throws Exception If file parameter is null and there are many file. Or
     *      if the file not exist. Or if the key not exist.
     */
    public function getConfig($key, $file = null)
    {
        $nbConfigFile = count($this->config);

        if ($file === null && $nbConfigFile > 1) {
            throw new Exception('Please indicate a file for get config '.$key);
        }

        if ($nbConfigFile === 1) {
            $file = key($this->config);
        }

        if (!isset($this->config[$file])) {
            throw new Exception('The file '.$file.' not exist for config '.$key);
        }

        $config = (array) $this->config[$file];

        if (!array_key_exists($key, $config)) {
            throw new Exception('The config key '.$key.' not exist in config');
        }

        return $config[$key];
    }
}
