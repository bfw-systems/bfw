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
        $this->assert('test Helpers\Cookies::create - prepare')
            ->given($headerArg = '')
            ->given($this->function->header = function($arg) use (&$headerArg) {
                $headerArg = $arg;
            })
        ;
        
        $this->assert('test Helpers\Cookies::create with default expire time')
            ->given($expireTime = new \DateTime)
            ->and($expireTime->modify('+1209600 second'))
            ->then
            ->variable(\BFW\Helpers\Cookies::create('unit_test', 'atoum'))
                ->isNull()
            //Cannot use ->function('header')->wasCalledWithArguments(...
            //Because the expire time have second
            ->given($cookieArgs = explode('; ', $headerArg))
            ->string($cookieArgs[0])
                ->isEqualTo('Set-Cookie: unit_test=atoum')
            ->string($cookieArgs[1])
                //Expires=Thu, 15 Nov 2018 06:46:16 Europe/Berlin
                ->matches('#Expires=[A-Z][a-z]{2}, [0-9]{2} [A-Z][a-z]{2} [0-9]{4} [0-9]{2}:[0-9]{2}:[0-9]{2} '.$expireTime->format('e').'#')
            ->string($cookieArgs[2])
                ->isEqualTo('Path=/')
            ->string($cookieArgs[3])
                ->isEqualTo('Domain=')
            ->string($cookieArgs[4])
                ->isEqualTo('HttpOnly')
            ->string($cookieArgs[5])
                ->isEqualTo('Secure')
            ->string($cookieArgs[6])
                ->isEqualTo('Samesite=lax')
            ->then
            ->given($expireMatch = [])
            ->and(preg_match(
                '#Expires=([A-Z][a-z]{2}, [0-9]{2} [A-Z][a-z]{2} [0-9]{4} [0-9]{2}:[0-9]{2}:[0-9]{2}) '.$expireTime->format('e').'#',
                $cookieArgs[1],
                $expireMatch
            ))
            ->and($cookieTime = \DateTime::createFromFormat(
                'D, d M Y H:i:s',
                $expireMatch[1]
            ))
            ->given($cookieTimestamp = (int) $cookieTime->format('U'))
            ->integer($cookieTimestamp)
                ->isGreaterThanOrEqualTo((int) $expireTime->format('U'))
                ->isLessThanOrEqualTo((int) $expireTime->format('U') + 10) //margin 10sec
        ;
        
        $this->assert('test Helpers\Cookies::create with default expire time')
            ->given($expireTime = new \DateTime)
            ->and($expireTime->modify('+42 second'))
            ->then
            ->variable(\BFW\Helpers\Cookies::create('unit_test', 'atoum', 42))
                ->isNull()
            //Cannot use ->function('header')->wasCalledWithArguments(...
            //Because the expire time have second
            ->given($cookieArgs = explode('; ', $headerArg))
            ->array($cookieArgs)
                ->size
                    ->isEqualTo(7)
            ->string($cookieArgs[0])
                ->isEqualTo('Set-Cookie: unit_test=atoum')
            ->string($cookieArgs[1])
                //Expires=Thu, 15 Nov 2018 06:46:16 Europe/Berlin
                ->matches('#Expires=[A-Z][a-z]{2}, [0-9]{2} [A-Z][a-z]{2} [0-9]{4} [0-9]{2}:[0-9]{2}:[0-9]{2} '.$expireTime->format('e').'#')
            ->then
            ->given($expireMatch = [])
            ->and(preg_match(
                '#Expires=([A-Z][a-z]{2}, [0-9]{2} [A-Z][a-z]{2} [0-9]{4} [0-9]{2}:[0-9]{2}:[0-9]{2}) '.$expireTime->format('e').'#',
                $cookieArgs[1],
                $expireMatch
            ))
            ->and($cookieTime = \DateTime::createFromFormat(
                'D, d M Y H:i:s',
                $expireMatch[1]
            ))
            ->given($cookieTimestamp = (int) $cookieTime->format('U'))
            ->integer($cookieTimestamp)
                ->isGreaterThanOrEqualTo((int) $expireTime->format('U'))
                ->isLessThanOrEqualTo((int) $expireTime->format('U') + 10) //margin 10sec
        ;
        
        $this->assert('test Helpers\Cookies::create without httpOnly, Secure and Samesite options')
            ->if(\BFW\Helpers\Cookies::$httpOnly = false)
            ->and(\BFW\Helpers\Cookies::$secure = false)
            ->and(\BFW\Helpers\Cookies::$sameSite = null)
            ->then
            ->variable(\BFW\Helpers\Cookies::create('unit_test', 'atoum'))
                ->isNull()
            //Cannot use ->function('header')->wasCalledWithArguments(...
            //Because the expire time have second
            ->given($cookieArgs = explode('; ', $headerArg))
            ->array($cookieArgs)
                ->size
                    ->isEqualTo(4)
            ->string($cookieArgs[0])
                ->isEqualTo('Set-Cookie: unit_test=atoum')
            ->string($cookieArgs[1])
                //Expires=Thu, 15 Nov 2018 06:46:16 Europe/Berlin
                ->matches('#Expires=[A-Z][a-z]{2}, [0-9]{2} [A-Z][a-z]{2} [0-9]{4} [0-9]{2}:[0-9]{2}:[0-9]{2} '.$expireTime->format('e').'#')
            ->string($cookieArgs[2])
                ->isEqualTo('Path=/')
            ->string($cookieArgs[3])
                ->isEqualTo('Domain=')
        ;
    }
}
