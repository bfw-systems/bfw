<?php

namespace BFW\Core\test\unit;

use \atoum;

require_once(__DIR__.'/../../../../../vendor/autoload.php');
require_once(__DIR__.'/../../../helpers/ErrorsRenderFunction.php');

/**
 * @engine isolate
 */
class Errors extends atoum
{
    use \BFW\Test\Helpers\Application;
    
    protected $mock;
    
    public function beforeTestMethod($testMethod)
    {
        $this->setRootDir(__DIR__.'/../../../../..');
        $this->createApp();
        $this->initApp();
        
        $this->mockGenerator
            ->makeVisible('defineErrorHandler')
            ->makeVisible('defineExceptionHandler')
            ->makeVisible('obtainErrorRender')
            ->makeVisible('obtainExceptionRender')
            ->makeVisible('defineRenderToUse')
            ->makeVisible('callRender')
            ->makeVisible('saveIntoPhpLog')
            ->makeVisible('obtainErrorType')
        ;
        
        if ($testMethod === 'testConstruct') {
            $this->mockGenerator->generate('BFW\Core\Errors');
            return;
        }
        
        $this->mockGenerator
            ->orphanize('__construct')
            ->generate('BFW\Core\Errors')
        ;
        
        $this->mock = new \mock\BFW\Core\Errors;
    }
    
    public function testConstruct()
    {
        $this->assert('test Core\Errors::__construct')
            ->given($defineErrorHandlerCalled = false)
            ->given($defineExceptionHandlerCalled = false)
            ->given($controller = new \atoum\mock\controller)
            ->and($controller->defineErrorHandler = function() use (&$defineErrorHandlerCalled) {
                $defineErrorHandlerCalled = true;
            })
            ->and($controller->defineExceptionHandler = function() use (&$defineExceptionHandlerCalled) {
                $defineExceptionHandlerCalled = true;
            })
            ->then
            ->object($mock = new \mock\BFW\Core\Errors($controller))
                ->isInstanceOf('\BFW\Core\Errors')
            ->boolean($defineErrorHandlerCalled)
                ->isTrue()
            ->boolean($defineExceptionHandlerCalled)
                ->isTrue()
        ;
    }
    
    public function testDefineErrorHandler()
    {
        $this->assert('test Core\Errors::defineErrorHandler without render')
            ->if($this->calling($this->mock)->obtainErrorRender = false)
            ->then
            ->variable($this->mock->defineErrorHandler())
                ->isNull()
            ->array($errorHandler = set_error_handler(function($errno, $errstr) {}))
            ->object($errorHandler[0])
                ->isNotInstanceOf('\BFW\Core\Errors') // Atoum system, not mine
            ->given(restore_error_handler()) // Cancel set_error_handler
        ;
        
        $this->assert('test Core\Errors::defineErrorHandler with render')
            //Just need obtainErrorRender not return false value.
            ->if($this->calling($this->mock)->obtainErrorRender = [$this->mock, 'errorHandler'])
            ->then
            ->variable($this->mock->defineErrorHandler())
                ->isNull()
            ->variable($errorHandler = set_error_handler(function($errno, $errstr) {}))
            ->object($errorHandler[0])
                ->isInstanceOf('\BFW\Core\Errors')
            ->string($errorHandler[1])
                ->isEqualTo('errorHandler')
            ->given(restore_error_handler()) // Cancel set_error_handler
        ;
    }
    
    public function testDefineExceptionHandler()
    {
        $this->assert('test Core\Errors::defineExceptionHandler without render')
            ->if($this->calling($this->mock)->obtainExceptionRender = false)
            ->then
            ->variable($this->mock->defineExceptionHandler())
                ->isNull()
            ->array($exceptionHandler = set_error_handler(function($ex) {}))
            ->object($exceptionHandler[0])
                ->isNotInstanceOf('\BFW\Core\Errors') // Atoum system, not mine
            ->given(restore_exception_handler()) // Cancel set_error_handler
        ;
        
        $this->assert('test Core\Errors::defineExceptionHandler with render')
            ->if($this->calling($this->mock)->obtainExceptionRender = [$this->mock, 'exceptionHandler'])
            ->then
            ->variable($this->mock->defineExceptionHandler())
                ->isNull()
            ->variable($exceptionHandler = set_exception_handler(function($ex) {}))
            ->object($exceptionHandler[0])
                ->isInstanceOf('\BFW\Core\Errors')
            ->string($exceptionHandler[1])
                ->isEqualTo('exceptionHandler')
            ->given(restore_exception_handler()) // Cancel set_error_handler
        ;
    }
    
