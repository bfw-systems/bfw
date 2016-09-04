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
    protected $modulePath;

    /**
     * Instanciation de la class avant chaque méthode de test
     */
    public function beforeTestMethod($testMethod)
    {
        $this->bfwPath = realpath(__DIR__.'/../../../../');
        $this->modulePath = $this->bfwPath.'/app/modules/';
        
        if ($testMethod !== 'testConstructor') {
            $this->mock = new MockModuleInstall(
                $this->bfwPath,
                $this->modulePath.'test'
            );
        }
    }
    
    public function testConstructor()
    {
        $this->assert('test constructor')
            ->if($this->mock = new MockModuleInstall(
                $this->bfwPath,
                $this->modulePath.'test'
            ))
            ->then
            ->string($this->mock->bfwPath)
                ->isEqualTo($this->bfwPath)
            ->string($this->mock->pathToModule)
                ->isEqualTo($this->modulePath.'test')
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
            ->string($this->mock->bfwConfigPath)
                ->isEqualTo($this->bfwPath.'/app/config/test')
            ->string($this->mock->bfwModulePath)
                ->isEqualTo($this->bfwPath.'/app/modules/test')
            ->string($this->mock->srcPath)
                ->isEqualTo($this->modulePath.'test/src')
            ->string($this->mock->configPath)
                ->isEqualTo('src')
            ->array($this->mock->configFiles)
                ->isEqualTo([])
            ->string($this->mock->installScript)
                ->isEqualTo('');
    }
    
    public function testLoadInfosWithFullConfig()
    {
        $this->assert('test loadInfos with install config (only srcPath)')
            ->if($this->mock->forceInfos([
                'srcPath' => 'src',
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
            ->string($this->mock->bfwConfigPath)
                ->isEqualTo($this->bfwPath.'/app/config/test')
            ->string($this->mock->bfwModulePath)
                ->isEqualTo($this->bfwPath.'/app/modules/test')
            ->string($this->mock->srcPath)
                ->isEqualTo($this->modulePath.'test/src')
            ->string($this->mock->configPath)
                ->isEqualTo('config')
            ->array($this->mock->configFiles)
                ->isEqualTo([
                    'config1.php',
                    'config2.json'
                ])
            ->string($this->mock->installScript)
                ->isEqualTo('install.php');
    }
    
    public function testInstallWithConfigFileAndAllGood()
    {
        $expectedOutput = 'test : Run install.'."\n"
                        .' > Create symbolic link ... '."\n"
                        ."\033[1;33m Not created. Module already exist in 'modules' directory.\033[0m\n"
                        .' > Copy config files : '."\n"
                        .' >> Create config directory for this module ... '
                        ."\033[1;33m Already exist.\033[0m\n"
                        .' >> Copy config1.php ... '
                        ."\033[1;33m Existe déjà.\033[0m\n"
                        .' >> Copy config2.json ... '
                        ."\033[1;33m Existe déjà.\033[0m\n"
                        .' >> Run install specific script : '
                        .' >> '."\033[1;33m".'No specific script declared. Pass'."\033[0m\n"
        ;
        
        $this->assert('test install with config files and all must be good')
            ->if($this->mock->forceInfos([
                'srcPath' => 'src',
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
                        .' > Create symbolic link ... '."\n"
                        .'[Force Reinstall: Remove symlink] '."\033[1;32m Done\033[0m\n"
                        .' > Copy config files : '."\n"
                        .' >> Create config directory for this module ... '
                        .'[Force Reinstall: Remove symlink] '."\033[1;32m Created. \033[0m\n"
                        .' >> Copy config1.php ... '
                        ."\033[1;33m Existe déjà.\033[0m\n"
                        .' >> Copy config2.json ... '
                        ."\033[1;33m Existe déjà.\033[0m\n"
                        .' >> Run install specific script : '
                        .' >> '."\033[1;33m".'No specific script declared. Pass'."\033[0m\n"
        ;
        
        $this->assert('test install with config files and the "force" mode. All must be good.')
            ->if($this->mock->forceInfos([
                'srcPath' => 'src',
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
            ->and($this->function->rmdir = true)
            ->and($this->function->file_exists = true)
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
                        .' > Create symbolic link ... '."\n"
                        ."\033[1;31m Symbolic link creation fail.\033[0m\n"
                        .' > Copy config files : '."\n"
                        .' >> Create config directory for this module ... '
                        ."\033[1;33m Already exist.\033[0m\n"
                        .' >> Copy config1.php ... '
                        ."\033[1;33m Existe déjà.\033[0m\n"
                        .' >> Copy config2.json ... '
                        ."\033[1;33m Existe déjà.\033[0m\n"
                        .' >> Run install specific script : '
                        .' >> '."\033[1;33m".'No specific script declared. Pass'."\033[0m\n"
        ;
        
        $this->assert('test install with a symlink error.')
            ->if($this->mock->forceInfos([
                'srcPath' => 'src',
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
            ->and($this->function->rmdir = true)
            ->and($this->function->symlink = function($target, $link) {
                if ($target === $this->modulePath.'test/src') {
                    return false;
                }
                
                return true;
            })
            ->and($this->function->file_exists = function($path) {
                if ($path === $this->bfwPath.'/app/modules/test') {
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
                        .' > Create symbolic link ... '."\n"
                        .'[Force Reinstall: Remove symlink] '
                        ."\033[1;31m Symbolic link remove fail.\033[0m\n"
        ;
        
        $this->assert('test install with config files and the "force" mode. It must be a unlink error.')
            ->if($this->mock->forceInfos([
                'srcPath' => 'src',
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
            ->and($this->function->rmdir = true)
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
                        .' > Create symbolic link ... '."\n"
                        .'[Force Reinstall: Remove symlink] '."\033[1;32m Done\033[0m\n"
                        .' > Copy config files : '."\n"
                        .' >> Create config directory for this module ... '
                        .'[Force Reinstall: Remove symlink] '
                        ."\033[1;31m Remove module config directory fail.\033[0m\n"
        ;
        
        $this->assert('test install with config files and the "force" mode. It must be a rmdir error.')
            ->if($this->mock->forceInfos([
                'srcPath' => 'src',
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
            ->and($this->function->rmdir = false)
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
                        .' > Create symbolic link ... '."\n"
                        ."\033[1;32m Done\033[0m\n"
                        .' > Copy config files : '."\n"
                        .' >> Create config directory for this module ... '
                        ."\033[1;31m Fail. \033[0m\n"
                        .' >> Run install specific script : '
                        .' >> '."\033[1;33m".'No specific script declared. Pass'."\033[0m\n"
        ;
        
        $this->assert('test install with config files and an error with the creation of the config directory')
            ->if($this->mock->forceInfos([
                'srcPath' => 'src',
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
            ->and($this->function->rmdir = true)
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
                        .' > Create symbolic link ... '."\n"
                        ."\033[1;33m Not created. Module already exist in 'modules' directory.\033[0m\n"
                        .' > Copy config files : '."\n"
                        .' >> Create config directory for this module ... '
                        ."\033[1;33m Already exist.\033[0m\n"
                        .' >> Copy config1.php ... '
                        ."\033[1;33m Existe déjà.\033[0m\n"
                        .' >> Copy config2.json ... '
                        ."\033[1;31m Config file not exist in module source.\033[0m\n"
                        .' >> Run install specific script : '
                        .' >> '."\033[1;33m".'No specific script declared. Pass'."\033[0m\n"
        ;
        
        $this->assert('test install with config files and an error because the config file not exist in source')
            ->if($this->mock->forceInfos([
                'srcPath' => 'src',
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
            ->and($this->function->rmdir = true)
            ->and($this->function->symlink = true)
            ->and($this->function->unlink = true)
            ->and($this->function->file_exists = function($path) {
                //var_dump($path);
                if($path === $this->bfwPath.'/app/config/test/config2.json') {
                    return false;
                }
                if($path === 'config/config2.json') {
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
                        .' > Create symbolic link ... '."\n"
                        ."\033[1;33m Not created. Module already exist in 'modules' directory.\033[0m\n"
                        .' > Copy config files : '."\n"
                        .' >> Create config directory for this module ... '
                        ."\033[1;33m Already exist.\033[0m\n"
                        .' >> Copy config1.php ... '
                        ."\033[1;33m Existe déjà.\033[0m\n"
                        .' >> Copy config2.json ... '
                        ."\033[1;31m Copy fail.\033[0m\n"
                        .' >> Run install specific script : '
                        .' >> '."\033[1;33m".'No specific script declared. Pass'."\033[0m\n"
        ;
        
        $this->assert('test install with config files and an error with copy')
            ->if($this->mock->forceInfos([
                'srcPath' => 'src',
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
            ->and($this->function->rmdir = true)
            ->and($this->function->symlink = true)
            ->and($this->function->unlink = true)
            ->and($this->function->file_exists = function($path) {
                //var_dump($path);
                if($path === $this->bfwPath.'/app/config/test/config2.json') {
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
                        .' > Create symbolic link ... '."\n"
                        ."\033[1;33m Not created. Module already exist in 'modules' directory.\033[0m\n"
                        .' > Copy config files : '."\n"
                        .' >> Create config directory for this module ... '
                        ."\033[1;33m Already exist.\033[0m\n"
                        .' >> Copy config1.php ... '
                        ."\033[1;33m Existe déjà.\033[0m\n"
                        .' >> Copy config2.json ... '
                        ."\033[1;32m Done\033[0m\n"
                        .' >> Run install specific script : '
                        .' >> '."\033[1;33m".'No specific script declared. Pass'."\033[0m\n"
        ;
        
        $this->assert('test install with config files and copy call without error.')
            ->if($this->mock->forceInfos([
                'srcPath' => 'src',
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
            ->and($this->function->rmdir = true)
            ->and($this->function->symlink = true)
            ->and($this->function->unlink = true)
            ->and($this->function->file_exists = function($path) {
                //var_dump($path);
                if($path === $this->bfwPath.'/app/config/test/config2.json') {
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
                        .' > Create symbolic link ... '."\n"
                        ."\033[1;33m Not created. Module already exist in 'modules' directory.\033[0m\n"
                        .' > Copy config files : '."\n"
                        .' >> '."\033[1;33m".'No config file declared. Pass'."\033[0m\n"
                        .' >> Run install specific script : '
                        .' >> '."\033[1;33m".'No specific script declared. Pass'."\033[0m\n"
        ;
        
        $this->assert('test install without config file')
            ->if($this->mock->forceInfos([
                'srcPath' => 'src',
                'configPath'    => 'config',
                'configFiles'   => [],
                'installScript' => false
            ]))
            ->and($this->function->realpath = function($path) {return $path;})
            ->and($this->mock->loadInfos())
            ->then
            
            ->if($this->function->copy = true)
            ->and($this->function->mkdir = false)
            ->and($this->function->rmdir = true)
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
