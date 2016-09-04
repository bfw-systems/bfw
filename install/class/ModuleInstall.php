<?php

namespace BFW\Install;

use \Exception;

class ModuleInstall
{
    const INSTALL_SCRIPT_VERSION = '3.0.0';

    /**
     * @var string $bfwPath : Path to root bfw project
     */
    protected $bfwPath = '';

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
     * @var string $pathToModule : Path to module which be installed
     */
    protected $pathToModule = '';

    /**
     * @var string $srcPath : Path to module source files
     */
    protected $srcPath = '';

    /**
     * @var string $configPath : Path to module config files
     */
    protected $configPath = '';

    /**
     * @var array $configFiles : List of config file to copy to config directory
     */
    protected $configFiles = [];

    /**
     * @var string|bool $installScript : Script to run for specific install from module
     *                                  Boolean on 2.x
     */
    protected $installScript = '';

    /**
     * Constructor
     * 
     * @param type $bfwPath
     * @param type $pathToModule
     */
    public function __construct($bfwPath, $pathToModule)
    {
        $this->bfwPath      = $bfwPath;
        $this->pathToModule = $pathToModule;

        $this->bfwConfigPath = $this->bfwPath.'/app/config/';
        $this->bfwModulePath = $this->bfwPath.'/app/modules/';
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
     * Load module informations from files
     * Define module name
     * 
     * @return void
     */
    public function loadInfos()
    {
        $pathExplode = explode('/', $this->pathToModule);
        $this->name  = $pathExplode[(count($pathExplode) - 1)];

        $this->bfwConfigPath .= $this->name;
        $this->bfwModulePath .= $this->name;

        $infos = \BFW\Module::installInfos($this->pathToModule);

        $this->srcPath    = $infos->srcPath;
        $this->configPath = $infos->srcPath;

        if (property_exists($infos, 'configFiles')) {
            $this->configFiles = (array) $infos->configFiles;
        }

        if (property_exists($infos, 'configPath')) {
            $this->configPath = $infos->configPath;
        }

        if (property_exists($infos, 'installScript')) {
            $this->installScript = $infos->installScript;
        }

        $this->srcPath = realpath($this->pathToModule.'/'.$this->srcPath);
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
        echo ' > Create symbolic link ... '."\n";

        $targetPath     = $this->bfwModulePath;
        $alreadyCreated = file_exists($targetPath);

        if ($alreadyCreated && $this->forceReinstall === true) {
            echo '[Force Reinstall: Remove symlink] ';
            $alreadyCreated = false;

            if (!unlink($targetPath)) {
                echo "\033[1;31m Symbolic link remove fail.\033[0m\n";
                throw new Exception('Reinstall fail. Symlink remove error');
            }
        }

        //If module already exist in "modules" directory
        if ($alreadyCreated) {
            echo "\033[1;33m Not created. Module already exist in 'modules' directory.\033[0m\n";
            return;
        }

        //If symbolic link create fail.
        if (!symlink($this->srcPath, $targetPath)) {
            echo "...\033[1;31m Symbolic link creation fail.\033[0m\n";
            return;
        }

        echo "\033[1;32m Done\033[0m\n";
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

        if ($this->configFiles === []) {
            echo ' >> '."\033[1;33m".'No config file declared. Pass'."\033[0m\n";
            return;
        }

        $configDirStatus = $this->createConfigDirectory();
        if ($configDirStatus === false) {
            return;
        }

        foreach ($this->configFiles as $configFile) {
            try {
                $this->copyConfigFile($configFile);
            } catch (Exception $e) {
                trigger_error(
                    'Module '.$this->name.' Config file '.$configFile.' copy error: '.$e->getMessage(),
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
        
        $targetDirectory = $this->bfwConfigPath;
        $alreadyExist    = file_exists($targetDirectory);
        
        if ($alreadyExist && $this->forceReinstall === true) {
            echo '[Force Reinstall: Remove symlink] ';
            $alreadyExist = false;
            
            if (!rmdir($targetDirectory)) {
                echo "\033[1;31m Remove module config directory fail.\033[0m\n";
                throw new Exception('Reinstall fail. Remove module config directory error.');
            }
        }
        
        if ($alreadyExist) {
            echo "\033[1;33m Already exist.\033[0m\n";
            return true;
        }
            
        if (mkdir($targetDirectory, 0755)) {
            echo "\033[1;32m Created. \033[0m\n";
            return true;
        }

        trigger_error('Module '.$this->name.' Error to create config directory', E_USER_WARNING);
        echo "\033[1;31m Fail. \033[0m\n";
        
        return false;
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

        $sourceFile = realpath($this->configFiles.'/'.$configFileName);
        $targetFile = realpath($this->bfwConfigPath.'/'.$configFileName);

        //Check if config file already exist
        if (file_exists($targetFile)) {
            echo "\033[1;33m Existe déjà.\033[0m\n";
            return;
        }

        //If source file not exist
        if (!file_exists($sourceFile)) {
            echo "\033[1;31m Config file not exist in module source.\033[0m\n";
            throw new Exception('Source file not exist');
        }

        //Alors on copie le fichier vers le dossier /configs/[monModule]/
        if (!copy($sourceFile, $targetFile)) {
            echo "\033[1;31m Copy fail.\033[0m\n";
            throw new Exception('Copy fail');
        }

        echo "\033[1;32m Done\033[0m\n";
    }

    /**
     * Run specific module install script if declared
     * 
     * @return void
     */
    protected function runInstallScript()
    {
        echo ' >> Run install specific script : ';

        if ($this->installScript === '' || $this->installScript === false) {
            echo ' >> '."\033[1;33m".'No specific script declared. Pass'."\033[0m\n";
            return;
        }

        if ($this->installScript === true) {
            $this->installScript = 'runInstallModule.php';
        }

        require_once($this->pathToModule.'/'.$this->installScript);
    }
}
