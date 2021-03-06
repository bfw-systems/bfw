<?php

namespace BFW\Helpers\test\unit;

use \atoum;

require_once(__DIR__.'/../../../../vendor/autoload.php');

/**
 * @engine isolate
 */
class Form extends atoum
{
    use \BFW\Test\Helpers\Application;
    
    protected $mock;
    
    public function beforeTestMethod($testMethod)
    {
        $this->setRootDir(__DIR__.'/../../../..');
        $this->createApp();
        $this->initApp();
        
        $this->mockGenerator
            ->makeVisible('saveToken')
            ->makeVisible('saveTokenInSession')
            ->makeVisible('obtainToken')
            ->makeVisible('obtainTokenFromSession')
            ->generate('BFW\Helpers\Form')
        ;
        
        if ($testMethod !== 'testConstruct') {
            $this->mock = new \mock\BFW\Helpers\Form('atoum');
        }
    }
    
    protected function generateToken($token, $date = null, $expire = 15)
    {
        if ($date === null) {
            $date = new \DateTime;
        }
        
        return (object) [
            'token'  => $token,
            'date'   => $date,
            'expire' => $expire
        ];
    }
    
    public function testConstruct()
    {
        $this->assert('test Helpers\Form::__construct with a formId')
            ->object($this->mock = new \mock\BFW\Helpers\Form('atoum'))
                ->isInstanceOf('\BFW\Helpers\Form')
        ;
        
        $this->assert('test Helpers\Form::__construct with empty formId')
            ->exception(function() {
                new \mock\BFW\Helpers\Form('');
            })
                ->hasCode(\BFW\Helpers\Form::ERR_FORM_ID_EMPTY)
        ;
    }
    
    public function testGetFormId()
    {
        $this->assert('test Helpers\Form::getFormId')
            ->string($this->mock->getFormId())
                ->isEqualTo('atoum')
        ;
    }
    
    public function testSaveToken()
    {
        $this->assert('test Helpers\Form::saveToken')
            ->given($saveInfos = $this->generateToken('123'))
            ->if($this->calling($this->mock)->saveTokenInSession = true)
            ->then
            ->variable($this->invoke($this->mock)->saveToken($saveInfos))
                ->isNull()
            ->mock($this->mock)
                ->call('saveTokenInSession')
                    ->once()
        ;
    }
    
    public function testSaveTokenInSession()
    {
        global $_SESSION;
        
        $this->assert('test Helpers\Form::saveTokenInSession')
            ->given($saveInfos = $this->generateToken('123'))
            ->then
            ->variable($this->invoke($this->mock)->saveTokenInSession($saveInfos))
                ->isNull()
            ->boolean(isset($_SESSION['formsTokens']['atoum']))
                ->isTrue()
            ->object($_SESSION['formsTokens']['atoum'])
                ->isIdenticalTo($saveInfos)
        ;
    }
    
    public function testObtainToken()
    {
        $this->assert('test Helpers\Form::obtainToken')
            ->if($this->calling($this->mock)->obtainTokenFromSession = null)
            ->then
            ->variable($this->invoke($this->mock)->obtainToken())
                ->isNull()
            ->mock($this->mock)
                ->call('obtainTokenFromSession')
                    ->once()
        ;
    }
    
    public function testObtainTokenFromSession()
    {
        global $_SESSION;
        
        $this->assert('test Helpers\Form::obtainTokenFromSession without token at all')
            ->exception(function() {
                $this->invoke($this->mock)->obtainTokenFromSession();
            })
                ->hasCode(\BFW\Helpers\Form::ERR_NO_TOKEN)
        ;
        
        $this->assert('test Helpers\Form::obtainTokenFromSession without token for this form')
            ->if($_SESSION['formsTokens'] = [])
            ->exception(function() {
                $this->invoke($this->mock)->obtainTokenFromSession();
            })
                ->hasCode(\BFW\Helpers\Form::ERR_NO_TOKEN_FOR_FORM_ID)
        ;
        
        $this->assert('test Helpers\Form::obtainTokenFromSession with a token')
            ->given($tokenInfos = $this->generateToken('123'))
            ->if($_SESSION['formsTokens']['atoum'] = $tokenInfos)
            ->object($this->invoke($this->mock)->obtainTokenFromSession())
                ->isIdenticalTo($tokenInfos)
        ;
    }
    
    public function testCreateToken()
    {
        $this->assert('test Helpers\Form::createToken')
            ->given($saveInfos = null)
            ->if($this->calling($this->mock)->saveToken = function ($saveToken) use (&$saveInfos) {
                $saveInfos = $saveToken;
            })
            ->then
            ->string($token = $this->mock->createToken())
                ->isNotEmpty()
            ->object($saveInfos)
                ->isInstanceOf('\stdClass')
            ->boolean(property_exists($saveInfos, 'token'))
                ->isTrue()
            ->boolean(property_exists($saveInfos, 'date'))
                ->isTrue()
            ->boolean(property_exists($saveInfos, 'expire'))
                ->isTrue()
            ->string($saveInfos->token)
                ->isEqualTo($token)
            ->object($saveInfos->date)
                ->isInstanceOf('\DateTime')
            ->integer($saveInfos->expire)
                ->isEqualTo(15)
        ;
    }
    
    public function testCheckToken()
    {
        global $_SESSION;
        
        $this->assert('test Helpers\Form::checkToken with a incorrect token')
            ->given($formId = $this->mock->getFormId())
            ->given($savedToken = $this->generateToken('123'))
            ->then
            ->if($this->calling($this->mock)->obtainToken = $savedToken)
            ->and($_SESSION['formsTokens'][$formId] = $savedToken)
            ->then
            
            ->boolean($this->mock->checkToken('456'))
                ->isFalse()
            ->boolean(isset($_SESSION['formsTokens'][$formId]))
                ->isTrue()
        ;
        
        $this->assert('test Helpers\Form::checkToken with an expired token')
            ->given($formId = $this->mock->getFormId())
            ->given($savedToken = $this->generateToken('123'))
            ->then
            ->if($savedToken->date->modify('-20 minutes'))
            ->and($this->calling($this->mock)->obtainToken = $savedToken)
            ->and($_SESSION['formsTokens'][$formId] = $savedToken)
            ->then
            
            ->boolean($this->mock->checkToken('123'))
                ->isFalse()
            ->boolean(isset($_SESSION['formsTokens'][$formId]))
                ->isFalse()
        ;
        
        $this->assert('test Helpers\Form::checkToken with a not expired token')
            ->given($formId = $this->mock->getFormId())
            ->given($savedToken = $this->generateToken('123'))
            ->then
            ->if($savedToken->date->modify('-5 minutes'))
            ->and($this->calling($this->mock)->obtainToken = $savedToken)
            ->and($_SESSION['formsTokens'][$formId] = $savedToken)
            ->then
            
            ->boolean($this->mock->checkToken('123'))
                ->isTrue()
            ->boolean(isset($_SESSION['formsTokens'][$formId]))
                ->isFalse()
        ;
    }
    
    public function testHasToken()
    {
        $this->assert('test Helpers\Form::hasToken if there is no token')
            ->if($this->calling($this->mock)->obtainToken = function() {
                throw new \Exception('No token.', \BFW\Helpers\Form::ERR_NO_TOKEN);
            })
            ->then
            ->boolean($this->mock->hasToken())
                ->isFalse()
        ;
        
        $this->assert('test Helpers\Form::hasToken with an existing token')
            ->if($this->calling($this->mock)->obtainToken = $this->generateToken('123'))
            ->then
            ->boolean($this->mock->hasToken())
                ->isTrue()
        ;
    }
}