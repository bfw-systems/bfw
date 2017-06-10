<?php

namespace BFW\Install\test\unit;

use \atoum;
use \BFW\Install\test\unit\mocks\ModuleInstall as MockModuleInstall;
use \BFW\Install\test\unit\mocks\Application as MockApp;
use \BFW\test\helpers\Errors;
use \BFW\test\helpers\Output;

require_once(__DIR__.'/../../../../vendor/autoload.php');

class ModuleInstall extends atoum
{
    /**
     * @var $mock Mock instance
     */
    protected $mock;
    
    /**
     * @var \BFW\Install\test\unit\mocks\Application $app BFW Application instance
     */
    protected $app;
    
    /**
     * @var array BFW config used by unit test
     */
    protected $forcedConfig;
    
    /**
     * @var string $bfwPath Fake project path
     */
    protected $bfwPath;
    
    /**
     * @var string $sourcePath Fake module path
     */
    protected $sourcePath;
    
    /**
     * @var string $modulePath Fake project app/modules path
     */
    protected $modulePath;
    
    /**
     * @var string $configPath Fake project app/config path
     */
    protected $configPath;

    /**
     * Call before each test method
     * Instantiate the mock
     * 
     * @param $testMethod string The name of the test method executed
     * 
     * @return void
     */
    public function beforeTestMethod($testMethod)
    {
        $this->forcedConfig = require(__DIR__.'/../../helpers/applicationConfig.php');
        
        //Remove the instance of the latest test
        MockApp::removeInstance();
        
        $options = [
            'forceConfig'     => $this->forcedConfig,
            'vendorDir'       => realpath(__DIR__.'/../../../../').'/vendor',
            'overrideMethods' => [
                'runCliFile'     => null,
                'initModules'    => null,
                'readAllModules' => null
            ]
        ];
        
        $this->app = MockApp::init($options);
        
        $this->sourcePath = ROOT_DIR.'/vendor/unit/test';
        $this->modulePath = MODULES_DIR;
        $this->configPath = CONFIG_DIR;
        
        if ($testMethod !== 'testConstructor') {
            $this->mock = new MockModuleInstall($this->sourcePath);
        }
    }
    
    /**
     * Test method for __construct()
     * 
     * @return void
     */
    public function testConstructor()
    {
        $this->assert('test constructor')
            ->if($this->mock = new MockModuleInstall($this->sourcePath))
            ->then
            ->string($this->mock->projectPath)
                ->isEqualTo(ROOT_DIR)
            ->string($this->mock->sourcePath)
                ->isEqualTo($this->sourcePath);
    }
    
    /**
     * Test method for getName()
     * 
     * @return void
     */
    public function testGetName()
    {
        $this->assert('test getName with default value')
            ->string($this->mock->getName())
                ->isEqualTo('');
        
        //Get name without default value tested on testLoadInfos()
    }
    
    /**
     * Test method for getSourcePath()
     * 
     * @return void
     */
    public function testGetSourcePath()
    {
        $this->assert('test getSourcePath with default value')
            ->string($this->mock->getSourcePath())
                ->isEqualTo($this->sourcePath);
        
        //Get sourcePath without default value tested on testLoadInfos()
    }
    
    /**
     * Test method for getSourceInstallScript()
     * 
     * @return void
     */
    public function testGetSourceInstallScript()
    {
        $this->assert('test getSourceInstallScript with default value')
            ->string($this->mock->getSourceInstallScript())
                ->isEqualTo('');
        
        //Get sourceInstallScript without default value tested on testLoadInfos()
    }
    
    /**
     * Test method for loadInfos() without install config
     * Should be throw an Exception
     * 
     * @return void
     */
    public function testLoadInfosWithoutConfig()
    {
        $this->assert('test loadInfos without install config (exception)')
            ->given($mock = $this->mock)
            ->then
            ->exception(function() use ($mock) {
                $mock->loadInfos();
            })
                ->hasCode($mock::ERR_LOAD_NO_PROPERTY_SRCPATH)
                ->hasMessage('srcPath must be present into bfwModulesInfos.json file for the module test');
    }
    
