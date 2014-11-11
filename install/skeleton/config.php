<?php
/**
 * Toute la configuration du framework
 * @author Vermeulen Maxime <bulton.fr@gmail.com>
 * @package bfw
 */
 
//*** Config BFW ***
$DebugMode    = true;     //True pour du dev (affiche toutes les erreurs), false pour de la prod (n'affiche rien)
$myVendorName = 'vendor'; //Le nom du dossier où sont les libs de composer (default: "vendor")
//*** Config BFW ***

//*** Memcache ***
$memcache_enabled = false;       //Permet d'activer ou non memcached
$memcache_host    = 'localhost'; //L'hote de connexion à memcached
$memcache_port    = 11211;       //Le port de connexion à memcached
//*** Memcache ***

//*** Base De Données ***
$bd_enabled = false; //Permet d'activer ou non la partie SQL
$bd_module  = '';
//*** Base De Données ***

//*** Template ***
$tpl_module = '';
//*** Template ***

//*** Controler ***
$ctr_module = '';
//*** Controler ***

//*** Adresse ***
$base_url = 'http://localhost';
//*** Adresse ***

//*** Controler par défaut ***
$DefaultController = 'index'; //Il s'agit du modele de page qui sera utilisé comme page index du site
//*** Controler par défaut ***
?>