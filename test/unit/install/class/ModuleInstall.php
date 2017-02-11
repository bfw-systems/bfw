<?php

namespace BFW\Install\test\unit;

use \atoum;
use \BFW\Install\test\unit\mocks\ModuleInstall as MockModuleInstall;
use \BFW\test\helpers\Errors;
use \BFW\test\helpers\Output;

require_once(__DIR__.'/../../../../vendor/autoload.php');

class ModuleInstall extends atoum
{
    /**
     * @var $mock : Instance du mock pour la class
     */
    protected $mock;
    
    protected $bfwPath;
    protected $sourcePath;
    protected $modulePath;
    protected $configPath;

    /**
     * Instanciation de la class avant chaque méthode de test
     */
    public function beforeTestMethod($testMethod)
    {
        $this->bfwPath = realpath(__DIR__.'/../../../../');
        $this->sourcePath = $this->bfwPath.'/vendor/unit/test';
        $this->modulePath = $this->bfwPath.'/app/modules/';
        $this->configPath = $this->bfwPath.'/app/config/';
        
        if ($testMethod !== 'testConstructor') {
            $this->mock = new MockModuleInstall(
                $this->bfwPath,
                $this->sourcePath
            );
        }
    }
    
    public function testConstructor()
    {
        $this->assert('test constructor')
            ->if($this->mock = new MockModuleInstall(
                $this->bfwPath,
                $this->sourcePath
            ))
            ->then
            ->string($this->mock->projectPath)
                ->isEqualTo($this->bfwPath)
            ->string($this->mock->sourcePath)
                ->isEqualTo($this->sourcePath)
            ->string($this->mock->bfwConfigPath)
                ->isEqualTo($this->bfwPath.'/app/config/')
            ->string($this->mock->bfwModulePath)
                ->isEqualTo($this->bfwPath.'/app/modules/');
    }
    
    public function testGetName()
    {
        $this->assert('test getName with default value')
            ->string($this->mock->getName())
                ->isEqualTo('');
        
        //Get name without default value tested on testLoadInfos()
    }
    
    public function testLoadInfosWithoutConfig()
    {
        $this->assert('test loadInfos without install config (exception)')
            ->given($mock = $this->mock)
            ->then
            ->exception(function() use ($mock) {
                $mock->loadInfos();
            })
                ->hasMessage('srcPath must be present in install json file for module test');
    }
    
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
                ->isEqualTo($this->bfwPath)
            ->string($this->mock->bfwConfigPath)
                ->isEqualTo($this->bfwPath.'/app/config/')
            ->string($this->mock->bfwModulePath)
                ->isEqualTo($this->bfwPath.'/app/modules/')
            ->string($this->mock->sourcePath)
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
                ->isEqualTo($this->bfwPath)
            ->string($this->mock->bfwConfigPath)
                ->isEqualTo($this->bfwPath.'/app/config/')
            ->string($this->mock->bfwModulePath)
                ->isEqualTo($this->bfwPath.'/app/modules/')
            ->string($this->mock->sourcePath)
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
            ->string($this->mock->sourceInstallScript)
                ->isEqualTo('install.php');
    }
    
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
                        .' > Run install specific script :'."\n"
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
                        .' > Run install specific script :'."\n"
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
                        .' > Run install specific script :'."\n"
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
    
    public function testForceInstallWithRmdirError()
    {
        $expectedOutput = 'test : Run install.'."\n"
                        .' > Create symbolic link ... '
                        .'[Force Reinstall: Remove symlink] '."\033[1;32mDone\033[0m\n"
                        .' > Copy config files : '."\n"
                        .' >> Create config directory for this module ... '
                        .'[Force Reinstall: Remove directory] '
                        ."\033[1;31mRemove module config directory fail.\033[0m\n"
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
    
    public function testInstallWithCreateConfigDirectoryError()
    {
        $expectedOutput = 'test : Run install.'."\n"
                        .' > Create symbolic link ... '
                        ."\033[1;32mDone\033[0m\n"
                        .' > Copy config files : '."\n"
                        .' >> Create config directory for this module ... '
                        ."\033[1;31mFail. \033[0m\n"
                        .' > Run install specific script :'."\n"
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
                        .' > Run install specific script :'."\n"
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
                        .' > Run install specific script :'."\n"
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
                        .' > Run install specific script :'."\n"
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
    
    public function testInstallWithoutConfigFile()
    {
        $expectedOutput = 'test : Run install.'."\n"
                        .' > Create symbolic link ... '
                        ."\033[1;33mNot created. Module already exist in 'modules' directory.\033[0m\n"
                        .' > Copy config files : '."\n"
                        .' >> '."\033[1;33m".'No config file declared. Pass'."\033[0m\n"
                        .' > Run install specific script :'."\n"
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
