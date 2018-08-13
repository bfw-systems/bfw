<?php

namespace BFW\Install\test\unit;

use \atoum;

require_once(__DIR__.'/../../../../vendor/autoload.php');

/**
 * @engine isolate
 */
class ModuleInstall extends atoum
{
    use \BFW\Test\Helpers\Install\Application;
    use \BFW\Test\Helpers\OutputBuffer;
    
    protected $mock;
    
    public function beforeTestMethod($testMethod)
    {
        $this->setRootDir(__DIR__.'/../../../..');
        $this->createApp();
        $this->initApp(); //Need constants
        
        if ($testMethod === 'testConstruct') {
            return;
        }
        
        $this->mockGenerator
            ->makeVisible('findModuleName')
            ->makeVisible('obtainInfosFromModule')
            ->makeVisible('checkPropertySrcPath')
            ->makeVisible('createSymbolicLink')
            ->makeVisible('copyConfigFiles')
            ->makeVisible('createConfigDirectory')
            ->makeVisible('removeRecursiveDirectory')
            ->makeVisible('copyConfigFile')
            ->makeVisible('checkInstallScript')
            ->generate('BFW\Test\Mock\Install\ModuleInstall')
        ;
        
        $this->mock = new \mock\BFW\Test\Mock\Install\ModuleInstall(
            ROOT_DIR.'vendor/bulton-fr/unit-test-module'
        );
    }
    
    protected function loadInfos($infos = null)
    {
        if (is_null($infos)) {
            $infos = (object) [
                'srcPath'       => 'src',
                'configFiles'   => 'mymodule.config.php',
                'configPath'    => 'config',
                'installScript' => 'install/postInsall.php'
            ];
        }
        
        $this
            ->if($this->calling($this->mock)->obtainInfosFromModule = function() use ($infos) {
                return $infos;
            })
            ->and($this->function->realpath = function($path) {
                return $path; //Because path not really exist
            })
            ->and($this->mock->loadInfos())
        ;
    }


    public function testConstruct()
    {
        $this->assert('test \Install\ModuleInstall::__construct')
            ->object($this->mock = new \mock\BFW\Test\Mock\Install\ModuleInstall(
                ROOT_DIR.'vendor/bulton-fr/unit-test-module'
            ))
                ->isInstanceOf('\BFW\Install\ModuleInstall')
            ->string($this->mock->getProjectPath())
                ->isEqualTo(ROOT_DIR)
            ->string($this->mock->getSourcePath())
                ->isEqualTo(ROOT_DIR.'vendor/bulton-fr/unit-test-module')
        ;
    }
    
    public function testFindModuleName()
    {
        $this->assert('test \Install\ModuleInstall::findModuleName')
            ->variable($this->mock->findModuleName())
                ->isNull()
            ->string($this->mock->getName())
                ->isEqualTo('unit-test-module')
            ->string($this->mock->getTargetSrcPath())
                ->isEqualTo(MODULES_DIR.'unit-test-module')
            ->string($this->mock->getTargetConfigPath())
                ->isEqualTo(CONFIG_DIR.'unit-test-module')
        ;
    }
    
    public function testObtainInfosFromModule()
    {
        $this->assert('test \Install\ModuleInstall::obtainInfosFromModule')
            //See \BFW\test\unit\Module::testInstallInfos()
            //We doing a direct return of the method \BFW\Module::installInfos()
            //so I can see more test to do with this.
        ;
    }
    
    public function testLoadInfosWithAllInfos()
    {
        $this->assert('test \Install\ModuleInstall::loadInfos with all infos')
            ->if($this->calling($this->mock)->obtainInfosFromModule = function() {
                return (object) [
                    'srcPath'       => 'src',
                    'configFiles'   => 'mymodule.config.php',
                    'configPath'    => 'config',
                    'installScript' => 'install/postInsall.php'
                ];
            })
            ->and($this->function->realpath = function($path) {
                return $path; //Because path not really exist
            })
            ->then
            ->variable($this->mock->loadInfos())
                ->isNull()
            ->string($this->mock->getName())
                ->isNotEmpty()
            ->string($this->mock->getTargetSrcPath())
                ->isNotEmpty()
            ->string($this->mock->getTargetConfigPath())
                ->isNotEmpty()
            ->string($this->mock->getSourceSrcPath())
                ->isEqualTo(ROOT_DIR.'vendor/bulton-fr/unit-test-module/src')
            ->string($this->mock->getSourceConfigPath())
                ->isEqualTo(ROOT_DIR.'vendor/bulton-fr/unit-test-module/config')
            ->array($this->mock->getConfigFilesList())
                ->isEqualTo([
                    'mymodule.config.php'
                ])
            ->string($this->mock->getSourceInstallScript())
                ->isEqualTo('install/postInsall.php')
        ;
    }
    
