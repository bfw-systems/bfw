<?php

namespace BFW\Core\test\unit;

use \atoum;
use \BFW\test\unit\mocks\ApplicationForceConfig as MockApp;
use \BFW\Core\test\unit\mocks\Errors as MockErrors;

require_once(__DIR__.'/../../../../../vendor/autoload.php');
require_once(__DIR__.'/../../../mocks/src/class/core/ErrorsFunctions.php');

/**
 * @engine isolate
 */
class Errors extends atoum
{
    /**
     * @var $mock : Instance du mock pour la class
     */
    protected $mock;
    
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
                    'method' => 'default_error_render'
                ],
                'cli'     => [
                    'class'  => '',
                    'method' => 'cli_error_render'
                ]
            ],
            'exceptionRenderFct' => [
                'active'  => false,
                'default' => [
                    'class'  => '',
                    'method' => 'default_exception_render'
                ],
                'cli'     => [
                    'class'  => '',
                    'method' => 'cli_exception_render'
                ]
            ],
            'memcached'          => [
                'enabled'      => false,
                'class'        => '',
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
        
        if ($testMethod === 'testConstructor') {
            return;
        }
        
        $this->mock = new MockErrors;
    }
    
    public function testConstructor()
    {
        $this->assert('test constructor')
            ->object($this->mock = new MockErrors)
                ->isInstanceOf('\BFW\Core\Errors');
    }
    
    public function testDefineErrorHandler()
    {
        $this->assert('test defineErrorHandler without render')
            ->variable($this->mock->callDefineErrorHandler())
                ->isNull();
        
        $this->assert('test defineErrorHandler with a function render')
            ->if($this->forcedConfig['errorRenderFct']['active'] = true)
            ->and($this->app->forceConfig($this->forcedConfig))
            ->then
            ->array($this->mock->callDefineErrorHandler())
                ->isEqualTo([
                    $this->mock,
                    'errorHandler'
                ]);
        
        $this->assert('test defineErrorHandler with a class render')
            ->if($this->forcedConfig['errorRenderFct']['active'] = true)
            ->and($this->forcedConfig['errorRenderFct']['cli'] = [
                'class'  => '\BFW\Core\test\unit\mocks\Errors',
                'method' => 'mockRender'
            ])
            ->and($this->app->forceConfig($this->forcedConfig))
            ->then
            ->array($this->mock->callDefineErrorHandler())
                ->isEqualTo([
                    $this->mock,
                    'errorHandler'
                ]);
    }
    
    public function testDefineExceptionHandler()
    {
        $this->assert('test defineExceptionHandler without render')
            ->variable($this->mock->callDefineExceptionHandler())
                ->isNull();
        
        $this->assert('test defineExceptionHandler with a function render')
            ->if($this->forcedConfig['exceptionRenderFct']['active'] = true)
            ->and($this->app->forceConfig($this->forcedConfig))
            ->then
            ->array($this->mock->callDefineExceptionHandler())
                ->isEqualTo([
                    $this->mock,
                    'exceptionHandler'
                ]);
        
        $this->assert('test defineExceptionHandler with a class render')
            ->if($this->forcedConfig['exceptionRenderFct']['active'] = true)
            ->and($this->forcedConfig['exceptionRenderFct']['cli'] = [
                'class'  => '\BFW\Core\test\unit\mocks\Errors',
                'method' => 'mockRender'
            ])
            ->and($this->app->forceConfig($this->forcedConfig))
            ->then
            ->array($this->mock->callDefineExceptionHandler())
                ->isEqualTo([
                    $this->mock,
                    'exceptionHandler'
                ]);
    }
    
    public function testGetErrorRender()
    {
        $mock = $this->mock;
        
        $this->assert('test getErrorRender cli render disabled')
            ->boolean($mock::getErrorRender())
                ->isFalse();
        
        $this->assert('test getErrorRender cli render enabled')
            ->if($this->forcedConfig['errorRenderFct']['active'] = true)
            ->and($this->app->forceConfig($this->forcedConfig))
            ->then
            ->array($mock::getErrorRender())
                ->isEqualTo(
                    [
                        'class'  => '',
                        'method' => 'cli_error_render'
                    ]
                );
        
        $this->assert('test getErrorRender cli without cli render defined in config');
        //unset error if on if/and function
        unset($this->forcedConfig['errorRenderFct']['cli']);
        $this->if($this->app->forceConfig($this->forcedConfig))
            ->array($mock::getErrorRender())
                ->isEqualTo(
                    [
                        'class'  => '',
                        'method' => 'default_error_render'
                    ]
                );
        
        $this->assert('test getErrorRender cli without render defined in config');
        //unset error if on if/and function
        unset($this->forcedConfig['errorRenderFct']['default']);
        $this->if($this->app->forceConfig($this->forcedConfig))
            ->boolean($mock::getErrorRender())
                ->isFalse();
    }
    
    public function testGetExceptionRender()
    {
        $mock = $this->mock;
        
        $this->assert('test getExceptionRender cli render disabled')
            ->boolean($mock::getExceptionRender())
                ->isFalse();
        
        $this->assert('test getExceptionRender cli render enabled')
            ->if($this->forcedConfig['exceptionRenderFct']['active'] = true)
            ->and($this->app->forceConfig($this->forcedConfig))
            ->then
            ->array($mock::getExceptionRender())
                ->isEqualTo(
                    [
                        'class'  => '',
                        'method' => 'cli_exception_render'
                    ]
                );
        
        $this->assert('test getExceptionRender cli without cli render defined in config');
        //unset error if on if/and function
        unset($this->forcedConfig['exceptionRenderFct']['cli']);
        $this->if($this->app->forceConfig($this->forcedConfig))
            ->array($mock::getExceptionRender())
                ->isEqualTo(
                    [
                        'class'  => '',
                        'method' => 'default_exception_render'
                    ]
                );
        
        $this->assert('test getExceptionRender cli without render defined in config');
        //unset error if on if/and function
        unset($this->forcedConfig['exceptionRenderFct']['default']);
        $this->if($this->app->forceConfig($this->forcedConfig))
            ->boolean($mock::getExceptionRender())
                ->isFalse();
    }
    
    /**
     * Tested in testGetErrorRender and testGetExceptionRender
     */
    public function testDefineRenderToUse()
    {
        
    }
    
    public function testExceptionHandler()
    {
        $this->assert('test exceptionHandler')
            ->given($mock = $this->mock)
            ->given($exceptionLine = __LINE__+1)
            ->given($exception = new \Exception(
                'exception Message',
                100001
            ))
            ->if($this->forcedConfig['exceptionRenderFct']['active'] = true)
            ->and($this->forcedConfig['exceptionRenderFct']['cli'] = [
                'class'  => '\BFW\Core\test\unit\mocks\Errors',
                'method' => 'mockRender'
            ])
            ->and($this->app->forceConfig($this->forcedConfig))
            ->then
            ->given($mock::exceptionHandler($exception))
            ->object($rendered = $mock::$lastRenderCallInfos)
            ->string($rendered->erreurType)
                ->isEqualTo('Exception Uncaught')
            ->string($rendered->errMsg)
                ->isEqualTo('exception Message')
            ->string($rendered->errFile)
                ->isEqualTo(__FILE__)
            ->integer($rendered->errLine)
                ->isEqualTo($exceptionLine)
            ->array($rendered->backtrace)
                ->size
                    ->isGreaterThan(0)
        ;
    }
    
    public function testErrorHandler()
    {
        $this->assert('test errorHandler')
            ->given($mock = $this->mock)
            ->given($errorLine = __LINE__)
            ->then
            ->if($this->forcedConfig['errorRenderFct']['active'] = true)
            ->and($this->forcedConfig['errorRenderFct']['cli'] = [
                'class'  => '\BFW\Core\test\unit\mocks\Errors',
                'method' => 'mockRender'
            ])
            ->and($this->app->forceConfig($this->forcedConfig))
            ->then
            ->given($mock::errorHandler(
                E_WARNING,
                'error message',
                __FILE__,
                $errorLine
            ))
            ->object($rendered = $mock::$lastRenderCallInfos)
            ->string($rendered->erreurType)
                ->isEqualTo('Warning')
            ->string($rendered->errMsg)
                ->isEqualTo('error message')
            ->string($rendered->errFile)
                ->isEqualTo(__FILE__)
            ->integer($rendered->errLine)
                ->isEqualTo($errorLine)
            ->array($rendered->backtrace)
                ->size
                    ->isGreaterThan(0)
        ;
    }
    
    public function testCallRender()
    {
        //Test with object is already tested before.
        
        global $fctLastRenderCallInfos;
        
        $this->assert('test callRender for function')
            ->given($mock = $this->mock)
            ->given($errorLine = __LINE__)
            ->then
            ->if($this->forcedConfig['errorRenderFct']['active'] = true)
            ->and($this->forcedConfig['errorRenderFct']['cli'] = [
                'class'  => '',
                'method' => 'fctErrorRender'
            ])
            ->and($this->app->forceConfig($this->forcedConfig))
            ->then
            ->given($mock::errorHandler(
                E_WARNING,
                'error message',
                __FILE__,
                $errorLine
            ))
            ->object($rendered = $fctLastRenderCallInfos)
            ->string($rendered->erreurType)
                ->isEqualTo('Warning')
            ->string($rendered->errMsg)
                ->isEqualTo('error message')
            ->string($rendered->errFile)
                ->isEqualTo(__FILE__)
            ->integer($rendered->errLine)
                ->isEqualTo($errorLine)
            ->array($rendered->backtrace)
                ->size
                    ->isGreaterThan(0)
        ;
    }
    
    public function testGetErrorType()
    {
        $this->assert('test getErrorType for E_ERROR')
            ->string($this->mock->callGetErrorType(E_ERROR))
                ->isEqualTo('Fatal');
        
        $this->assert('test getErrorType for E_CORE_ERROR')
            ->string($this->mock->callGetErrorType(E_CORE_ERROR))
                ->isEqualTo('Fatal');
        
        $this->assert('test getErrorType for E_USER_ERROR')
            ->string($this->mock->callGetErrorType(E_USER_ERROR))
                ->isEqualTo('Fatal');
        
        $this->assert('test getErrorType for E_COMPILE_ERROR')
            ->string($this->mock->callGetErrorType(E_COMPILE_ERROR))
                ->isEqualTo('Fatal');
        
        $this->assert('test getErrorType for E_RECOVERABLE_ERROR')
            ->string($this->mock->callGetErrorType(E_RECOVERABLE_ERROR))
                ->isEqualTo('Fatal');
        
        $this->assert('test getErrorType for E_WARNING')
            ->string($this->mock->callGetErrorType(E_WARNING))
                ->isEqualTo('Warning');
        
        $this->assert('test getErrorType for E_CORE_WARNING')
            ->string($this->mock->callGetErrorType(E_CORE_WARNING))
                ->isEqualTo('Warning');
        
        $this->assert('test getErrorType for E_USER_WARNING')
            ->string($this->mock->callGetErrorType(E_USER_WARNING))
                ->isEqualTo('Warning');
        
        $this->assert('test getErrorType for E_COMPILE_WARNING')
            ->string($this->mock->callGetErrorType(E_COMPILE_WARNING))
                ->isEqualTo('Warning');
        
        $this->assert('test getErrorType for E_PARSE')
            ->string($this->mock->callGetErrorType(E_PARSE))
                ->isEqualTo('Parse');
        
        $this->assert('test getErrorType for E_NOTICE')
            ->string($this->mock->callGetErrorType(E_NOTICE))
                ->isEqualTo('Notice');
        
        $this->assert('test getErrorType for E_USER_NOTICE')
            ->string($this->mock->callGetErrorType(E_USER_NOTICE))
                ->isEqualTo('Notice');
        
        $this->assert('test getErrorType for E_STRICT')
            ->string($this->mock->callGetErrorType(E_STRICT))
                ->isEqualTo('Strict');
        
        $this->assert('test getErrorType for E_DEPRECATED')
            ->string($this->mock->callGetErrorType(E_DEPRECATED))
                ->isEqualTo('Deprecated');
        
        $this->assert('test getErrorType for E_USER_DEPRECATED')
            ->string($this->mock->callGetErrorType(E_USER_DEPRECATED))
                ->isEqualTo('Deprecated');
        
        $this->assert('test getErrorType for "test"')
            ->string($this->mock->callGetErrorType('test'))
                ->isEqualTo('Unknown');
    }
    
    /*
     * Will be tested with test install script
     * Not easy to test from here
     */
    public function testDefaultCliErrorRender()
    {
        
    }
    
    /*
     * Will be tested with test install script
     * Not easy to test from here
     */
    public function testDefaultErrorRender()
    {
        
    }
}
