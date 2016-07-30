<?php

namespace BFW\test\unit;
use \atoum;

require_once(__DIR__.'/../../../../vendor/autoload.php');

class Form extends atoum
{
    /**
     * @var $class : Instance de la class
     */
    protected $class;

    /**
     * Instanciation de la class avant chaque méthode de test
     */
    public function beforeTestMethod($testMethod)
    {
        $this->class = new \BFW\Form('form_unit_test');
    }
    
    public function testCreateToken()
    {
        global $_SESSION;
        
        $this->assert('Call createToken with formId')
            ->given($token = $this->class->createToken())
            ->string($token)
                ->isNotEmpty()
            ->given($tokenInfos = $_SESSION['token']['form_unit_test'])
            ->object($tokenInfos)
            ->boolean(property_exists($tokenInfos, 'token'))
                ->isTrue()
            ->string($tokenInfos->token)
                ->isEqualTo($token)
            ->boolean(property_exists($tokenInfos, 'date'))
                ->isTrue()
            ->object($tokenInfos->date)
                ->isInstanceOf('\DateTime');
        
        $this->class = new \BFW\Form('');
        $this->assert('Call createToke with empty formId')
            ->given($class = $this->class)
            ->exception(function() use ($class) {
                $class->createToken();
            })
                ->hasMessage('Form id is undefined.');
    }
    
    public function testCheckToken()
    {
        $class = $this->class;
        
        global $_SESSION;
        unset($_SESSION['token']);
        
        $this->assert('Call checkToken with no generated token')
            ->exception(function() use ($class) {
                $class->checkToken('');
            })
                ->hasMessage('no token found');
        
        $this->assert('Call checkToken with not existing token for form name')
            ->given($otherForm = new \BFW\Form('otherForm'))
            ->given($otherForm->createToken())
            ->exception(function() use ($class) {
                $class->checkToken('');
            })
                ->hasMessage('no token found for form id form_unit_test');
        
        $formToken = $this->class->createToken();
        
        $this->assert('Call checkToken with a bad token')
            ->boolean($this->class->checkToken('abcd'))
                ->isFalse();
        
        $this->assert('Call checkToken with the good token')
            ->boolean($this->class->checkToken($formToken))
                ->isTrue();
        
        $this->assert('Call checkToken to check if token has been removed')
            ->exception(function() use ($class, $formToken) {
                $class->checkToken($formToken);
            })
                ->hasMessage('no token found for form id form_unit_test');
        
        $this->assert('Call checkToken with expired token')
            ->given($formToken = $this->class->createToken())
            ->given(
                $dateTimeExpired = &$_SESSION['token']['form_unit_test']->date
            )
            ->given($dateTimeExpired->modify('-1 hour'))
            ->boolean($this->class->checkToken($formToken))
                ->isFalse();
    }
}
