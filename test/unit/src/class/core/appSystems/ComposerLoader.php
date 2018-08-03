<?php

namespace BFW\Core\AppSystems\test\unit;

use \atoum;

require_once(__DIR__.'/../../../../../../vendor/autoload.php');

/**
 * @engine isolate
 */
class ComposerLoader extends atoum
{
    use \BFW\Test\Helpers\Application;
    
    protected $mock;
    
    public function beforeTestMethod($testMethod)
    {
        $this->mockGenerator
            ->makeVisible('obtainVendorDir')
            ->makeVisible('addComposerNamespaces')
        ;
        
        $this->mock = new \mock\BFW\Core\AppSystems\ComposerLoader;
        
        $this->setRootDir(__DIR__.'/../../../../../..');
        $this->createApp();
        $this->initApp();
    }
    
    public function testInit()
    {
        $this->assert('test Core\AppSystems\ComposerLoader::isInit before init')
            ->boolean($this->mock->isInit())
                ->isFalse()
        ;
        
        $this->assert('test Core\AppSystems\ComposerLoader::init and isInit after')
            ->variable($this->mock->init())
                ->isNull()
            ->object($this->mock->getLoader())
                ->isInstanceOf('\Composer\Autoload\ClassLoader')
            ->boolean($this->mock->isInit())
                ->isTrue()
        ;
    }
    
    public function testInvoke()
    {
        $this->assert('test Core\AppSystems\ComposerLoader::__invoke')
            ->if($this->mock->init())
            ->then
            ->object($this->mock->__invoke())
                ->isIdenticalTo($this->mock->getLoader())
        ;
    }
    
    public function testToRun()
    {
        $this->assert('test Core\AppSystems\ComposerLoader::toRun')
            ->boolean($this->mock->toRun())
                ->isFalse()
        ;
    }
    
    public function testObtainVendorDir()
    {
        $this->assert('test Core\AppSystems\ComposerLoader::obtainVendorDir')
            ->string($this->mock->obtainVendorDir())
                ->isNotEmpty()
                ->isEqualTo(realpath($this->rootDir.'/vendor').'/')
        ;
    }
    
    public function testAddComposerNamespaces()
    {
        $this->assert('test Core\AppSystems\ComposerLoader::addComposerNamespaces')
            ->if($this->mock->init())
            ->then
            ->array($prefixes = $this->mock->getLoader()->getPrefixesPsr4())
                ->hasKeys(['Controller\\', 'Modeles\\', 'Modules\\'])
            //All size is equal to 2 because we call initApp before
            ->array($prefixes['Controller\\'])
                ->size->isEqualTo(2)
            ->array($prefixes['Modeles\\'])
                ->size->isEqualTo(2)
            ->array($prefixes['Modules\\'])
                ->size->isEqualTo(2)
        ;
    }
}