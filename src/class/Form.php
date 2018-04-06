<?php

namespace BFW;

use \DateTime;
use \Exception;
use \stdClass;

/**
 * Class to manage html forms
 * Only manage form's token.
 */
class Form
{
    /**
     * @const ERR_NO_TOKEN Exception code if there is no token declared
     */
    const ERR_NO_TOKEN = 1307001;
    
    /**
     * @const ERR_NO_TOKEN_FOR_FORM_ID Exception code if there is no token for
     * the form id.
     */
    const ERR_NO_TOKEN_FOR_FORM_ID = 1307002;
    
    /**
     * @const ERR_FORM_ID_EMPTY Exception code if the form id is not declared.
     */
    const ERR_FORM_ID_EMPTY = 1307003;
    
    /**
     * @var string $formId The form id 
     */
    protected $formId = '';

    /**
     * Constructor
     * Define the form's id.
     * 
     * @param string $formId The form's id
     */
    public function __construct($formId)
    {
        $this->formId = (string) $formId;
        
        if (empty($this->formId)) {
            throw new Exception('Form id is empty.', $this::ERR_FORM_ID_EMPTY);
        }
    }
    
    /**
     * Getter accessor to the property formId
     * 
     * @return string
     */
    public function getFormId()
    {
        return $this->formId;
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

        $_SESSION['formsTokens'][$this->formId] = $saveInfos;
    }

    /**
     * Get the token informations
     * 
     * @return \stdClass
     */
    protected function obtainToken()
    {
        return $this->obtainTokenFromSession();
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
    protected function obtainTokenFromSession()
    {
        global $_SESSION;

        if (!isset($_SESSION['formsTokens'])) {
            throw new Exception('no token found', $this::ERR_NO_TOKEN);
        }

        if (!isset($_SESSION['formsTokens'][$this->formId])) {
            throw new Exception(
                'no token found for the form id '.$this->formId,
                $this::ERR_NO_TOKEN_FOR_FORM_ID
            );
        }

        return $_SESSION['formsTokens'][$this->formId];
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
        $saveInfos = (object) [
            'token' => uniqid(rand(), true),
            'date'  => new DateTime
        ];

        $this->saveToken($saveInfos);
        return $saveInfos->token;
    }

    /**
     * Check the token receive with the generated token
     * 
     * @param string $tokenToCheck The token receive from user
     * @param int $timeExpire (default 15) time on minute during which the
     *  token is valid
     * 
     * @throws \Exception If the token not exist
     * 
     * @return boolean
     */
    public function checkToken($tokenToCheck, $timeExpire = 15)
    {
        //Throw Exception
        $tokenInfos = $this->obtainToken();

        $token      = $tokenInfos->token;
        $dateCreate = $tokenInfos->date;

        if ($token !== $tokenToCheck) {
            return false;
        }

        $limitDate = new DateTime;
        $limitDate->modify('-'.(int) $timeExpire.' minutes');

        if ($dateCreate < $limitDate) {
            return false;
        }

        unset($_SESSION['formsTokens'][$this->formId]);
        return true;
    }
    
    /**
     * Check if the form has a token
     * 
     * @return boolean
     */
    public function hasToken()
    {
        try {
            $this->obtainToken();
        } catch (Exception $e) {
            return false;
        }
        
        return true;
    }
}
