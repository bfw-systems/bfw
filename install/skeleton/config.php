<?php
/**
 * Toute la configuration du framework
 * @author Vermeulen Maxime <bulton.fr@gmail.com>
 * @package bfw
 */
 
//*** Config BFW ***
$DebugMode = true; //True pour du dev (affiche toutes les erreurs), false pour de la prod (n'affiche rien)
$myVendorName = 'vendor'; //Le nom du dossier où sont les libs de composer (default: "vendor")
//*** Config BFW ***

//*** Base De Données ***
$bd_enabled = false; //Permet d'activer ou non la partie SQL
$bd_module = ''; //suggest package: bfw-sql
//*** Base De Données ***

//*** Template ***
$tpl_module = ''; //suggest package: bfw-template
//*** Template ***

//*** Controler ***
$ctr_module = ''; //suggest package: bfw-controller
$ctr_class = false;
$ctr_defaultMethode = 'index'; //La méthode à appeler si aucune n'est définie dans l'url (pour tous les contrôleurs)
//*** Controler ***

//*** Adresse ***
$base_url = 'http://localhost';
//*** Adresse ***

//*** Controler par défaut ***
$DefaultController = 'index'; //Il s'agit du modele de page qui sera utilisé comme page index du site
//*** Controler par défaut ***
?>