<?php

namespace BFW\Helpers\test\unit;

use \atoum;
use \BFW\Helpers\Secure as BfwSecure;
use \BFW\Helpers\test\unit\mocks\Secure as MockSecure;

require_once(__DIR__.'/../../../../vendor/autoload.php');

class Secure extends atoum
{
    protected function initApp($sqlSecureMethod)
    {
        $forcedConfig = require(__DIR__.'/../../helpers/applicationConfig.php');
        $forcedConfig['sqlSecureMethod'] = $sqlSecureMethod;
        
        $options = [
            'forceConfig' => $forcedConfig,
            'vendorDir'   => __DIR__.'/../../../../vendor'
        ];
        
        $this->function->scandir = ['.', '..'];
        \BFW\test\unit\mocks\Application::init($options);
    }
    
    public static function secureMethod($str)
    {
        return 'testSecurised_'.$str;
    }
    
    public function testHash()
    {
        $this->assert('test Secure::hash')
            ->string($hashed = BfwSecure::hash('test'))
                ->length
                    ->isEqualTo(32);
    }
    
    public function testSecuriseKnownTypes()
    {
        $this->assert('test Secure::securiseKnownTypes for int and integer types')
            ->integer(BfwSecure::securiseKnownTypes(10, 'int'))
                ->isEqualTo(10)
            ->integer(BfwSecure::securiseKnownTypes(10, 'integer'))
                ->isEqualTo(10)
            ->integer(BfwSecure::securiseKnownTypes('10', 'integer'))
                ->isEqualTo(10)
            ->boolean(BfwSecure::securiseKnownTypes('test', 'int'))
                ->isFalse();
        
        $this->assert('test Secure::securiseKnownTypes for float and double types')
            ->float(BfwSecure::securiseKnownTypes(10.2, 'float'))
                ->isEqualTo(10.2)
            ->float(BfwSecure::securiseKnownTypes(10.2, 'double'))
                ->isEqualTo(10.2)
            ->float(BfwSecure::securiseKnownTypes('10', 'float'))
                ->isEqualTo(10.0)
            ->boolean(BfwSecure::securiseKnownTypes('test', 'double'))
                ->isFalse();
        
        $this->assert('test Secure::securiseKnownTypes for bool and boolean types')
            ->boolean(BfwSecure::securiseKnownTypes(true, 'bool'))
                ->isTrue()
            ->boolean(BfwSecure::securiseKnownTypes(false, 'boolean'))
                ->isFalse()
            ->boolean(BfwSecure::securiseKnownTypes('true', 'bool'))
                ->isTrue()
            ->boolean(BfwSecure::securiseKnownTypes('test', 'boolean'))
                ->isFalse();
        
        $this->assert('test Secure::securiseKnownTypes for email type')
            ->boolean(BfwSecure::securiseKnownTypes('vmATbulton.fr', 'email'))
                ->isFalse()
            ->string(BfwSecure::securiseKnownTypes('vm@bulton.fr', 'email'))
                ->isEqualTo('vm@bulton.fr');
        
        $this->assert('test Secure::securiseKnownTypes exception with other type')
            ->exception(function() {
                BfwSecure::securiseKnownTypes('test securise', 'text');
            })
                ->hasMessage('Unknown type')
            ->exception(function() {
                BfwSecure::securiseKnownTypes('test securise', 'mixed');
            })
                ->hasMessage('Unknown type');
    }
    
    public function testSecurise()
    {
        $this->initApp('');
        
        $this->assert('test Secure::securise for direct data')
            ->integer(MockSecure::securise('10', 'int', false))
                ->isEqualTo(10)
            ->string(MockSecure::securise('test securise', 'text', false))
                ->isEqualTo('test securise');
        
        $this->assert('test Secure::securise for array data')
            ->array(MockSecure::securise([
                    'a' => 'test',
                    1 => 'test2'
                ],
                'text',
                false
            ))
                ->isEqualTo([
                    'a' => 'test',
                    1 => 'test2'
                ]);
        
        $this->assert('test Secure::securise with addslashes text')
            ->string(MockSecure::securise('it\'s a test !', 'text', false))
                ->isEqualTo('it\\\'s a test !');
        
        $this->assert('test Secure::securise with html text')
            ->string(MockSecure::securise('<p>Test</p>', 'text', true))
                ->isEqualTo('&lt;p&gt;Test&lt;/p&gt;');
    }
    
    public function testSecuriseWithSecureMethod()
    {
        $this->assert('test Secure::securise with a secure method')
            ->if($this->initApp(['\BFW\Helpers\test\unit\Secure', 'secureMethod']))
            ->then
            ->string(MockSecure::securise('test', 'text', false))
                ->isEqualTo('testSecurised_test');
    }
    
    public function testGetSqlSecureMethod()
    {
        $this->initApp(['\BFW\Helpers\test\unit\Secure', 'secureMethod']);
        
        $this->assert('test Secure::getSqlSecureMethod')
            ->array(MockSecure::getSqlSecureMethod())
                ->isEqualTo([
                    '\BFW\Helpers\test\unit\Secure',
                    'secureMethod'
                ]);
    }
    
    public function testGetSecurisedKeyInArray()
    {
        $this->initApp('');
        
        $this->assert('test Secure::getSecurisedKeyInArray')
            ->given($testedArray = [
                'a' => 'test',
                1 => 'test2'
            ])
            ->string(MockSecure::getSecurisedKeyInArray($testedArray, 'a', 'text'))
                ->isEqualTo('test');
        
        $this->assert('test Secure::getSecurisedKeyInArray exception')
            ->exception(function() use ($testedArray) {
                MockSecure::getSecurisedKeyInArray($testedArray, 'b', 'text');
            })
                ->hasMessage('The key b not exist');
    }
    
    public function testgetSecurisedPostKey()
    {
        $_POST = [
            'login' => 'test login',
            'password' => 'test pwd'
        ];
        
        $this->initApp('');
        
        $this->assert('test Secure::getSecurisedPostKey')
            ->string(MockSecure::getSecurisedPostKey('login', 'text'))
                ->isEqualTo('test login');
    }
    
    public function testgetSecurisedGetKey()
    {
        $_GET = [
            'id' => 12350,
            'page' => 2
        ];
        
        $this->initApp('');
        
        $this->assert('test Secure::getSecurisedGetKey')
            ->integer(MockSecure::getSecurisedGetKey('id', 'int'))
                ->isEqualTo(12350);
    }
}
