<?php

namespace BFW\Install\test\unit;

use \atoum;

require_once(__DIR__.'/../../../../vendor/autoload.php');

/**
 * @engine isolate
 */
class Application extends atoum
{
    use \BFW\Test\Helpers\Install\Application;
    use \BFW\Test\Helpers\OutputBuffer;
    
    public function beforeTestMethod($testMethod)
    {
        $this->setRootDir(__DIR__.'/../../../..');
        $this->createApp();
        $this->initApp();
    }
    
    /**
     * Test method for __constructor() and getInstance()
     * 
     * @return void
     */
    public function testConstructAndGetInstance()
    {
        $this->assert('test Install\Application::__construct')
            ->object($app = \BFW\Test\Mock\Install\Application::getInstance())
                ->isInstanceOf('\BFW\Install\Application')
            ->object(\BFW\Test\Mock\Install\Application::getInstance())
                ->isIdenticalTo($app)
        ;
    }
    
    public function testObtainAppSystemList()
    {
        $this->assert('test Install\Application::obtainAppSystemList')
            ->array($list = $this->app->obtainParentAppSystemList())
                ->size
                    ->isEqualTo(9)
            ->string($list['composerLoader'])
                ->isEqualTo('\BFW\Core\AppSystems\ComposerLoader')
            ->string($list['config'])
                ->isEqualTo('\BFW\Core\AppSystems\Config')
            ->string($list['constants'])
                ->isEqualTo('\BFW\Core\AppSystems\Constants')
            ->string($list['memcached'])
                ->isEqualTo('\BFW\Core\AppSystems\Memcached')
            ->string($list['moduleManager'])
                ->isEqualTo('\BFW\Install\Core\AppSystems\ModuleManager')
            ->string($list['moduleList'])
                ->isEqualTo('\BFW\Install\Core\AppSystems\ModuleList')
            ->string($list['monolog'])
                ->isEqualTo('\BFW\Core\AppSystems\Monolog')
            ->string($list['options'])
                ->isEqualTo('\BFW\Core\AppSystems\Options')
            ->string($list['subjectList'])
                ->isEqualTo('\BFW\Core\AppSystems\SubjectList')
        ;
    }
    
    public function testRun()
    {
        $this->assert('test Install\Application::run')
            ->given($runTasks = new \mock\BFW\RunTasks([], 'ApplicationTasks'))
            ->if($this->app->setRunTasks($runTasks))
            ->then
            
            ->variable($this->app->run())
                ->isNull()
            ->mock($runTasks)
                ->call('run')
                    ->once()
                ->call('sendNotify')
                    ->withArguments('bfw_install_done')
                        ->once()
            ->then
            
            ->given(
                $records = $this
                    ->app
                    ->getMonolog()
                    ->getLogger()
                    ->getHandlers()[0]
                    ->getRecords()
            )
            ->string($records[2]['message'])
                ->isEqualTo('running framework install')
            ->string($records[6]['context']['action'])
                ->isEqualTo('bfw_install_done')
        ;
        
        /*
         * [2018-08-25 21:18:54] bfw.DEBUG: Currently during the initialization framework step. [] []
         * [2018-08-25 21:18:54] bfw.DEBUG: Framework initializing done. [] []
         * [2018-08-25 21:18:54] bfw.DEBUG: running framework install [] []
         * [2018-08-25 21:18:54] bfw.DEBUG: Subject notify event {"action":"ApplicationTasks_start_run_tasks"} []
         * [2018-08-25 21:18:54] bfw.DEBUG: Subject notify event {"action":"ApplicationTasks_end_run_tasks"} []
         * [2018-08-25 21:18:54] bfw.DEBUG: RunTask notify {"prefix":"ApplicationTasks","action":"bfw_install_done"} []
         * [2018-08-25 21:18:54] bfw.DEBUG: Subject notify event {"action":"bfw_install_done"} []
         */
    }
}