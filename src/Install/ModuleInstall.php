<?php

namespace BFW\Install;

use \Exception;

/**
 * Class to get module install informations and run install of the module
 */
class ModuleInstall
{
    use \BFW\Traits\BasicCliMsg;
    
    /**
     * @const ERR_LOAD_NO_PROPERTY_SRCPATH Exception code if the property
     * "srcPath" is not present into the bfwModulesInfos.json file.
     */
    const ERR_LOAD_NO_PROPERTY_SRCPATH = 1402001;
    
    /**
     * @const ERR_REINSTALL_FAIL_UNLINK Exception code if the reinstall fail
     * because the module symlink can not be remove.
     */
    const ERR_REINSTALL_FAIL_UNLINK = 1402002;
    
    /**
     * @const ERR_REINSTALL_FAIL_REMOVE_CONFIG_DIR Exception code if the
     * reinstall fail because the config directory can not be remove.
     */
    const ERR_REINSTALL_FAIL_REMOVE_CONFIG_DIR = 1402003;
    
    /**
     * @const ERR_COPY_CONFIG_SRC_FILE_NOT_EXIST Exception code if the source
     * config file not exist.
     */
    const ERR_COPY_CONFIG_SRC_FILE_NOT_EXIST = 1402004;
    
    /**
     * @const ERR_COPY_CONFIG_FAIL Exception code if the copy of the config
     * file has failed.
     */
    const ERR_COPY_CONFIG_FAIL = 1402005;
    
    /**
     * @const ERR_LOAD_EMPTY_PROPERTY_SRCPATH Exception code if the property
     * srcPath is empty during the load of the module
     */
    const ERR_LOAD_EMPTY_PROPERTY_SRCPATH = 1402006;
    
    /**
     * @const ERR_LOAD_PATH_NOT_EXIST Exception code if the path define into
     * srcPath or configPath not exist
     */
    const ERR_LOAD_PATH_NOT_EXIST = 1402007;
    
    /**
     * @const ERR_INSTALL_FAIL_SYMLINK Exception code if the symlink fail
     */
    const ERR_INSTALL_FAIL_SYMLINK = 1402008;
    
    /**
     * @const ERR_FAIL_CREATE_CONFIG_DIR Exception code if the config directory
     * can not be create.
     */
    const ERR_FAIL_CREATE_CONFIG_DIR = 1402009;
    
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
     * @var bool|string|array $sourceInstallScript : Script to run for a
     *  specific install of the module
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
    public function __construct(string $modulePath)
    {
        $this->projectPath = ROOT_DIR;
        $this->sourcePath  = $modulePath;
    }

    /**
     * Get accessor to module name
     * 
     * @return string : Module name
     */
    public function getName(): string
    {
        return $this->name;
    }
    
    /**
     * Get accessor to source module path
     * 
     * @return string
     */
    public function getSourcePath(): string
    {
        return $this->sourcePath;
    }
    
    /**
     * Get accessor to the install script file or list
     * 
     * @return bool|string|array
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
    public function getProjectPath(): string
    {
        return $this->projectPath;
    }

    /**
     * Get accessor to the property forceReinstall
     * 
     * @return boolean
     */
    public function getForceReinstall(): bool
    {
        return $this->forceReinstall;
    }

    /**
     * Get accessor to the property sourceSrcPath
     * 
     * @return string
     */
    public function getSourceSrcPath(): string
    {
        return $this->sourceSrcPath;
    }

    /**
     * Get accessor to the property sourceConfigPath
     * 
     * @return string
     */
    public function getSourceConfigPath(): string
    {
        return $this->sourceConfigPath;
    }

    /**
     * Get accessor to the property configFilesList
     * 
     * @return array
     */
    public function getConfigFilesList(): array
    {
        return $this->configFilesList;
    }

    /**
     * Get accessor to the property targetSrcPath
     * 
     * @return string
     */
    public function getTargetSrcPath(): string
    {
        return $this->targetSrcPath;
    }

    /**
     * Get accessor to the property targetConfigPath
     * 
     * @return string
     */
    public function getTargetConfigPath(): string
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
    protected function obtainInfosFromModule(): \stdClass
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
     * @throws \Exception If a check fail
     */
    protected function checkPropertySrcPath(\stdClass $infos): bool
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
    public function install(bool $reinstall)
    {
        if (empty($this->sourceSrcPath)) {
            $this->loadInfos();
        }
        
        $this->forceReinstall = $reinstall;
        
        \BFW\Application::getInstance()
            ->getMonolog()
            ->getLogger()
            ->debug(
                'Installing module.',
                ['name' => $this->name]
            );
        
        $this->displayMsgNLInCli($this->name.' : Run install.');
        
        try {
            $this->createSymbolicLink();
            $this->copyConfigFiles();
            $this->checkInstallScript();
        } catch (Exception $e) {
            trigger_error(
                'Module '.$this->name.' install error : '.$e->getMessage(),
                E_USER_WARNING
            );
        
            \BFW\Application::getInstance()
                ->getMonolog()
                ->getLogger()
                ->debug(
                    'Module installation failed.',
                    ['name' => $this->name]
                );
        }
    }
    
