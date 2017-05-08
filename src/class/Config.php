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
     * @var string $configDir Complete path of the readed directory
     */
    protected $configDir = '';

    /**
     * @var string[] $configFiles List of files to read
     */
    protected $configFiles = [];

    /**
     * @var array $config List of config value found
     */
    protected $config = [];

    /**
     * Constructor
     * Define properties configDirName and configDir
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
        $this->searchAllConfigFiles($this->configDir);
        
        foreach ($this->configFiles as $fileKey => $filePath) {
            $this->loadConfigFile($fileKey, $filePath);
        }
    }

    /**
     * Search all config files in a directory
     * Search also in sub-directory (2nd parameter)
     * 
     * @param string $dirPath The directory path where is run the search
     * @param string $pathIntoFirstDir (default '') Used when this method
     *  reads a subdirectory. It's the path from the directory read during
     *  the first call to this method.
     * 
     * @return void
     */
    protected function searchAllConfigFiles($dirPath, $pathIntoFirstDir = '')
    {
        if (!file_exists($dirPath)) {
            return;
        }

        //Remove some value in list of file
        $listFiles = array_diff(scandir($dirPath), ['.', '..']);

        foreach ($listFiles as $file) {
            $fileKey  = $pathIntoFirstDir.$file;
            $readPath = $dirPath.'/'.$file;

            if (is_file($readPath)) {
                $this->configFiles[$fileKey] = $readPath;
            } elseif (is_link($readPath)) {
                $this->configFiles[$fileKey] = realpath($readPath);
            } elseif (is_dir($readPath)) {
                $this->searchAllConfigFiles(
                    $readPath,
                    $pathIntoFirstDir.$file.'/'
                );
            }
        }
    }

    /**
     * Load a config file.
     * Find the file's extension and call the method to parse the file
     * 
     * @param string $fileKey The file's key. Most of the time, the path to
     *  the file from the $this->configDir value
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
     * @param string $fileKey The file's key. Most of the time, the path to
     *  the file from the $this->configDir value
     * @param string $filePath The path to the file
     * 
     * @return void
     * 
     * @throws Exception If there is an error from the json parser
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
     * @param string $fileKey The file's key. Most of the time, the path to
     *  the file from the $this->configDir value
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
     * @param string $key The asked key for the value
     * @param string $file (default null) If many file is loaded, the file name
     *  where is the key. Is the file is into a sub-directory, the
     *  sub-directory should be present.
     * 
     * @return mixed
     * 
     * @throws Exception If file parameter is null and there are many files. Or
     *  if the file not exist. Or if the key not exist.
     */
    public function getConfig($key, $file = null)
    {
        $nbConfigFile = count($this->config);

        if ($file === null && $nbConfigFile > 1) {
            throw new Exception(
                'There are many config files. Please indicate the file to'
                .' obtain the config '.$key
            );
        }

        if ($nbConfigFile === 1) {
            $file = key($this->config);
        }

        if (!isset($this->config[$file])) {
            throw new Exception(
                'The file '.$file.' has not been found for config '.$key
            );
        }

        $config = (array) $this->config[$file];
        if (!array_key_exists($key, $config)) {
            throw new Exception('The config key '.$key.' has not been found');
        }

        return $config[$key];
    }
}
