<?php

namespace BFW\Install;

use \Exception;

/**
 * Class to get module install informations and run install of the module
 */
class ModuleInstall
{
    /**
     * @const ERR_LOAD_NO_PROPERTY_SRCPATH Exception code if the property
     * "srcPath" is not present into the bfwModulesInfos.json file.
     */
    const ERR_LOAD_NO_PROPERTY_SRCPATH = 1102001;
    
    /**
     * @const ERR_REINSTALL_FAIL_SYMLINK Exception code if the reinstall fail
     * because the module symlink can not be remove.
     */
    const ERR_REINSTALL_FAIL_SYMLINK = 1102002;
    
    /**
     * @const ERR_REINSTALL_FAIL_REMOVE_CONFIG_DIR Exception code if the
     * reinstall fail because the config directory can not be remove.
     */
    const ERR_REINSTALL_FAIL_REMOVE_CONFIG_DIR = 1102003;
    
    /**
     * @const ERR_COPY_CONFIG_SRC_FILE_NOT_EXIST Exception code if the source
     * config file not exist.
     */
    const ERR_COPY_CONFIG_SRC_FILE_NOT_EXIST = 1102004;
    
    /**
     * @const ERR_COPY_CONFIG_FAIL Exception code if the copy of the config
     * file has failed.
     */
    const ERR_COPY_CONFIG_FAIL = 1102005;
    
    /**
     * @const ERR_LOAD_EMPTY_PROPERTY_SRCPATH Exception code if the property
     * srcPath is empty during the load of the module
     */
    const ERR_LOAD_EMPTY_PROPERTY_SRCPATH = 1102006;
    
    /**
     * @const ERR_LOAD_PATH_NOT_EXIST Exception code if the path define into
     * srcPath or configPath not exist
     */
    const ERR_LOAD_PATH_NOT_EXIST = 1102007;
    
    /**
     * @var string $projectPath : Path to root bfw project
     */
    protected $projectPath = '';
    
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
     *  into project module directory
     */
    protected $sourceSrcPath = '';
    
    /**
     * @var string $sourceConfigPath : Path to directory contains config file
     *  to install into projet config directory
     */
    protected $sourceConfigPath = '';
    
