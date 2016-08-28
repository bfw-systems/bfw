<?php

namespace BFW\Memcache\test\unit;

use \atoum;
use \BFW\test\unit\mocks\ApplicationForceConfig as MockApp;

require_once(__DIR__.'/../../../../../vendor/autoload.php');

class Memcache extends atoum
{
    /**
     * @var $class : Instance de la class
     */
    protected $class;
    
    protected $app;
    protected $forcedConfig = [];

    /**
     * Instanciation de la class avant chaque méthode de test
     */
    public function beforeTestMethod($testMethod)
    {
        $this->forcedConfig = [
            'debug'              => false,
            'errorRenderFct'     => [
                'default' => '',
                'cli'     => ''
            ],
            'exceptionRenderFct' => [
                'active'  => false,
                'default' => '',
                'cli'     => ''
            ],
            'memcached'          => [
                'enabled'      => false,
                'class'        => '\BFW\Memcache\Memcache',
                'persistentId' => null,
                'server'       => [
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
        
        $this->app = MockApp::init([
            'forceConfig' => $this->forcedConfig,
            'vendorDir'   => __DIR__.'/../../../../../vendor'
        ]);
        
        //$this->class = new \BFW\Memcache\Memcache($this->app);
    }
    
    protected function connectToServer($testName)
    {
        $this->assert('Connect to server for test '.$testName)
            ->if($this->forcedConfig['memcached']['server'][0] = [
                    'host'       => 'localhost',
                    'port'       => 11211,
                    'persistent' => true
            ])
            ->and($this->app->forceConfig($this->forcedConfig))
            ->and($this->class = new \BFW\Memcache\Memcache($this->app));
    }
    
    /**
     * @php <= 7.0
     */
    public function testConstructorWithoutServer()
    {
        $this->assert('test constructor without memcache server')
            ->object($this->class = new \BFW\Memcache\Memcache($this->app))
                ->isInstanceOf('\BFW\Memcache\Memcache');
    }
    
    /**
     * @php <= 7.0
     */
    public function testConstructorWithServer()
    {
        $this->assert('test constructor with a memcache server')
            ->if($this->forcedConfig['memcached']['server'][0] = [
                    'host' => 'localhost',
                    'port' => 11211
            ])
            ->and($this->app->forceConfig($this->forcedConfig))
            ->then
            ->object($this->class = new \BFW\Memcache\Memcache($this->app))
                ->isInstanceOf('\BFW\Memcache\Memcache')
            ->and($this->class->close());
    }
    
    /**
     * @php <= 7.0
     */
    public function testConstructorWithBadServer()
    {
        $this->assert('test constructor with a bad memcache server infos')
            ->if($this->forcedConfig['memcached']['server'][0] = [
                    'host' => 'localhost',
                    'port' => 11212
            ])
            ->and($this->app->forceConfig($this->forcedConfig))
            ->then
            ->given($app = $this->app)
            ->when(function() use ($app) {
                new \BFW\Memcache\Memcache($app);
            })
            ->error()
                ->exists()
                ->withType(E_NOTICE)
                ->withMessage('Memcache::connect(): Server localhost (tcp 11212, udp 0) failed with: Connection refused (111)')
            ->error()
                ->exists()
                ->withType(E_WARNING)
                ->withMessage('Memcache::connect(): Can\'t connect to localhost:11212, Connection refused (111)')
        ;
    }
    
    /**
     * @php <= 7.0
     * @TODO I don't know how to test the effect of "timeout".
     */
    public function testConstructorWithTimeout()
    {
        $this->assert('test constructor with a memcache server and edit timeout')
            ->if($this->forcedConfig['memcached']['server'][0] = [
                    'host'    => 'localhost',
                    'port'    => 11211,
                    'timeout' => 5
            ])
            ->and($this->app->forceConfig($this->forcedConfig))
            ->then
            ->object($this->class = new \BFW\Memcache\Memcache($this->app))
                ->isInstanceOf('\BFW\Memcache\Memcache')
            ->and($this->class->close());
    }
    
    /**
     * @php <= 7.0
     * @TODO I don't know how to test the effect of "persistent" in this context.
     */
    public function testConstructorWithPersistant()
    {
        $this->assert('test constructor with a memcache server and edit timeout')
            ->if($this->forcedConfig['memcached']['server'][0] = [
                    'host'       => 'localhost',
                    'port'       => 11211,
                    'persistent' => true
            ])
            ->and($this->app->forceConfig($this->forcedConfig))
            ->then
            ->object($this->class = new \BFW\Memcache\Memcache($this->app))
                ->isInstanceOf('\BFW\Memcache\Memcache')
            ->and($this->class->close());
    }
    
    /**
     * @php <= 7.0
     */
    public function testIfExists()
    {
        $this->connectToServer(__METHOD__);
        $this->class->delete('test');
        sleep(2);
        
        $this->assert('test ifExists with a key which does not exist')
            ->boolean($this->class->ifExists('test'))
                ->isFalse();
        
        $this->assert('test ifExists with a key which does exist')
            ->if($this->class->set('test', 'unit test', null, 100))
            ->and(sleep(2))
            ->then
            ->boolean($this->class->ifExists('test'))
                ->isTrue()
            ->and($this->class->delete('test')); //Remove tested key
        
        $this->assert('test ifExists exception')
            ->given($class = $this->class)
            ->exception(function() use ($class) {
                $class->ifExists(10);
            })
                ->hasMessage('The $key parameters must be a string');
        
        $this->and($this->class->close());
    }
    
    /**
     * @php <= 7.0
     */
    public function testMajExpire()
    {
        $this->connectToServer(__METHOD__);
        $this->class->delete('test');
        sleep(2);
        
        $this->assert('test majExpire with a key which does not exist')
            ->given($class = $this->class)
            ->exception(function() use ($class) {
                $class->majExpire('test', 150);
            })
                ->hasMessage('The key test not exist on memcache(d) server');
        
        $this->assert('test majExpire with a key which does exist')
            ->if($this->class->set('test', 'unit test', null, 3600))
            ->and(sleep(2))
            ->then
            ->boolean($this->class->majExpire('test', 150))
                ->isTrue()
            ->and($this->class->delete('test')); //Remove tested key
        
        $this->assert('test majExpire exception')
            ->given($class = $this->class)
            ->exception(function() use ($class) {
                $class->majExpire(10, '150');
            })
                ->hasMessage('Once of parameters $key or $expire not have a correct type.');
        
        $this->and($this->class->close());
    }
}
