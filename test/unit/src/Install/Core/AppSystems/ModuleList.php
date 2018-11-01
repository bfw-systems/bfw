<?php

namespace BFW\Install\Core\AppSystems\test\unit;

use \atoum;

require_once(__DIR__.'/../../../../../../vendor/autoload.php');

/**
 * @engine isolate
 */
class ModuleList extends atoum
{
    use \BFW\Test\Helpers\Application;
    
    protected $mock;
    
    public function beforeTestMethod($testMethod)
    {
        $this->mockGenerator
            ->makeVisible('loadAllModules')
            ->makeVisible('runAllCoreModules')
            ->makeVisible('runAllAppModules')
            ->makeVisible('runModule')
        ;
        
        $this->mock = new \mock\BFW\Install\Core\AppSystems\ModuleList;
    }
    
    public function testRunAndIsRun()
    {
        $this->assert('test Install\Core\AppSystems\ModuleList::isRun before run')
            ->boolean($this->mock->isRun())
                ->isFalse()
        ;
        
        $this->assert('test Install\Core\AppSystems\ModuleList::run and isRun after')
            ->and($this->calling($this->mock)->loadAllModules = null)
            ->and($this->calling($this->mock)->runAllCoreModules = null)
            ->and($this->calling($this->mock)->runAllAppModules = null)
            ->variable($this->mock->run())
                ->isNull()
            ->boolean($this->mock->isRun())
                ->isTrue()
            ->mock($this->mock)
                ->call('loadAllModules')
                    ->once()
                ->call('runAllCoreModules')
                    ->never()
                ->call('runAllAppModules')
                    ->never()
        ;
    }
}