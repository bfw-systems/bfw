<?php

namespace BFW\Core\test\unit;

use \atoum;

require_once(__DIR__.'/../../../../../vendor/autoload.php');

/**
 * @engine isolate
 */
class Cli extends atoum
{
    use \BFW\Test\Helpers\Application;
    
    protected $mock;
    
    public function beforeTestMethod($testMethod)
    {
        $this->setRootDir(__DIR__.'/../../../../..');
        $this->createApp();
        $this->initApp();
        
        $this->mockGenerator
            ->makeVisible('checkFile')
            ->makeVisible('execFile')
            ->generate('BFW\Core\Cli')
        ;
        
        $this->mock = new \mock\BFW\Core\Cli;
    }
    
    public function testObtainFileFromArg()
    {
        $this->assert('test Core\Cli::obtainFileFromArg without arg')
            ->if($this->function->getopt = [])
            ->then
            ->exception(function() {
                $this->mock->obtainFileFromArg();
            })
                ->hasCode(\BFW\Core\Cli::ERR_NO_FILE_SPECIFIED_IN_ARG)
        ;
        
        $this->assert('test Core\Cli::obtainFileFromArg without arg')
            ->if($this->function->getopt = [
                'f' => 'unitTestCli'
            ])
            ->then
            ->string($this->mock->obtainFileFromArg())
                ->isEqualTo(CLI_DIR.'unitTestCli.php')
        ;
    }
    
    public function testRunAndGetExecutedFile()
    {
        $this->assert('test Core\Cli::getExecutedFile for default value')
            ->string($this->mock->getExecutedFile())
                ->isEmpty()
        ;
        
        $this->assert('test Core\Cli::run - prepare')
            ->and($this->calling($this->mock)->execFile = true)
            ->then
        ;
        
        $this->assert('test Core\Cli::run with checkFile fail')
            ->if($this->calling($this->mock)->checkFile = false)
            ->variable($this->mock->run('unitTestCli.php'))
                ->isNull()
            ->mock($this->mock)
                ->call('execFile')
                    ->never()
            ->string($this->mock->getExecutedFile())
                ->isEqualTo('unitTestCli.php')
        ;
        
        $this->assert('test Core\Cli::run with checkFile success')
            ->if($this->calling($this->mock)->checkFile = true)
            ->variable($this->mock->run('unitTestCli2.php'))
                ->isNull()
            ->mock($this->mock)
                ->call('execFile')
                    ->once()
            ->string($this->mock->getExecutedFile())
                ->isEqualTo('unitTestCli2.php')
        ;
    }
    
    public function testCheckFile()
    {
        $this->assert('test Core\Cli::checkFile if file not exist')
            ->if($this->function->file_exists = false)
            ->then
            ->exception(function() {
                $this->mock->checkFile();
            })
                ->hasCode(\BFW\Core\Cli::ERR_FILE_NOT_FOUND)
        ;
        
        $this->assert('test Core\Cli::checkFile if file exist')
            ->if($this->function->file_exists = true)
            ->then
            ->boolean($this->mock->checkFile())
                ->isTrue()
        ;
    }
    
    public function testExecFile()
    {
        //Require not mockable, so we can't test with file to execute.
    }
}
