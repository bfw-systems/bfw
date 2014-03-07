<?php
/**
 * Toute la configuration du framework
 * @author Vermeulen Maxime
 * @package BFW
 */
 
//*** Config BFW ***
$DebugMode = false;
//*** Config BFW ***

//*** Base De Données ***
$bd_enabled = false; //Permet d'activer ou non la partie SQL
$bd_module = ''; //suggest package: BFW_Sql
//*** Base De Données ***

//*** Template ***
$tpl_module = ''; //suggest package: BFW_Template
//*** Template ***

//*** Controler ***
$ctr_module = ''; //suggest package: BFW_Controler
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