    /**
     * Test method for loadInfos() with install config
     * When config only contains srcPath
     * 
     * @return void
     */
    public function testLoadInfosWithMinConfig()
    {
        $this->assert('test loadInfos with install config (only srcPath)')
            ->if($this->mock->forceInfos([
                'srcPath' => 'src'
            ]))
            ->and($this->function->realpath = function($path) {return $path;})
            ->and($this->mock->loadInfos())
            ->then
            ->string($this->mock->getName())
                ->isEqualTo('test')
            ->string($this->mock->projectPath)
                ->isEqualTo(ROOT_DIR)
            ->string($this->mock->getSourcePath())
                ->isEqualTo($this->sourcePath)
            ->string($this->mock->sourceSrcPath)
                ->isEqualTo($this->sourcePath.'/src')
            ->string($this->mock->targetSrcPath)
                ->isEqualTo($this->modulePath.'test')
            ->string($this->mock->sourceConfigPath)
                ->isEqualTo($this->sourcePath.'/src')
            ->string($this->mock->targetConfigPath)
                ->isEqualTo($this->configPath.'test')
            ->array($this->mock->configFilesList)
                ->isEqualTo([])
            ->string($this->mock->sourceInstallScript)
                ->isEqualTo('');
    }
    
    /**
     * Test method for loadInfos() with install config
     * 
     * @return void
     */
    public function testLoadInfosWithFullConfig()
    {
        $this->assert('test loadInfos with install config')
            ->if($this->mock->forceInfos([
                'srcPath'       => 'src',
                'configPath'    => 'config',
                'configFiles'   => [
                    'config1.php',
                    'config2.json'
                ],
                'installScript' => 'install.php'
            ]))
            ->and($this->function->realpath = function($path) {return $path;})
            ->and($this->mock->loadInfos())
            ->then
            ->string($this->mock->getName())
                ->isEqualTo('test')
            ->string($this->mock->projectPath)
                ->isEqualTo(ROOT_DIR)
            ->string($this->mock->getSourcePath())
                ->isEqualTo($this->sourcePath)
            ->string($this->mock->sourceSrcPath)
                ->isEqualTo($this->sourcePath.'/src')
            ->string($this->mock->targetSrcPath)
                ->isEqualTo($this->modulePath.'test')
            ->string($this->mock->sourceConfigPath)
                ->isEqualTo($this->sourcePath.'/config')
            ->string($this->mock->targetConfigPath)
                ->isEqualTo($this->configPath.'test')
            ->array($this->mock->configFilesList)
                ->isEqualTo([
                    'config1.php',
                    'config2.json'
                ])
            ->string($this->mock->getSourceInstallScript())
                ->isEqualTo('install.php');
    }
    
    /**
     * Test method for install() when all is good
     * 
     * @return void
     */
    public function testInstallWithConfigFileAndAllGood()
    {
        $expectedOutput = 'test : Run install.'."\n"
                        .' > Create symbolic link ... '
                        ."\033[1;33mNot created. Module already exist in 'modules' directory.\033[0m\n"
                        .' > Copy config files : '."\n"
                        .' >> Create config directory for this module ... '
                        ."\033[1;33mAlready exist.\033[0m\n"
                        .' >> Copy config1.php ... '
                        ."\033[1;33mAlready exist.\033[0m\n"
                        .' >> Copy config2.json ... '
                        ."\033[1;33mAlready exist.\033[0m\n"
                        .' > Check install specific script :'."\n"
                        ." >> \033[1;33m".'No specific script declared. Pass'."\033[0m\n"
        ;
        
        $this->assert('test install with config files and all must be good')
            ->if($this->mock->forceInfos([
                'srcPath'       => 'src',
                'configPath'    => 'config',
                'configFiles'   => [
                    'config1.php',
                    'config2.json'
                ],
                'installScript' => false
            ]))
            ->and($this->function->realpath = function($path) {return $path;})
            ->and($this->mock->loadInfos())
            ->then
            
            ->if($this->function->copy = true)
            ->and($this->function->symlink = true)
            ->and($this->function->mkdir = true)
            ->and($this->function->file_exists = true)
            ->then
            
            //Output() closure not working with function mocking.
            ->if($output = new Output)
            ->and($output->startCatchOutput())
            ->given($this->mock->install(false))
            ->and($output->endCatchOutput())
            ->then
            
            ->string($output->getOutput())
                ->isEqualTo($expectedOutput);
    }
    
