<?php
/**
 * Interface en rapport avec la classe Cache
 * @author Vermeulen Maxime <bulton.fr@gmail.com>
 * @version 1.0
 */

namespace BFWInterface;

/**
 * Interface de la classe Cache
 * @package bfw
 */
interface ICache
{
    /**
     * Constructeur
     * 
     * @param string $controler Le controleur qu'on doit lire
     */
    public function __construct($controler);
    
    /**
     * Lance la création du cache
     */
    public function run();
    
    /**
     * Accesseur vers l'attribut $controler
     * 
     * @param string $controler Le controleur qu'on doit lire
     */
    public function set_controler($controler);
}
?>