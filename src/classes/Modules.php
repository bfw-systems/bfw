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
     * @var array $loadOrder Liste de l'ordre de chargement des modules pour chaque temps de chargement
     */
    protected $loadOrder = array();
    
    /**
     * Constructeur
     */
    public function __construct()
    {
        $this->_kernel = getKernel();
    }
    
    /**
     * Permet de déclarer un nouveau module depuis un fichier json
     * 
     * @param string $path : Le chemin vers le fichier json
     * 
     * @throws \Exception Erreur sur la déclaration des options
     */
    public function newFromJson($path)
    {
        $filePath = $path.'/module.json';
        $jsonInfo = file_get_contents($filePath);
        $modInfo  = json_decode($jsonInfo, true);
        
        if(!isset($modInfo['name']))
        {
            throw new \Exception('Le nom du module n\'est pas déclaré (path: '.$path.').');
        }
        
        if(isset($modInfo['params']) && !is_array($modInfo['params']))
        {
            throw new \Exception('Les paramètres du module '.$name.' n\'est pas au bon format.');
        }
        
        $name   = $modInfo['name'];
        $params = $modInfo['params'];
        
        $this->initMod($name, $params);
        $this->addPath($name, $path);
    }
    
    /**
     * Permet de déclarer un nouveau modules
     * 
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
        $this->initParameters($params, 'runFile', 'inclus.php');
        $this->initMod($name, $params);
    }
    
    /**
     * Permet de déclarer un nouveau modules
     * 
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
    protected function initMod($name, $params=array())
    {
        if($this->exists($name))
        {
            throw new \Exception('Le module '.$name.' existe déjà.');
        }
        
        if(!is_array($params))
        {
            throw new \Exception('Les options du module '.$name.' doivent être déclarer sous la forme d\'un array.');
        }

        $time     = $this->initParameters($params, 'time', modulesLoadTime_EndInit);
        $require  = $this->initParameters($params, 'require', array());
        $priority = $this->initParameters($params, 'priority', 0);
        $runFile  = $this->initParameters($params, 'runFile', false);
        
        $this->modList[$name] = array(
            'name' => $name,
            'time' => $time,
            'require' => $require,
            'priority' => $priority,
            'runFile' => $runFile
        );
    }
    
    /**
     * Permet d'initialiser un paramètre
     * 
     * @param array  &$params : Les paramètres du module
     * @param string $key     : La clé du paramètre à initialiser
     * @param mixed  $default : La valeur par défaut
     */
    protected function initParameters(&$params, $key, $default)
    {
        if(!isset($params[$key]))
        {
            $params[$key] = $default;
        }
        
        if(is_int($default))
        {
            $params[$key] = (int) $params[$key];
        }
        
        if(is_array($default) && !is_array($params[$key]))
        {
            $params[$key] = array($params[$key]);
        }
        
        return $params[$key];
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
     * Permet de définir un module comme chargé
     * 
     * @param string $name Le nom du module
     * 
     * @throws \Exception : Si le module n'existe pas
     * 
     * @return void
     */
    public function loaded($name)
    {
        if(!$this->exists($name))
        {
            throw new \Exception('Module '.$name.' not exists.');
        }
        
        $this->modLoad[] = $name;
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
        $toLoad = array();
        
        foreach($this->modList as &$mod)
        {
            if($mod['time'] == $timeToLoad)
            {
                $toLoad[] = $mod;
            }
        }
        unset($mod); //kill ref
        
        uasort($toLoad, array($this, 'sortPriority'));
        
        foreach($toLoad as &$mod)
        {
            //Une exception est levé par modToLoad s'il y a une problème;
            $this->modToLoad($mod, $arrayToLoad);
        }
        unset($mod); //kill ref
        
        /*
        var_dump($arrayToLoad);
        if($timeToLoad == 'endInit') {exit;}
        //*/
        
        $this->loadOrder[$timeToLoad] = $arrayToLoad;
        
        return $arrayToLoad;
    }
    
    protected function sortPriority($mod1, $mod2)
    {
        $prio1 = $mod1['priority'];
        $prio2 = $mod2['priority'];
        
        if($prio1 === $prio2)
        {
            return 0;
        }
        
        return ($prio1 > $prio2) ? -1 : 1;
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
    protected function modToLoad(&$mod, &$arrayToLoad, $waitToLoad=array())
    {
        if(in_array($mod['name'], $arrayToLoad) || isset($waitToLoad[$mod['name']]))
        {
            return true;
        }
        
        $waitToLoad[$mod['name']] = true;
        
        $require = $mod['require'];
        $load    = true;
        
        foreach($require as $modRequire)
        {
            if(!array_key_exists($modRequire, $this->modList))
            {
                throw new \Exception('La dépendance '.$modRequire.' du module '.$mod['name'].' n\'a pas été trouvé.');
            }
            
            if(!$this->isLoad($modRequire))
            {
                $load = $this->modToLoad($this->modList[$modRequire], $arrayToLoad, $waitToLoad);
            }
        }
        
        if($load)
        {
            $arrayToLoad[] = $mod['name'];
            unset($waitToLoad[$mod['name']]);
        }
        
        return $load;
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
        if($regen === true || is_null($this->notLoad))
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
    
    /**
     * Retourne le tableau loggant l'ordre de chargements des modules
     * 
     * @return array
     */
    public function getLoadOrder()
    {
        return $this->loadOrder;
    }
}