    /**
     * Test method for install() when all is good and with force option
     * 
     * @return void
     */
    public function testForceInstallWithConfigFileAndAllGood()
    {
        $expectedOutput = 'test : Run install.'."\n"
                        .' > Create symbolic link ... '
                        .'[Force Reinstall: Remove symlink] '."\033[1;32mDone\033[0m\n"
                        .' > Copy config files : '."\n"
                        .' >> Create config directory for this module ... '
                        .'[Force Reinstall: Remove directory] '."\033[1;32mCreated.\033[0m\n"
                        .' >> Copy config1.php ... '
                        ."\033[1;32mDone\033[0m\n"
                        .' >> Copy config2.json ... '
                        ."\033[1;32mDone\033[0m\n"
                        .' > Check install specific script :'."\n"
                        ." >> \033[1;33m".'No specific script declared. Pass'."\033[0m\n"
        ;
        
        $this->assert('test install with config files and the "force" mode. All must be good.')
            ->if($this->mock->forceInfos([
                'srcPath'       => 'src',
                'configPath'    => 'config',
                'configFiles'   => [
                    'config1.php',
                    'config2.json'
                ],
                'installScript' => false
            ]))
            ->and($this->function->realpath = function($path) {return $path;})
            ->and($this->mock->loadInfos())
            ->then
            
            ->if($this->function->copy = true)
            ->and($this->function->symlink = true)
            ->and($this->function->unlink = true)
            ->and($this->function->mkdir = true)
            ->and(MockModuleInstall::$removeDirectoryStatus = true)
            ->and($this->function->file_exists = function($path) {
                //Test file already exist in config app directory
                if(
                    $path === $this->configPath.'test/config1.php' ||
                    $path === $this->configPath.'test/config2.json'
                ) {
                    return false;
                }
                
                return true;
            })
            ->then
            
            //Output() closure not working with function mocking.
            ->if($output = new Output)
            ->and($output->startCatchOutput())
            ->given($this->mock->install(true))
            ->and($output->endCatchOutput())
            ->then
            
            ->string($output->getOutput())
                ->isEqualTo($expectedOutput);
    }
    
    /**
     * Test method for install() when there is a symlink error
     * 
     * @return void
     */
    public function testInstallWithSymlinkError()
    {
        $expectedOutput = 'test : Run install.'."\n"
                        .' > Create symbolic link ... '
                        ."\033[1;31mSymbolic link creation fail.\033[0m\n"
                        .' > Copy config files : '."\n"
                        .' >> Create config directory for this module ... '
                        ."\033[1;33mAlready exist.\033[0m\n"
                        .' >> Copy config1.php ... '
                        ."\033[1;33mAlready exist.\033[0m\n"
                        .' >> Copy config2.json ... '
                        ."\033[1;33mAlready exist.\033[0m\n"
                        .' > Check install specific script :'."\n"
                        ." >> \033[1;33m".'No specific script declared. Pass'."\033[0m\n"
        ;
        
        $this->assert('test install with a symlink error.')
            ->if($this->mock->forceInfos([
                'srcPath'       => 'src',
                'configPath'    => 'config',
                'configFiles'   => [
                    'config1.php',
                    'config2.json'
                ],
                'installScript' => false
            ]))
            ->and($this->function->realpath = function($path) {return $path;})
            ->and($this->mock->loadInfos())
            ->then
            
            ->if($this->function->copy = true)
            ->and($this->function->unlink = true)
            ->and($this->function->mkdir = true)
            ->and(MockModuleInstall::$removeDirectoryStatus = true)
            ->and($this->function->symlink = function($target, $link) {
                if ($target === $this->sourcePath.'/src') {
                    return false;
                }
                
                return true;
            })
            ->and($this->function->file_exists = function($path) {
                if ($path === $this->modulePath.'test') {
                    return false;
                }
                
                return true;
            })
            ->then
            
            //Output() closure not working with function mocking.
            ->if($output = new Output)
            ->and($output->startCatchOutput())
            ->given($this->mock->install(false))
            ->and($output->endCatchOutput())
            ->then
            
            ->string($output->getOutput())
                ->isEqualTo($expectedOutput);
    }
    
