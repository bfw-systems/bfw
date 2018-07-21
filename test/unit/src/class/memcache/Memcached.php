<?php

namespace BFW\Memcache\test\unit;

use \atoum;

require_once(__DIR__.'/../../../../../vendor/autoload.php');

/**
 * @engine isolate
 * @php >= 7.0
 */
class Memcached extends atoum
{
    use \BFW\Test\Helpers\Application;
    use MemcacheTrait;
    
    protected $mock;
    protected $addServersArg = [];
    
    public function beforeTestMethod($testMethod)
    {
        $this->mockGenerator
            ->makeVisible('completeServerInfos')
            ->makeVisible('testConnect')
            ->makeVisible('generateServerList')
            ->generate('BFW\Memcache\Memcached')
        ;
        
        $this->setRootDir(__DIR__.'/../../../../..');
        $this->createApp();
        $this->initApp();
        
        if ($testMethod === 'testConstruct') {
            return;
        }
        
        $this->mock = new \mock\BFW\Memcache\Memcached;
    }
    
    public function testConstruct()
    {
        //How test the construt ? We can't access to persistant value into
        //the \Memcached object. So there is not test to do here :/
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
            ->if($this->calling($this->mock)->generateServerList = [])
            ->and($this->calling($this->mock)->completeServerInfos = function ($infos) {
                return $infos;
            })
            ->and($this->calling($this->mock)->addServers = function($servers) use ($that) {
                $that->addServersArg = $servers;
            })
            ->and($this->calling($this->mock)->testConnect = true)
        ;
    }
    
    public function testConnectToServersWithoutServer()
    {
        $this->assert('test Memcache\Memcached::connectToServers without server to connect')
            ->if($this->mockMethodsUsedByConnectToServer())
            ->then
            ->variable($this->mock->connectToServers())
                ->isNull()
            ->array($this->addServersArg)
                ->isEmpty()
        ;
    }
    
    public function testConnectToServersWithOneServer()
    {
        $this->assert('test Memcache\Memcached::connectToServers with one memcache server')
            ->given($config = $this->app->getConfig()->getValue('memcached', 'memcached.php'))
            ->if($config['servers'][0]['host'] = 'localhost')
            ->and($config['servers'][0]['port'] = 11211)
            ->and($this->app->getConfig()->setConfigKeyForFile(
                'memcached.php',
                'memcached',
                $config
            ))
            ->then
            
            ->given($this->mock = new \mock\BFW\Memcache\Memcached)
            ->if($this->mockMethodsUsedByConnectToServer())
            ->then
            
            ->variable($this->mock->connectToServers())
                ->isNull()
            ->array($this->addServersArg)
                ->isNotEmpty()
            ->array($this->addServersArg[0])
                ->isEqualTo([
                   'localhost', //host
                    11211, //port
                    0, //weight
                ])
        ;
    }
    
    public function testConnectToServersWithManyServerAndWithPersistent()
    {
        $this->assert('test Memcache\Memcached::connectToServers with many memcache server and with persistent')
            ->given($config = $this->app->getConfig()->getValue('memcached', 'memcached.php'))
            ->if($config['servers'][0]['host'] = 'localhost')
            ->and($config['servers'][0]['port'] = 11211)
            ->and($config['servers'][1] = $config['servers'][0])
            ->and($config['servers'][1]['port'] = 11212)
            ->and($config['servers'][1]['weight'] = 1)
            ->and($config['servers'][2] = $config['servers'][0])
            ->and($config['servers'][2]['port'] = 11213)
            ->and($config['servers'][2]['weight'] = 2)
            ->and($this->app->getConfig()->setConfigKeyForFile(
                'memcached.php',
                'memcached',
                $config
            ))
            ->then
            
            ->given($this->mock = new \mock\BFW\Memcache\Memcached)
            ->if($this->mockMethodsUsedByConnectToServer())
            ->and($this->calling($this->mock)->generateServerList = function() {
                return [
                    'localhost:11212'
                ];
            })
            ->then
            
            ->variable($this->mock->connectToServers())
                ->isNull()
            ->array($this->addServersArg)
                ->isNotEmpty()
                ->size
                    ->isEqualTo(2) //Not 3, because persistent not added
            ->array($this->addServersArg[0])
                ->isEqualTo([
                   'localhost', //host
                    11211, //port
                    0, //weight
                ])
            ->array($this->addServersArg[1])
                ->isEqualTo([
                   'localhost', //host
                    11213, //port
                    2, //weight
                ])
        ;
    }
    
    public function testGenerateServerList()
    {
        $this->assert('test Memcache\Memcached::generateServerList without server')
            ->if($this->calling($this->mock)->getServerList = function() {
                return [];
            })
            ->then
            ->array($this->mock->generateServerList())
                ->isEmpty()
        ;
            
        $this->assert('test Memcache\Memcached::generateServerList with servers')
            ->if($this->calling($this->mock)->getServerList = function() {
                return [
                    [
                        'host'   => 'mc1.localhost.com',
                        'port'   => 11211,
                        'weight' => 1
                    ],
                    [
                        'host'   => 'mc2.localhost.com',
                        'port'   => 11212,
                        'weight' => 4
                    ]
                ];
            })
            ->then
            ->array($this->mock->generateServerList())
                ->isEqualTo([
                    'mc1.localhost.com:11211',
                    'mc2.localhost.com:11212',
                ])
        ;
    }
    
    //******************* NOW TEST THE TRAIT METHODS *******************\\
    
    protected function testUpdateExpireCheckReplaceArgs()
    {
        $this
            ->mock($this->mock)
                ->call('replace')
                    ->withArguments('unit-test-lib', 'atoum', 42)
                    ->once()
        ;
    }
}
