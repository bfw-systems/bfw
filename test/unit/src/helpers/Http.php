<?php

namespace BFW\Helpers\test\unit;

use \atoum;

require_once(__DIR__.'/../../../../vendor/autoload.php');

/**
 * @engine isolate
 */
class Http extends atoum
{
    public function testRedirect()
    {
        $this->assert('test Helpers\Http::redirect with not permanent redirect')
            ->if($this->function->http_response_code = null)
            ->and($this->function->header = null)
            ->then
            
            ->variable(\BFW\Helpers\Http::redirect('/atoum.php'))
                ->isNull()
            ->function('http_response_code')
                ->wasCalledWithArguments(302)
                    ->atLeastOnce()
            ->function('header')
                ->wasCalledWithArguments('Location: /atoum.php')
                    ->atLeastOnce()
        ;
        
        $this->assert('test Helpers\Http::redirect with a permanent redirect')
            ->if($this->function->http_response_code = null)
            ->and($this->function->header = null)
            ->then
            
            ->variable(\BFW\Helpers\Http::redirect('/atoum.php', true))
                ->isNull()
            ->function('http_response_code')
                ->wasCalledWithArguments(301)
                    ->atLeastOnce()
            ->function('header')
                ->wasCalledWithArguments('Location: /atoum.php')
                    ->atLeastOnce()
        ;
        
        //Can not test the call to exit keyword because he exit atoum too
    }
}