    /**
     * Test method for install() when there is a unlink error
     * 
     * @return void
     */
    public function testForceInstallWithUnlinkError()
    {
        $expectedOutput = 'test : Run install.'."\n"
                        .' > Create symbolic link ... '
                        .'[Force Reinstall: Remove symlink] '
                        ."\033[1;31mSymbolic link remove fail.\033[0m\n"
        ;
        
        $this->assert('test install with config files and the "force" mode. It must be a unlink error.')
            ->if($this->mock->forceInfos([
                'srcPath'       => 'src',
                'configPath'    => 'config',
                'configFiles'   => [
                    'config1.php',
                    'config2.json'
                ],
                'installScript' => false
            ]))
            ->and($this->function->realpath = function($path) {return $path;})
            ->and($this->mock->loadInfos())
            ->then
            
            ->if($this->function->copy = true)
            ->and($this->function->file_exists = true)
            ->and($this->function->mkdir = true)
            ->and(MockModuleInstall::$removeDirectoryStatus = true)
            ->and($this->function->symlink = true)
            ->and($this->function->unlink = function($path) {
                if ($path === $this->modulePath.'test') {
                    return false;
                }
                
                return true;
            })
            ->then
            
            //Output() and Error() closure not working with function mocking.
            ->if($error = new Errors)
            ->and($output = new Output)
            ->then
            
            ->and($error->startCatchError())
            ->and($output->startCatchOutput())
            ->given($this->mock->install(true))
            ->and($error->endCatchError())
            ->and($output->endCatchOutput())
            ->then
            
            ->string($output->getOutput())
                ->isEqualTo($expectedOutput)
            
            ->error(function() use ($error)
            {
                $error->setError();
            })
                ->withMessage('Reinstall fail. Symlink remove error');
    }
    
    /**
     * Test method for install() when there is a rmdir error
     * 
     * @return void
     */
    public function testForceInstallWithRmdirError()
    {
        $expectedOutput = 'test : Run install.'."\n"
                        .' > Create symbolic link ... '
                        .'[Force Reinstall: Remove symlink] '."\033[1;32mDone\033[0m\n"
                        .' > Copy config files : '."\n"
                        .' >> Create config directory for this module ... '
                        .'[Force Reinstall: Remove directory] '
                        ."\033[1;31mRemove the module config directory have fail.\033[0m\n"
        ;
        
        $this->assert('test install with config files and the "force" mode. It must be a rmdir error.')
            ->if($this->mock->forceInfos([
                'srcPath'       => 'src',
                'configPath'    => 'config',
                'configFiles'   => [
                    'config1.php',
                    'config2.json'
                ],
                'installScript' => false
            ]))
            ->and($this->function->realpath = function($path) {return $path;})
            ->and($this->mock->loadInfos())
            ->then
            
            ->if($this->function->copy = true)
            ->and($this->function->file_exists = true)
            ->and($this->function->mkdir = true)
            ->and(MockModuleInstall::$removeDirectoryStatus = false)
            ->and($this->function->symlink = true)
            ->and($this->function->unlink = true)
            ->then
            
            //Output() and Error() closure not working with function mocking.
            ->if($error = new Errors)
            ->and($output = new Output)
            ->then
            
            ->and($error->startCatchError())
            ->and($output->startCatchOutput())
            ->given($this->mock->install(true))
            ->and($error->endCatchError())
            ->and($output->endCatchOutput())
            ->then
            
            ->string($output->getOutput())
                ->isEqualTo($expectedOutput)
            
            ->error(function() use ($error)
            {
                $error->setError();
            })
                ->withMessage('Reinstall fail. Remove module config directory error.');
    }
    
    /**
     * Test method for install() when there is an error during the config
     * directory creation
     * 
     * @return void
     */
    public function testInstallWithCreateConfigDirectoryError()
    {
        $expectedOutput = 'test : Run install.'."\n"
                        .' > Create symbolic link ... '
                        ."\033[1;32mDone\033[0m\n"
                        .' > Copy config files : '."\n"
                        .' >> Create config directory for this module ... '
                        ."\033[1;31mFail. \033[0m\n"
                        .' > Check install specific script :'."\n"
                        ." >> \033[1;33m".'No specific script declared. Pass'."\033[0m\n"
        ;
        
        $this->assert('test install with config files and an error with the creation of the config directory')
            ->if($this->mock->forceInfos([
                'srcPath'       => 'src',
                'configPath'    => 'config',
                'configFiles'   => [
                    'config1.php',
                    'config2.json'
                ],
                'installScript' => false
            ]))
            ->and($this->function->realpath = function($path) {return $path;})
            ->and($this->mock->loadInfos())
            ->then
            
            ->if($this->function->copy = true)
            ->and($this->function->file_exists = false)
            ->and($this->function->mkdir = false)
            ->and(MockModuleInstall::$removeDirectoryStatus = true)
            ->and($this->function->symlink = true)
            ->and($this->function->unlink = true)
            ->then
            
            //Output() and Error() closure not working with function mocking.
            ->if($error = new Errors)
            ->and($output = new Output)
            ->then
            
            ->and($error->startCatchError())
            ->and($output->startCatchOutput())
            ->given($this->mock->install(false))
            ->and($error->endCatchError())
            ->and($output->endCatchOutput())
            ->then
            
            ->string($output->getOutput())
                ->isEqualTo($expectedOutput)
            
            ->error(function() use ($error)
            {
                $error->setError();
            })
                ->withMessage('Module test Error to create config directory');
    }
    
