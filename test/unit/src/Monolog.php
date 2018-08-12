<?php

namespace BFW\test\unit;

use \atoum;

require_once(__DIR__.'/../../../vendor/autoload.php');

/**
 * @engine isolate
 */
class Monolog extends atoum
{
    use \BFW\Test\Helpers\Application;
    
    protected $mock;
    
    protected $config;
    
    public function beforeTestMethod($testMethod)
    {
        $this->setRootDir(__DIR__.'/../../..');
        $this->createApp();
        $this->initApp();
        
        $this->mockGenerator
            ->makeVisible('checkHandlerInfos')
            ->makeVisible('checkHandlerName')
            ->makeVisible('checkHandlerArgs')
            ->generate('BFW\Monolog')
        ;
        
        $this->config = new \BFW\Config('bfw');
        $this->config->setConfigForFilename(
            'monolog.php',
            [
                'handlers' => []
            ]
        );
        
        if ($testMethod !== 'testConstruct') {
            $this->mock = new \mock\BFW\Monolog('atoum', $this->config);
        }
    }
    
    protected function obtainTestHandler()
    {
        return [
            'name' => '\Monolog\Handler\StreamHandler',
            'args' => [
                APP_DIR.'logs/bfw/bfw.log',
                \Monolog\Logger::DEBUG
            ]
        ];
    }
    
    public function testConstruct()
    {
        $this->assert('test Monolog::__construct')
            ->object($mock = new \mock\BFW\Monolog('atoum', $this->config))
                ->isInstanceOf('\BFW\Monolog')
        ;
    }
    
    public function testGetChannelName()
    {
        $this->assert('test Monolog::getChannelName')
            ->string($this->mock->getChannelName())
                ->isEqualTo('atoum')
        ;
    }
    
    public function testGetConfig()
    {
        $this->assert('test Monolog::getConfig')
            ->object($this->mock->getConfig())
                ->isIdenticalTo($this->config)
        ;
    }
    
    public function testGetLogger()
    {
        $this->assert('test Monolog::getLogger')
            ->object($this->mock->getLogger())
                ->isInstanceOf('\Monolog\Logger')
        ;
    }
    
    public function testGetHandlers()
    {
        $this->assert('test Monolog::getHandlers')
            ->array($this->mock->getHandlers())
                ->isEmpty()
        ;
    }
    
    public function testAddAllHandlers()
    {
        $this->assert('test Monolog::addAllHandlers - prepare')
            ->given($this->calling($this->mock)->addNewHandler = null)
            ->given($handlerInfosFileLog = $this->obtainTestHandler())
        ;
        
        $this->assert('test Monolog::addAllHandlers without handler')
            ->variable($this->mock->addAllHandlers())
                ->isNull()
            ->mock($this->mock)
                ->call('addNewHandler')
                    ->never()
        ;
        
        $this->assert('test Monolog::addAllHandlers with one handler')
            ->if($this->config->setConfigKeyForFilename(
                'monolog.php',
                'handlers',
                [$handlerInfosFileLog]
            ))
            ->then
            ->variable($this->mock->addAllHandlers())
                ->isNull()
            ->mock($this->mock)
                ->call('addNewHandler')
                    ->withArguments($handlerInfosFileLog)
                    ->once()
        ;
        
        $this->assert('test Monolog::addAllHandlers with handlers config object format')
            ->if($this->config->setConfigKeyForFilename(
                'monolog.php',
                'handlers',
                $handlerInfosFileLog
            ))
            ->then
            ->variable($this->mock->addAllHandlers())
                ->isNull()
            ->mock($this->mock)
                ->call('addNewHandler')
                    ->withArguments($handlerInfosFileLog)
                    ->once()
        ;
        
        $this->assert('test Monolog::addAllHandlers with handlers config bad value')
            ->if($this->config->setConfigKeyForFilename(
                'monolog.php',
                'handlers',
                123
            ))
            ->then
            ->exception(function() {
                $this->mock->addAllHandlers();
            })
                ->hasCode(\BFW\Monolog::ERR_HANDLERS_LIST_FORMAT)
        ;
    }
    
