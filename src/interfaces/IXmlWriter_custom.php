<?php
/**
 * Interface en rapport avec la classe XmlWriter_custom
 * @author Vermeulen Maxime <bulton.fr@gmail.com>
 * @version 1.0
 */

namespace BFWInterface;

/**
 * Interface de la classe XmlWriter_custom
 * @package bfw
 */
interface IXmlWriter_custom
{
    /**
     * Constructeur
     */
    public function __construct();
    
    /**
     * Créer une balise avec ces attributs (les balises principales, avec d'autres balise dedans en général)
     * 
     * @param string $element    Nom de la balise
     * @param array  $attributes (default: array()) Les attributs de la balise
     */
    public function push($element, $attributes = array());
    
    /**
     * Créer une balise simple, avec ces attributs et son contenu
     * 
     * @param string $element    Nom de la balise
     * @param string $content    Le contenu de la balise
     * @param array  $attributes (default: array()) Les attributs de la balise
     */ 
    public function element($element, $content, $attributes = array());
    
    /**
     * Créer une balise avec ![CDATA pour mettre du xhtml dedans
     * 
     * @param string $element    Nom de la balise
     * @param string $content    Le contenu de la balise
     * @param array  $attributes (default: array()) Les attributs de la balise
     */
    public function element_cdata($element, $content, $attributes = array());
    
    /**
     * Créer une balise autofermante
     * 
     * @param string $element    Nom de la balise
     * @param array  $attributes (default: array()) Les attributs de la balise
     */
    public function emptyelement($element, $attributes = array());
    
    /**
     * Ferme une balise ouverte avec push()
     */
    public function pop();
    
    /**
     * Retourne le résultat du xml
     * 
     * @return string Le xml
     */
    public function getXml();
}
?>