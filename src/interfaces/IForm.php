<?php
/**
 * Interface en rapport avec la classe Form
 * @author Vermeulen Maxime <bulton.fr@gmail.com>
 * @version 1.0
 */

namespace BFWInterface;

/**
 * Interface de la classe Form
 * @package bfw
 */
interface IForm
{
    /**
     * Constructeur
     * 
     * @param string $idForm L'id du formulaire
     * @return void
     */
    public function __construct($idForm=null);
    
    /**
     * Accesseur set sur id_form
     * 
     * @param string $idForm L'id du formulaire
     * @return void
     */
    public function set_idForm($idForm);
    
    /**
     * Permet de créer un token pour le formulaire
     * 
     * @return string Le token à mettre dans un champ input de type hidden.
     */
    public function create_token();
    
    /**
     * Permet de vérifier si le token est correct
     * 
     * @return bool True si le toke est bon, false sinon.
     */
    public function verif_token();
}
?>