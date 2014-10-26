<?php
/**
 * Classes gérant les modules
 * @author Vermeulen Maxime <bulton.fr@gmail.com>
 * @version 2.0
 */

namespace BFW;

/**
 * Gestions des modules
 * @package bfw
 */
class Modules implements \BFWInterface\IModules
{
    /**
     * @var $_kernel L'instance du Kernel
     */
    protected $_kernel;
    
    /**
     * @var $modList Liste des modules inclus
     */
    protected $modList = array();
    
    /**
     * @var $modLoad Liste des modules chargé
     */
    protected $modLoad = array();
    
    /**
     * @var $notLoad Liste des modules qui n'ont pas été chargé.
     */
    protected $notLoad = null;
    
    /**
     * Constructeur
     */
    public function __construct()
    {
        $this->_kernel = getKernel();
    }

    /**
     * Permet de déclarer un nouveau modules
     * @param string $name   Le nom du modules
     * @param array  $params Options pour le chargement des modules.
     * Liste des clés du tableau : 
     * - time (string, constante) : Le moment auquel sera chargé le module. Plusieurs valeurs possible. Ce sont des constantes
     *     modulesLoadTime_Module : Chargement immédiat. Avant la classe visiteur et les path en constante
     *     modulesLoadTime_Visiteur : Après la classe Visiteur. Les path en constante n'existe pas
     *     modulesLoadTime_EndInit : A la fin de l'initialisation du framework (défaut)
     * - require (string, array) : Si le module doit avoir d'autre module de chargé avant.
     * 
     * @throws \Exception Erreur sur la déclaration des options
     */ 
    public function newMod($name, $params=array())
    {
        if($this->exists($name))
        {
            throw new \Exception('Le module '.$name.' existe déjà.');
        }
        
        if(!is_array($params))
        {
            throw new \Exception('Les options du module '.$name.' doivent être déclarer sous la forme d\'un array.');
        }
        
        $time    = (isset($params['time'])) ? $params['time'] : modulesLoadTime_EndInit;
        $require = array();
        
        if(isset($params['require']))
        {
            if(is_string($params['require']))
            {
                $params['require'] = array($params['require']);
            }
            
            $require = $params['require'];
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
     * @param string $name Le nom du module
     * 
     * @return bool true s'il existe, false sinon
     */
    public function exists($name)
    {
        return array_key_exists($name, $this->modList);
    }
    
    /**
     * Permet de vérifier si un module est chargé
     * 
     * @param string $name Le nom du module
     * 
     * @return bool true s'il est chargé, false sinon
     */
    public function isLoad($name)
    {
        return in_array($name, $this->modLoad);
    }
    
    /**
     * Ajoute le path pour un module donné
     * 
     * @param string $name Le nom du module
     * @param string $path Le chemin réel du module
     * 
     * @throws \Exception Le module n'existe pas
     */
    public function addPath($name, $path)
    {
        if(!$this->exists($name)) {throw new \Exception('Le module '.$name.' n\'existe pas.');}
        $this->modList[$name]['path'] = $path;
    }
    
    /**
     * Liste des modules à charger à un moment précis.
     * 
     * @param string $timeToLoad Le temps auquel doivent être chargé les modules
     * 
     * @throws \Exception Erreur au chargement d'un module
     * 
     * @return array Liste des modules à charger
     */
    public function listToLoad($timeToLoad)
    {
        $arrayToLoad = array();
        
        foreach($this->modList as $mod)
        {
            if($mod['time'] == $timeToLoad)
            {
                //Une exception est levé par modToLoad s'il y a une problème;
                $this->modToLoad($mod, $arrayToLoad);
            }
        }
        
        return $arrayToLoad;
    }
    
    /**
     * Permet de vérifier si un module peut être chargé
     * 
     * @param array $mod         Le module à vérifier pour le chargement
     * @param array $arrayToLoad (ref) Liste des modules à charger
     * 
     * @throws \Exception Erreur avec les dépendances
     * 
     * @return bool Si le module peut être chargé ou non.
     */
    protected function modToLoad($mod, &$arrayToLoad)
    {
        if(!in_array($mod['name'], $arrayToLoad))
        {
            $require = $mod['require'];
            $load    = true;
            
            if(count($require) > 0)
            {
                foreach($require as $modRequire)
                {
                    if(!array_key_exists($modRequire, $this->modList))
                    {
                        throw new \Exception('La dépendance '.$modRequire.' du module '.$mod['name'].' n\'a pas été trouvé.');
                    }
                    
                    if(!$this->isLoad($modRequire) && $this->modList[$modRequire]['time'] != $mod['time'])
                    {
                        throw new \Exception('La dépendance '.$modRequire.' du module '.$mod['name'].' n\'est pas encore chargé. Vous devez charger votre module plus tard.');
                    }
                    
                    $load = $this->modToLoad($this->modList[$modRequire], $arrayToLoad);
                }
            }
            
            if($load) {$arrayToLoad[] = $mod['name'];}
            return $load;
        }
        
        return true;
    }
    
    /**
     * Liste les modules non chargé
     * 
     * @param bool $regen (default: false) Permet de regénérer la liste ou non
     * 
     * @return array Liste des modules non chargé
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
     * @return bool True si des modules n'ont pas pu être chargé, false sinon.
     */
    public function isModulesNotLoad()
    {
        $diff = $this->listNotLoad();
        
        if(count($diff) > 0) {return true;}
        return false;
    }
    
    /**
     * Retourne les infos sur un module
     * 
     * @param string $name Le nom du module dont on veux les infos
     * 
     * @throws \Exception Le module n'existe pas.
     * 
     * @return array Les infos sur le module
     */
    public function getModuleInfos($name)
    {
        if(!$this->exists($name)) {throw new \Exception('Le module '.$name.' n\'existe pas.');}
        return $this->modList[$name];
    }
}