<?php

namespace BFW\Memcache\test\unit;

use \atoum;
use \BFW\test\unit\mocks\ApplicationForceConfig as MockApp;

require_once(__DIR__.'/../../../../../vendor/autoload.php');

/**
 * @engine isolate
 */
class Memcached extends atoum
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
                'active'  => false,
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
                'active'  => false,
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
                    'host' => 'localhost',
                    'port' => 11211,
            ])
            ->and($this->app->forceConfig($this->forcedConfig))
            ->and($this->class = new \BFW\Memcache\test\unit\mocks\Memcached($this->app));
    }
    
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
            throw new \Exception('Error : Could not be define memcached version. Return is '.$cmdReturn);
        }
        
        return $matches[2];
    }
    
    public function testConstructorWithoutServer()
    {
        $memcachedVersion = $this->getMemcachedVersion();
        $this->assert('test constructor without memcache server');
        
        if($memcachedVersion < '3.0.0') {
            $this->object($this->class = new \BFW\Memcache\Memcached($this->app))
                    ->isInstanceOf('\BFW\Memcache\Memcached');
        } else {
            $this->given($app = $this->app)
                ->exception(function() use ($app) {
                    new \BFW\Memcache\Memcached($app);
                })
                    ->hasMessage('No memcached server connected.');
        }
    }
    
    public function testConstructorWithServer()
    {
        $this->assert('test constructor with a memcache server')
            ->if($this->forcedConfig['memcached']['server'][0] = [
                    'host' => 'localhost',
                    'port' => 11211
            ])
            ->and($this->app->forceConfig($this->forcedConfig))
            ->then
            ->object($this->class = new \BFW\Memcache\Memcached($this->app))
                ->isInstanceOf('\BFW\Memcache\Memcached')
            ->and($this->class->quit());
    }
    
    public function testConstructorWithMultipleInstance()
    {
        $this->assert('test constructor with multiple instance to memcache server')
            ->if($this->forcedConfig['memcached']['persistentId'] = 'testpersistent')
            ->and($this->forcedConfig['memcached']['server'][0] = [
                    'host' => 'localhost',
                    'port' => 11211
            ])
            ->and($this->app->forceConfig($this->forcedConfig))
            ->then
            ->object($this->class = new \BFW\Memcache\Memcached($this->app))
                ->isInstanceOf('\BFW\Memcache\Memcached')
            ->object($this->class = new \BFW\Memcache\Memcached($this->app))
                ->isInstanceOf('\BFW\Memcache\Memcached')
            ->and($this->class->quit());
    }
    
    public function testConstructorWithBadServer()
    {
        $exceptionMsg     = 'Memcached server localhost:11212 not connected';
        $memcachedVersion = $this->getMemcachedVersion();
        
        if($memcachedVersion >= '3.0.0') {
            $exceptionMsg = 'No memcached server connected.';
        }
        
        $this->assert('test constructor with a bad memcache server infos')
            ->if($this->forcedConfig['memcached']['server'][0] = [
                    'host' => 'localhost',
                    'port' => 11212
            ])
            ->and($this->app->forceConfig($this->forcedConfig))
            ->then
            ->given($app = $this->app)
            ->exception(function() use ($app) {
                new \BFW\Memcache\Memcached($app);
            })
                ->hasMessage($exceptionMsg)
        ;
    }
    
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
            ->given($serverInfos = $this->forcedConfig['memcached']['server'][0])
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
                ->hasMessage('Memcache(d) server information is not an array.');
    }
    
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
                ->hasMessage('The $key parameters must be a string');
        
        $this->and($this->class->quit());
    }
    
    public function testMajExpire()
    {
        $this->connectToServer(__METHOD__);
        $this->class->delete('test');
        
        $this->assert('test majExpire with a key which does not exist')
            ->given($class = $this->class)
            ->exception(function() use ($class) {
                $class->majExpire('test', 150);
            })
                ->hasMessage('The key test not exist on memcache(d) server');
        
        $this->assert('test majExpire with a key which does exist')
            ->if($this->class->set('test', 'unit test', 3600))
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
        
        $this->and($this->class->quit());
    }
}