    public function testLoadInfosWithMinimumInfos()
    {
        $this->assert('test \Install\ModuleInstall::loadInfos with minimum infos')
            ->if($this->calling($this->mock)->obtainInfosFromModule = function() {
                return (object) [
                    'srcPath' => 'src'
                ];
            })
            ->and($this->function->realpath = function($path) {
                return $path; //Because path not really exist
            })
            ->then
            ->variable($this->mock->loadInfos())
                ->isNull()
            ->string($this->mock->getName())
                ->isNotEmpty()
            ->string($this->mock->getTargetSrcPath())
                ->isNotEmpty()
            ->string($this->mock->getTargetConfigPath())
                ->isNotEmpty()
            ->string($this->mock->getSourceSrcPath())
                ->isEqualTo(ROOT_DIR.'vendor/bulton-fr/unit-test-module/src')
            ->string($this->mock->getSourceConfigPath())
                ->isEqualTo(ROOT_DIR.'vendor/bulton-fr/unit-test-module/src')
            ->array($this->mock->getConfigFilesList())
                ->isEmpty()
            ->string($this->mock->getSourceInstallScript())
                ->isEmpty()
        ;
    }
    
    public function testLoadInfosWithBadInfos()
    {
        $this->assert('test \Install\ModuleInstall::loadInfos with bad src path')
            ->if($this->calling($this->mock)->obtainInfosFromModule = function() {
                return (object) [
                    'srcPath' => 'src'
                ];
            })
            ->and($this->function->realpath = function($path) {
                return false;
            })
            ->then
            ->exception(function() {
                $this->mock->loadInfos();
            })
                ->hasCode(\BFW\Install\ModuleInstall::ERR_LOAD_PATH_NOT_EXIST)
        ;
    }
    
    public function testCheckPropertySrcPath()
    {
        $this->assert('test \Install\ModuleInstall::checkPropertySrcPath without error')
            ->boolean($this->mock->checkPropertySrcPath((object) [
                'srcPath' => 'src'
            ]))
                ->isTrue()
        ;
        
        $this->assert('test \Install\ModuleInstall::checkPropertySrcPath without datas')
            ->exception(function() {
                $this->mock->checkPropertySrcPath((object) []);
            })
                ->hasCode(\BFW\Install\ModuleInstall::ERR_LOAD_NO_PROPERTY_SRCPATH)
        ;
        
        $this->assert('test \Install\ModuleInstall::checkPropertySrcPath with empty srcPath')
            ->exception(function() {
                $this->mock->checkPropertySrcPath((object) [
                    'srcPath' => ''
                ]);
            })
                ->hasCode(\BFW\Install\ModuleInstall::ERR_LOAD_EMPTY_PROPERTY_SRCPATH)
        ; 
    }
    
    public function testInstallWithoutInfosLoaded()
    {
        $this->assert('test \Install\ModuleInstall::install without infos loaded')
            ->given($loadInfosCalled = false)
            ->if($this->calling($this->mock)->loadInfos = function() use (&$loadInfosCalled) {
                $loadInfosCalled = true;
            })
            ->then
                
            ->if($this->calling($this->mock)->createSymbolicLink = true)
            ->and($this->calling($this->mock)->copyConfigFiles = true)
            ->and($this->calling($this->mock)->checkInstallScript = true)
            ->then
            
            ->given($lastFlushedMsg = '')
            ->if($this->defineOutputBuffer($lastFlushedMsg))
            ->then
            
            ->variable($this->mock->install(false))
                ->isNull()
            ->boolean($loadInfosCalled)
                ->isTrue()
            ->string($lastFlushedMsg)
                ->isEqualto(' : Run install.'."\n")
        ;
    }
    