    public function testObtainErrorRender()
    {
        $this->assert('test Core\Errors::obtainErrorRender')
            ->given($renderInfos = null)
            ->if($this->calling($this->mock)->defineRenderToUse = function($renderConfig) use (&$renderInfos) {
                $renderInfos = $renderConfig;
                return true;
            })
            ->then
            ->boolean($this->mock->obtainErrorRender())
                ->isTrue()
            ->array($renderInfos)
                ->isNotEmpty()
        ;
    }
    
    public function testObtainExceptionRender()
    {
        $this->assert('test Core\Errors::obtainExceptionRender')
            ->given($renderInfos = null)
            ->if($this->calling($this->mock)->defineRenderToUse = function($renderConfig) use (&$renderInfos) {
                $renderInfos = $renderConfig;
                return true;
            })
            ->then
            ->boolean($this->mock->obtainExceptionRender())
                ->isTrue()
            ->array($renderInfos)
                ->isNotEmpty()
        ;
    }
    
    public function testDefineRenderToUse()
    {
        $this->assert('test Core\Errors::defineRenderToUse - prepare')
            ->if($renderFcts = $this->app->getConfig()->getValue('errorRenderFct', 'errors.php'))
        ;
        
        $this->assert('test Core\Errors::defineRenderToUse if disabled')
            ->if($renderFcts['enabled'] = false)
            ->then
            ->boolean($this->mock->defineRenderToUse($renderFcts))
                ->isFalse()
        ;
        
        $this->assert('test Core\Errors::defineRenderToUse without config value')
            ->boolean($this->mock->defineRenderToUse(['enabled' => true]))
                ->isFalse()
        ;
    }
    
    public function testDefineRenderToUseForCli()
    {
        $this->assert('test Core\Errors::defineRenderToUse - in cli - prepare')
            ->if($renderFcts = $this->app->getConfig()->getValue('errorRenderFct', 'errors.php'))
        ;
        
        $this->assert('test Core\Errors::defineRenderToUse if render enabled and in cli')
            ->if($renderFcts['enabled'] = true)
            ->and($this->constant->PHP_SAPI = 'cli')
            ->then
            ->array($render = $this->mock->defineRenderToUse($renderFcts))
            ->boolean(array_key_exists('class', $render))
                ->isTrue()
            ->boolean(array_key_exists('method', $render))
                ->isTrue()
            ->string($render['class'])
                ->isEqualTo('\BFW\Core\ErrorsDisplay')
            ->string($render['method'])
                ->isEqualTo('defaultCliErrorRender')
        ;
    }
    
    public function testDefineRenderToUseNotForCli()
    {
        $this->assert('test Core\Errors::defineRenderToUse - not in cli - prepare')
            ->if($renderFcts = $this->app->getConfig()->getValue('errorRenderFct', 'errors.php'))
        ;
        
        $this->assert('test Core\Errors::defineRenderToUse if render enabled but not in cli')
            ->if($renderFcts['enabled'] = true)
            ->and($this->constant->PHP_SAPI = 'cgi-fcgi')
            ->then
            ->array($render = $this->mock->defineRenderToUse($renderFcts))
            ->boolean(array_key_exists('class', $render))
                ->isTrue()
            ->boolean(array_key_exists('method', $render))
                ->isTrue()
            ->string($render['class'])
                ->isEqualTo('\BFW\Core\ErrorsDisplay')
            ->string($render['method'])
                ->isEqualTo('defaultErrorRender')
        ;
    }
    
    public function testExceptionHandler()
    {
        $this->assert('test Core\Errors::exceptionHandler')
            ->given($callRenderArgs = [])
            ->given($exception = new \Exception('excep. from unit test', 1304001))
            ->if($this->calling($this->mock)->obtainExceptionRender = function() {
                return [$this, 'fakeRender'];
            })
            ->and($this->calling($this->mock)->callRender = function(...$args) use (&$callRenderArgs) {
                $callRenderArgs = $args;
            })
            ->then
            ->variable($this->mock->exceptionHandler($exception))
                ->isNull()
            ->array($callRenderArgs)
                ->isNotEmpty()
                ->size->isEqualTo(7)
            ->array($callRenderArgs[0]) //Render to use
                ->isEqualTo([$this->mock, 'fakeRender'])
            ->string($callRenderArgs[1]) //Error type
                ->isEqualTo('Exception Uncaught')
            ->string($callRenderArgs[2]) //Exception message
                ->isEqualTo('excep. from unit test')
            ->string($callRenderArgs[3]) //File
                ->isNotEmpty()
            ->integer($callRenderArgs[4]) //Line
                ->isGreaterThan(0)
            ->array($callRenderArgs[5]) //Trace
                ->isNotEmpty()
            ->integer($callRenderArgs[6]) //Exception code
                ->isEqualTo(1304001)
        ;
    }
    
