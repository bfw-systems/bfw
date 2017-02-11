<?php

namespace BFW\Install;

use \Exception;

/**
 * Class to get module install informations and run install of the module
 */
class ModuleInstall
{
    /**
     * @const INSTALL_SCRIPT_VERSION : Script's version
     */
    const INSTALL_SCRIPT_VERSION = '3.0.0';

    /**
     * @var string $projectPath : Path to root bfw project
     */
    protected $projectPath = '';
    
    /**
     * @var string $bfwConfigPath : Path to bfw config directory
     */
    protected $bfwConfigPath = '';
    
    /**
     * @var string $bfwModulePath : Path to bfw modules directory
     */
    protected $bfwModulePath = '';
    
    /**
     * @var boolean $forceReinstall : Force complete reinstall of module
     */
    protected $forceReinstall = false;
    
    /**
     * @var string $name : Module name
     */
    protected $name = '';
    
    /**
     * @var string $sourcePath : Path to module which be installed
     */
    protected $sourcePath = '';
    
    /**
     * @var string $sourceSrcPath : Path to directory contains file to install
     *  in project module directory
     */
    protected $sourceSrcPath = '';
    
    /**
     * @var string $sourceConfigPath : Path to directory contains config file
     *  to install in projet config directory
     */
    protected $sourceConfigPath = '';
    
    /**
     * @var array $configFiles : List of config file
     *                              to copy on the config directory
     */
    protected $configFilesList = [];
    
    /**
     * @var string|bool $sourceInstallScript : Script to run for a specific
     *  install of the module
     */
    protected $sourceInstallScript = '';
    
    /**
     * @var string $targetSrcPath : Path to directory where module will be
     *  installed
     */
    protected $targetSrcPath = '';
    
    /**
     * @var string $targetConfigPath : Path to directory where config files
     *  will be installed
     */
    protected $targetConfigPath = '';
    
    /**
     * Constructor
     * 
     * @param string $projectPath Path to root bfw project
     * @param string $modulePath Path to module which be installed
     */
    public function __construct($projectPath, $modulePath)
    {
        $this->projectPath = $projectPath;
        $this->sourcePath  = $modulePath;
        
        $this->bfwConfigPath = $this->projectPath.'/app/config/';
        $this->bfwModulePath = $this->projectPath.'/app/modules/';
    }

    /**
     * Get accessor to module name
     * 
     * @return string : Module name
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Find the module name and declare path to target directories install.
     * 
     * @return void
     */
    protected function findModuleName()
    {
        $pathExplode = explode('/', $this->sourcePath);
        $this->name  = $pathExplode[(count($pathExplode) - 1)];
        
        $this->targetSrcPath    = $this->bfwModulePath.$this->name;
        $this->targetConfigPath = $this->bfwConfigPath.$this->name;
    }
    
    /**
     * Get infos for this module from BFW Module class
     * It's a separate method for easy override.
     * 
     * @return \stdClass
     */
    protected function getInfosFromModule()
    {
        return \BFW\Module::installInfos($this->sourcePath);
    }
    
    /**
     * Load module informations from files
     * 
     * @return void
     */
    public function loadInfos()
    {
        $this->findModuleName();
        
        $infos = $this->getInfosFromModule();
        
        //check if srcPath is define
        if (!property_exists($infos, 'srcPath')) {
            throw new Exception(
                'srcPath must be present in install json file for module '
                .$this->name
            );
        }
        
        //Defines default paths
        $this->sourceSrcPath    = $infos->srcPath;
        $this->sourceConfigPath = $infos->srcPath;

        //Defines properties
        
        if (property_exists($infos, 'configFiles')) {
            $this->configFilesList = (array) $infos->configFiles;
        }

        if (property_exists($infos, 'configPath')) {
            $this->sourceConfigPath = $infos->configPath;
        }

        if (property_exists($infos, 'installScript')) {
            $this->sourceInstallScript = $infos->installScript;
        }

        $this->sourceSrcPath = realpath(
            $this->sourcePath.'/'.$this->sourceSrcPath
        );
        
        $this->sourceConfigPath = realpath(
            $this->sourcePath.'/'.$this->sourceConfigPath
        );
    }

    /**
     * Run module installation
     * 
     * @param boolean $reinstall : If we force reinstall module
     * 
     * @return void
     */
    public function install($reinstall)
    {
        $this->forceReinstall = $reinstall;
        
        echo $this->name." : Run install.\n";
        
        try {
            $this->createSymbolicLink();
            $this->copyConfigFiles();
            $this->runInstallScript();
        } catch (Exception $e) {
            trigger_error('Module '.$this->name.' install error : '.$e->getMessage(), E_USER_WARNING);
        }
    }
    
    /**
     * Create symlink in bfw project module directory
     * 
     * @return void
     * 
     * @throws Exception : If remove symlink fail for reinstall option
     */
    protected function createSymbolicLink()
    {
        echo ' > Create symbolic link ... ';

        $alreadyCreated = file_exists($this->targetSrcPath);

        //If symlink already exist and it's a reinstall mode
        if ($alreadyCreated && $this->forceReinstall === true) {
            echo '[Force Reinstall: Remove symlink] ';
            $alreadyCreated = false;

            //Error with remove symlink
            if (!unlink($this->targetSrcPath)) {
                echo "\033[1;31mSymbolic link remove fail.\033[0m\n";
                throw new Exception('Reinstall fail. Symlink remove error');
            }
        }

        //If module already exist in "modules" directory
        if ($alreadyCreated) {
            echo "\033[1;33m"
                .'Not created. Module already exist in \'modules\' directory.'
                ."\033[0m\n";
            return;
        }

        //If symbolic link create fail.
        if (!symlink($this->sourceSrcPath, $this->targetSrcPath)) {
            echo "\033[1;31mSymbolic link creation fail.\033[0m\n";
            return;
        }

        echo "\033[1;32mDone\033[0m\n";
    }

