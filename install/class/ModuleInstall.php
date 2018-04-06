<?php

namespace BFW\Install;

use \Exception;
use \BFW\Helpers\Cli;

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
     * @const ERR_REINSTALL_FAIL_UNLINK Exception code if the reinstall fail
     * because the module symlink can not be remove.
     */
    const ERR_REINSTALL_FAIL_UNLINK = 1102002;
    
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
     * @const ERR_INSTALL_FAIL_SYMLINK Exception code if the symlink fail
     */
    const ERR_INSTALL_FAIL_SYMLINK = 1102008;
    
    /**
     * @const ERR_FAIL_CREATE_CONFIG_DIR Exception code if the config directory
     * can not be create.
     */
    const ERR_FAIL_CREATE_CONFIG_DIR = 1102009;
    
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
        if (empty($this->sourceSrcPath)) {
            $this->loadInfos();
        }
        
        $this->forceReinstall = $reinstall;
        
        Cli::displayMsgNL($this->name.' : Run install.');
        
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
        Cli::displayMsg(' > Create symbolic link ... ');

        $alreadyCreated = file_exists($this->targetSrcPath);

        //If symlink already exist and it's a reinstall mode
        if ($alreadyCreated && $this->forceReinstall === true) {
            Cli::displayMsg('[Force Reinstall: Remove symlink] ');
            $alreadyCreated = false;

            //Error with remove symlink
            if (!unlink($this->targetSrcPath)) {
                Cli::displayMsgNL('Symbolic link remove fail.', 'red', 'bold');
                
                throw new Exception(
                    'Reinstall fail. Symlink remove error',
                    $this::ERR_REINSTALL_FAIL_UNLINK
                );
            }
        }

        //If module already exist in "modules" directory
        if ($alreadyCreated) {
            Cli::displayMsgNL(
                'Not created. Module already exist in \'modules\' directory.',
                'yellow',
                'bold'
            );
            return;
        }

        //If the creation of the symbolic link has failed.
        if (!symlink($this->sourceSrcPath, $this->targetSrcPath)) {
            Cli::displayMsgNL('Symbolic link creation fail.', 'red', 'bold');
            
            throw new Exception(
                'Symbolic link creation fail.',
                $this::ERR_INSTALL_FAIL_SYMLINK
            );
        }

        Cli::displayMsgNL('Done', 'green', 'bold');
    }

    /**
     * Create a directory into project config directory for the module and
     * copy all config files declared to him
     * 
     * @return void
     */
    protected function copyConfigFiles()
    {
        Cli::displayMsgNL(' > Copy config files : ');

        //No file to copy
        if ($this->configFilesList === []) {
            Cli::displayMsg(' >> ');
            Cli::displayMsgNL(
                'No config file declared. Pass',
                'yellow',
                'bold'
            );
            
            return;
        }

        //Create the module directory into the config directory.
        $this->createConfigDirectory();

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
     * @return void
     * 
     * @throws Exception : If remove directory fail with the reinstall option
     */
    protected function createConfigDirectory()
    {
        Cli::displayMsg(' >> Create config directory for this module ... ');
        
        $alreadyExist = file_exists($this->targetConfigPath);
        
        //If the directory already exist and if it's a reinstall
        if ($alreadyExist && $this->forceReinstall === true) {
            Cli::displayMsg('[Force Reinstall: Remove directory] ');
            
            $alreadyExist = false;
            if (!$this->removeRecursiveDirectory($this->targetConfigPath)) {
                Cli::displayMsgNL(
                    'Remove the module config directory have fail',
                    'red',
                    'bold'
                );
                
                throw new Exception(
                    'Reinstall fail. Remove module config directory error.',
                    $this::ERR_REINSTALL_FAIL_REMOVE_CONFIG_DIR
                );
            }
        }
        
        //If the directory already exist, nothing to do
        if ($alreadyExist) {
            Cli::displayMsgNL('Already exist', 'yellow', 'bold');
            return;
        }
        
        //Create the directory
        if (!mkdir($this->targetConfigPath, 0755)) {
            Cli::displayMsgNL('Fail', 'red', 'bold');
            
            throw new Exception(
                'Error to create the config directory.',
                $this::ERR_FAIL_CREATE_CONFIG_DIR
            );
        }

        Cli::displayMsgNL('Done', 'green', 'bold');
    }
    
    /**
     * Remove folders recursively
     * 
     * @see http://php.net/manual/fr/function.rmdir.php#110489
     * 
     * @param string $dirPath Path to directory to remove
     * 
     * @return boolean
     */
    protected function removeRecursiveDirectory($dirPath)
    {
        $fileList = array_diff(scandir($dirPath), ['.','..']);
        
        foreach ($fileList as $filePath) {
            if (is_dir($filePath)) {
                $this->removeRecursiveDirectory($filePath);
            } else {
                unlink($filePath);
            }
        }
        
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
        Cli::displayMsg(' >> Copy '.$configFileName.' ... ');

        //Define paths to the config file
        $sourceFile = realpath($this->sourceConfigPath.'/'.$configFileName);
        $targetFile = realpath($this->targetConfigPath).'/'.$configFileName;

        //Check if config file already exist
        if (file_exists($targetFile)) {
            Cli::displayMsgNL('Already exist', 'yellow', 'bold');
            return;
        }

        //If source file not exist
        if (!file_exists($sourceFile)) {
            Cli::displayMsgNL(
                'Config file not exist in module source.',
                'red',
                'bold'
            );
            
            throw new Exception(
                'Source file not exist',
                $this::ERR_COPY_CONFIG_SRC_FILE_NOT_EXIST
            );
        }

        //Alors on copie le fichier vers le dossier /configs/[monModule]/
        if (!copy($sourceFile, $targetFile)) {
            Cli::displayMsgNL('Fail', 'red', 'bold');
            throw new Exception(
                'Copy fail',
                $this::ERR_COPY_CONFIG_FAIL
            );
        }

        Cli::displayMsgNL('Done', 'green', 'bold');
    }

    /**
     * Check if a specific install script is declared for the module and if
     * the value is true, use the default script name "runInstallModule.php".
     * 
     * @return void
     */
    protected function checkInstallScript()
    {
        Cli::displayMsgNL(' > Check install specific script :');

        //If no script to complete the install
        if (
            $this->sourceInstallScript === ''
            || $this->sourceInstallScript === false
        ) {
            Cli::displayMsg(' >> ');
            Cli::displayMsgNL(
                'No specific script declared. Pass',
                'yellow',
                'bold'
            );
            
            return;
        }

        //If the module ask a install script but not declare the name
        //Use the default name
        if ($this->sourceInstallScript === true) {
            $this->sourceInstallScript = 'runInstallModule.php';
        }
        
        Cli::displayMsg(' >> ');
        Cli::displayMsgNL(
            'Scripts find. Add to list to execute.',
            'yellow',
            'bold'
        );
    }
    
    /**
     * Run the module specific install script
     * 
     * @param string $scriptName The script name to execute
     * 
     * @return void
     */
    public function runInstall($scriptName) {
        Cli::displayMsg(' >> ');
        Cli::displayMsgNL('Execute script '.$scriptName, 'yellow', 'bold');
        
        require_once($this->sourcePath.'/'.$this->sourceInstallScript);
        Cli::displayMsgNL('');
    }
}
