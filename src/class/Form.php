<?php

namespace BFW;

use \DateTime;
use \Exception;
use \stdClass;

class Form
{
    protected $formId;
    
    public function __construct($formId)
    {
        $this->formId = $formId;
    }
    
    protected function saveToken($saveInfos)
    {
        //Default to session
        $this->saveTokenInSession($saveInfos);
    }
    
    protected function saveTokenInSession($saveInfos)
    {
        global $_SESSION;
        
        $_SESSION['token'][$this->idForm] = $saveInfos;
    }
    
    protected function getToken()
    {
        //Default from session
        return $this->getTokenFromSession();
    }
    
    protected function getTokenFromSession()
    {
        global $_SESSION;
        
        if(!isset($_SESSION['token'])) {
            throw new Exception('no token found');
        }
        
        if(!isset($_SESSION['token'][$this->formId])) {
            throw new Exception('no token found for form id '.$this->formId);
        }
        
        return $_SESSION['token'][$this->formId];
    }
    
    public function createToken()
    {
        if(is_null($this->idForm)) {
            throw new Exception('Form id is undefined.');
        }
        
        $token = uniqid(rand(), true);
        $datetime = new DateTime;
        
        $saveInfos = new stdClass;
        $saveInfos->token = $token;
        $saveInfos->date  = $datetime;
        
        $this->saveToken($saveInfos);
        return $token;
    }
    
    public function checkToken($tokenToCheck, $timeExp=15)
    {
        //Throw Exception
        $tokenInfos = $this->getToken();
        
        $token      = $tokenInfos->token;
        $dateCreate = $tokenInfos->date;
        
        if($token !== $tokenToCheck) {
            return false;
        }
        
        $limitDate = new DateTime;
        $limitDate->modify('-'.(int) $timeExp.' minutes');

        if($dateCreate < $limitDate) {
            return false;
        }
        
        unset($_SESSION['token'][$this->idForm]);
        return true;
    }
}