    /**
     * Test method for install() when there is an error because a config file
     *  not exist
     * 
     * @return void
     */
    public function testInstallWithNoConfigFileExistsError()
    {
        $expectedOutput = 'test : Run install.'."\n"
                        .' > Create symbolic link ... '
                        ."\033[1;33mNot created. Module already exist in 'modules' directory.\033[0m\n"
                        .' > Copy config files : '."\n"
                        .' >> Create config directory for this module ... '
                        ."\033[1;33mAlready exist.\033[0m\n"
                        .' >> Copy config1.php ... '
                        ."\033[1;33mAlready exist.\033[0m\n"
                        .' >> Copy config2.json ... '
                        ."\033[1;31mConfig file not exist in module source.\033[0m\n"
                        .' > Check install specific script :'."\n"
                        ." >> \033[1;33m".'No specific script declared. Pass'."\033[0m\n"
        ;
        
        $this->assert('test install with config files and an error because the config file not exist in source')
            ->if($this->mock->forceInfos([
                'srcPath'       => 'src',
                'configPath'    => 'config',
                'configFiles'   => [
                    'config1.php',
                    'config2.json'
                ],
                'installScript' => false
            ]))
            ->and($this->function->realpath = function($path) {return $path;})
            ->and($this->mock->loadInfos())
            ->then
            
            ->if($this->function->copy = true)
            ->and($this->function->mkdir = false)
            ->and(MockModuleInstall::$removeDirectoryStatus = true)
            ->and($this->function->symlink = true)
            ->and($this->function->unlink = true)
            ->and($this->function->file_exists = function($path) {
                //Test file already exist in config app directory
                if($path === $this->configPath.'test/config2.json') {
                    return false;
                }
                
                //Test file exist in source
                if($path === $this->sourcePath.'/config/config2.json') {
                    return false;
                }
                
                return true;
            })
            ->then
            
            //Output() and Error() closure not working with function mocking.
            ->if($error = new Errors)
            ->and($output = new Output)
            ->then
            
            ->and($error->startCatchError())
            ->and($output->startCatchOutput())
            ->given($this->mock->install(false))
            ->and($error->endCatchError())
            ->and($output->endCatchOutput())
            ->then
            
            ->string($output->getOutput())
                ->isEqualTo($expectedOutput)
            
            ->error(function() use ($error)
            {
                $error->setError();
            })
                ->withMessage('Source file not exist');
    }
    
    /**
     * Test method for install() when there is an error during the copy of a
     *  config file
     * 
     * @return void
     */
    public function testInstallWithCopyError()
    {
        $expectedOutput = 'test : Run install.'."\n"
                        .' > Create symbolic link ... '
                        ."\033[1;33mNot created. Module already exist in 'modules' directory.\033[0m\n"
                        .' > Copy config files : '."\n"
                        .' >> Create config directory for this module ... '
                        ."\033[1;33mAlready exist.\033[0m\n"
                        .' >> Copy config1.php ... '
                        ."\033[1;33mAlready exist.\033[0m\n"
                        .' >> Copy config2.json ... '
                        ."\033[1;31mCopy fail.\033[0m\n"
                        .' > Check install specific script :'."\n"
                        ." >> \033[1;33m".'No specific script declared. Pass'."\033[0m\n"
        ;
        
        $this->assert('test install with config files and an error with copy')
            ->if($this->mock->forceInfos([
                'srcPath'       => 'src',
                'configPath'    => 'config',
                'configFiles'   => [
                    'config1.php',
                    'config2.json'
                ],
                'installScript' => false
            ]))
            ->and($this->function->realpath = function($path) {return $path;})
            ->and($this->mock->loadInfos())
            ->then
            
            ->if($this->function->copy = false)
            ->and($this->function->mkdir = false)
            ->and(MockModuleInstall::$removeDirectoryStatus = true)
            ->and($this->function->symlink = true)
            ->and($this->function->unlink = true)
            ->and($this->function->file_exists = function($path) {
                //Test file already exist in config app directory
                if($path === $this->configPath.'test/config2.json') {
                    return false;
                }
                
                return true;
            })
            ->then
            
            //Output() and Error() closure not working with function mocking.
            ->if($error = new Errors)
            ->and($output = new Output)
            ->then
            
            ->and($error->startCatchError())
            ->and($output->startCatchOutput())
            ->given($this->mock->install(false))
            ->and($error->endCatchError())
            ->and($output->endCatchOutput())
            ->then
            
            ->string($output->getOutput())
                ->isEqualTo($expectedOutput)
            
            ->error(function() use ($error)
            {
                $error->setError();
            })
                ->withMessage('Copy fail');
    }
    
