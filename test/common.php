<?php
/**
 * Actions à effectuer lors de l'initialisation du module par le framework.
 * @author Vermeulen Maxime <bulton.fr@gmail.com>
 * @package bfw-sql
 * @version 1.0
 */

//Disons que nous somme à l'origine du projet
//Je déclare une variable $rootPath à ici pour me simplifier mes inclusions.
$rootPath = realpath(__DIR__.'/../').'/';

require(__DIR__.'/Psr4AutoloaderClass.php');

$loader = new Psr4AutoloaderClass;
$loader->register();
$loader->addNamespace('BFW\\', __DIR__.'/../src/classes/');
$loader->addNamespace('BFWInterface\\', __DIR__.'/../src/interfaces/');
$loader->addNamespace('BFW\tests\units\\', __DIR__.'/classes/');

//---- Load config ----
$forceConfig = true;
require_once($rootPath.'install/skeleton/config.php');
$base_url = 'http://test.bulton.fr/bfw-v2/';

//---- Load BFW Kernel ----
require_once($rootPath.'src/BFW_init.php');

error_reporting(E_ALL);
ini_set('display_errors', 'On');
ini_set('html_errors', true);
