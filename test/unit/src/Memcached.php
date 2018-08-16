<?php

namespace BFW\test\unit;

use \atoum;

require_once(__DIR__.'/../../../vendor/autoload.php');

/**
 * @engine isolate
 */
class Memcached extends atoum
{
    use \BFW\Test\Helpers\Application;
    
    protected $mock;
    protected $addServersArg = [];
    
    public function beforeTestMethod($testMethod)
    {
        $this->mockGenerator
            ->makeVisible('completeServerInfos')
            ->makeVisible('testConnect')
            ->makeVisible('generateServerList')
            ->generate('BFW\Memcached')
        ;
        
        $this->setRootDir(__DIR__.'/../../..');
        $this->createApp();
        $this->initApp();
        
        if ($testMethod === 'testConstruct') {
            return;
        }
        
        $this->mock = new \mock\BFW\Memcached;
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
        $this->assert('test Memcached::connectToServers without server to connect')
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
        $this->assert('test Memcached::connectToServers with one memcache server')
            ->given($config = $this->app->getConfig()->getValue('memcached', 'memcached.php'))
            ->if($config['servers'][0]['host'] = 'localhost')
            ->and($config['servers'][0]['port'] = 11211)
            ->and($this->app->getConfig()->setConfigKeyForFilename(
                'memcached.php',
                'memcached',
                $config
            ))
            ->then
            
            ->given($this->mock = new \mock\BFW\Memcached)
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
        $this->assert('test Memcached::connectToServers with many memcache server and with persistent')
            ->given($config = $this->app->getConfig()->getValue('memcached', 'memcached.php'))
            ->if($config['servers'][0]['host'] = 'localhost')
            ->and($config['servers'][0]['port'] = 11211)
            ->and($config['servers'][1] = $config['servers'][0])
            ->and($config['servers'][1]['port'] = 11212)
            ->and($config['servers'][1]['weight'] = 1)
            ->and($config['servers'][2] = $config['servers'][0])
            ->and($config['servers'][2]['port'] = 11213)
            ->and($config['servers'][2]['weight'] = 2)
            ->and($this->app->getConfig()->setConfigKeyForFilename(
                'memcached.php',
                'memcached',
                $config
            ))
            ->then
            
            ->given($this->mock = new \mock\BFW\Memcached)
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
        $this->assert('test Memcached::generateServerList without server')
            ->if($this->calling($this->mock)->getServerList = function() {
                return [];
            })
            ->then
            ->array($this->mock->generateServerList())
                ->isEmpty()
        ;
            
        $this->assert('test Memcached::generateServerList with servers')
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
    
    public function testCompleteServerInfos()
    {
        $this->assert('test Memcached::completeServerInfos with no infos')
            ->given($infos = [])
            ->variable($this->mock->completeServerInfos($infos))
                ->isNull()
            ->array($infos)
                ->isEqualTo([
                    'host'   => null,
                    'port'   => null,
                    'weight' => 0
                ])
        ;
        
        $this->assert('test Memcached::completeServerInfos with somes infos')
            ->given($infos = [
                'port' => 11211
            ])
            ->variable($this->mock->completeServerInfos($infos))
                ->isNull()
            ->array($infos)
                ->isEqualTo([
                    'host'   => null,
                    'port'   => 11211,
                    'weight' => 0
                ])
        ;
        
        $this->assert('test Memcached::completeServerInfos with all infos')
            ->given($infos = [
                    'host'   => 'localhost',
                    'port'   => 11211,
                    'weight' => 1
            ])
            ->variable($this->mock->completeServerInfos($infos))
                ->isNull()
            ->array($infos)
                ->isEqualTo([
                    'host'   => 'localhost',
                    'port'   => 11211,
                    'weight' => 1
                ])
        ;
    }
    
    public function testTestConnect()
    {
        $this->assert('test Memcached::testConnect without server')
            ->and($this->calling($this->mock)->getStats = false)
            ->then
            
            ->exception(function() {
                $this->mock->testConnect();
            })
                ->hasCode(\BFW\Memcached::ERR_NO_SERVER_CONNECTED)
        ;
        
        $this->assert('test Memcached::testConnect with a not connected server')
            ->and($this->calling($this->mock)->getStats = function() {
                return [
                    'unit'  => ['uptime' => 10],
                    'test'  => ['uptime' => -1],
                    'with'  => ['uptime' => 9],
                    'atoum' => ['uptime' => 5],
                ];
            })
            ->then
            
            ->given($mock = $this->mock)
            ->exception(function() {
                $this->mock->testConnect();
            })
                ->hasCode($mock::ERR_A_SERVER_IS_NOT_CONNECTED)
        ;
        
        $this->assert('test Memcached::testConnect with a not connected server')
            ->and($this->calling($this->mock)->getStats = function() {
                return [
                    'unit'  => ['uptime' => 10],
                    'test'  => ['uptime' => 1],
                    'with'  => ['uptime' => 9],
                    'atoum' => ['uptime' => 5],
                ];
            })
            ->then
            
            ->boolean($this->mock->testConnect())
                ->isTrue()
        ;
    }
    
    public function testIfExists()
    {
        $this->assert('test Memcached::ifExists with not existing key')
            ->if($this->calling($this->mock)->get = false)
            ->then
            ->boolean($this->mock->ifExists('phpunit'))
                ->isFalse()
        ;
        
        $this->assert('test Memcached::ifExists with a existing key')
            ->if($this->calling($this->mock)->get = true)
            ->then
            ->boolean($this->mock->ifExists('atoum'))
                ->isTrue()
        ;
    }
    
    public function testUpdateExpire()
    {
        $this->assert('test Memcached::updateExpire with not exist key')
            ->if($this->calling($this->mock)->ifExists = false)
            ->then
            ->exception(function() {
                $this->mock->updateExpire('phpunit', 10);
            })
                ->hasCode(\BFW\Memcached::ERR_KEY_NOT_EXIST)
        ;
        
        $this->assert('test Memcached::updateExpire with exist key')
            ->if($this->calling($this->mock)->ifExists = true)
            ->and($this->calling($this->mock)->touch = true)
            ->then
            ->boolean($this->mock->updateExpire('unit-test-lib', 42))
                ->isTrue()
            ->mock($this->mock)
                ->call('touch')
                    ->withArguments('unit-test-lib', 42)
                    ->once()
        ;
    }
}
