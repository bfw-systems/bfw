<?php

namespace BFW\Core\AppSystems\test\unit;

use \atoum;

require_once(__DIR__.'/../../../../../vendor/autoload.php');

/**
 * @engine isolate
 */
class Constants extends atoum
{
    use \BFW\Test\Helpers\Application;
    
    protected $mock;
    
    public function beforeTestMethod($testMethod)
    {
        $this->mockGenerator
            ->makeVisible('obtainRootDir')
        ;
        
        $this->setRootDir(__DIR__.'/../../../../..');
        $this->createApp();
        $this->app->setAppSystemToInstantiate([
            'options' => '\BFW\Core\AppSystems\Options'
        ]);
        
        /**
         * Throw exception because monolog property not exist.
         * It's true, we have deleted him just before, but if I add it, I
         * should add Config, and so Constants too.
         * But if I have remove Constants from the list, it's because I will
         * have the error "constant XXX already defined" when I will test init
         * method.
         */
        try {
            $this->initApp();
        } catch (\Exception $e) {}
        
        if ($testMethod === 'testConstructor') {
            return;
        }
        
        $this->mock = new \mock\BFW\Core\AppSystems\Constants;
    }
    
    public function testConstructor()
    {
        $this->assert('test Core\AppSystems\Constants::__construct')
            ->given($this->mock = new \mock\BFW\Core\AppSystems\Constants)
            ->given($rootDir = realpath($this->rootDir).'/')
            ->then
            
            ->boolean(defined('ROOT_DIR'))->isTrue()
            ->string(ROOT_DIR)->isEqualTo($rootDir)
            
            ->boolean(defined('APP_DIR'))->isTrue()
            ->string(APP_DIR)->isEqualTo($rootDir.'app/')
            
            ->boolean(defined('SRC_DIR'))->isTrue()
            ->string(SRC_DIR)->isEqualTo($rootDir.'src/')
            
            ->boolean(defined('WEB_DIR'))->isTrue()
            ->string(WEB_DIR)->isEqualTo($rootDir.'web/')
            
            ->boolean(defined('CONFIG_DIR'))->isTrue()
            ->string(CONFIG_DIR)->isEqualTo($rootDir.'app/config/')
            
            ->boolean(defined('MODULES_DIR'))->isTrue()
            ->string(MODULES_DIR)->isEqualTo($rootDir.'app/modules/')
            
            ->boolean(defined('CLI_DIR'))->isTrue()
            ->string(CLI_DIR)->isEqualTo($rootDir.'src/cli/')
        ;
    }
    
    public function testInvoke()
    {
        $this->assert('test Core\AppSystems\Constants::__invoke')
            ->variable($this->mock->__invoke())
                ->isNull()
        ;
    }
    
    public function testToRun()
    {
        $this->assert('test Core\AppSystems\Constants::toRun')
            ->boolean($this->mock->toRun())
                ->isFalse()
        ;
    }
    
    public function testObtainRootDir()
    {
        $this->assert('test Core\AppSystems\Constants::obtainRootDir')
            ->string($this->mock->obtainRootDir())
                ->isNotEmpty()
                ->isEqualTo(realpath($this->rootDir).'/')
        ;
    }
}