    /**
     * Create a directory in bfw project config directory for this module and
     * copy all config files declared in this directory
     * 
     * @return void
     */
    protected function copyConfigFiles()
    {
        echo ' > Copy config files : '."\n";

        //No file to copy
        if ($this->configFilesList === []) {
            echo ' >> '
                ."\033[1;33m"
                .'No config file declared. Pass'
                ."\033[0m\n";
            
            return;
        }

        //Create the module directory in config directory.
        $configDirStatus = $this->createConfigDirectory();
        if ($configDirStatus === false) {
            return;
        }

        //Copy each config file declared
        foreach ($this->configFilesList as $configFile) {
            try {
                $this->copyConfigFile($configFile);
            } catch (Exception $e) {
                trigger_error(
                    'Module '.$this->name.' Config file '.$configFile
                    .' copy error: '.$e->getMessage(),
                    E_USER_WARNING
                );
            }
        }
    }

    /**
     * Create a directory in bfw project config directory for this module
     * 
     * @return boolean : If directory exist.
     * 
     * @throws Exception : If remove directory fail for reinstall option
     */
    protected function createConfigDirectory()
    {
        echo ' >> Create config directory for this module ... ';
        
        $alreadyExist = file_exists($this->targetConfigPath);
        
        //If the directory already exist and if it's a reinstall
        if ($alreadyExist && $this->forceReinstall === true) {
            echo '[Force Reinstall: Remove directory] ';
            
            $calledClass  = get_called_class(); //Autorize extends this class
            $alreadyExist = false;
            
            if (!$calledClass::removeDirectory($this->targetConfigPath)) {
                echo "\033[1;31m"
                    .'Remove module config directory fail.'
                    ."\033[0m\n";
                
                throw new Exception(
                    'Reinstall fail. Remove module config directory error.'
                );
            }
        }
        
        //If the directory already exist, nothing to do
        if ($alreadyExist) {
            echo "\033[1;33mAlready exist.\033[0m\n";
            return true;
        }
        
        //Create the directory
        if (mkdir($this->targetConfigPath, 0755)) {
            echo "\033[1;32mCreated.\033[0m\n";
            return true;
        }

        //If error during the directory creation
        trigger_error('Module '.$this->name.' Error to create config directory', E_USER_WARNING);
        echo "\033[1;31mFail. \033[0m\n";
        
        return false;
    }
    
    /**
     * Supprime les dossiers récursivement
     * 
     * @param string $dirPath Le chemin vers le dossier
     * 
     * @return boolean
     */
    protected static function removeDirectory($dirPath)
    {
        $calledClass  = get_called_class(); //Autorize extends this class
        
        $dir = opendir($dirPath);
        if ($dir === false) {
            return false;
        }
        
        while (($file = readdir($dir)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            
            $removeStatus = true;
            $filePath     = $dirPath.'/'.$file;
            
            if (is_dir($filePath)) {
                $removeStatus = $calledClass::removeDirectory($filePath);
            } elseif (is_file($filePath) || is_link($filePath)) {
                $removeStatus = unlink($filePath);
            }
            
            if ($removeStatus === false) {
                return false;
            }
        }
        
        closedir($dir);
        return rmdir($dirPath);
    }
    
    /**
     * Copy a config file into config directory for this module
     * 
     * @param string $configFileName : The config filename
     * 
     * @return void
     * 
     * @throws Exception If copy fail or if source file not exist
     */
    protected function copyConfigFile($configFileName)
    {
        echo ' >> Copy '.$configFileName.' ... ';

        //Define paths to the config file
        $sourceFile = realpath($this->sourceConfigPath.'/'.$configFileName);
        $targetFile = realpath($this->targetConfigPath).'/'.$configFileName;

        //Check if config file already exist
        if (file_exists($targetFile)) {
            echo "\033[1;33mAlready exist.\033[0m\n";
            return;
        }

        //If source file not exist
        if (!file_exists($sourceFile)) {
            echo "\033[1;31mConfig file not exist in module source.\033[0m\n";
            throw new Exception('Source file not exist');
        }

        //Alors on copie le fichier vers le dossier /configs/[monModule]/
        if (!copy($sourceFile, $targetFile)) {
            echo "\033[1;31mCopy fail.\033[0m\n";
            throw new Exception('Copy fail');
        }

        echo "\033[1;32mDone\033[0m\n";
    }

    /**
     * Run specific module install script if declared
     * 
     * @return void
     */
    protected function runInstallScript()
    {
        echo ' > Run install specific script :'."\n";

        //If no script to complete the install
        if (
            $this->sourceInstallScript === ''
            || $this->sourceInstallScript === false
        ) {
            echo " >> \033[1;33m"
                .'No specific script declared. Pass'
                ."\033[0m\n";
            
            return;
        }

        //If the module ask a install script but not declare the name
        //Use the default name
        if ($this->sourceInstallScript === true) {
            $this->sourceInstallScript = 'runInstallModule.php';
        }

        //Include the file for complete the install
        require_once($this->sourcePath.'/'.$this->sourceInstallScript);
        echo "\n";
    }
}
