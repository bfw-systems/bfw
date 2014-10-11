<?php
/**
 * Interface en rapport avec la classe Modules
 * @author Vermeulen Maxime <bulton.fr@gmail.com>
 * @version 1.0
 */

namespace BFWInterface;

/**
 * Interface de la classe Modules
 * @package bfw
 */
interface IModules
{
    /**
     * Constructeur
     * @return void
     */
    public function __construct();
    
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
    public function newMod($name, $params=array());
    
    /**
     * Permet de vérifier si un module existe
     * 
     * @param string $name Le nom du module
     * 
     * @return bool true s'il existe, false sinon
     */
    public function exists($name);
    
    /**
     * Permet de vérifier si un module est chargé
     * 
     * @param string $name Le nom du module
     * 
     * @return bool true s'il est chargé, false sinon
     */
    public function isLoad($name);
    
    /**
     * Ajoute le path pour un module donné
     * 
     * @param string $name Le nom du module
     * @param string $path Le chemin réel du module
     * 
     * @throws \Exception Le module n'existe pas
     * @return void
     */
    public function addPath($name, $path);
    
    /**
     * Liste des modules à charger à un moment précis.
     * 
     * @param string $timeToLoad Le temps auquel doivent être chargé les modules
     * 
     * @throws \Exception Erreur au chargement d'un module
     * 
     * @return array Liste des modules à charger
     */
    public function listToLoad($timeToLoad);
    
    /**
     * Liste les modules non chargé
     * 
     * @param bool $regen (default: false) Permet de regénérer la liste ou non
     * 
     * @return array Liste des modules non chargé
     */
    public function listNotLoad($regen=false);
    
    /**
     * Permet de savoir si des modules n'ont pas pu être chargé
     * 
     * @return bool True si des modules n'ont pas pu être chargé, false sinon.
     */
    public function isModulesNotLoad();
    
    /**
     * Retourne les infos sur un module
     * 
     * @param string $name Le nom du module dont on veux les infos
     * 
     * @throws \Exception Le module n'existe pas.
     * 
     * @return array Les infos sur le module
     */
    public function getModuleInfos($name);
}
?>