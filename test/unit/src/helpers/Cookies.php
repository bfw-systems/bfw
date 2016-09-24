<?php

namespace BFW\Helpers\test\unit;

use \atoum;
use \DateTime;

require_once(__DIR__.'/../../../../vendor/autoload.php');

class Cookies extends atoum
{
    public function testCreate()
    {
        $nowDateTime = new DateTime;
        $cookieInfos = null;
        
        $this->function->time = $nowDateTime->format('U');
        $this->function->setcookie = function($name, $value, $expire) use (&$cookieInfos) {
            $cookieInfos = (object) [
                'name'   => $name,
                'value'  => $value,
                'expire' => $expire
            ];
        };
        
        $this->assert('test Cookies::create without expire')
            ->if(\BFw\Helpers\Cookies::create('test', 'test value'))
            ->then
            ->string($cookieInfos->name)
                ->isEqualTo('test')
            ->string($cookieInfos->value)
                ->isEqualTo('test value')
            ->integer($cookieInfos->expire)
                ->isEqualTo($nowDateTime->format('U') + 1209600);
        
        $this->assert('test Cookies::create with expire')
            ->if(\BFw\Helpers\Cookies::create('test2', 'test2 value', 230))
            ->then
            ->string($cookieInfos->name)
                ->isEqualTo('test2')
            ->string($cookieInfos->value)
                ->isEqualTo('test2 value')
            ->integer($cookieInfos->expire)
                ->isEqualTo($nowDateTime->format('U') + 230);
    }
}
