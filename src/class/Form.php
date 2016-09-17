<?php

namespace BFW;

use \DateTime;
use \Exception;
use \stdClass;

/**
 * Class to manage html form
 * Actuely, only manage token for form.
 */
class Form
{
    /**
     * @var string $formId The form id 
     */
    protected $formId = '';

    /**
     * Constructor
     * Define form's id.
     * 
     * @param string $formId The form id
     */
    public function __construct($formId)
    {
        $this->formId = (string) $formId;
    }

    /**
     * Save the form's token
     * 
     * @param \stdClass $saveInfos Infos about token (id and expire time)
     * 
     * @return void
     */
    protected function saveToken($saveInfos)
    {
        //Default to session
        $this->saveTokenInSession($saveInfos);
    }

    /**
     * Save a token in php session
     * 
     * @global array $_SESSION
     * 
     * @param \stdClass $saveInfos Infos about token (id and expire time)
     * 
     * @return void
     */
    protected function saveTokenInSession($saveInfos)
    {
        global $_SESSION;

        $_SESSION['token'][$this->formId] = $saveInfos;
    }

    /**
     * Get the token informations
     * 
     * @return \stdClass
     */
    protected function getToken()
    {
        //Default from session
        return $this->getTokenFromSession();
    }

    /**
     * Get a token from the session
     * 
     * @global array $_SESSION
     * 
     * @return \stdClass
     * 
     * @throws Exception If there are no token
     */
    protected function getTokenFromSession()
    {
        global $_SESSION;

        if (!isset($_SESSION['token'])) {
            throw new Exception('no token found');
        }

        if (!isset($_SESSION['token'][$this->formId])) {
            throw new Exception('no token found for form id '.$this->formId);
        }

        return $_SESSION['token'][$this->formId];
    }

    /**
     * Create a token for the form and return the token
     * 
     * @return string
     * 
     * @throws Exception If the form id is undefined
     */
    public function createToken()
    {
        if (empty($this->formId)) {
            throw new Exception('Form id is undefined.');
        }

        $token    = uniqid(rand(), true);
        $datetime = new DateTime;

        $saveInfos        = new stdClass;
        $saveInfos->token = $token;
        $saveInfos->date  = $datetime;

        $this->saveToken($saveInfos);
        return $token;
    }

    /**
     * Check the token receive with the generated token
     * 
     * @param string $tokenToCheck The token receive from user
     * @param int $timeExp (default 15) time on minute which the token is valid
     * 
     * @return boolean
     */
    public function checkToken($tokenToCheck, $timeExp = 15)
    {
        //Throw Exception
        $tokenInfos = $this->getToken();

        $token      = $tokenInfos->token;
        $dateCreate = $tokenInfos->date;

        if ($token !== $tokenToCheck) {
            return false;
        }

        $limitDate = new DateTime;
        $limitDate->modify('-'.(int) $timeExp.' minutes');

        if ($dateCreate < $limitDate) {
            return false;
        }

        unset($_SESSION['token'][$this->formId]);
        return true;
    }
}
