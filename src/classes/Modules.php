<?php
/**
 * Classes gérant les modules
 * @author Vermeulen Maxime
 * @version 2.0
 */

namespace BFW;

/**
 * Gestions des modules
 * @package BFW
 */
class Modules implements \BFWInterface\IModules
{
    /**
     * @var $_kernel : L'instance du Kernel
     */
    private $_kernel;
    
    /**
     * @var $modList : Liste des modules inclus
     */
    private $modList = array();
    
    /**
     * @var $modLoad : Liste des modules chargé
     */
    private $modLoad = array();
    
    /**
     * @var $notLoad : Liste des modules qui n'ont pas été chargé.
     */
    private $notLoad = null;
    
    /**
     * Constructeur
     */
    public function __construct()
    {
        $this->_kernel = getKernel();
    }

    /**
     * Permet de déclarer un nouveau modules
     * @param string $name  : Le nom du modules
     * @param array $params : Options pour le chargement des modules.
     *                          Liste des clés du tableau : 
     *                              - time (string, constante) : Le moment auquel sera chargé le module. Plusieurs valeurs possible. Ce sont des constantes
     *                                          modulesLoadTime_Module : Chargement immédiat. Avant la classe visiteur et les path en constante
     *                                          modulesLoadTime_Visiteur : Après la classe Visiteur. Les path en constante n'existe pas
     *                                          modulesLoadTime_EndInit : A la fin de l'initialisation du framework (défaut)
     *                              - require (string, array) : Si le module doit avoir d'autre module de chargé avant.
     */ 
    public function newMod($name, $params=array())
    {
        if(isset($this->mod_list[$name]))
        {
            throw new \Exception('Le module '.$name.' existe déjà.');
            return false;
        }
        
        $time = (isset($params['time'])) ? $params['time'] : modulesLoadTime_EndInit;
        $require = array();
        
        if(isset($params['require']))
        {
            if(is_string($params['require']))
            {
                $require = array($params['require']);
            }
            elseif(is_array($params['require']))
            {
                $require = $params['require'];
            }
        }
        
        $this->modList[$name] = array(
            'name' => $name,
            'time' => $time,
            'require' => $require
        );
    }
    
    /**
     * Permet de vérifier si un module existe
     * 
     * @param string $name : Le nom du module
     * 
     * @return bool : true s'il existe, false sinon
     */
    public function exists($name)
    {
        return in_array($name, $this->modList);
    }
    
    /**
     * Permet de vérifier si un module est chargé
     * 
     * @param string $name : Le nom du module
     * 
     * @return bool : true s'il est chargé, false sinon
     */
    public function isLoad($name)
    {
        return in_array($name, $this->modLoad);
    }
    
    /**
     * Ajoute le path pour un module donné
     * 
     * @param string $name : Le nom du module
     * @param string $path : Le chemin réel du module
     */
    public function addPath($name, $path)
    {
        if($this->exists($name))
        {
            $this->modList[$name]['path'] = $path;
        }
        else
        {
            throw new \Exception('Le module '.$name.' n\'existe pas.');
        }
    }
    
    /**
     * Liste des modules à charger à un moment précis.
     * 
     * @param string $timeToLoad : Le temps auquel doivent être chargé les modules
     * 
     * @return array : Liste des modules à charger
     */
    public function listToLoad($timeToLoad)
    {
        $arrayToLoad = array();
        
        foreach($this->modList as $mod)
        {
            if($mod['time'] == $timeToLoad)
            {
                if(!$this->modToLoad($mod, $arrayToLoad))
                {
                    throw new \Exception('Une erreur est survenu au chargement du module '.$mod['name']);
                }
            }
        }
        
        return $arrayToLoad;
    }
    
    /**
     * Permet de vérifier si un module peut être chargé
     * 
     * @param array $mod         : Le module à vérifier pour le chargement
     * @param array $arrayToLoad : (référence) Liste des modules à charger
     * 
     * @return bool : Si le module peut être chargé ou non.
     */
    protected function modToLoad($mod, &$arrayToLoad)
    {
        if(!in_array($mod['name'], $arrayToLoad))
        {
            $require = $mod['require'];
            $load = true;
            
            if(count($require) > 0)
            {
                foreach($require as $modRequire)
                {
                    if(!array_key_exists($modRequire, $this->modList))
                    {
                        throw new \Exception('La dépendance '.$modRequire.' du module '.$mod['name'].' n\'a pas été trouvé.');
                        $load = false;
                    }
                    else
                    {
                        if(!$this->isLoad($modRequire) || $this->modList[$modRequire]['time'] != $mod['time'])
                        {
                            throw new \Exception('La dépendance '.$modRequire.' du module '.$mod['name'].' n\'est pas encore chargé. Vous devez charger votre module plus tard.');
                            $load = false;
                        }
                        else
                        {
                            $load = $this->modToLoad($this->modList[$modRequire], $arrayToLoad);
                        }
                    }
                }
            }
            
            if($load)
            {
                $arrayToLoad[] = $mod['name'];
            }
            
            return $load;
        }
        
        return true;
    }
    
    /**
     * Liste les modules non chargé
     * 
     * @param bool $regen : Permet de regénérer la liste ou non
     * 
     * @return array : Liste des modules non chargé
     */
    public function listNotLoad($regen=false)
    {
        if($regen == true || $this->notLoad != null)
        {
            $this->notLoad = array_diff($this->modList, $this->modLoad);
        }
        
        return $this->notLoad;
    }
    
    /**
     * Permet de savoir si des modules n'ont pas pu être chargé
     * 
     * @return bool : True si des modules n'ont pas pu être chargé, false sinon.
     */
    public function isModulesNotLoad()
    {
        $diff = $this->listNotLoad();
        
        if(count($diff) > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    /**
     * Retourne les infos sur un module
     * 
     * @param string $name : Le nom du module dont on veux les infos
     * 
     * @return array : Les infos sur le module
     */
    public function getModuleInfos($name)
    {
        if($this->exists($name))
        {
            return $this->modList[$name];
        }
        else
        {
            throw new \Exception('Le module '.$name.' n\'existe pas.');
            return array();
        }
    }
}