    public function testInstallWithInfosLoaded()
    {
        $this->assert('test \Install\ModuleInstall::install with infos loaded')
            ->if($this->loadInfos())
            ->then
            
            ->if($this->calling($this->mock)->createSymbolicLink = true)
            ->and($this->calling($this->mock)->copyConfigFiles = true)
            ->and($this->calling($this->mock)->checkInstallScript = true)
            ->then
            
            ->given($lastFlushedMsg = '')
            ->if($this->defineOutputBuffer($lastFlushedMsg))
            ->then
            
            ->variable($this->mock->install(false))
                ->isNull()
            ->string($lastFlushedMsg)
                ->isEqualto('unit-test-module : Run install.'."\n")
            ->boolean($this->mock->getForceReinstall())
                ->isFalse()
            ->mock($this->mock)
                ->call('loadInfos')
                    ->before($this->mock($this->mock)->call('install'))
                    ->once()
                ->call('createSymbolicLink')
                    ->once()
                ->call('copyConfigFiles')
                    ->once()
                ->call('checkInstallScript')
                    ->once()
        ;
        
        $this->assert('test \Install\ModuleInstall::install with infos loaded and force reinstall status')
            ->given($lastFlushedMsg = '')
            ->then
            
            ->variable($this->mock->install(true))
                ->isNull()
            ->string($lastFlushedMsg)
                ->isEqualto('unit-test-module : Run install.'."\n")
            ->boolean($this->mock->getForceReinstall())
                ->isTrue()
        ;
        
        $this->assert('test \Install\ModuleInstall::install with infos loaded and an error')
            ->given($lastFlushedMsg = '')
            ->then
            
            ->if($this->calling($this->mock)->createSymbolicLink = function() {
                throw new \Exception('Mocked error', 123);
            })
            ->then
            
            ->when(function() {
                $this->mock->install(true);
            })
            ->error()
                ->withType(E_USER_WARNING)
                ->withMessage('Module unit-test-module install error : Mocked error')
                ->exists()
        ;
    }
    
    public function testCreateSymbolicLink()
    {
        $this
            ->given($lastFlushedMsg = '')
            ->if($this->defineOutputBuffer($lastFlushedMsg))
        ;
        
        $this->assert('test \Install\ModuleInstall::createSymbolicLink with new install')
            ->if($this->loadInfos())
            ->and($this->function->file_exists = false)
            ->and($this->function->symlink = true)
            ->then
            
            ->variable($this->mock->createSymbolicLink())
                ->isNull()
            ->string($lastFlushedMsg)
                ->isEqualTo(
                    ' > Create symbolic link ... '.
                    "\033[1;32mDone\n\033[0m"
                )
        ;
        
        $this->assert('test \Install\ModuleInstall::createSymbolicLink with fail on install')
            ->given($lastFlushedMsg = '')
            ->and($this->function->file_exists = false)
            ->and($this->function->symlink = false)
            ->then
            
            ->exception(function() {
                $this->mock->createSymbolicLink();
            })
                ->hasCode(\BFW\Install\ModuleInstall::ERR_INSTALL_FAIL_SYMLINK)
            ->string($lastFlushedMsg)
                ->isEqualTo(
                    ' > Create symbolic link ... '.
                    "\033[1;31mSymbolic link creation fail.\n\033[0m"
                )
        ;
        
        $this->assert('test \Install\ModuleInstall::createSymbolicLink when already install and not reinstall')
            ->given($lastFlushedMsg = '')
            ->and($this->function->file_exists = true)
            ->and($this->mock->setForceReinstall(false))
            ->then
            
            ->variable($this->mock->createSymbolicLink())
                ->isNull()
            ->string($lastFlushedMsg)
                ->isEqualTo(
                    ' > Create symbolic link ... '.
                    "\033[1;33mNot created. Module already exist in 'modules' directory.\n\033[0m"
                )
        ;
        
        $this->assert('test \Install\ModuleInstall::createSymbolicLink when already install, with reinstall but fail')
            ->given($lastFlushedMsg = '')
            ->and($this->function->file_exists = true)
            ->and($this->function->unlink = false)
            ->and($this->mock->setForceReinstall(true))
            ->then
            
            ->exception(function() {
                $this->mock->createSymbolicLink();
            })
                ->hasCode(\BFW\Install\ModuleInstall::ERR_REINSTALL_FAIL_UNLINK)
            ->string($lastFlushedMsg)
                ->isEqualTo(
                    ' > Create symbolic link ... [Force Reinstall: Remove symlink] '.
                    "\033[1;31mSymbolic link remove fail.\n\033[0m"
                )
        ;
        
        $this->assert('test \Install\ModuleInstall::createSymbolicLink with reinstall success')
            ->given($lastFlushedMsg = '')
            ->and($this->function->file_exists = true)
            ->and($this->function->unlink = true)
            ->and($this->function->symlink = true)
            ->and($this->mock->setForceReinstall(true))
            ->then
            
            ->variable($this->mock->createSymbolicLink())
                ->isNull()
            ->string($lastFlushedMsg)
                ->isEqualTo(
                    ' > Create symbolic link ... [Force Reinstall: Remove symlink] '.
                    "\033[1;32mDone\n\033[0m"
                )
        ;
    }
    
