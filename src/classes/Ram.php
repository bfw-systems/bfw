<?php
/**
 * Classe en rapport avec Memcache
 * 
 * @author Vermeulen Maxime <bulton.fr@gmail.com>
 * @version 1.0
 */

namespace BFW;

use \Exception;

/**
 * Gestion du serveur avec memcache
 * @package bfw
 */
class Ram implements \BFWInterface\IRam
{
    /**
     * @var $_kernel L'instance du Kernel
     */
    protected $_kernel;
    
    /**
     * @var $server_connect Permet de savoir si on est connecté au serveur.
     */
    protected $server_connect = false;
    
    /**
     * @var $Server Le serveur
     */
    protected $Server;
    
    /**
     * @var $debug Permet d'activer ou non le mode débug
     */
    public $debug = false;
    
    
    /**
     * Constructeur
     * Se connecte au serveur memcache indiqué
     * 
     * @param string  $host l'host du serveur memcache
     * @param integer $port le port du serveur memcache
     * 
     * @throws Exception : Si l'extension php-memcache n'est présente
     *                     Si les infos sont pas au bon format
     *                     Si la connexion échoue
     */
    public function __construct($host, $port)
    {
        $this->_kernel = getKernel();
        
        //Vérification si l'extension memcache est bien loadé
        if(!extension_loaded('memcache'))
        {
            throw new Exception('Memcache php extension is not loaded.');
        }
        
        //Vérifie que les infos sont bien au bon typage
        if(!(is_string($host) && is_integer($port)))
        {
            throw new Exception('Memcache connexion informations format is not correct.');
        }
        
        $this->Server = new \Memcache;
        
        //Se connexion ... exception si fail
        if($this->Server->connect($host, $port) === false)
        {
            throw new Exception('Memcache connect fail.');
        }
        
        $this->server_connect = true;
    }
    
    /**
     * Permet de stocker une clé en mémoire ou de la mettre à jour
     * 
     * @param string $key    Clé correspondant à la valeur
     * @param mixed  $data   Les nouvelles données. Il n'est pas possible de stocker une valeur de type resource.
     * @param int    $expire (default: 0) Le temps en seconde avant expiration. 0 illimité, max 30jours
     * 
     * @throws \Exception Erreur dsans les paramètres donnée à la méthode
     * 
     * @return bool
     */
    public function setVal($key, $data, $expire=0)
    {
        $verifParams = verifTypeData(array(
            array('type' => 'string', 'data' => $key),
            array('type' => 'int', 'data' => $expire)
        ));
        
        if(!$verifParams || gettype($data) == 'resource')
        {
            throw new \Exception('Erreur dans les paramètres de Ram->setVal()');
        }
        
        $valDataServer = $this->Server->get($key);
        if($valDataServer !== false)
        {
            return $this->Server->replace($key, $data, 0, $expire);
        }
        
        return $this->Server->set($key, $data, 0, $expire);
    }
    
    /**
     * On modifie le temps avant expiration des infos sur le serveur memcached pour une clé choisie.
     * 
     * @param string $key la clé disignant les infos concerné
     * @param int    $exp le nouveau temps avant expiration (0: pas d'expiration, max 30jours)
     * 
     * @throws \Exception Erreur dsans les paramètres donnée à la méthode
     * 
     * @return boolean|null
     */
    public function majExpire($key, $exp)
    {
        $verifParams = verifTypeData(array(
            array('type' => 'string', 'data' => $key),
            array('type' => 'int', 'data' => $exp)
        ));
        
        if(!$verifParams)
        {
            throw new \Exception('Erreur dans les paramètres de Ram->majExpire()');
        }
        
        $ret = $this->Server->get($key); //Récupère la valeur
        
        //On la "modifie" en remettant la même valeur mais en changeant le temps
        //avant expiration si une valeur a été retournée
        if($ret !== false)
        {
            if($this->Server->replace($key, $ret, 0, $exp)) {return true;}
        }
        
        return false;
    }
    
    /**
     * Permet de savoir si la clé existe
     * 
     * @param string $key la clé disignant les infos concernées
     * 
     * @throws \Exception Erreur dsans les paramètres donnée à la méthode
     * 
     * @return bool
     */
    public function ifExists($key)
    {
        $verifParams = verifTypeData(array(array('type' => 'string', 'data' => $key)));
        
        if(!$verifParams)
        {
            throw new \Exception('Erreur dans les paramètres de Ram->ifExists()');
        }
        
        $ret = $this->Server->get($key); //Récupère la valeur
        
        if($ret === false) {return false;}
        return true;
    }
    
    /**
     * Supprime une clé
     * 
     * @param string $key la clé disignant les infos concernées
     * 
     * @return bool
     */
    public function delete($key)
    {
        if($this->Server->delete($key)) {return true;}
        return false;
    }
    
    /**
     * Permet de retourner la valeur d'une clé.
     * 
     * @param string $key Clé correspondant à la valeur
     * 
     * @throws \Exception Erreur dsans les paramètres donnée à la méthode
     * 
     * @return mixed La valeur demandée
     */
    public function getVal($key)
    {
        $verifParams = verifTypeData(array(array('type' => 'string', 'data' => $key)));
        
        if(!$verifParams)
        {
            throw new \Exception('Erreur dans les paramètres de Ram->getVal()');
        }
        
        return $this->Server->get($key);
    }
}