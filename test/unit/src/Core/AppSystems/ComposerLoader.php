<?php

namespace BFW\Core\AppSystems\test\unit;

use \atoum;

require_once(__DIR__.'/../../../../../vendor/autoload.php');

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
        
        $this->setRootDir(__DIR__.'/../../../../..');
        $this->createApp();
        $this->initApp();
        
        if ($testMethod === 'testConstructor') {
            return;
        }
        
        $this->mock = new \mock\BFW\Core\AppSystems\ComposerLoader;
    }
    
    public function testConstructor()
    {
        $this->assert('test Core\AppSystems\ComposerLoader::__construct')
            ->given($this->mock = new \mock\BFW\Core\AppSystems\ComposerLoader)
            ->object($this->mock->getLoader())
                ->isInstanceOf('\Composer\Autoload\ClassLoader')
        ;
    }
    
    public function testInvoke()
    {
        $this->assert('test Core\AppSystems\ComposerLoader::__invoke')
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
            ->array($prefixes = $this->mock->getLoader()->getPrefixesPsr4())
                ->hasKeys(['Modules\\'])
            //All size is equal to 2 because we call initApp before
            ->array($prefixes['Modules\\'])
                ->size->isEqualTo(2)
        ;
    }
}
