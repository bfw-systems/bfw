<?php

namespace BFW\Helpers;

use \DateTime;
use \Exception;

/**
 * Class to manage html forms
 * Only manage form's token.
 */
class Form
{
    /**
     * @const ERR_NO_TOKEN Exception code if there is no token declared
     */
    const ERR_NO_TOKEN = 1606001;
    
    /**
     * @const ERR_NO_TOKEN_FOR_FORM_ID Exception code if there is no token for
     * the form id.
     */
    const ERR_NO_TOKEN_FOR_FORM_ID = 1606002;
    
    /**
     * @const ERR_FORM_ID_EMPTY Exception code if the form id is not declared.
     */
    const ERR_FORM_ID_EMPTY = 1606003;
    
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
    public function __construct(string $formId)
    {
        $this->formId = $formId;
        
        if (empty($this->formId)) {
            throw new Exception('Form id is empty.', $this::ERR_FORM_ID_EMPTY);
        }
    }
    
    /**
     * Getter accessor to the property formId
     * 
     * @return string
     */
    public function getFormId(): string
    {
        return $this->formId;
    }

    /**
     * Save the form's token
     * 
     * @param object $saveInfos Infos about token (id and expire time)
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
     * @param object $saveInfos Infos about token (id and expire time)
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
     * @return object
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
     * @return object
     * 
     * @throws \Exception If there are no token
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
     * @param int $expire (default 15) time on minute during which the
     *  token is valid
     * 
     * @return string
     * 
     * @throws \Exception If the form id is undefined
     */
    public function createToken(int $expire = 15): string
    {
        $token = uniqid(rand(), true);
        
        $saveInfos = (object) [
            'token' => $token,
            'date'  => new DateTime,
            'expire' => $expire
        ];

        $this->saveToken($saveInfos);
        return $saveInfos->token;
    }

    /**
     * Check the token receive with the generated token
     * 
     * @param string $tokenToCheck The token receive from user
     * 
     * @throws \Exception If the token not exist
     * 
     * @return boolean
     */
    public function checkToken(string $tokenToCheck): bool
    {
        //Throw Exception
        $tokenInfos = $this->obtainToken();

        $token      = $tokenInfos->token;
        $dateCreate = $tokenInfos->date;
        $timeExpire = $tokenInfos->expire;

        if ($token !== $tokenToCheck) {
            return false;
        }

        $limitDate = new DateTime;
        $limitDate->modify('-'.$timeExpire.' minutes');
        
        unset($_SESSION['formsTokens'][$this->formId]);
        
        if ($dateCreate < $limitDate) {
            return false;
        }

        return true;
    }
    
    /**
     * Check if the form has a token
     * 
     * @return boolean
     */
    public function hasToken(): bool
    {
        try {
            $this->obtainToken();
        } catch (Exception $e) {
            return false;
        }
        
        return true;
    }
}
