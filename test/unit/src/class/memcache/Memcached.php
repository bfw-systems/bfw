<?php

namespace BFW\Memcache\test\unit;

use \atoum;
use \BFW\Memcache\test\unit\mocks\Memcached as MockMemcached;
use \BFW\test\helpers\ApplicationInit as AppInit;

require_once(__DIR__.'/../../../../../vendor/autoload.php');

/**
 * @engine isolate
 */
class Memcached extends atoum
{
    /**
     * @var $class Class instance
     */
    protected $class;
    
    /**
     * @var \BFW\test\helpers\ApplicationInit $app BFW Application instance
     */
    protected $app;
    
    /**
     * @var array $forcedConfig Config used for all test into this file
     */
    protected $forcedConfig = [];

    /**
     * Call before each test method
     * Define forced config
     * Instantiate BFW Application class
     * 
     * @param $testMethod string The name of the test method executed
     * 
     * @return void
     */
    public function beforeTestMethod($testMethod)
    {
        $this->forcedConfig = [
            'debug'              => false,
            'errorRenderFct'     => [
                'enabled' => false,
                'default' => [
                    'class'  => '',
                    'method' => ''
                ],
                'cli'     => [
                    'class'  => '',
                    'method' => ''
                ]
            ],
            'exceptionRenderFct' => [
                'enabled' => false,
                'default' => [
                    'class'  => '',
                    'method' => ''
                ],
                'cli'     => [
                    'class'  => '',
                    'method' => ''
                ]
            ],
            'memcached'          => [
                'enabled'      => false,
                'class'        => '\BFW\Memcache\Memcached',
                'persistentId' => null,
                'servers'       => [
                    [
                        'host'       => '',
                        'port'       => 0,
                        'timeout'    => null,
                        'persistent' => false,
                        'weight'     => 0
                    ]
                ]
            ]
        ];
        
        $this->app = AppInit::init([
            'forceConfig' => $this->forcedConfig
        ]);
        
        //$this->class = new \BFW\Memcache\Memcache($this->app);
    }
    
    /**
     * Connect to localhost memcache server
     * 
     * @param string $testName The test method name which have call this method
     * 
     * @return void
     */
    protected function connectToServer($testName)
    {
        $this->assert('Connect to server for test '.$testName)
            ->if($this->forcedConfig['memcached']['servers'][0] = [
                    'host' => 'localhost',
                    'port' => 11211,
            ])
            ->and($this->app->forceConfig($this->forcedConfig))
            ->and($this->class = new MockMemcached);
    }
    
    /**
     * Obtain memcached extension version
     * 
     * @return string
     * 
     * @throws \Exception If the version could not be defined
     */
    protected function getMemcachedVersion()
    {
        $cmdReturn = trim(shell_exec('php --re memcached | grep "version"'));
        
        $matches = [];
        $pregMatch = preg_match(
            '/(.*)version ((\d+).(\d+).(\d+))(.*)/mi',
            $cmdReturn,
            $matches
        );
        
        if($pregMatch === false) {
            throw new \Exception(
                'Error : Could not be define memcached version. Return is '
                .$cmdReturn
            );
        }
        
        return $matches[2];
    }
    
    /**
     * Test method for __constructor() when no server is declared
     * 
     * @return void
     */
    public function testConstructorWithoutServer()
    {
        $memcachedVersion = $this->getMemcachedVersion();
        $this->assert('test constructor without memcache server');
        
        if($memcachedVersion < '3.0.0') {
            $this->object($this->class = new \BFW\Memcache\Memcached)
                    ->isInstanceOf('\BFW\Memcache\Memcached');
        } else {
            $this->given($app = $this->app)
                ->exception(function() {
                    new \BFW\Memcache\Memcached;
                })
                    ->hasCode(\BFW\Memcache\Memcached::NO_SERVER_CONNECTED)
                    ->hasMessage('No memcached server connected.');
        }
    }
    
    /**
     * Test method for __constructor() with a declared server
     * 
     * @return void
     */
    public function testConstructorWithServer()
    {
        $this->assert('test constructor with a memcache server')
            ->if($this->forcedConfig['memcached']['servers'][0] = [
                    'host' => 'localhost',
                    'port' => 11211
            ])
            ->and($this->app->forceConfig($this->forcedConfig))
            ->then
            ->object($this->class = new \BFW\Memcache\Memcached)
                ->isInstanceOf('\BFW\Memcache\Memcached')
            ->and($this->class->quit());
    }
    
    /**
     * Test method for __constructor() with a declared server and ???
     * 
     * @TODO Check this test, it seem to be a WTF
     * 
     * @return void
     */
    public function testConstructorWithMultipleInstance()
    {
        $this->assert('test constructor with multiple instance to memcache server')
            ->if($this->forcedConfig['memcached']['persistentId'] = 'testpersistent')
            ->and($this->forcedConfig['memcached']['servers'][0] = [
                    'host' => 'localhost',
                    'port' => 11211
            ])
            ->and($this->app->forceConfig($this->forcedConfig))
            ->then
            ->object($this->class = new \BFW\Memcache\Memcached)
                ->isInstanceOf('\BFW\Memcache\Memcached')
            ->object($this->class = new \BFW\Memcache\Memcached)
                ->isInstanceOf('\BFW\Memcache\Memcached')
            ->and($this->class->quit());
    }
    