    public function testErrorHandler()
    {
        $this->assert('test Core\Errors::errorHandler')
            ->given($callRenderArgs = [])
            ->if($this->calling($this->mock)->obtainErrorType = function() {
                return 'Notice';
            })
            ->and($this->calling($this->mock)->obtainErrorRender = function() {
                return [$this, 'fakeRender'];
            })
            ->and($this->calling($this->mock)->callRender = function(...$args) use (&$callRenderArgs) {
                $callRenderArgs = $args;
            })
            ->then
            ->variable($this->mock->errorHandler(
                E_NOTICE,
                'error from unit test',
                __FILE__,
                __LINE__
            ))
                ->isNull()
            ->array($callRenderArgs)
                ->isNotEmpty()
                ->size->isEqualTo(7)
            ->array($callRenderArgs[0]) //Render to use
                ->isEqualTo([$this->mock, 'fakeRender'])
            ->string($callRenderArgs[1]) //Error type
                ->isEqualTo('Notice')
            ->string($callRenderArgs[2]) //Error message
                ->isEqualTo('error from unit test')
            ->string($callRenderArgs[3]) //File
                ->isEqualTo(__FILE__)
            ->integer($callRenderArgs[4]) //Line
                ->isGreaterThan(0)
            ->array($callRenderArgs[5]) //Trace
                ->isNotEmpty()
            ->variable($callRenderArgs[6]) //Exception code
                ->isNull()
        ;
    }
    
    public function testCallRenderWithClass()
    {
        $this->assert('test \Core\Errors::callRender with a class')
            ->if($renderConfig = $this->app->getConfig()->getValue('errorRenderFct', 'errors.php'))
            ->and($renderInfos = $renderConfig['default'])
            ->and($renderInfos['class'] = '\BFW\Test\Helpers\ErrorsRenderClass')
            ->and($renderInfos['method'] = 'render')
            ->then
            
            ->given($saveIntoPhpLogArgs = [])
            ->and($this->calling($this->mock)->saveIntoPhpLog = function(...$args) use (&$saveIntoPhpLogArgs) {
                $saveIntoPhpLogArgs = $args;
            })
            ->then
            
            ->given($errType = 'Notice')
            ->and($errMsg = 'error from unit test')
            ->and($errFile = __FILE__)
            ->and($errLine = __LINE__)
            ->and($backtrace = debug_backtrace())
            ->then
            
            ->variable($this->mock->callRender(
                $renderInfos,
                $errType,
                $errMsg,
                $errFile,
                $errLine,
                $backtrace
            ))
                ->isNull()
            ->array($saveIntoPhpLogArgs)
                ->isEqualTo([$errType, $errMsg, $errFile, $errLine])
            ->then
            
            ->given($errorRender = \BFW\Test\Helpers\ErrorsRenderClass::getInstance())
            ->string($errorRender->errType)
                ->isEqualTo($errType)
            ->string($errorRender->errMsg)
                ->isEqualTo($errMsg)
            ->string($errorRender->errFile)
                ->isEqualTo($errFile)
            ->integer($errorRender->errLine)
                ->isEqualTo($errLine)
            ->array($errorRender->backtrace)
                ->isEqualTo($backtrace)
        ;
    }
    
    public function testCallRenderWithFunction()
    {
        $this->assert('test \Core\Errors::callRender with a function')
            ->if($renderConfig = $this->app->getConfig()->getValue('errorRenderFct', 'errors.php'))
            ->and($renderInfos = $renderConfig['default'])
            ->and($renderInfos['class'] = '')
            ->and($renderInfos['method'] = '\BFW\Test\Helpers\errorsRenderFunction')
            ->then
            
            ->given($saveIntoPhpLogArgs = [])
            ->and($this->calling($this->mock)->saveIntoPhpLog = function(...$args) use (&$saveIntoPhpLogArgs) {
                $saveIntoPhpLogArgs = $args;
            })
            ->then
            
            ->given($errType = 'Notice')
            ->and($errMsg = 'error from unit test')
            ->and($errFile = __FILE__)
            ->and($errLine = __LINE__)
            ->and($backtrace = debug_backtrace())
            ->then
            
            ->variable($this->mock->callRender(
                $renderInfos,
                $errType,
                $errMsg,
                $errFile,
                $errLine,
                $backtrace
            ))
                ->isNull()
            ->array($saveIntoPhpLogArgs)
                ->isEqualTo([$errType, $errMsg, $errFile, $errLine])
            ->then
            
            ->given($errorRender = \BFW\Test\Helpers\ErrorsRenderClass::getInstance())
            ->string($errorRender->errType)
                ->isEqualTo($errType)
            ->string($errorRender->errMsg)
                ->isEqualTo($errMsg)
            ->string($errorRender->errFile)
                ->isEqualTo($errFile)
            ->integer($errorRender->errLine)
                ->isEqualTo($errLine)
            ->array($errorRender->backtrace)
                ->isEqualTo($backtrace)
        ;
    }
    
