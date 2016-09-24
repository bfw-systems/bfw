<?php

namespace BFW\Helpers\test\unit;

use \atoum;

require_once(__DIR__.'/../../../../vendor/autoload.php');

class Datas extends atoum
{
    public function testCheckType()
    {
        $this->assert('test Datas::checkType with bad parameter formats')
            ->boolean(\BFW\Helpers\Datas::checkType('test'))
                ->isFalse()
            ->boolean(\BFW\Helpers\Datas::checkType(['test']))
                ->isFalse();
        
        $this->assert('test Datas::checkType with bad infos')
            ->boolean(\BFW\Helpers\Datas::checkType([['type' => '', 'data' => 'test']]))
                ->isFalse()
            ->boolean(\BFW\Helpers\Datas::checkType([['type' => 'string']]))
                ->isFalse()
            ->boolean(\BFW\Helpers\Datas::checkType([['data' => 'string']]))
                ->isFalse()
            ->boolean(\BFW\Helpers\Datas::checkType([[]]))
                ->isFalse();
        
        $this->assert('test Datas::checkType with bad type')
            ->boolean(\BFW\Helpers\Datas::checkType([['type' => 10, 'data' => 'test']]))
                ->isFalse()
            ->boolean(\BFW\Helpers\Datas::checkType([['type' => 'string', 'data' => 10]]))
                ->isFalse();
        
        $this->assert('test Datas::checkType with empty array')
            ->boolean(\BFW\Helpers\Datas::checkType([]))
                ->isTrue();
        
        $this->assert('test Datas::checkType with good datas')
            ->boolean(\BFW\Helpers\Datas::checkType([['type' => 'integer', 'data' => 10]]))
                ->isTrue()
            ->boolean(\BFW\Helpers\Datas::checkType([['type' => 'int', 'data' => 10]]))
                ->isTrue()
            ->boolean(\BFW\Helpers\Datas::checkType([['type' => 'float', 'data' => 10.2]]))
                ->isTrue()
            ->boolean(\BFW\Helpers\Datas::checkType([['type' => 'double', 'data' => 10.2]]))
                ->isTrue()
            ->boolean(\BFW\Helpers\Datas::checkType([['type' => 'string', 'data' => 'test']]))
                ->isTrue();
    }
    
    public function testCheckMail()
    {
        $this->assert('test Datas::checkMail with bad mail format')
            ->boolean(\BFW\Helpers\Datas::checkMail('vmATbulton.fr'))
                ->isFalse();
        
        $this->assert('test Datas::checkMail with good mail format')
            ->boolean(\BFW\Helpers\Datas::checkMail('vm@bulton.fr'))
                ->isTrue();
    }
}
