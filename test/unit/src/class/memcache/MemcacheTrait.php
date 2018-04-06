<?php

namespace BFW\Memcache\test\unit;

use \ReflectionClass;

trait MemcacheTrait
{
    public function testCompleteServerInfos()
    {
        $this->assert('test Memcache\MemcacheTrait::completeServerInfos with not array arg')
            ->given($mock = $this->mock)
            ->exception(function() {
                $infos = 'atoum';
                $this->mock->completeServerInfos($infos);
            })
                ->hasCode($mock::ERR_SERVER_INFOS_FORMAT)
        ;
        
        $this->assert('test Memcache\MemcacheTrait::completeServerInfos with no infos')
            ->given($infos = [])
            ->variable($this->mock->completeServerInfos($infos))
                ->isNull()
            ->array($infos)
                ->isEqualTo([
                    'host'       => null,
                    'port'       => null,
                    'weight'     => 0,
                    'timeout'    => null,
                    'persistent' => false
                ])
        ;
        
        $this->assert('test Memcache\MemcacheTrait::completeServerInfos with somes infos')
            ->given($infos = [
                'port'    => 11211,
                'timeout' => 50
            ])
            ->variable($this->mock->completeServerInfos($infos))
                ->isNull()
            ->array($infos)
                ->isEqualTo([
                    'host'       => null,
                    'port'       => 11211,
                    'weight'     => 0,
                    'timeout'    => 50,
                    'persistent' => false
                ])
        ;
        
        $this->assert('test Memcache\MemcacheTrait::completeServerInfos with all infos')
            ->given($infos = [
                    'host'       => 'localhost',
                    'port'       => 11211,
                    'weight'     => 1,
                    'timeout'    => 50,
                    'persistent' => true
            ])
            ->variable($this->mock->completeServerInfos($infos))
                ->isNull()
            ->array($infos)
                ->isEqualTo([
                    'host'       => 'localhost',
                    'port'       => 11211,
                    'weight'     => 1,
                    'timeout'    => 50,
                    'persistent' => true
                ])
        ;
    }
    
    public function testTestConnect()
    {
        $this->assert('test Memcache\MemcacheTrait::testConnect without server');
        
        //No thanks to \Memcache ...
        if (method_exists($this->mock, 'getExtendedStats')) {
            $this
                ->if($this->calling($this->mock)->getExtendedStats = false)
            ;
        }
        
        $this
            ->and($this->calling($this->mock)->getStats = false)
            ->then
            
            ->given($mock = $this->mock)
            ->exception(function() {
                $this->mock->testConnect();
            })
                ->hasCode($mock::ERR_NO_SERVER_CONNECTED)
        ;
        
        $this->assert('test Memcache\MemcacheTrait::testConnect with a not connected server');
        
        //No thanks to \Memcache ...
        if (method_exists($this->mock, 'getExtendedStats')) {
            $this
                ->if($this->calling($this->mock)->getExtendedStats = function() {
                    return [
                        'unit'  => ['uptime' => 10],
                        'test'  => ['uptime' => -1],
                        'with'  => ['uptime' => 9],
                        'atoum' => ['uptime' => 5],
                    ];
                })
            ;
        }
        
        $this
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
        
        $this->assert('test Memcache\MemcacheTrait::testConnect with a not connected server');
        
        //No thanks to \Memcache ...
        if (method_exists($this->mock, 'getExtendedStats')) {
            $this
                ->if($this->calling($this->mock)->getExtendedStats = function() {
                    return [
                        'unit'  => ['uptime' => 10],
                        'test'  => ['uptime' => 1],
                        'with'  => ['uptime' => 9],
                        'atoum' => ['uptime' => 5],
                    ];
                })
            ;
        }
        
        $this
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
        $this->assert('test Memcache\MemcacheTrait::ifExists with bad arg')
            ->given($mock = $this->mock)
            ->exception(function() {
                $this->mock->ifExists([]);
            })
                ->hasCode($mock::ERR_IFEXISTS_PARAM_TYPE)
        ;
        
        $this->assert('test Memcache\MemcacheTrait::ifExists with not existing key')
            ->if($this->calling($this->mock)->get = false)
            ->then
            ->boolean($this->mock->ifExists('phpunit'))
                ->isFalse()
        ;
        
        $this->assert('test Memcache\MemcacheTrait::ifExists with a existing key')
            ->if($this->calling($this->mock)->get = true)
            ->then
            ->boolean($this->mock->ifExists('atoum'))
                ->isTrue()
        ;
    }
    
    public function testUpdateExpire()
    {
        $mock = $this->mock;
        
        $this->assert('test Memcache\MemcacheTrait::updateExpire with bad args')
            ->exception(function() {
                $this->mock->updateExpire([], 'test');
            })
                ->hasCode($mock::ERR_UPDATEEXPIRE_PARAM_TYPE)
            //Not check all case (1 good arg, 1 bad, etc) because we don't test
            //the helper Datas here.
        ;
        
        $this->assert('test Memcache\MemcacheTrait::updateExpire with not exist key')
            ->if($this->calling($this->mock)->ifExists = false)
            ->then
            ->exception(function() {
                $this->mock->updateExpire('phpunit', 10);
            })
                ->hasCode($mock::ERR_KEY_NOT_EXIST)
        ;
        
        $this->assert('test Memcache\MemcacheTrait::updateExpire with exist key')
            ->given($replaceArgs = [])
            ->if($this->calling($this->mock)->ifExists = true)
            ->if($this->calling($this->mock)->get = function() {
                return 'atoum';
            })
            ->and($this->calling($this->mock)->replace = function (...$args) use (&$replaceArgs) {
                $replaceArgs = $args;
                return true;
            })
            ->then
            ->boolean($this->mock->updateExpire('unit-test-lib', 42))
                ->isTrue()
            ->array($replaceArgs)
                ->isNotEmpty()
        ;
        
        $this->testUpdateExpireCheckReplaceArgs($replaceArgs);
    }
}
