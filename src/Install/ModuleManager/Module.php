<?php

namespace BFW\Install\ModuleManager;

use Exception;
use bultonFr\Utils\Files\FileManager;

class Module
{
    /**
     * @const EXCEP_DELETE_ENABLED_MODULE Exception code if the user want
     * delete a module which is always enabled.
     */
    const EXCEP_DELETE_ENABLED_MODULE = 1702001;
    
    /**
     * The monolog logger instance
     *
     * @var \Monolog\Logger $logger
     */
    protected $logger;

    /**
     * The FileManager instance used to do action on files
     *
     * @var \bultonFr\Utils\Files\FileManager $fileManager
     */
    protected $fileManager;
    
    /**
     * The module's name
     *
     * @var string
     */
    protected $name;
    
    /**
     * The path to the source of the module in the vendor directory
     * The value is set by a setter only, so is possible the value is not
     * into the vendor directory if the user not send that.
     *
     * Used only with the add action.
     *
     * @var string
     */
    protected $vendorPath = '';
    
    /**
     * The path of the module into /app/modules/available folder
     *
     * @var string
     */
    protected $availablePath = '';
    
    /**
     * The path of the module into /app/modules/enabled folder
     *
     * @var string
     */
    protected $enabledPath = '';
    
    /**
     * The path of the config module into /app/config folder
     *
     * @var string
     */
    protected $configPath = '';
    
    /**
     * The class which contain info abouts the module
     *
     * @var \BFW\Install\ModuleManager\ModulesInfo|null
     */
    protected $info;
    
    /**
     * Constructor
     *
     * Obtain the logger instance.
     * Instanciate the FileManager.
     * Define paths into /app/modules/* and /app/config.
     *
     * @param string $name The module's name
     */
    public function __construct(string $name)
    {
        $this->logger = \BFW\Application::getInstance()
            ->getMonolog()
            ->getLogger()
        ;

        $this->fileManager = new FileManager($this->logger);
        
        $this->name          = $name;
        $this->availablePath = MODULES_AVAILABLE_DIR.$name;
        $this->enabledPath   = MODULES_ENABLED_DIR.$name;
        $this->configPath    = CONFIG_DIR.$name;
    }

    /**
     * Get the value of logger
     *
     * @return \Monolog\Logger
     */
    public function getLogger(): \Monolog\Logger
    {
        return $this->logger;
    }

    /**
     * Get the value of fileManager
     *
     * @return \bultonFr\Utils\Files\FileManager
     */
    public function getFileManager(): FileManager
    {
        return $this->fileManager;
    }
    
    /**
     * Get the value of name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the value of vendorPath
     *
     * @return string
     */
    public function getVendorPath(): string
    {
        return $this->vendorPath;
    }
    
    /**
     * Set the value of vendorPath
     *
     * @param string $path The path of the module into vendor
     *
     * @return $this
     */
    public function setVendorPath(string $path): self
    {
        $this->vendorPath = $path;

        return $this;
    }

    /**
     * Get the value of availablePath
     *
     * @return string
     */
    public function getAvailablePath(): string
    {
        return $this->availablePath;
    }

