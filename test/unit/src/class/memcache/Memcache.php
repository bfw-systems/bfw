<?php

namespace BFW\Memcache\test\unit;

use \atoum;

require_once(__DIR__.'/../../../../../vendor/autoload.php');

/**
 * @engine isolate
 * @php < 7.0
 */
class Memcache extends atoum
{
    use \BFW\Test\Helpers\Application;
    use MemcacheTrait;
    
    protected $mock;
    protected $addServerArgs = [];
    
    public function beforeTestMethod($testMethod)
    {
        $this->mockGenerator
            ->makeVisible('completeServerInfos')
            ->makeVisible('testConnect')
            ->generate('BFW\Memcache\Memcache')
        ;
        
        $this->setRootDir(__DIR__.'/../../../../..');
        $this->createApp();
        $this->initApp();
        
        if (
            $testMethod === 'testConstructPhp7' ||
            $testMethod === 'testConstructPhp5'
        ) {
            return;
        }
        
        $this->mock = new \mock\BFW\Memcache\Memcache;
    }
    
    public function testConstruct()
    {
        $this->assert('test \Memcache\Memcache::__construct with php5')
            ->object($this->mock = new \mock\BFW\Memcache\Memcache)
                ->isInstanceOf('\BFW\Memcache\Memcache')
        ;
    }
    
    public function testGetConfig()
    {
        $this->assert('test \Memcache\Memcache::getConfig')
            ->array($this->mock->getConfig())
                ->isNotEmpty()
        ;
    }
    
    protected function mockMethodsUsedByConnectToServer()
    {
        $this
            ->given($that = $this)
            ->if($this->calling($this->mock)->completeServerInfos = function ($infos) {
                return $infos;
            })
            ->and($this->calling($this->mock)->addServer = function(...$args) use ($that) {
                $that->addServerArgs[] = $args;
            })
            ->and($this->calling($this->mock)->testConnect = true)
        ;
    }
    
    public function testConnectToServersWithoutServer()
    {
        $this->assert('test Memcache\Memcache::connectToServers without server to connect')
            ->if($this->mockMethodsUsedByConnectToServer())
            ->then
            ->variable($this->mock->connectToServers())
                ->isNull()
            ->array($this->addServerArgs)
                ->isEmpty()
        ;
    }
    
    public function testConnectToServersWithOneServer()
    {
        $this->assert('test Memcache\Memcache::connectToServers with one memcache server')
            ->given($config = $this->app->getConfig()->getValue('memcached', 'memcached.php'))
            ->if($config['servers'][0]['host'] = 'localhost')
            ->and($config['servers'][0]['port'] = 11211)
            ->and($this->app->getConfig()->setConfigKeyForFile(
                'memcached.php',
                'memcached',
                $config
            ))
            ->then
            
            ->given($this->mock = new \mock\BFW\Memcache\Memcache)
            ->if($this->mockMethodsUsedByConnectToServer())
            ->then
            
            ->variable($this->mock->connectToServers())
                ->isNull()
            ->array($this->addServerArgs)
                ->isNotEmpty()
            ->array($this->addServerArgs[0])
                ->isEqualTo([
                   'localhost', //host
                    11211, //port
                    false, //persistent
                    1, //weight
                ])
        ;
    }
    
    public function testConnectToServersWithTwoServerAndTimeout()
    {
        $this->assert('test Memcache\Memcache::connectToServers with two memcache server and timeout config')
            ->given($config = $this->app->getConfig()->getValue('memcached', 'memcached.php'))
            ->if($config['servers'][0]['host'] = 'localhost')
            ->and($config['servers'][0]['port'] = 11211)
            ->and($config['servers'][1] = $config['servers'][0])
            ->and($config['servers'][1]['port'] = 11212)
            ->and($config['servers'][1]['timeout'] = 50)
            ->and($this->app->getConfig()->setConfigKeyForFile(
                'memcached.php',
                'memcached',
                $config
            ))
            ->then
            
            ->given($this->mock = new \mock\BFW\Memcache\Memcache)
            ->if($this->mockMethodsUsedByConnectToServer())
            ->then
            
            ->variable($this->mock->connectToServers())
                ->isNull()
            ->array($this->addServerArgs)
                ->isNotEmpty()
                ->size
                    ->isEqualTo(2)
            ->array($this->addServerArgs[0])
                ->isEqualTo([
                   'localhost', //host
                    11211, //port
                    false, //persistent
                    1, //weight
                ])
            ->array($this->addServerArgs[1])
                ->isEqualTo([
                   'localhost', //host
                    11212, //port
                    false, //persistent
                    1, //weight
                    50, //timeout
                ])
        ;
    }
    
    //******************* NOW TEST THE TRAIT METHODS *******************\\
    
    protected function testUpdateExpireCheckReplaceArgs()
    {
        $this
            ->mock($this->mock)
                ->call('replace')
                    ->withArguments('unit-test-lib', 'atoum', 0, 42)
                    ->once()
        ;
    }
}
