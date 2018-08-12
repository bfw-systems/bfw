<?php

namespace BFW\Helpers\test\unit;

use \atoum;

require_once(__DIR__.'/../../../../vendor/autoload.php');

/**
 * @engine isolate
 */
class Datas extends atoum
{
    public function testCheckType()
    {
        $this->assert('test Helpers\Datas::checkType with bad infos')
            ->exception(function() {
                \BFW\Helpers\Datas::checkType([42]);
            })
                ->hasCode(\BFW\Helpers\Datas::ERR_CHECKTYPE_INFOS_FORMAT)
        ;
        
        $this->assert('test Helpers\Datas::checkType with missing data key')
            ->exception(function() {
                \BFW\Helpers\Datas::checkType([[
                    'type' => 'integer'
                ]]);
            })
                ->hasCode(\BFW\Helpers\Datas::ERR_CHECKTYPE_DATA_OR_TYPE_VALUE_FORMAT)
        ;
        
        $this->assert('test Helpers\Datas::checkType with missing type key')
            ->exception(function() {
                \BFW\Helpers\Datas::checkType([[
                    'data' => 42
                ]]);
            })
                ->hasCode(\BFW\Helpers\Datas::ERR_CHECKTYPE_DATA_OR_TYPE_VALUE_FORMAT)
        ;
        
        $this->assert('test Helpers\Datas::checkType with empty type key')
            ->exception(function() {
                \BFW\Helpers\Datas::checkType([[
                    'data' => 42,
                    'type' => ''
                ]]);
            })
                ->hasCode(\BFW\Helpers\Datas::ERR_CHECKTYPE_DATA_OR_TYPE_VALUE_FORMAT)
        ;
        
        $this->assert('test Helpers\Datas::checkType with expected type not equal to data type')
            ->boolean(\BFW\Helpers\Datas::checkType([[
                'data' => 42,
                'type' => 'string'
            ]]))
                ->isFalse()
        ;
        
        $this->assert('test Helpers\Datas::checkType with correct type')
            ->boolean(\BFW\Helpers\Datas::checkType([
                [
                    'data' => 42,
                    'type' => 'int'
                ], [
                    'data' => 42,
                    'type' => 'integer'
                ], [
                    'data' => 3.14,
                    'type' => 'float'
                ], [
                    'data' => 3.14,
                    'type' => 'double'
                ], [
                    'data' => 'atoum',
                    'type' => 'string'
                ]
            ]))
                ->isTrue()
        ;
    }
    
    public function testCheckMail()
    {
        $this->assert('test Helpers\Datas::checkMail with bad mail')
            ->boolean(\BFW\Helpers\Datas::checkMail('test@unit'))
                ->isFalse()
        ;
        
        $this->assert('test Helpers\Datas::checkMail with correct mail')
            ->boolean(\BFW\Helpers\Datas::checkMail('test@unit.com'))
                ->isTrue()
        ;
    }
}
