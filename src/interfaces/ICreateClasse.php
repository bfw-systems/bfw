<?php
/**
 * Interface en rapport avec la classe CreateClasse
 * @author Vermeulen Maxime
 * @version 1.0
 */

namespace BFWInterface;

/**
 * Interface de la classe CreateClasse
 * @package BFW
 */
interface ICreateClasse
{
    /**
     * Constructeur
     * 
     * @param string $nom     : Le nom de la futur classe
     * @param array  $options : Les options de la classe
     */
    public function __construct($nom, $options=array());
    
    /**
     * Retourne le contenu de la futur classe
     * 
     * @return string : La futur classe
     */
    public function get_file();
    
    /**
     * Créer un attribut à la nouvelle classe
     * 
     * @param string $nom : Le nom de l'attribut
     * @param array  $opt : Les options de l'attribut (porter/get/set). Par défaut à (protected/true/true).
     *                      Il est possible de déclarer un type via l'option "type". Par défaut à rien.
     *                      Et de déclarer une valeur par défaut via l'option "default".
     *                      Si la valeur par défaut est un string, il faut déclarer l'option "default_string" 
     *                      qui ajoutera des ' autour de la valeur par défaut.
     * 
     * @return bool : True si réussi, False si existe déjà.
     */
    public function createAttribut($nom, $opt=array());
    
    /**
     * Créer une nouvelle méthode pour la classe
     * 
     * @todo Gestion des arguments pour la méthode
     * 
     * @param string $nom    : Le nom de la méthode
     * @param string $porter : La porté de la méthode. Par défaut private.
     */
    public function createMethode($nom, $porter='private');
    
    /**
     * Lance la génération de la classe.
     * 
     * @return string : La classe généré
     */
    public function genere();
}
?>