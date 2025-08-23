<?php

namespace BFW\Test\Unit;

use \Exception;
use \BFW\Container;
use \BFW\ContainerNotFoundException;
use \BFW\ContainerException;
use \BFW\Core\AppSystems\SystemInterface;

class ContainerTest extends \atoum
{
    use \BFW\Test\Helpers\CreateModule;
    
    protected $mock;
    
    public function beforeTestMethod($method)
    {
        $this->mock = new \mock;
    }
    
    public function testConstructor()
    {
        $this->assert('Container::__construct')
            ->object($container = new Container())
                ->isInstanceOf('\BFW\Container')
                ->isInstanceOf('\Psr\Container\ContainerInterface')
        ;
    }
    
    public function testSetAndGet()
    {
        $this->assert('Container::set and Container::get')
            ->given($container = new Container())
            ->and($testValue = 'test_value')
            ->when($container->set('test_service', $testValue))
            ->then
                ->string($container->get('test_service'))
                    ->isEqualTo($testValue)
        ;
    }
    
    public function testHas()
    {
        $this->assert('Container::has')
            ->given($container = new Container())
            ->and($container->set('existing_service', 'value'))
            ->then
                ->boolean($container->has('existing_service'))
                    ->isTrue()
                ->boolean($container->has('non_existing_service'))
                    ->isFalse()
        ;
    }
    
    public function testGetWithCallable()
    {
        $this->assert('Container::get with callable')
            ->given($container = new Container())
            ->and($callable = function($c) { return 'called_result'; })
            ->when($container->set('callable_service', $callable))
            ->then
                ->string($container->get('callable_service'))
                    ->isEqualTo('called_result')
        ;
    }
    
    public function testGetNotFound()
    {
        $this->assert('Container::get not found')
            ->given($container = new Container())
            ->exception(function() use($container) {
                $container->get('non_existing_service');
            })
                ->isInstanceOf('\BFW\ContainerNotFoundException')
                ->isInstanceOf('\Psr\Container\NotFoundExceptionInterface')
                ->hasCode(Container::ERR_SERVICE_NOT_FOUND)
        ;
    }
    
    public function testRemove()
    {
        $this->assert('Container::remove')
            ->given($container = new Container())
            ->and($container->set('test_service', 'value'))
            ->when($container->remove('test_service'))
            ->then
                ->boolean($container->has('test_service'))
                    ->isFalse()
        ;
    }
    
    public function testGetServiceIds()
    {
        $this->assert('Container::getServiceIds')
            ->given($container = new Container())
            ->and($container->set('service1', 'value1'))
            ->and($container->set('service2', 'value2'))
            ->then
                ->array($container->getServiceIds())
                    ->contains('service1')
                    ->contains('service2')
                    ->hasSize(2)
        ;
    }
    
    public function testWithAppSystemMock()
    {
        $this->assert('Container with AppSystem mock')
            ->given($container = new Container())
            ->and($mockSystem = new \mock\BFW\Core\AppSystems\SystemInterface())
            ->and($this->calling($mockSystem)->__invoke = 'mocked_result')
            ->when($container->set('mock_system', $mockSystem))
            ->then
                ->string($container->get('mock_system'))
                    ->isEqualTo('mocked_result')
        ;
    }
}