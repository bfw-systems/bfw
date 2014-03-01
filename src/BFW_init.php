<?php
/**
 * Gère tout le noyau du framework. Est appelé sur chaque page.
 * @author Vermeulen Maxime
 * @package BFW
 */

ob_start(); //Tamporisation du tampon de sortie html. Permet que le code html ne sorte qu'à la fin et non petit à petit (permet d'utiliser les fonctions changeant les headers ou cookie à n'importe quel moment par exemple)

//Définition des chemins d'accès
if(!isset($rootPath))
{
    $rootPath = $_SERVER['DOCUMENT_ROOT'].'/';
}

//Fichier de config
require_once($rootPath.'config.php');
//Fichier de config

if((isset($noSession) && $noSession == false) || !isset($noSession))
{ 
    session_set_cookie_params(0); //permet de detruire le cookie de session si le navigateur quitte
    session_start(); //Ouverture des sessions
}

//Class Loader
$loader = require($rootPath.'vendor/autoload.php');

$loader->add('controller', $rootPath.'controllers');
$loader->add('modules',    $rootPath.'modules');
$loader->add('modeles',    $rootPath.'modeles');
//Class Loader

//Instancie la classe Kernel
$BFWKernel = new BFW\Kernel;
$BFWKernel->set_debug($DebugMode);
header('Content-Type: text/html; charset=utf-8'); //On indique un header en utf-8 de type html

//Inclusion fonction
$dir = opendir($rootPath.'kernel/fonctions'); //Ouverture du dossier fonctions se trouvant à la racine
$dir_arr = array('.', '..'); //Les fichiers & dossiers à ignorer à la lecture

while(false !== ($file = readdir($dir))) //Si on a un fichier
{
    //Si c'est un fichier, et que ce n'est pas une sauvegarde auto, on inclu.
    if(!in_array($file, $dir_arr) && !preg_match("#~$#", $file))
    {
        require_once($rootPath.'kernel/fonctions/'.$file);
    }
}

closedir($dir); //Fermeture du dossier
unset($dir, $dir_arr, $file); //Suppression des variables
//Fin Inclusion fonction

//Sql
if($bd_enabled)
{
    if(file_exists($rootPath.'modules/'.$bd_module.'/kernel_init.php'))
    {
        require_once($rootPath.'modules/'.$bd_module.'/kernel_init.php');
    }
    else {throw new \Exception('Module '.$db_module.' not found.');}
}
//Sql

//Template
if(file_exists($rootPath.'modules/'.$tpl_module.'/kernel_init.php'))
{
    require_once($rootPath.'modules/'.$tpl_module.'/kernel_init.php');
}
else {throw new \Exception('Module '.$tpl_module.' not found.');}
//Template

//Serveur memcache (permet de stocker des infos direct sur la ram avec ou sans limite dans le temps)
$Memcache = new BFW\Ram;
//Fin Serveur memcache

//Inclusions des modules
$Modules = new BFW\Modules;

$dir = opendir($rootPath.'modules');
$dir_arr = array('.', '..', '.htaccess');

while(false !== ($file = readdir($dir)))
{
    //Si le fichier existe, on inclus le fichier principal du mod
    if(file_exists($rootPath.'modules/'.$file.'/'.$file.'.php'))
    {
        //echo 'Inclus module : '.$file.'<br/>';
        require_once($rootPath.'modules/'.$file.'/'.$file.'.php');
    }
}
closedir($dir);
unset($dir, $dir_arr, $file);
//Inclusions des modules

//Visiteur
$Visiteur = new BFW\Visiteur;
//Visiteur

//Chargement des modules
$time = 'after_visiteur';
if(array_key_exists($time, $Modules->mod_load))
{
    if(is_array($Modules->mod_load[$time]))
    {
        foreach($Modules->mod_load[$time] as $name)
        {
            require_once($rootPath.'modules/'.$name.'/inclus.php');
        }
    }
}
//Chargement des modules

//Chemin
/**
 * @name path_cache : Chemin vers la racine du dossier cache
 */
define('path_cache', $rootPath.'cache/');

/**
 * @name path_controler : Chemin vers la racine du dossier controlers
 */
define('path_controler', $rootPath.'controlers/');

/**
 * @name path_modeles : Chemin vers la racine du dossier modeles
 */
define('path_modeles', $rootPath.'modeles/');

/**
 * @name path_modules : Chemin vers la racine du dossier modules
 */
define('path_modules', $rootPath.'modules/');

/**
 * @name path_view : Chemin vers la racine du dossier view
 */
define('path_view', $rootPath.'view/');

/**
 * @name path_view : Chemin vers la racine du dossier web
 */
define('path_view', $rootPath.'web/');

/**
 * @name path : Chemin vers la racine
 */
define('path', $rootPath);
//Chemin

//Chargement des modules
$time = 'end_kernel';
if(array_key_exists($time, $Modules->mod_load))
{
    if(is_array($Modules->mod_load[$time]))
    {
        foreach($Modules->mod_load[$time] as $name)
        {
            require_once($path.'modules/'.$name.'/inclus.php');
        }
    }
}
//Chargement des modules
?>