    /**
     * Get the value of enabledPath
     *
     * @return string
     */
    public function getEnabledPath(): string
    {
        return $this->enabledPath;
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
     * Get the value of info
     *
     * @return \BFW\Install\ModuleManager\ModulesInfo|null
     */
    public function getInfo()
    {
        return $this->info;
    }
    
    /**
     * Execute the installation action
     * * Create a symlink from module in vendor into /app/modules/available
     * * Create config directory for the module
     * * Copy configs files
     *
     * @return void
     */
    public function doAdd()
    {
        $this->readModuleInfo($this->vendorPath);
        $this->fileManager->createSymLink(
            $this->vendorPath,
            $this->availablePath
        );
        $this->copyAllConfigFiles();
    }
    
    /**
     * Execute the enable action
     * * Create the symlink from module in /app/modules/available into
     *   /app/modules/enabled
     *
     * @return void
     */
    public function doEnable()
    {
        $this->readModuleInfo($this->availablePath);
        $srcPath = $this->info->getSrcPath();
        
        $this->fileManager->createSymLink(
            $this->availablePath.'/'.$srcPath,
            $this->enabledPath
        );
    }
    
    /**
     * Execute the disable action
     * * Remove the symlink into /app/modules/enabled
     *
     * @return void
     */
    public function doDisable()
    {
        $this->readModuleInfo($this->availablePath);
        $this->fileManager->removeSymLink($this->enabledPath);
    }
    
    /**
     * Execute the delete action
     * * Remove the module's directory into /app/modules/available
     * * Remove module's config files
     *
     * @return void
     */
    public function doDelete()
    {
        $this->readModuleInfo($this->availablePath);
        
        if (file_exists($this->enabledPath)) {
            throw new Exception(
                'Module '.$this->name.' is always enabled. Please disable it before delete',
                static::EXCEP_DELETE_ENABLED_MODULE
            );
        }
        
        if (is_link($this->availablePath)) {
            $this->fileManager->removeSymLink($this->availablePath);
        } else {
            $this->fileManager->removeRecursiveDirectory($this->availablePath);
        }

        $this->deleteConfigFiles();
    }
    
    /**
     * Instanciate the moduleInfo class.
     *
     * @param string $modulePath The module's path
     *
     * @return void
     */
    protected function readModuleInfo(string $modulePath)
    {
        $this->logger->debug(
            'Module - Read module info',
            ['name' => $this->name, 'path' => $modulePath]
        );
        
        $moduleInfo = \BFW\Module::installInfo($modulePath);
        $this->info = new ModuleInfo($moduleInfo);
    }
    
    /**
     * Copy all module's declared config files.
     *
     * @return void
     */
    protected function copyAllConfigFiles()
    {
        $sourceConfigPath = $this->availablePath.'/'.$this->info->getConfigPath();
        
        $this->logger->debug(
            'Module - Copy config files',
            [
                'name'             => $this->name,
                'configPath'       => $this->configPath,
                'sourceConfigPath' => $sourceConfigPath,
                'configFiles'      => $this->info->getConfigFiles()
            ]
        );
        
        $configFileList = $this->info->getConfigFiles(); //Need tmp var for empty()
        if (empty($configFileList)) {
            return;
        }
        
        if (file_exists($this->configPath) === false) {
            $this->fileManager->createDirectory($this->configPath);
        }

        $this->copyConfigFile(
            $sourceConfigPath.'manifest.json',
            $this->configPath.'/manifest.json'
        );

        foreach ($configFileList as $configFilename) {
            $this->copyConfigFile(
                $sourceConfigPath.$configFilename,
                $this->configPath.'/'.$configFilename
            );
        }
    }

    /**
     * Copy a config file.
     *
     * @param string $sourcePath The source file path
     * @param string $destPath the destination file path
     *
     * @return void
     */
    protected function copyConfigFile(string $sourcePath, string $destPath)
    {
        try {
            $this->fileManager->copyFile($sourcePath, $destPath);
        } catch (Exception $e) {
            if ($e->getCode() !== FileManager::EXCEP_FILE_EXIST) {
                throw $e;
            }
        }
    }
    
    /**
     * Delete all module's config files and the module's directory in /app/config
     *
     * @return void
     */
    protected function deleteConfigFiles()
    {
        $this->logger->debug(
            'Module - Delete config files',
            ['name' => $this->name, 'configPath' => $this->configPath]
        );
        
        if (file_exists($this->configPath) === false) {
            return;
        }
        
        $this->fileManager->removeRecursiveDirectory($this->configPath);
    }
    
    /**
     * Check if the module has a installation script.
     *
     * @return boolean
     */
    public function hasInstallScript(): bool
    {
        $installScript = $this->info->getInstallScript(); //For empty()

        if (empty($installScript)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Execute the module installation script into the scope of the method.
     *
     * @return void
     */
    public function runInstallScript()
    {
        if ($this->hasInstallScript() === false) {
            return;
        }
        
        $this->logger->debug(
            'Module - Run install script',
            [
                'name'          => $this->name,
                'installScript' => $this->info->getInstallScript()
            ]
        );
        
        require_once($this->availablePath.'/'.$this->info->getInstallScript());
    }
}
