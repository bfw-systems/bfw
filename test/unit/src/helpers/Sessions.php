<?php

namespace BFW\Helpers\test\unit;

use \atoum;

require_once(__DIR__.'/../../../../vendor/autoload.php');

class Sessions extends atoum
{
    public function testIsStartedForCli()
    {
        $this->assert('test Sessions::isStarted for cli')
            ->if($this->constant->PHP_SAPI = 'cli')
            ->then
            ->boolean(\BFW\Helpers\Sessions::isStarted())
                ->isFalse();
    }
    
    public function testIsStartedForNotActive()
    {
        $this->assert('test Sessions::isStarted for session not active')
            ->if($this->constant->PHP_SAPI = 'www')
            ->and($this->function->session_status = PHP_SESSION_NONE)
            ->then
            ->boolean(\BFW\Helpers\Sessions::isStarted())
                ->isFalse();
    }
    
    public function testIsStartedForActive()
    {
        $this->assert('test Sessions::isStarted for session active')
            ->if($this->constant->PHP_SAPI = 'www')
            ->and($this->function->session_status = PHP_SESSION_ACTIVE)
            ->then
            ->boolean(\BFW\Helpers\Sessions::isStarted())
                ->isTrue();
    }
}
