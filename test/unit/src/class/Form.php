<?php

namespace BFW\test\unit;

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
        $this->createApp();
        $this->initApp();
        
        $this->mockGenerator
            ->makeVisible('saveToken')
            ->makeVisible('saveTokenInSession')
            ->makeVisible('obtainToken')
            ->makeVisible('obtainTokenFromSession')
            ->generate('BFW\Form')
        ;
        
        if ($testMethod !== 'testConstruct') {
            $this->mock = new \mock\BFW\Form('atoum');
        }
    }
    
    public function testConstruct()
    {
        $this->assert('test Form::__construct with a formId')
            ->object($this->mock = new \mock\BFW\Form('atoum'))
                ->isInstanceOf('\BFW\Form')
        ;
        
        $this->assert('test Form::__construct with empty formId')
            ->exception(function() {
                new \mock\BFW\Form('');
            })
                ->hasCode(\BFW\Form::ERR_FORM_ID_EMPTY)
        ;
    }
    
    public function testGetFormId()
    {
        $this->assert('test Form::getFormId')
            ->string($this->mock->getFormId())
                ->isEqualTo('atoum')
        ;
    }
    
    public function testSaveToken()
    {
        $this->assert('test Form::saveToken')
            ->given($saveInfos = (object) [
                'token' => '123',
                'date'  => new \DateTime
            ])
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
        
        $this->assert('test Form::saveTokenInSession')
            ->given($saveInfos = (object) [
                'token' => '123',
                'date'  => new \DateTime
            ])
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
        $this->assert('test Form::obtainToken')
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
        
        $this->assert('test Form::obtainTokenFromSession without token at all')
            ->exception(function() {
                $this->invoke($this->mock)->obtainTokenFromSession();
            })
                ->hasCode(\BFW\Form::ERR_NO_TOKEN)
        ;
        
        $this->assert('test Form::obtainTokenFromSession without token for this form')
            ->if($_SESSION['formsTokens'] = [])
            ->exception(function() {
                $this->invoke($this->mock)->obtainTokenFromSession();
            })
                ->hasCode(\BFW\Form::ERR_NO_TOKEN_FOR_FORM_ID)
        ;
        
        $this->assert('test Form::obtainTokenFromSession with a token')
            ->given($tokenInfos = (object) [
                'token' => '123',
                'date'  => new \DateTime
            ])
            ->if($_SESSION['formsTokens']['atoum'] = $tokenInfos)
            ->object($this->invoke($this->mock)->obtainTokenFromSession())
                ->isIdenticalTo($tokenInfos)
        ;
    }
    
    public function testCreateToken()
    {
        $this->assert('test Form::createToken')
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
            ->string($saveInfos->token)
                ->isEqualTo($token)
            ->object($saveInfos->date)
                ->isInstanceOf('\DateTime')
        ;
    }
    
    public function testCheckToken()
    {
        global $_SESSION;
        
        $this->assert('test Form::checkToken with a incorrect token')
            ->given($formId = $this->mock->getFormId())
            ->given($savedToken = (object) [
                'token' => '123',
                'date'  => new \DateTime
            ])
            ->then
            ->if($this->calling($this->mock)->obtainToken = $savedToken)
            ->and($_SESSION['formsTokens'][$formId] = $savedToken)
            ->then
            
            ->boolean($this->mock->checkToken('456'))
                ->isFalse()
            ->boolean(isset($_SESSION['formsTokens'][$formId]))
                ->isTrue()
        ;
        
        $this->assert('test Form::checkToken with an expired token')
            ->given($formId = $this->mock->getFormId())
            ->given($savedToken = (object) [
                'token' => '123',
                'date'  => new \DateTime
            ])
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
        
        $this->assert('test Form::checkToken with a not expired token')
            ->given($formId = $this->mock->getFormId())
            ->given($savedToken = (object) [
                'token' => '123',
                'date'  => new \DateTime
            ])
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
        $this->assert('test Form::hasToken if there is no token')
            ->if($this->calling($this->mock)->obtainToken = function() {
                throw new \Exception('No token.', \BFW\Form::ERR_NO_TOKEN);
            })
            ->then
            ->boolean($this->mock->hasToken())
                ->isFalse()
        ;
        
        $this->assert('test Form::hasToken with an existing token')
            ->if($this->calling($this->mock)->obtainToken = (object) [
                'token' => '123',
                'date'  => new \DateTime
            ])
            ->then
            ->boolean($this->mock->hasToken())
                ->isTrue()
        ;
    }
}