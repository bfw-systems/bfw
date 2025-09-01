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

        // First, handle manifest.json - always copy/update it for comparison
        $this->copyConfigFile(
            $sourceConfigPath.'manifest.json',
            $this->configPath.'/manifest.json'
        );

        // Check if we need to update config files
        $this->updateConfigFiles($sourceConfigPath, $configFileList);

        // Copy any new config files that don't exist yet
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
            // For manifest.json, we need special handling for updates
            if (basename($destPath) === 'manifest.json' && file_exists($destPath)) {
                $this->updateManifestForCopy($sourcePath, $destPath);
                return;
            }

            $this->fileManager->copyFile($sourcePath, $destPath);
        } catch (Exception $e) {
            if ($e->getCode() !== FileManager::EXCEP_FILE_EXIST) {
                throw $e;
            }
        }
    }

    /**
     * Handle manifest.json updates during copy operations
     *
     * @param string $sourcePath Source manifest path
     * @param string $destPath Destination manifest path
     *
     * @return void
     */
    protected function updateManifestForCopy(string $sourcePath, string $destPath)
    {
        $sourceManifest = $this->loadManifest($sourcePath);
        $appManifest = $this->loadManifest($destPath);

        // If app manifest doesn't have autoUpdate flag, add it with default true
        if (!isset($appManifest['autoUpdate'])) {
            $appManifest['autoUpdate'] = true;
        }

        // Add any new config file entries from source that don't exist in app
        foreach ($sourceManifest as $configFile => $info) {
            if ($configFile === 'autoUpdate') {
                continue; // Don't overwrite autoUpdate setting
            }

            if (!isset($appManifest[$configFile])) {
                $appManifest[$configFile] = $info;
            }
        }

        // Write updated manifest back
        try {
            $content = json_encode($appManifest, JSON_PRETTY_PRINT);
            file_put_contents($destPath, $content);
        } catch (Exception $e) {
            $this->logger->warning(
                'Module - Failed to update manifest during copy',
                [
                    'name' => $this->name,
                    'path' => $destPath,
                    'error' => $e->getMessage()
                ]
            );
        }
    }

    /**
     * Update config files if version changes are detected and auto-update is enabled
     *
     * @param string $sourceConfigPath The source config path
     * @param array $configFileList List of config files
     *
     * @return void
     */
    protected function updateConfigFiles(string $sourceConfigPath, array $configFileList)
    {
        $sourceManifestPath = $sourceConfigPath.'manifest.json';
        $appManifestPath = $this->configPath.'/manifest.json';

        // If no source manifest, nothing to compare
        if (!file_exists($sourceManifestPath)) {
            return;
        }

        // If no app manifest exists yet, this is first install - no update needed
        if (!file_exists($appManifestPath)) {
            return;
        }

        $sourceManifest = $this->loadManifest($sourceManifestPath);
        $appManifest = $this->loadManifest($appManifestPath);

        // Check if auto-update is disabled
        if (!$this->isAutoUpdateEnabled($appManifest)) {
            $this->logger->debug(
                'Module - Auto-update disabled for config files',
                ['name' => $this->name]
            );
            return;
        }

        $this->logger->debug(
            'Module - Checking for config file updates',
            [
                'name' => $this->name,
                'sourceConfigPath' => $sourceConfigPath,
                'configFiles' => $configFileList
            ]
        );

        foreach ($configFileList as $configFilename) {
            $this->updateConfigFileIfNeeded(
                $sourceConfigPath,
                $configFilename,
                $sourceManifest,
                $appManifest
            );
        }

        // Update manifest with new versions after successful updates
        $this->updateManifest($sourceManifest, $appManifestPath);
    }

    /**
     * Load and decode manifest.json file
     *
     * @param string $manifestPath Path to manifest.json file
     *
     * @return array|object Decoded manifest data
     */
    protected function loadManifest(string $manifestPath)
    {
        if (!file_exists($manifestPath)) {
            return [];
        }

        $content = file_get_contents($manifestPath);
        $manifest = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->logger->warning(
                'Module - Invalid manifest.json format',
                [
                    'name' => $this->name,
                    'path' => $manifestPath,
                    'error' => json_last_error_msg()
                ]
            );
            return [];
        }

        return $manifest;
    }

    /**
     * Check if auto-update is enabled in the app manifest
     *
     * @param array $appManifest The application manifest data
     *
     * @return bool True if auto-update is enabled
     */
    protected function isAutoUpdateEnabled(array $appManifest): bool
    {
        return isset($appManifest['autoUpdate']) && $appManifest['autoUpdate'] === true;
    }

    /**
     * Update a single config file if version has changed
     *
     * @param string $sourceConfigPath Source config directory path
     * @param string $configFilename Name of the config file
     * @param array $sourceManifest Source manifest data
     * @param array $appManifest Application manifest data
     *
     * @return void
     */
    protected function updateConfigFileIfNeeded(
        string $sourceConfigPath,
        string $configFilename,
        array $sourceManifest,
        array $appManifest
    ) {
        $sourceFilePath = $sourceConfigPath.$configFilename;
        $appFilePath = $this->configPath.'/'.$configFilename;

        // Skip if source file doesn't exist
        if (!file_exists($sourceFilePath)) {
            return;
        }

        // Skip if app file doesn't exist (will be copied by copyConfigFile)
        if (!file_exists($appFilePath)) {
            return;
        }

        $sourceVersion = $this->getConfigFileVersion($sourceManifest, $configFilename);
        $appVersion = $this->getConfigFileVersion($appManifest, $configFilename);

        // If versions are the same, no update needed
        if ($sourceVersion === $appVersion) {
            return;
        }

        $this->logger->info(
            'Module - Config file version change detected',
            [
                'name' => $this->name,
                'file' => $configFilename,
                'currentVersion' => $appVersion,
                'newVersion' => $sourceVersion
            ]
        );

        // Create backup before updating
        $this->backupConfigFile($appFilePath);

        // Copy the new version
        try {
            $this->fileManager->copyFile($sourceFilePath, $appFilePath);
            
            $this->logger->info(
                'Module - Config file updated successfully',
                [
                    'name' => $this->name,
                    'file' => $configFilename,
                    'version' => $sourceVersion
                ]
            );
        } catch (Exception $e) {
            $this->logger->error(
                'Module - Failed to update config file',
                [
                    'name' => $this->name,
                    'file' => $configFilename,
                    'error' => $e->getMessage()
                ]
            );
        }
    }

    /**
     * Get version for a specific config file from manifest
     *
     * @param array $manifest Manifest data
     * @param string $configFilename Name of the config file
     *
     * @return string Version string or empty string if not found
     */
    protected function getConfigFileVersion(array $manifest, string $configFilename): string
    {
        if (!isset($manifest[$configFilename]) || !isset($manifest[$configFilename]['version'])) {
            return '';
        }

        return (string) $manifest[$configFilename]['version'];
    }

    /**
     * Create a backup of the existing config file
     *
     * @param string $configFilePath Path to the config file to backup
     *
     * @return void
     */
    protected function backupConfigFile(string $configFilePath)
    {
        if (!file_exists($configFilePath)) {
            return;
        }

        $backupPath = $configFilePath.'.backup.'.date('Y-m-d-H-i-s');

        try {
            $this->fileManager->copyFile($configFilePath, $backupPath);
            
            $this->logger->info(
                'Module - Config file backed up',
                [
                    'name' => $this->name,
                    'original' => $configFilePath,
                    'backup' => $backupPath
                ]
            );
        } catch (Exception $e) {
            $this->logger->warning(
                'Module - Failed to backup config file',
                [
                    'name' => $this->name,
                    'file' => $configFilePath,
                    'error' => $e->getMessage()
                ]
            );
        }
    }

    /**
     * Update the application manifest with new version information
     *
     * @param array $sourceManifest Source manifest data
     * @param string $appManifestPath Path to application manifest file
     *
     * @return void
     */
    protected function updateManifest(array $sourceManifest, string $appManifestPath)
    {
        if (!file_exists($appManifestPath)) {
            return;
        }

        $appManifest = $this->loadManifest($appManifestPath);

        // Preserve autoUpdate setting and merge new version info
        foreach ($sourceManifest as $configFile => $info) {
            if ($configFile === 'autoUpdate') {
                continue; // Don't overwrite autoUpdate setting
            }

            if (isset($info['version'])) {
                $appManifest[$configFile]['version'] = $info['version'];
            }
        }

        try {
            $content = json_encode($appManifest, JSON_PRETTY_PRINT);
            file_put_contents($appManifestPath, $content);

            $this->logger->debug(
                'Module - Manifest updated',
                ['name' => $this->name, 'path' => $appManifestPath]
            );
        } catch (Exception $e) {
            $this->logger->warning(
                'Module - Failed to update manifest',
                [
                    'name' => $this->name,
                    'path' => $appManifestPath,
                    'error' => $e->getMessage()
                ]
            );
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
