<?php

namespace BFW\Install\test\unit;

use \atoum;

require_once(__DIR__.'/../../../../../vendor/autoload.php');

/**
 * @engine isolate
 */
class Application extends atoum
{
    use \BFW\Install\Test\Helpers\Application;
    use \BFW\Test\Helpers\OutputBuffer;
    
    public function beforeTestMethod($testMethod)
    {
        $this->setRootDir(__DIR__.'/../../../../..');
        $this->createApp();
        
        if ($testMethod === 'testRun') {
            $this->initApp();
        }
    }
    
    /**
     * Test method for __constructor() and getInstance()
     * 
     * @return void
     */
    public function testConstructAndGetInstance()
    {
        $this->assert('test Install\Application::__construct')
            ->object($app = \BFW\Install\Test\Mock\Application::getInstance())
                ->isInstanceOf('\BFW\Install\Application')
            ->object(\BFW\Install\Test\Mock\Application::getInstance())
                ->isIdenticalTo($app)
        ;
    }
    
    public function testDefineCoreSystemList()
    {
        $this->assert('test Install\Application::defineCoreSystemList')
            ->array($list = $this->app->getCoreSystemList())
                ->size
                    ->isEqualTo(10)
            ->object($list['composerLoader'])
                ->isInstanceOf('\BFW\Core\AppSystems\ComposerLoader')
            ->object($list['config'])
                ->isInstanceOf('\BFW\Core\AppSystems\Config')
            ->object($list['constants'])
                ->isInstanceOf('\BFW\Core\AppSystems\Constants')
            ->object($list['ctrlRouterLink'])
                ->isInstanceOf('\BFW\Core\AppSystems\CtrlRouterLink')
            ->object($list['memcached'])
                ->isInstanceOf('\BFW\Core\AppSystems\Memcached')
            ->object($list['moduleInstall'])
                ->isInstanceOf('\BFW\Install\Core\AppSystems\ModuleInstall')
            ->object($list['moduleList'])
                ->isInstanceOf('\BFW\Install\Core\AppSystems\ModuleList')
            ->object($list['monolog'])
                ->isInstanceOf('\BFW\Core\AppSystems\Monolog')
            ->object($list['options'])
                ->isInstanceOf('\BFW\Core\AppSystems\Options')
            ->object($list['subjectList'])
                ->isInstanceOf('\BFW\Core\AppSystems\SubjectList')
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
                    ->withArguments('bfw_modules_install_done')
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
            ->string($records[4]['message'])
                ->isEqualTo('running framework install')
            ->string($records[8]['context']['action'])
                ->isEqualTo('bfw_modules_install_done')
        ;
        
        /*
         * [2018-08-02 23:36:34] bfw.DEBUG: Currently during the initialization framework step. [] []
         * [2018-08-02 23:36:34] bfw.DEBUG: RunTask notify {"prefix":"BfwApp","action":"bfw_ctrlRouterLink_subject_added"} []
         * [2018-08-02 23:36:34] bfw.DEBUG: Subject notify event {"action":"bfw_ctrlRouterLink_subject_added"} []
         * [2018-08-02 23:36:34] bfw.DEBUG: Framework initializing done. [] []
         * [2018-08-02 23:36:34] bfw.DEBUG: running framework install [] []
         * [2018-08-02 23:36:34] bfw.DEBUG: Subject notify event {"action":"ApplicationTasks_start_run_tasks"} []
         * [2018-08-02 23:36:34] bfw.DEBUG: Subject notify event {"action":"ApplicationTasks_end_run_tasks"} []
         * [2018-08-02 23:36:34] bfw.DEBUG: RunTask notify {"prefix":"ApplicationTasks","action":"bfw_modules_install_done"} []
         * [2018-08-02 23:36:34] bfw.DEBUG: Subject notify event {"action":"bfw_modules_install_done"} []
         */
    }
}