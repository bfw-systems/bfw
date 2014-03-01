<?php
/**
 * Classes gérant les modules
 * @author Vermeulen Maxime
 * @version 1.0
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
     * @var $mod_list : Liste des modules inclus
     */
    private $mod_list = array();
    
    /**
     * @var $mod_load : Liste des modules chargé
     */
    public $mod_load = array();
    
    /**
     * Constructeur
     */
    public function __construct()
    {
        $this->_kernel = getKernel();
    }

    /**
     * Permet de déclarer un nouveau modules
     * @param string $name : Le nom du modules
     * @param string $time [opt] : Si indiqué, le module sera chargé à un moment précis du noyau, 
     *                              sinon il sera chargé directement.
     */ 
    public function new_mods($name, $time=null)
    {
        $this->mod_list[] = $name;
        if($time != null)
        {
            $this->mod_load[$time][] = $name;
        }
    }
    
    /**
     * Permet de vérifier si un module existe
     * @param string $name : Le nom du module
     * @return bool : true s'il existe, false sinon
     */
    public function isset_mods($name)
    {
        return in_array($name, $this->mod_list);
    }
}