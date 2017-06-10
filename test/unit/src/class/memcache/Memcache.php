<?php

namespace BFW\Memcache\test\unit;

use \atoum;
use \BFW\Memcache\test\unit\mocks\Memcache as MockMemcache;
use \BFW\test\helpers\ApplicationInit as AppInit;

require_once(__DIR__.'/../../../../../vendor/autoload.php');

/**
 * @engine isolate
 */
class Memcache extends atoum
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
                'class'        => '\BFW\Memcache\Memcache',
                'persistentId' => null,
                'servers'      => [
                    [
                        'host'       => '',
                        'port'       => 0,
                        'weight'     => 0,
                        'timeout'    => null,
                        'persistent' => false
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
                    'host'       => 'localhost',
                    'port'       => 11211,
                    'persistent' => true
            ])
            ->and($this->app->forceConfig($this->forcedConfig))
            ->and($this->class = new MockMemcache);
    }
    
    /**
     * @php < 7.0
     * 
     * Test method for __constructor() when no server is declared
     * This method is only executed when PHP < 7.0 because after, the \Memcache
     * class not exist.
     * 
     * @return void
     */
    public function testConstructorWithoutServer()
    {
        $this->assert('test constructor without memcache server')
            ->given($exceptionMsg = '')
            ->given($exceptionCode = 0)
            ->when(function() use (&$exceptionMsg, &$exceptionCode) {
                    try {
                        new \BFW\Memcache\Memcache;
                    } catch (\Exception $e) {
                        $exceptionMsg  = $e->getMessage();
                        $exceptionCode = $e->getCode();
                    }
                })
            ->error()
                ->withType(E_WARNING)
                ->withPattern('/Memcache(Pool)?::getextendedstats\(\): No servers added to memcache connection/')
                ->exists()
            ->string($exceptionMsg)
                ->isEqualTo('No memcached server connected.')
            ->integer($exceptionCode)
                ->isEqualTo(\BFW\Memcache\Memcache::ERR_NO_SERVER_CONNECTED);
    }
    
    /**
     * @php < 7.0
     * 
     * Test method for __constructor() with a declared server
     * This method is only executed when PHP < 7.0 because after, the \Memcache
     * class not exist.
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
            ->object($this->class = new \BFW\Memcache\Memcache)
                ->isInstanceOf('\BFW\Memcache\Memcache')
            ->and($this->class->close());
    }
    
    /**
     * @php < 7.0
     * 
     * Test method for __constructor() with a declared server but when the
     * server not exist.
     * This method is only executed when PHP < 7.0 because after, the \Memcache
     * class not exist.
     * 
     * @return void
     */
    public function testConstructorWithBadServer()
    {
        $this->assert('test constructor with a bad memcache server infos')
            ->if($this->forcedConfig['memcached']['servers'][0] = [
                    'host' => 'localhost',
                    'port' => 11212
            ])
            ->and($this->app->forceConfig($this->forcedConfig))
            ->then
            ->given($exceptionMsg = '')
            ->given($exceptionCode = '')
            ->when(function() use (&$exceptionMsg, &$exceptionCode) {
                    try {
                        new \BFW\Memcache\Memcache;
                    } catch (\Exception $e) {
                        $exceptionMsg  = $e->getMessage();
                        $exceptionCode = $e->getCode();
                    }
                })
            ->error()
                ->exists()
                ->withType(E_NOTICE)
                ->withMessage('MemcachePool::getextendedstats(): Server localhost (tcp 11212, udp 0) failed with: Connection refused (111)')
            ->string($exceptionMsg)
                ->isEqualTo('Memcached server localhost:11212 not connected')
            ->integer($exceptionCode)
                ->isEqualTo(\BFW\Memcache\Memcache::ERR_A_SERVER_IS_NOT_CONNECTED)
        ;
    }
    
    /**
     * @php < 7.0
     * @TODO I don't know how to test the effect of "timeout".
     * 
     * Test method for __constructor() with the "timeout" parameter
     * This method is only executed when PHP < 7.0 because after, the \Memcache
     * class not exist.
     * 
     * @return void
     */
    public function testConstructorWithTimeout()
    {
        $this->assert('test constructor with a memcache server and edit timeout')
            ->if($this->forcedConfig['memcached']['servers'][0] = [
                    'host'    => 'localhost',
                    'port'    => 11211,
                    'timeout' => 5
            ])
            ->and($this->app->forceConfig($this->forcedConfig))
            ->then
            ->object($this->class = new \BFW\Memcache\Memcache)
                ->isInstanceOf('\BFW\Memcache\Memcache')
            ->and($this->class->close());
    }
    
    /**
     * @php < 7.0
     * @TODO I don't know how to test the effect of "persistent" in this context.
     * 
     * Test method for __constructor() with the "persistent" parameter
     * This method is only executed when PHP < 7.0 because after, the \Memcache
     * class not exist.
     * 
     * @return void
     */
    public function testConstructorWithPersistant()
    {
        $this->assert('test constructor with a memcache server and edit timeout')
            ->if($this->forcedConfig['memcached']['servers'][0] = [
                    'host'       => 'localhost',
                    'port'       => 11211,
                    'persistent' => true
            ])
            ->and($this->app->forceConfig($this->forcedConfig))
            ->then
            ->object($this->class = new \BFW\Memcache\Memcache)
                ->isInstanceOf('\BFW\Memcache\Memcache')
            ->and($this->class->close());
    }
    
    /**
     * @php < 7.0
     * 
     * Test method for getServerInfos()
     * This method is only executed when PHP < 7.0 because after, the \Memcache
     * class not exist.
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
            ->if($serverInfos['timeout'] = 10)
            ->and($this->class->callGetServerInfos($serverInfos))
            ->then
            ->array($serverInfos)
                ->isEqualTo([
                    'host'       => 'localhost',
                    'port'       => 11211,
                    'timeout'    => 10,
                    'persistent' => true,
                    'weight'     => 0,
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
     * @php < 7.0
     * 
     * Test method for ifExists()
     * This method is only executed when PHP < 7.0 because after, the \Memcache
     * class not exist.
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
            ->if($this->class->set('test', 'unit test', null, 100))
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
        
        $this->and($this->class->close());
    }
    
    /**
     * @php < 7.0
     * 
     * Test method for updateExpire()
     * This method is only executed when PHP < 7.0 because after, the \Memcache
     * class not exist.
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
            ->if($this->class->set('test', 'unit test', null, 3600))
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
        
        $this->and($this->class->close());
    }
}
