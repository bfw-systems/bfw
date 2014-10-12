<?php
/**
 * Classe en rapport avec Memcache
 * 
 * @author Vermeulen Maxime <bulton.fr@gmail.com>
 * @version 1.0
 */

namespace BFW;

/**
 * Gestion du serveur avec memcache
 * @package bfw
 */
class Ram implements \BFWInterface\IRam
{
    /**
     * @var $_kernel L'instance du Kernel
     */
    private $_kernel;
    
    /**
     * @var $server_connect Permet de savoir si on est connecté au serveur.
     */
    private $server_connect = false;
    
    /**
     * @var $Server Le serveur
     */
    private $Server;
    
    /**
     * @var $debug Permet d'activer ou non le mode débug
     */
    public $debug = false;
    
    
    /**
     * Constructeur
     * Se connecte au serveur memcache indiqué, par défaut au localhost
     * 
     * @param string $name (default:"localhost") le nom du serveur memcache
     * 
     * @return boolean|null
     */
    public function __construct($name='localhost')
    {
        $this->_kernel = getKernel();
        
        if(extension_loaded('memcache') && is_string($name))
        {
            $this->Server = new \Memcache;
            if($this->Server->connect($name))
            {
                $this->server_connect = true;
                return true;
            }
            else {return false;}
        }
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
        $default = false;
        $verifParams = verifTypeData(array(
            array('type' => 'string', 'data' => $key),
            array('type' => 'int', 'data' => $expire)
        ));
        
        if(!$verifParams || gettype($data) == 'resource')
        {
            if($this->_kernel->getDebug()) {throw new \Exception('Erreur dans les paramètres de Ram->setVal()');}
            else {return $default;}
        }
        
        if($this->server_connect == true)
        {
            $valDataServer = $this->Server->get($key);
            
            if($valDataServer !== false)
            {
                return $this->Server->replace($key, $data, 0, $expire);
            }
            else
            {
                return $this->Server->set($key, $data, 0, $expire);
            }
        }
        else
        {
            global $path;
            
            $stock = array('expire' => $expire, 'create' => time(), 'data' => $data);
            $filePutContentReturn = file_put_contents($path.'kernel/Memcache_ifnoExt/'.$key.'.txt', json_encode($stock));
            
            if($filePutContentReturn === false) {return false;}
            return true;
        }
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
        $default = false;
        $verifParams = verifTypeData(array(
            array('type' => 'string', 'data' => $key),
            array('type' => 'int', 'data' => $exp)
        ));
        
        if(!$verifParams)
        {
            if($this->_kernel->getDebug()) {throw new \Exception('Erreur dans les paramètres de Ram->majExpire()');}
            else {return $default;}
        }
        
        
        
        if($this->server_connect == true)
        {
            $ret = $this->Server->get($key); //Récupère la valeur
            
            //On la "modifie" en remettant la même valeur mais en changeant le temps
            //avant expiration si une valeur a été retournée
            if($ret !== false)
            {
                if($this->Server->replace($key, $ret, 0, $exp)) {return true;}
            }
            return false;
        }
        else
        {
            global $path;
            if(file_exists($path.'kernel/Memcache_ifnoExt/'.$key.'.txt'))
            {
                $data = json_decode(file_get_contents($path.'kernel/Memcache_ifnoExt/'.$key.'.txt'));
                $data->expire = $exp;
                $data->create = time();
                
                $filePutContentReturn = file_put_contents($path.'kernel/Memcache_ifnoExt/'.$key.'.txt', json_encode($data));
                
                if($filePutContentReturn === false) {return false;}
                return true;
            }
        }
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
        $default = false;
        $verifParams = verifTypeData(array(array('type' => 'string', 'data' => $key)));
        
        if(!$verifParams)
        {
            if($this->_kernel->getDebug()) {throw new \Exception('Erreur dans les paramètres de Ram->ifExists()');}
            else {return $default;}
        }
        
        if($this->server_connect == true)
        {
            $ret = $this->Server->get($key); //Récupère la valeur
            
            if($ret === false) {return false;}
            else {return true;}
        }
        else
        {
            global $path;
            return file_exists($path.'kernel/Memcache_ifnoExt/'.$key.'.txt');
        }
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
        if($this->server_connect == true)
        {
            if($this->Server->delete($key)) {return true;}
            else {return false;}
        }
        else
        {
            global $path;
            
            if(file_exists($path.'kernel/Memcache_ifnoExt/'.$key.'.txt'))
            {
                return unlink($path.'kernel/Memcache_ifnoExt/'.$key.'.txt');
            }
            return true;
        }
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
        $default = false;
        $verifParams = verifTypeData(array(array('type' => 'string', 'data' => $key)));
        
        if(!$verifParams)
        {
            if($this->_kernel->getDebug()) {throw new \Exception('Erreur dans les paramètres de Ram->getVal()');}
            else {return $default;}
        }
        
        if($this->server_connect == true) //Récupère la valeur
        {
            return $this->Server->get($key);
        }
        else
        {
            global $path;
                
            if(file_exists($path.'kernel/Memcache_ifnoExt/'.$key.'.txt'))
            {
                $json = json_decode(file_get_contents($path.'kernel/Memcache_ifnoExt/'.$key.'.txt'));
                
                $expire = $json->expire;
                $create = $json->create;
                
                if($expire != 0)
                {
                    $calcul = $create+$expire;
                    $now = time();
                    
                    if($calcul >= $now)
                    {
                        $data = $json->data;
                    }
                }
                else
                {
                    $data = $json->data;
                }
            }
            
            if(empty($data)) {return false;}
            return $data;
        }
    }
}