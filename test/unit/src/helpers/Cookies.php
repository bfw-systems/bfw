<?php

namespace BFW\Helpers\test\unit;

use \atoum;

require_once(__DIR__.'/../../../../vendor/autoload.php');

/**
 * @engine isolate
 */
class Cookies extends atoum
{
    public function testCreate()
    {
        $this->assert('test Helpers\Cookies::create with default expire time')
            ->given($time = time())
            ->given($setCookieArgs = [])
            ->if($this->function->setcookie = function(...$args) use (&$setCookieArgs) {
                $setCookieArgs = $args;
            })
            ->then
            
            ->variable(\BFW\Helpers\Cookies::create('unit_test', 'atoum'))
                ->isNull()
            ->string($setCookieArgs[0])
                ->isEqualTo('unit_test')
            ->string($setCookieArgs[1])
                ->isEqualTo('atoum')
            ->integer($setCookieArgs[2])
                ->isGreaterThanOrEqualTo($time + 1209600)
                ->isLessThanOrEqualTo($time + 1209600 + 10) //margin 10sec
        ;
        
        $this->assert('test Helpers\Cookies::create with an expire time')
            ->given($time = time())
            ->given($setCookieArgs = [])
            ->if($this->function->setcookie = function(...$args) use (&$setCookieArgs) {
                $setCookieArgs = $args;
            })
            ->then
            
            ->variable(\BFW\Helpers\Cookies::create('unit_test', 'atoum', 42))
                ->isNull()
            ->string($setCookieArgs[0])
                ->isEqualTo('unit_test')
            ->string($setCookieArgs[1])
                ->isEqualTo('atoum')
            ->integer($setCookieArgs[2])
                ->isGreaterThanOrEqualTo($time + 42)
                ->isLessThanOrEqualTo($time + 42 + 10) //margin 10sec
        ;
    }
}