    /**
     * Test method for __constructor() with a declared server but when the
     * server not exist.
     * 
     * @return void
     */
    public function testConstructorWithBadServer()
    {
        $exceptionMsg     = 'Memcached server localhost:11212 not connected';
        $exceptionCode    = \BFW\Memcache\Memcached::ERR_A_SERVER_IS_NOT_CONNECTED;
        $memcachedVersion = $this->getMemcachedVersion();
        
        if($memcachedVersion >= '3.0.0') {
            $exceptionMsg  = 'No memcached server connected.';
            $exceptionCode = \BFW\Memcache\Memcached::ERR_NO_SERVER_CONNECTED;
        }
        
        $this->assert('test constructor with a bad memcache server infos')
            ->if($this->forcedConfig['memcached']['servers'][0] = [
                    'host' => 'localhost',
                    'port' => 11212
            ])
            ->and($this->app->forceConfig($this->forcedConfig))
            ->then
            ->given($app = $this->app)
            ->exception(function() {
                new \BFW\Memcache\Memcached;
            })
                ->hasCode($exceptionCode)
                ->hasMessage($exceptionMsg)
        ;
    }
    
    /**
     * Test method for getServerInfos()
     * 
     * @return void
     */
    public function testGetServerInfos()
    {
        $this->connectToServer(__METHOD__);
        
        $this->assert('test getServerInfos without datas')
            ->given($serverInfos = [])
            ->if($this->class->callGetServerInfos($serverInfos))
            ->then
            ->array($serverInfos)
                ->isEqualTo([
                    'host'       => null,
                    'port'       => null,
                    'weight'     => 0,
                    'timeout'    => null,
                    'persistent' => false,
                ]);
        
        $this->assert('test getServerInfos with datas')
            ->given($serverInfos = $this->forcedConfig['memcached']['servers'][0])
            ->if($serverInfos['weight'] = 10)
            ->and($this->class->callGetServerInfos($serverInfos))
            ->then
            ->array($serverInfos)
                ->isEqualTo([
                    'host'       => 'localhost',
                    'port'       => 11211,
                    'weight'     => 10,
                    'timeout'    => null,
                    'persistent' => false
                ]);
        
        $this->assert('test getServerInfos exception')
            ->given($class = $this->class)
            ->exception(function () use ($class) {
                $serverInfos = 'test';
                $class->callGetServerInfos($serverInfos);
            })
                ->hasCode($class::ERR_SERVER_INFOS_FORMAT)
                ->hasMessage('Memcache(d) server information is not an array.');
    }
    
    /**
     * Test method for ifExists()
     * 
     * @return void
     */
    public function testIfExists()
    {
        $this->connectToServer(__METHOD__);
        $this->class->delete('test');
        
        $this->assert('test ifExists with a key which does not exist')
            ->boolean($this->class->ifExists('test'))
                ->isFalse();
        
        $this->assert('test ifExists with a key which does exist')
            ->if($this->class->set('test', 'unit test', 100))
            ->then
            ->boolean($this->class->ifExists('test'))
                ->isTrue()
            ->and($this->class->delete('test')); //Remove tested key
        
        $this->assert('test ifExists exception')
            ->given($class = $this->class)
            ->exception(function() use ($class) {
                $class->ifExists(10);
            })
                ->hasCode($class::ERR_IFEXISTS_PARAM_TYPE)
                ->hasMessage(
                    'The $key parameters must be a string. '
                    .'Currently the value is a/an integer and is equal to 10'
                );
        
        $this->and($this->class->quit());
    }
    
    /**
     * Test method for updateExpire()
     * 
     * @return void
     */
    public function testUpdateExpire()
    {
        $this->connectToServer(__METHOD__);
        $this->class->delete('test');
        
        $this->assert('test majExpire with a key which does not exist')
            ->given($class = $this->class)
            ->exception(function() use ($class) {
                $class->updateExpire('test', 150);
            })
                ->hasCode($class::ERR_KEY_NOT_EXIST)
                ->hasMessage('The key test not exist on memcache(d) server');
        
        $this->assert('test majExpire with a key which does exist')
            ->if($this->class->set('test', 'unit test', 3600))
            ->then
            ->boolean($this->class->updateExpire('test', 150))
                ->isTrue()
            ->and($this->class->delete('test')); //Remove tested key
        
        $this->assert('test majExpire exception')
            ->given($class = $this->class)
            ->exception(function() use ($class) {
                $class->updateExpire(10, '150');
            })
                ->hasCode($class::ERR_UPDATEEXPIRE_PARAM_TYPE)
                ->hasMessage('Once of parameters $key or $expire not have a correct type.');
        
        $this->and($this->class->quit());
    }
}