    public function testSaveIntoPhpLog()
    {
        $this->assert('test \Core\Errors::saveIntoPhpLog')
            ->given($errorLogMsg = '')
            ->if($this->function->error_log = function($message) use (&$errorLogMsg) {
                $errorLogMsg = $message;
            })
            ->then
            
            ->given($errType = 'Notice')
            ->and($errMsg = 'error from unit test')
            ->and($errFile = __FILE__)
            ->and($errLine = __LINE__)
            ->then
            
            ->variable($this->mock->saveIntoPhpLog(
                $errType,
                $errMsg,
                $errFile,
                $errLine
            ))
                ->isNull()
            ->string($message = 'Error detected : '.$errType.' '.$errMsg.' at '.$errFile.':'.$errLine)
        ;
    }
    
    public function testObtainErrorType()
    {
        $this->assert('test \Core\Errors::obtainErrorType with E_ERROR')
            ->string($this->mock->obtainErrorType(E_ERROR))
                ->isEqualTo('Fatal')
        ;
        
        $this->assert('test \Core\Errors::obtainErrorType with E_CORE_ERROR')
            ->string($this->mock->obtainErrorType(E_CORE_ERROR))
                ->isEqualTo('Fatal')
        ;
        
        $this->assert('test \Core\Errors::obtainErrorType with E_USER_ERROR')
            ->string($this->mock->obtainErrorType(E_USER_ERROR))
                ->isEqualTo('Fatal')
        ;
        
        $this->assert('test \Core\Errors::obtainErrorType with E_COMPILE_ERROR')
            ->string($this->mock->obtainErrorType(E_COMPILE_ERROR))
                ->isEqualTo('Fatal')
        ;
        
        $this->assert('test \Core\Errors::obtainErrorType with E_RECOVERABLE_ERROR')
            ->string($this->mock->obtainErrorType(E_RECOVERABLE_ERROR))
                ->isEqualTo('Fatal')
        ;
        
        $this->assert('test \Core\Errors::obtainErrorType with E_WARNING')
            ->string($this->mock->obtainErrorType(E_WARNING))
                ->isEqualTo('Warning')
        ;
        
        $this->assert('test \Core\Errors::obtainErrorType with E_CORE_WARNING')
            ->string($this->mock->obtainErrorType(E_CORE_WARNING))
                ->isEqualTo('Warning')
        ;
        
        $this->assert('test \Core\Errors::obtainErrorType with E_USER_WARNING')
            ->string($this->mock->obtainErrorType(E_USER_WARNING))
                ->isEqualTo('Warning')
        ;
        
        $this->assert('test \Core\Errors::obtainErrorType with E_COMPILE_WARNING')
            ->string($this->mock->obtainErrorType(E_COMPILE_WARNING))
                ->isEqualTo('Warning')
        ;
        
        $this->assert('test \Core\Errors::obtainErrorType with E_PARSE')
            ->string($this->mock->obtainErrorType(E_PARSE))
                ->isEqualTo('Parse')
        ;
        
        $this->assert('test \Core\Errors::obtainErrorType with E_NOTICE')
            ->string($this->mock->obtainErrorType(E_NOTICE))
                ->isEqualTo('Notice')
        ;
        
        $this->assert('test \Core\Errors::obtainErrorType with E_USER_NOTICE')
            ->string($this->mock->obtainErrorType(E_USER_NOTICE))
                ->isEqualTo('Notice')
        ;
        
        $this->assert('test \Core\Errors::obtainErrorType with E_STRICT')
            ->string($this->mock->obtainErrorType(E_STRICT))
                ->isEqualTo('Strict')
        ;
        
        $this->assert('test \Core\Errors::obtainErrorType with E_DEPRECATED')
            ->string($this->mock->obtainErrorType(E_DEPRECATED))
                ->isEqualTo('Deprecated')
        ;
        
        $this->assert('test \Core\Errors::obtainErrorType with E_USER_DEPRECATED')
            ->string($this->mock->obtainErrorType(E_USER_DEPRECATED))
                ->isEqualTo('Deprecated')
        ;
        
        $this->assert('test \Core\Errors::obtainErrorType with Unknown type')
            ->string($this->mock->obtainErrorType(42))
                ->isEqualTo('Unknown')
        ;
    }
}
