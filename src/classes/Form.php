<?php
/**
 * Classes en rapport avec les formulaires
 * @author Vermeulen Maxime <bulton.fr@gmail.com>
 * @version 1.0
 */
 
namespace BFW;

use \Exception;

/**
 * Permet de gérer les formulaire (gestion des tokens)
 * @package bfw
 */
class Form implements \BFWInterface\IForm
{
    /**
     * @var $_kernel L'instance du Kernel
     */
    protected $_kernel;
    
    /**
     * @var $idForm L'id du formulaire
     */
    protected $idForm;
    
    /**
     * Constructeur
     * 
     * @param string $idForm L'id du formulaire
     */
    public function __construct($idForm=null)
    {
        $this->_kernel = getKernel();
        
        $this->idForm = $idForm;
    }
    
    /**
     * Accesseur set sur id_form
     * 
     * @param string $idForm L'id du formulaire
     */
    public function setIdForm($idForm)
    {
        $this->idForm = $idForm;
    }
    
    /**
     * Permet de créer un token pour le formulaire
     * 
     * @return string Le token à mettre dans un champ input de type hidden.
     * 
     * @throws \Exception : Si le nom du formulaire n'est pas définie.
     */
    public function tokenCreate()
    {
        if(is_null($this->idForm)) {throw new Exception('Form name is undefined.');}
        
        $Id = uniqid(rand(), true);
        $date = new Date();
        
        global $_SESSION;
        $_SESSION['token'][$this->idForm] = array(
            'token' => $Id,
            'date' => $date->getDate()
        );
        
        return $Id;
    }
    
    /**
     * Permet de vérifier si le token est correct
     * 
     * @return bool True si le toke est bon, false sinon.
     */
    public function tokenVerif()
    {
        global $_SESSION, $_POST;
        
        if(isset($_SESSION['token']) && is_array($_SESSION['token']))
        {
            if(isset($_SESSION['token'][$this->idForm]) && is_array($_SESSION['token'][$this->idForm]))
            {
                $token = $_SESSION['token'][$this->idForm]['token'];
                $date_create = $_SESSION['token'][$this->idForm]['date'];
                $date_createDT = new Date($date_create);
                
                if(isset($_POST['token']) && $_POST['token'] == $token)
                {
                    $date_limit = new Date();
                    $date_limit->modify('-15 minute');
                    
                    if($date_createDT >= $date_limit)
                    {
                        unset($_SESSION['token'][$this->idForm]);
                        return true;
                    }
                }
            }
        }
        
        return false;
    }
}