    /**
     * @var array $configFiles : List of config file(s) to copy into the
     *  module config directory of project
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
     * @param string $modulePath Path to the module which be installed
     */
    public function __construct($modulePath)
    {
        $this->projectPath = ROOT_DIR;
        $this->sourcePath  = $modulePath;
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
     * Get accessor to source module path
     * 
     * @return string
     */
    public function getSourcePath()
    {
        return $this->sourcePath;
    }
    
    /**
     * Get accessor to the install script file or list
     * 
     * @return string|array
     */
    public function getSourceInstallScript()
    {
        return $this->sourceInstallScript;
    }
    
    /**
     * Get accessor to the property projectPath
     * 
     * @return string
     */
    public function getProjectPath()
    {
        return $this->projectPath;
    }

    /**
     * Get accessor to the property forceReinstall
     * 
     * @return boolean
     */
    public function getForceReinstall()
    {
        return $this->forceReinstall;
    }

    /**
     * Get accessor to the property sourceSrcPath
     * 
     * @return string
     */
    public function getSourceSrcPath()
    {
        return $this->sourceSrcPath;
    }

    /**
     * Get accessor to the property sourceConfigPath
     * 
     * @return string
     */
    public function getSourceConfigPath()
    {
        return $this->sourceConfigPath;
    }

    /**
     * Get accessor to the property configFilesList
     * 
     * @return string
     */
    public function getConfigFilesList()
    {
        return $this->configFilesList;
    }

    /**
     * Get accessor to the property targetSrcPath
     * 
     * @return string
     */
    public function getTargetSrcPath()
    {
        return $this->targetSrcPath;
    }

    /**
     * Get accessor to the property targetConfigPath
     * 
     * @return string
     */
    public function getTargetConfigPath()
    {
        return $this->targetConfigPath;
    }
    
    /**
     * Find the module name and declare path to target directories install.
     * 
     * @return void
     */
    protected function findModuleName()
    {
        $pathExploded = explode('/', $this->sourcePath);
        $this->name   = $pathExploded[(count($pathExploded) - 1)];
        
        $this->targetSrcPath    = MODULES_DIR.$this->name;
        $this->targetConfigPath = CONFIG_DIR.$this->name;
    }
    
    /**
     * Get infos for the module from BFW Module class
     * 
     * @return \stdClass
     */
    protected function obtainInfosFromModule()
    {
        return \BFW\Module::installInfos($this->sourcePath);
    }
    
    /**
     * Load module infos from files
     * 
     * @return void
     */
    public function loadInfos()
    {
        $this->findModuleName();
        
        $infos = $this->obtainInfosFromModule();
        $this->checkPropertySrcPath($infos);
        
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
        
        if (is_bool($this->sourceSrcPath) || is_bool($this->sourceConfigPath)) {
            throw new Exception(
                'The srcPath or the configPath (if define) properties not exist'
                .' for the module '.$this->name,
                $this::ERR_LOAD_PATH_NOT_EXIST
            );
        }
    }
    
    /**
     * Somes check about the property srcPath.
     * 
     * @param \stdClass $infos
     * 
     * @return boolean
     * 
     * @throws Exception If a check fail
     */
    protected function checkPropertySrcPath($infos)
    {
        if (!property_exists($infos, 'srcPath')) {
            throw new Exception(
                'srcPath must be present into bfwModulesInfos.json file'
                .' for the module '.$this->name,
                $this::ERR_LOAD_NO_PROPERTY_SRCPATH
            );
        }
        
        if (empty($infos->srcPath)) {
            throw new Exception(
                'srcPath property should not be empty into'
                .' bfwModulesInfos.json file for the module '.$this->name,
                $this::ERR_LOAD_EMPTY_PROPERTY_SRCPATH
            );
        }
        
        return true;
    }

    /**
     * Run module install
     * 
     * @param boolean $reinstall : If we force the reinstall of the module
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
            $this->checkInstallScript();
        } catch (Exception $e) {
            trigger_error(
                'Module '.$this->name.' install error : '.$e->getMessage(),
                E_USER_WARNING
            );
        }
    }
    
    /**
     * Create symlink into project module directory
     * 
     * @return void
     * 
     * @throws Exception : If remove symlink fail with the reinstall option
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
                throw new Exception(
                    'Reinstall fail. Symlink remove error',
                    $this::ERR_REINSTALL_FAIL_SYMLINK
                );
            }
        }

        //If module already exist in "modules" directory
        if ($alreadyCreated) {
            echo "\033[1;33m"
                .'Not created. Module already exist in \'modules\' directory.'
                ."\033[0m\n";
            return;
        }

        //If the creation of the symbolic link has failed.
        if (!symlink($this->sourceSrcPath, $this->targetSrcPath)) {
            echo "\033[1;31mSymbolic link creation fail.\033[0m\n";
            return;
        }

        echo "\033[1;32mDone\033[0m\n";
    }

    /**
     * Create a directory into project config directory for the module and
     * copy all config files declared to him
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

        //Create the module directory into the config directory.
        $configDirStatus = $this->createConfigDirectory();
        if ($configDirStatus === false) {
            return;
        }

        //Copy each config file declared
        foreach ($this->configFilesList as $configFileName) {
            try {
                $this->copyConfigFile($configFileName);
            } catch (Exception $e) {
                trigger_error(
                    'Module '.$this->name.' Config file '.$configFileName
                    .' copy error: '.$e->getMessage(),
                    E_USER_WARNING
                );
            }
        }
    }

    /**
     * Create a directory into project config directory for the module
     * 
     * @return boolean : If directory exist.
     * 
     * @throws Exception : If remove directory fail with the reinstall option
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
                    .'Remove the module config directory have fail.'
                    ."\033[0m\n";
                
                throw new Exception(
                    'Reinstall fail. Remove module config directory error.',
                    $this::ERR_REINSTALL_FAIL_REMOVE_CONFIG_DIR
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
        trigger_error(
            'Module '.$this->name.' : Error to create the config directory',
            E_USER_WARNING
        );
        echo "\033[1;31mFail. \033[0m\n";
        
        return false;
    }
    
    /**
     * Remove folders recursively
     * 
     * @param string $dirPath Path to directory to remove
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
     * Copy a config file into config directory for the module
     * 
     * @param string $configFileName : The config filename
     * 
     * @return void
     * 
     * @throws Exception If copy fail or if the source file not exist
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
            throw new Exception(
                'Source file not exist',
                $this::ERR_COPY_CONFIG_SRC_FILE_NOT_EXIST
            );
        }

        //Alors on copie le fichier vers le dossier /configs/[monModule]/
        if (!copy($sourceFile, $targetFile)) {
            echo "\033[1;31mCopy fail.\033[0m\n";
            throw new Exception(
                'Copy fail',
                $this::ERR_COPY_CONFIG_FAIL
            );
        }

        echo "\033[1;32mDone\033[0m\n";
    }

    /**
     * Check if a specific install script is declared for the module and if
     * the value is true, use the default script name "runInstallModule.php".
     * 
     * @return void
     */
    protected function checkInstallScript()
    {
        echo ' > Check install specific script :'."\n";

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
        
        echo " >> \033[1;33m"
            .'Scripts find. Add to list to execute.'
            ."\033[0m\n";
    }
    
    /**
     * Run the module specific install script
     * 
     * @param string $scriptName The script name to execute
     * 
     * @return void
     */
    public function runInstall($scriptName) {
        echo " >> \033[1;33m".'Execute script '.$scriptName."\033[0m\n";
        
        require_once($this->sourcePath.'/'.$this->sourceInstallScript);
        echo "\n";
    }
}
