<?php

namespace BFW\Helpers\test\unit;

use \atoum;

require_once(__DIR__.'/../../../../vendor/autoload.php');

/**
 * @engine isolate
 */
class Sessions extends atoum
{
    public function testIsStartedWithCli()
    {
        $this->assert('test Helpers\Sessions::isStarted with cli')
            ->if($this->constant->PHP_SAPI = 'cli')
            ->then
            ->boolean(\BFW\Helpers\Sessions::isStarted())
                ->isFalse()
        ;
    }
    
    public function testIsStarted()
    {
        $this->given($this->constant->PHP_SAPI = 'www');
        
        $this->assert('test Helpers\Sessions::isStarted if it\'s started')
            ->if($this->function->session_status = function() {
                return PHP_SESSION_ACTIVE;
            })
            ->then
            ->boolean(\BFW\Helpers\Sessions::isStarted())
                ->isTrue()
        ;
        
        $this->assert('test Helpers\Sessions::isStarted if it\'s not started')
            ->if($this->function->session_status = null)
            ->then
            ->boolean(\BFW\Helpers\Sessions::isStarted())
                ->isFalse()
        ;
    }
}
