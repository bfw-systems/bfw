<?php

namespace BFW\Core\AppSystems\test\unit;

use \atoum;

require_once(__DIR__.'/../../../../../vendor/autoload.php');

/**
 * @engine isolate
 */
class Memcached extends atoum
{
    use \BFW\Test\Helpers\Application;
    
    protected $mock;
    
    public function beforeTestMethod($testMethod)
    {
        $this->mockGenerator
            ->makeVisible('loadMemcached')
        ;
        
        $this->setRootDir(__DIR__.'/../../../../..');
        $this->createApp();
        $this->initApp();
        
        if ($testMethod === 'testConstructor') {
            return;
        }
        
        $this->mock = new \mock\BFW\Core\AppSystems\Memcached;
    }
    
    public function testConstructor()
    {
        $this->assert('test Core\AppSystems\Memcached::__construct')
            ->given($this->mock = new \mock\BFW\Core\AppSystems\Memcached)
            ->if($this->calling($this->mock)->loadMemcached = null)
            ->variable($this->mock->getMemcached())
                ->isNull()
            ->mock($this->mock)
                ->call('loadMemcached')
                    ->once()
        ;
    }
    
    public function testInvoke()
    {
        $this->assert('test Core\AppSystems\Memcached::__invoke')
            ->variable($this->mock->__invoke())
                ->isNull() //default value because memcached disabled
        ;
    }
    
    public function testToRun()
    {
        $this->assert('test Core\AppSystems\Memcached::toRun')
            ->boolean($this->mock->toRun())
                ->isFalse()
        ;
    }
    
    public function testLoadMemcached()
    {
        $this->assert('test Core\AppSystems\Memcached::loadMemcached - prepare')
            ->given($config = \BFW\Application::getInstance()->getConfig())
            ->given($memcacheConfig = $config->getConfigByFilename('memcached.php'))
            ->and($config->setConfigForFilename('memcached.php', $memcacheConfig))
        ;
        
        $this->assert('test Core\AppSystems\Memcached::loadMemcached with memcached disabled')
            ->variable($this->mock->loadMemcached())
                ->isNull()
            ->variable($this->mock->getMemcached())
                ->isNull()
        ;
        
        $this->assert('test Core\AppSystems\Memcached::loadMemcached with memcached enabled')
            ->if($memcacheConfig['memcached']['enabled'] = true)
            ->and($memcacheConfig['memcached']['servers'][0]['host'] = 'localhost')
            ->and($memcacheConfig['memcached']['servers'][0]['port'] = 11211)
            ->and($config->setConfigForFilename('memcached.php', $memcacheConfig))
            ->then
            ->variable($this->mock->loadMemcached())
                ->isNull()
            ->object($this->mock->getMemcached())
                ->isInstanceOf('\BFW\Memcached')
            ->object($this->mock->__invoke())
                ->isIdenticalTo($this->mock->getMemcached())
            ->array($this->mock->getMemcached()->getServerList())
                ->isNotEmpty()
        ;
        
        $this->assert('test Core\AppSystems\Memcached::loadMemcached with a memcached error')
            ->if($memcacheConfig['memcached']['enabled'] = true)
            ->and($memcacheConfig['memcached']['servers'][0]['host'] = 'localhost')
            ->and($memcacheConfig['memcached']['servers'][0]['port'] = 11212)
            ->and($config->setConfigForFilename('memcached.php', $memcacheConfig))
            ->then
            ->when(function() {
                $this->mock->loadMemcached();
            })
            ->error()
                ->withType(E_USER_WARNING)
                ->withMessage('Memcached connexion error #1103001 : No memcached server connected.')
                ->exists()
            ->variable($this->mock->getMemcached())
                ->isNull()
        ;
    }
}