    /**
     * Test method for install() without error during copy of config files.
     * 
     * @return void
     */
    public function testInstallWithCopyConfigFile()
    {
        $expectedOutput = 'test : Run install.'."\n"
                        .' > Create symbolic link ... '
                        ."\033[1;33mNot created. Module already exist in 'modules' directory.\033[0m\n"
                        .' > Copy config files : '."\n"
                        .' >> Create config directory for this module ... '
                        ."\033[1;33mAlready exist.\033[0m\n"
                        .' >> Copy config1.php ... '
                        ."\033[1;33mAlready exist.\033[0m\n"
                        .' >> Copy config2.json ... '
                        ."\033[1;32mDone\033[0m\n"
                        .' > Check install specific script :'."\n"
                        ." >> \033[1;33m".'No specific script declared. Pass'."\033[0m\n"
        ;
        
        $this->assert('test install with config files and copy call without error.')
            ->if($this->mock->forceInfos([
                'srcPath'       => 'src',
                'configPath'    => 'config',
                'configFiles'   => [
                    'config1.php',
                    'config2.json'
                ],
                'installScript' => false
            ]))
            ->and($this->function->realpath = function($path) {return $path;})
            ->and($this->mock->loadInfos())
            ->then
            
            ->if($this->function->copy = true)
            ->and($this->function->mkdir = false)
            ->and(MockModuleInstall::$removeDirectoryStatus = true)
            ->and($this->function->symlink = true)
            ->and($this->function->unlink = true)
            ->and($this->function->file_exists = function($path) {
                //Test file already exist in config app directory
                if($path === $this->configPath.'test/config2.json') {
                    return false;
                }
                
                return true;
            })
            ->then
            
            //Output() and Error() closure not working with function mocking.
            ->if($output = new Output)
            ->and($output->startCatchOutput())
            ->given($this->mock->install(false))
            ->and($output->endCatchOutput())
            ->then
            
            ->string($output->getOutput())
                ->isEqualTo($expectedOutput);
    }
    
    /**
     * Test method for install() without config file
     * 
     * @return void
     */
    public function testInstallWithoutConfigFile()
    {
        $expectedOutput = 'test : Run install.'."\n"
                        .' > Create symbolic link ... '
                        ."\033[1;33mNot created. Module already exist in 'modules' directory.\033[0m\n"
                        .' > Copy config files : '."\n"
                        .' >> '."\033[1;33m".'No config file declared. Pass'."\033[0m\n"
                        .' > Check install specific script :'."\n"
                        ." >> \033[1;33m".'No specific script declared. Pass'."\033[0m\n"
        ;
        
        $this->assert('test install without config file')
            ->if($this->mock->forceInfos([
                'srcPath'       => 'src',
                'configPath'    => 'config',
                'configFiles'   => [],
                'installScript' => false
            ]))
            ->and($this->function->realpath = function($path) {return $path;})
            ->and($this->mock->loadInfos())
            ->then
            
            ->if($this->function->copy = true)
            ->and($this->function->mkdir = false)
            ->and(MockModuleInstall::$removeDirectoryStatus = true)
            ->and($this->function->symlink = true)
            ->and($this->function->unlink = true)
            ->and($this->function->file_exists = true)
            ->then
            
            //Output() and Error() closure not working with function mocking.
            ->if($output = new Output)
            ->and($output->startCatchOutput())
            ->given($this->mock->install(false))
            ->and($output->endCatchOutput())
            ->then
            
            ->string($output->getOutput())
                ->isEqualTo($expectedOutput);
    }
}