    /**
     * Create symlink into project module directory
     * 
     * @return void
     * 
     * @throws \Exception : If remove symlink fail with the reinstall option
     */
    protected function createSymbolicLink()
    {
        $this->displayMsgInCli(' > Create symbolic link ... ');

        $alreadyCreated = file_exists($this->targetSrcPath);

        //If symlink already exist and it's a reinstall mode
        if ($alreadyCreated && $this->forceReinstall === true) {
            $this->displayMsgInCli('[Force Reinstall: Remove symlink] ');
            $alreadyCreated = false;

            //Error with remove symlink
            if (!unlink($this->targetSrcPath)) {
                $this->displayMsgNLInCli('Symbolic link remove fail.', 'red', 'bold');
                
                throw new Exception(
                    'Reinstall fail. Symlink remove error',
                    $this::ERR_REINSTALL_FAIL_UNLINK
                );
            }
        }

        //If module already exist in "modules" directory
        if ($alreadyCreated) {
            $this->displayMsgNLInCli(
                'Not created. Module already exist in \'modules\' directory.',
                'yellow',
                'bold'
            );
            return;
        }

        //If the creation of the symbolic link has failed.
        if (!symlink($this->sourceSrcPath, $this->targetSrcPath)) {
            $this->displayMsgNLInCli('Symbolic link creation fail.', 'red', 'bold');
            
            throw new Exception(
                'Symbolic link creation fail.',
                $this::ERR_INSTALL_FAIL_SYMLINK
            );
        }

        $this->displayMsgNLInCli('Done', 'green', 'bold');
    }

    /**
     * Create a directory into project config directory for the module and
     * copy all config files declared to him
     * 
     * @return void
     */
    protected function copyConfigFiles()
    {
        $this->displayMsgNLInCli(' > Copy config files : ');

        //No file to copy
        if ($this->configFilesList === []) {
            $this->displayMsgInCli(' >> ');
            $this->displayMsgNLInCli(
                'No config file declared. Pass',
                'yellow',
                'bold'
            );
            
            return;
        }

        //Create the module directory into the config directory.
        $this->createConfigDirectory();
        
        //Copy manifest json file
        $this->copyConfigFile('manifest.json');

        //Copy each config file declared
        foreach ($this->configFilesList as $configFileName) {
            $this->copyConfigFile($configFileName);
        }
    }

    /**
     * Create a directory into project config directory for the module
     * 
     * @return void
     * 
     * @throws \Exception : If remove directory fail with the reinstall option
     */
    protected function createConfigDirectory()
    {
        $this->displayMsgInCli(' >> Create config directory for this module ... ');
        
        $alreadyExist = file_exists($this->targetConfigPath);
        
        //If the directory already exist and if it's a reinstall
        if ($alreadyExist && $this->forceReinstall === true) {
            $this->displayMsgInCli('[Force Reinstall: Remove directory] ');
            
            $alreadyExist = false;
            if (!$this->removeRecursiveDirectory($this->targetConfigPath)) {
                $this->displayMsgNLInCli(
                    'Remove the module config directory have fail',
                    'red',
                    'bold'
                );
                
                throw new Exception(
                    'Reinstall fail. Remove module config directory error',
                    $this::ERR_REINSTALL_FAIL_REMOVE_CONFIG_DIR
                );
            }
        }
        
        //If the directory already exist, nothing to do
        if ($alreadyExist) {
            $this->displayMsgNLInCli('Already exist', 'yellow', 'bold');
            return;
        }
        
        //Create the directory
        if (!mkdir($this->targetConfigPath, 0755)) {
            $this->displayMsgNLInCli('Fail', 'red', 'bold');
            
            throw new Exception(
                'Error to create the config directory.',
                $this::ERR_FAIL_CREATE_CONFIG_DIR
            );
        }

        $this->displayMsgNLInCli('Done', 'green', 'bold');
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
    protected function removeRecursiveDirectory(string $dirPath): bool
    {
        $itemList = array_diff(scandir($dirPath), ['.', '..']);
        
        foreach ($itemList as $itemName) {
            $itemPath = $dirPath.'/'.$itemName;
            
            if (is_dir($itemPath)) {
                $this->removeRecursiveDirectory($itemPath);
            } else {
                unlink($itemPath);
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
     * @throws \Exception If copy fail or if the source file not exist
     */
    protected function copyConfigFile(string $configFileName)
    {
        $this->displayMsgInCli(' >> Copy '.$configFileName.' ... ');

        //Define paths to the config file
        $sourceFile = realpath($this->sourceConfigPath.'/'.$configFileName);
        $targetFile = realpath($this->targetConfigPath).'/'.$configFileName;

        //Check if config file already exist
        if (file_exists($targetFile)) {
            $this->displayMsgNLInCli('Already exist', 'yellow', 'bold');
            return;
        }

        //If source file not exist
        if (!file_exists($sourceFile)) {
            $this->displayMsgNLInCli(
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
            $this->displayMsgNLInCli('Fail', 'red', 'bold');
            throw new Exception(
                'Copy fail',
                $this::ERR_COPY_CONFIG_FAIL
            );
        }

        $this->displayMsgNLInCli('Done', 'green', 'bold');
    }

    /**
     * Check if a specific install script is declared for the module and if
     * the value is true, use the default script name "runInstallModule.php".
     * 
     * @return void
     */
    protected function checkInstallScript()
    {
        $this->displayMsgNLInCli(' > Check install specific script :');

        //If no script to complete the install
        if (
            empty($this->sourceInstallScript)
            || $this->sourceInstallScript === false
        ) {
            $this->displayMsgInCli(' >> ');
            $this->displayMsgNLInCli(
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
        
        $this->displayMsgInCli(' >> ');
        $this->displayMsgNLInCli(
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
    public function runInstallScript(string $scriptName) {
        $this->displayMsgInCli(' >> ');
        $this->displayMsgNLInCli('Execute script '.$scriptName, 'yellow', 'bold');
        
        require_once($this->sourcePath.'/'.$scriptName);
        $this->displayMsgNLInCli('');
    }
}
