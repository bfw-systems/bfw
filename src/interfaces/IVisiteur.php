<?php
/**
 * Interface en rapport avec la classe Visiteur
 * @author Vermeulen Maxime <bulton.fr@gmail.com>
 * @version 1.0
 */

namespace BFWInterface;

/**
 * Interface de la classe Visiteur
 * @package bfw
 */
interface IVisiteur
{
    /**
     * Accesseur get vers les attributs
     * 
     * @param string $name Le nom de l'attribut
     * 
     * @return mixed La valeur de l'attribut
     */
    public function __get($name);
    
    /**
     * Accesseur set vers les attributs
     * 
     * @param string $name Le nom de l'attribut
     * @param mixed  $val  La nouvelle valeure de l'attribut
     * @return void
     */
    public function __set($name, $val);
    
    /**
     * Constructeur
     * Récupère les infos et instancie la session
     * @return void
     */
    public function __construct();
}
?>