    public function testAddNewHandler()
    {
        $this->assert('test Monolog::addNewHandler - prepare')
            ->given($this->calling($this->mock)->checkHandlerInfos = null)
            ->given($handlerInfosFileLog = $this->obtainTestHandler())
        ;
        
        $this->assert('test Monolog::addNewHandler')
            ->variable($this->mock->addNewHandler($handlerInfosFileLog))
                ->isNull()
            ->array($mockHandlersList = $this->mock->getHandlers())
                ->isNotEmpty()
            ->array($monologHandlersList = $this->mock->getLogger()->getHandlers())
                ->isNotEmpty()
            ->object($mockHandlersList[0])
                ->isIdenticalTo($mockHandlersList[0])
                ->isInstanceOf('\Monolog\Handler\StreamHandler')
            ->variable($mockHandlersList[0]->getStream())
                ->isNull()
            ->string($mockHandlersList[0]->getUrl())
                ->isEqualTo(APP_DIR.'logs/bfw/bfw.log')
            ->variable($mockHandlersList[0]->getLevel())
                ->isEqualTo(\Monolog\Logger::DEBUG)
        ;
    }
    
    public function testCheckHandlerInfos()
    {
        $this->assert('test Monolog::checkHandlerInfos')
            ->given($this->calling($this->mock)->checkHandlerName = null)
            ->given($this->calling($this->mock)->checkHandlerArgs = null)
            ->given($handlerInfosFileLog = $this->obtainTestHandler())
            ->then
            
            ->variable($this->mock->checkHandlerInfos($handlerInfosFileLog))
                ->isNull()
            ->mock($this->mock)
                ->call('checkHandlerName')->once()
                ->call('checkHandlerArgs')->once()
        ;
    }
    
    public function testCheckHandlerName()
    {
        $this->assert('test Monolog::checkHandlerName without name property')
            ->exception(function() {
                $this->mock->checkHandlerName([]);
            })
                ->hasCode(\BFW\Monolog::ERR_HANDLER_INFOS_MISSING_NAME)
        ;
        
        $this->assert('test Monolog::checkHandlerName when is not a string')
            ->exception(function() {
                $this->mock->checkHandlerName([
                    'name' => 123
                ]);
            })
                ->hasCode(\BFW\Monolog::ERR_HANDLER_NAME_NOT_A_STRING)
        ;
        
        $this->assert('test Monolog::checkHandlerName when is not an existing class')
            ->exception(function() {
                $this->mock->checkHandlerName([
                    'name' => '\unitTest'
                ]);
            })
                ->hasCode(\BFW\Monolog::ERR_HANDLER_CLASS_NOT_FOUND)
        ;
        
        $this->assert('test Monolog::checkHandlerName when is all good')
            ->variable($this->mock->checkHandlerName([
                'name' => '\Monolog\Handler\StreamHandler'
            ]))
                ->isNull()
        ;
    }
    
    public function testCheckHandlerArgs()
    {
        $this->assert('test Monolog::checkHandlerArgs without args property')
            ->given($handlerInfos = [])
            ->variable($this->mock->checkHandlerArgs($handlerInfos))
                ->isNull()
            ->array($handlerInfos)
                ->hasKey('args')
            ->array($handlerInfos['args'])
                ->isEmpty()
        ;
        
        $this->assert('test Monolog::checkHandlerArgs with args property is not an array')
            ->given($handlerInfos = [
                'args' => 123
            ])
            ->variable($this->mock->checkHandlerArgs($handlerInfos))
                ->isNull()
            ->array($handlerInfos)
                ->hasKey('args')
            ->array($handlerInfos['args'])
                ->isEqualTo([123])
        ;
        
        $this->assert('test Monolog::checkHandlerArgs with args property is an array')
            ->given($handlerInfos = [
                'args' => [APP_DIR.'logs/bfw/bfw.log']
            ])
            ->variable($this->mock->checkHandlerArgs($handlerInfos))
                ->isNull()
            ->array($handlerInfos)
                ->hasKey('args')
            ->array($handlerInfos['args'])
                ->isEqualTo([APP_DIR.'logs/bfw/bfw.log'])
        ;
    }
}