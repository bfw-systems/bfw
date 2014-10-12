<?php
/**
 * Interface en rapport avec la classe CreateClasse
 * @author Vermeulen Maxime <bulton.fr@gmail.com>
 * @version 1.0
 */

namespace BFWInterface;

/**
 * Interface de la classe CreateClasse
 * @package bfw
 */
interface ICreateClasse
{
    /**
     * Constructeur
     * 
     * @param string $nom     Le nom de la futur classe
     * @param array  $options Les options de la classe
     * @return void
     */
    public function __construct($nom, $options=array());
    
    /**
     * Retourne le contenu de la futur classe
     * 
     * @return string La futur classe
     */
    public function getFile();
    
    /**
     * Créer un attribut à la nouvelle classe
     * 
     * @param string $nom Le nom de l'attribut
     * @param array  $opt (default: array()) Les options de l'attribut : 
     * - string porter         : La porté de l'attribut. Par défaut à "protected"
     * - bool   get            : Si un get doit être créé. Par défaut à true
     * - bool   set            : Si un set doit être créé. Par défaut à true
     * - string type           : Le type de l'attribut. Par défaut aucun type prédéfini.
     * - mixed  default        : Valeur par défaut de l'attribut.
     * - bool   default_string : Permet d'indiqué que la valeur par défaut est de type string (met des ' autour.)
     * 
     * @TODO : Enlever default_string et repérer dynamiquement le type de la valeur.
     * 
     * @return bool True si réussi, False si existe déjà.
     */
    public function createAttribut($nom, $opt=array());
    
    /**
     * Créer une nouvelle méthode pour la classe
     * 
     * @todo Gestion des arguments pour la méthode
     * 
     * @param string $nom    Le nom de la méthode
     * @param string $porter La porté de la méthode. Par défaut private.
     * @return void
     */
    public function createMethode($nom, $porter='private');
    
    /**
     * Lance la génération de la classe.
     * 
     * @return string La classe généré
     */
    public function genere();
}
?>