    public function testCopyConfigFiles()
    {
        $this
            ->given($lastFlushedMsg = '')
            ->if($this->defineOutputBuffer($lastFlushedMsg))
        ;
        
        $this->assert('test \Install\ModuleInstall::copyConfigFiles without config file')
            ->if($this->loadInfos((object) [
                'srcPath' => 'src'
            ]))
            ->then
            
            ->variable($this->mock->copyConfigFiles())
                ->isNull()
            ->string($lastFlushedMsg)
                ->isEqualTo(
                    ' > Copy config files : '."\n".
                    " >> \033[1;33mNo config file declared. Pass\n\033[0m"
                )
        ;
        
        $this->assert('test \Install\ModuleInstall::copyConfigFiles with config file but config dir fail')
            ->given($lastFlushedMsg = '')
            ->if($this->loadInfos())
            ->and($this->calling($this->mock)->createConfigDirectory = function() {
                throw new \Exception(
                    'Error to create the config directory.',
                    \BFW\Install\ModuleInstall::ERR_FAIL_CREATE_CONFIG_DIR
                );
            })
            ->then
            
            ->exception(function() {
                $this->mock->copyConfigFiles();
            })
                ->hasCode(\BFW\Install\ModuleInstall::ERR_FAIL_CREATE_CONFIG_DIR)
            ->string($lastFlushedMsg)
                ->isEqualTo(' > Copy config files : '."\n")
        ;
        
        $this->assert('test \Install\ModuleInstall::copyConfigFiles with a config file')
            ->given($lastFlushedMsg = '')
            ->if($this->loadInfos())
            ->and($this->calling($this->mock)->createConfigDirectory = true)
            ->and($this->calling($this->mock)->copyConfigFile = true)
            ->then
            
            ->variable($this->mock->copyConfigFiles())
                ->isNull()
            ->string($lastFlushedMsg)
                ->isEqualTo(' > Copy config files : '."\n")
            ->mock($this->mock)
                ->call('copyConfigFile')
                    ->withArguments('manifest.json')
                        ->once()
                    ->withArguments('mymodule.config.php')
                        ->once()
        ;
    }
    
    public function testCreateConfigDirectory()
    {
        $this
            ->given($lastFlushedMsg = '')
            ->if($this->defineOutputBuffer($lastFlushedMsg))
        ;
        
        $this->assert('test \Install\ModuleInstall::createConfigDirectory with new install')
            ->if($this->loadInfos())
            ->and($this->function->file_exists = false)
            ->and($this->function->mkdir = true)
            ->then
            
            ->variable($this->mock->createConfigDirectory())
                ->isNull()
            ->string($lastFlushedMsg)
                ->isEqualTo(
                    ' >> Create config directory for this module ... '.
                    "\033[1;32mDone\n\033[0m"
                )
        ;
        
        $this->assert('test \Install\ModuleInstall::createConfigDirectory with fail on install')
            ->given($lastFlushedMsg = '')
            ->and($this->function->file_exists = false)
            ->and($this->function->mkdir = false)
            ->then
            
            ->exception(function() {
                $this->mock->createConfigDirectory();
            })
                ->hasCode(\BFW\Install\ModuleInstall::ERR_FAIL_CREATE_CONFIG_DIR)
            ->string($lastFlushedMsg)
                ->isEqualTo(
                    ' >> Create config directory for this module ... '.
                    "\033[1;31mFail\n\033[0m"
                )
        ;
        
        $this->assert('test \Install\ModuleInstall::createConfigDirectory when already install and not reinstall')
            ->given($lastFlushedMsg = '')
            ->and($this->function->file_exists = true)
            ->and($this->mock->setForceReinstall(false))
            ->then
            
            ->variable($this->mock->createConfigDirectory())
                ->isNull()
            ->string($lastFlushedMsg)
                ->isEqualTo(
                    ' >> Create config directory for this module ... '.
                    "\033[1;33mAlready exist\n\033[0m"
                )
        ;
        
        $this->assert('test \Install\ModuleInstall::createConfigDirectory when already install, with reinstall but fail')
            ->given($lastFlushedMsg = '')
            ->and($this->function->file_exists = true)
            ->and($this->calling($this->mock)->removeRecursiveDirectory = false)
            ->and($this->mock->setForceReinstall(true))
            ->then
            
            ->exception(function() {
                $this->mock->createConfigDirectory();
            })
                ->hasCode(\BFW\Install\ModuleInstall::ERR_REINSTALL_FAIL_REMOVE_CONFIG_DIR)
            ->string($lastFlushedMsg)
                ->isEqualTo(
                    ' >> Create config directory for this module ... [Force Reinstall: Remove directory] '.
                    "\033[1;31mRemove the module config directory have fail\n\033[0m"
                )
        ;
        
        $this->assert('test \Install\ModuleInstall::createConfigDirectory with reinstall success')
            ->given($lastFlushedMsg = '')
            ->and($this->function->file_exists = true)
            ->and($this->calling($this->mock)->removeRecursiveDirectory = true)
            ->and($this->function->mkdir = true)
            ->and($this->mock->setForceReinstall(true))
            ->then
            
            ->variable($this->mock->createConfigDirectory())
                ->isNull()
            ->string($lastFlushedMsg)
                ->isEqualTo(
                    ' >> Create config directory for this module ... [Force Reinstall: Remove directory] '.
                    "\033[1;32mDone\n\033[0m"
                )
        ;
    }
    
