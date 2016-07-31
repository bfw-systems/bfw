<?php

namespace BFW;

use \Exception;

class Config
{
    protected $configDirName = '';

    protected $configDir     = '';

    protected $configFiles   = [];

    protected $config        = [];

    public function __construct($configDirName)
    {
        $this->configDirName = $configDirName;
        $this->configDir     = CONFIG_DIR.$this->configDirName;

        $this->searchAllConfigsFiles($this->configDir);
        $this->loadAllConfigsFiles();
    }

    protected function searchAllConfigsFiles($dirPath, $pathFromRoot = '')
    {
        if (!file_exists($dirPath)) {
            return;
        }

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

    protected function loadAllConfigsFiles()
    {
        foreach ($this->configFiles as $fileKey => $filePath) {
            $this->loadConfigFile($fileKey, $filePath);
        }
    }

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

    protected function loadJsonConfigFile($fileKey, $filePath)
    {
        $json   = file_get_contents($filePath);
        $config = json_decode($json);

        if ($config === null) {
            throw new Exception(json_last_error_msg());
        }

        $this->config[$fileKey] = $config;
    }

    protected function loadPhpConfigFile($fileKey, $filePath)
    {
        $this->config[$fileKey] = require($filePath);
    }

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

        if (!isset($config[$key])) {
            throw new Exception('The config key '.$key.' not exist in config');
        }

        return $config[$key];
    }
}