    public function testRemoveRecursiveDirectory()
    {
        $this->assert('test \Install\ModuleInstall::removeRecursiveDirectory without file')
            ->if($this->function->scandir = ['.', '..'])
            ->and($this->function->unlink = true)
            ->and($this->function->rmdir = true)
            ->then
            
            ->boolean($this->mock->removeRecursiveDirectory(MODULES_DIR.'unit-test-module'))
                ->isTrue()
            ->mock($this->mock)
                ->call('removeRecursiveDirectory')
                    ->once()
            ->function('unlink')
                ->never()
        ;
        
        $this->assert('test \Install\ModuleInstall::removeRecursiveDirectory with only file')
            ->if($this->function->scandir = ['.', '..', 'test.php', 'src.php'])
            ->and($this->function->is_dir = false)
            ->and($this->function->unlink = true)
            ->and($this->function->rmdir = true)
            ->then
            
            ->boolean($this->mock->removeRecursiveDirectory(MODULES_DIR.'unit-test-module'))
                ->isTrue()
            ->mock($this->mock)
                ->call('removeRecursiveDirectory')
                    ->once()
            ->function('unlink')
                ->wasCalled()
                    ->twice()
        ;
        
        $this->assert('test \Install\ModuleInstall::removeRecursiveDirectory with file and directory')
            ->if($this->function->scandir = function($path) {
                if ($path === MODULES_DIR.'unit-test-module') {
                    return ['.', '..', 'test.php', 'src', 'config.php'];
                }
                
                return ['.', '..'];
            })
            ->and($this->function->is_dir = function($path) {
                if ($path === 'src') {
                    return true;
                }
                
                return false;
            })
            ->and($this->function->unlink = true)
            ->and($this->function->rmdir = true)
            ->then
            
            ->boolean($this->mock->removeRecursiveDirectory(MODULES_DIR.'unit-test-module'))
                ->isTrue()
            ->mock($this->mock)
                ->call('removeRecursiveDirectory')
                    ->twice()
            ->function('unlink')
                ->wasCalled()
                    ->twice()
        ;
    }
    
    public function testCopyConfigFile()
    {
        $this
            ->given($lastFlushedMsg = '')
            ->if($this->defineOutputBuffer($lastFlushedMsg))
            ->and($this->function->realpath = function($path) {
                return $path;
            })
        ;
        
        $this->assert('test \Install\ModuleInstall::copyConfigFile when it is a success')
            ->if($this->loadInfos())
            ->and($this->function->file_exists[1] = false)
            ->and($this->function->file_exists[2] = true)
            ->and($this->function->copy = true)
            ->then
            
            ->variable($this->mock->copyConfigFile('mymodule.config.php'))
                ->isNull()
            ->string($lastFlushedMsg)
                ->isEqualTo(
                    ' >> Copy mymodule.config.php ... '.
                    "\033[1;32mDone\n\033[0m"
                )
            ->function('copy')
                ->wasCalledWithArguments(
                    ROOT_DIR.'vendor/bulton-fr/unit-test-module/config/mymodule.config.php',
                    CONFIG_DIR.'unit-test-module/mymodule.config.php'
                )
                    ->once()
        ;
        
        $this->assert('test \Install\ModuleInstall::copyConfigFile with fail on copy')
            ->given($lastFlushedMsg = '')
            ->and($this->function->file_exists[1] = false)
            ->and($this->function->file_exists[2] = true)
            ->and($this->function->copy = false)
            ->then
            
            ->exception(function() {
                $this->mock->copyConfigFile('mymodule.config.php');
            })
                ->hasCode(\BFW\Install\ModuleInstall::ERR_COPY_CONFIG_FAIL)
            ->string($lastFlushedMsg)
                ->isEqualTo(
                    ' >> Copy mymodule.config.php ... '.
                    "\033[1;31mFail\n\033[0m"
                )
        ;
        
        $this->assert('test \Install\ModuleInstall::copyConfigFile when already exist')
            ->given($lastFlushedMsg = '')
            ->and($this->function->file_exists[1] = true)
            ->then
            
            ->variable($this->mock->copyConfigFile('mymodule.config.php'))
                ->isNull()
            ->string($lastFlushedMsg)
                ->isEqualTo(
                    ' >> Copy mymodule.config.php ... '.
                    "\033[1;33mAlready exist\n\033[0m"
                )
        ;
        
        $this->assert('test \Install\ModuleInstall::createConfigDirectory when source file not exist')
            ->given($lastFlushedMsg = '')
            ->and($this->function->file_exists[1] = false)
            ->and($this->function->file_exists[2] = false)
            ->then
            
            ->exception(function() {
                $this->mock->copyConfigFile('mymodule.config.php');
            })
                ->hasCode(\BFW\Install\ModuleInstall::ERR_COPY_CONFIG_SRC_FILE_NOT_EXIST)
            ->string($lastFlushedMsg)
                ->isEqualTo(
                    ' >> Copy mymodule.config.php ... '.
                    "\033[1;31mConfig file not exist in module source.\n\033[0m"
                )
        ;
    }
    
    public function testCheckInstallScript()
    {
        $this
            ->given($lastFlushedMsg = '')
            ->if($this->defineOutputBuffer($lastFlushedMsg))
            ->and($this->function->realpath = function($path) {
                return $path;
            })
        ;
        
        $this->assert('test \Install\ModuleInstall::checkInstallScript without file')
            ->if($this->mock->setSourceInstallScript(''))
            ->then
            
            ->variable($this->mock->checkInstallScript())
                ->isNull()
            ->string($lastFlushedMsg)
                ->isEqualTo(
                    ' > Check install specific script :'."\n".
                    " >> \033[1;33mNo specific script declared. Pass\n\033[0m"
                )
        ;
        
        $this->assert('test \Install\ModuleInstall::checkInstallScript with default filename')
            ->given($lastFlushedMsg = '')
            ->if($this->mock->setSourceInstallScript(true))
            ->then
            
            ->variable($this->mock->checkInstallScript())
                ->isNull()
            ->string($lastFlushedMsg)
                ->isEqualTo(
                    ' > Check install specific script :'."\n".
                    " >> \033[1;33mScripts find. Add to list to execute.\n\033[0m"
                )
            ->string($this->mock->getSourceInstallScript())
                ->isEqualTo('runInstallModule.php')
        ;
        
        $this->assert('test \Install\ModuleInstall::checkInstallScript with defined filename')
            ->given($lastFlushedMsg = '')
            ->if($this->mock->setSourceInstallScript('postInstall.php'))
            ->then
            
            ->variable($this->mock->checkInstallScript())
                ->isNull()
            ->string($lastFlushedMsg)
                ->isEqualTo(
                    ' > Check install specific script :'."\n".
                    " >> \033[1;33mScripts find. Add to list to execute.\n\033[0m"
                )
            ->string($this->mock->getSourceInstallScript())
                ->isEqualTo('postInstall.php')
        ;
    }
    
    public function testRunInstallScript()
    {
        $this->assert('test \Install\ModuleInstall::runInstallScript');
        //Require_once not mockable, so we can't test.
    }
}