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
$dir = opendir(__DIR__.'/fonctions'); //Ouverture du dossier fonctions se trouvant à la racine
$dir_arr = array('.', '..'); //Les fichiers & dossiers à ignorer à la lecture

while(false !== ($file = readdir($dir))) //Si on a un fichier
{
    //Si c'est un fichier, et que ce n'est pas une sauvegarde auto, on inclu.
    if(!in_array($file, $dir_arr) && !preg_match("#~$#", $file))
    {
        require_once(__DIR__.'/fonctions/'.$file);
    }
}

closedir($dir); //Fermeture du dossier
unset($dir, $dir_arr, $file); //Suppression des variables
//Fin Inclusion fonction

//Load module
$modulesToLoad = array();

//SQL
if($bd_enabled)
{
    $modulesToLoad['bd'] = array('name' => $bd_module, 'action' => 'load');
}

//Template
$modulesToLoad['tpl'] = array('name' => $tpl_module, 'action' => 'load');

//Controller
$modulesToLoad['ctr'] = array('name' => $ctr_module, 'action' => 'test');

foreach($modulesToLoad as $key => $moduleLoad)
{
    $pathToModule = $rootPath.'modules/'.$moduleLoad['name'];
    if(!empty($pathToModule))
    {
        if(is_link($pathToModule))
        {
            $pathToModule = readlink($pathToModule);
            
            if(file_exists($pathToModule.'/bfw_modules_info.php'))
            {
                require_once($pathToModule.'/bfw_modules_info.php');
                
                if(!empty($modulePath))
                {
                    $pathToModule .= '/'.$modulePath;
                }
            }
        }
        
        if(!file_exists($pathToModule.'/kernel_init.php'))
        {
            define('kernelModuleLoad_'.$key.'_test', false);
            throw new \Exception('Module '.$moduleLoad['name'].' not found.');
        }
        
        define('kernelModuleLoad_'.$key.'_test', true);
        define('kernelModuleLoad_'.$key.'_path', $pathToModule.'/kernel_init.php');
        
        if($moduleLoad['action'] == 'load')
        {
            require_once($pathToModule.'/kernel_init.php');
        }
    }
    else
    {
        define('kernelModuleLoad_'.$key.'_test', false);
    }
}
//Load module

//Serveur memcache (permet de stocker des infos direct sur la ram avec ou sans limite dans le temps)
$Memcache = new BFW\Ram;
//Fin Serveur memcache

//Inclusions des modules
$Modules = new BFW\Modules;

/**
 * @name modulesLoadTime_Module : Pour charger les modules directement à leurs lecture
 */
define('modulesLoadTime_Module', 'module');

/**
 * @name modulesLoadTime_Visiteur : Pour changer les modules après l'initialisation et la récupération des infos visiteur
 */
define('modulesLoadTime_Visiteur', 'visiteur');

/**
 * @name modulesLoadTime_EndInit : Pour charger les modules une fois que le framework à fini de s'initialiser, avant l'appel au controleur.
 */
define('modulesLoadTime_EndInit', 'endInit');

$dir = opendir($rootPath.'modules');
$dir_arr = array('.', '..', '.htaccess');

while(false !== ($file = readdir($dir)))
{
    $path = $rootPath.'modules/'.$file;
    
    if(is_link($rootPath.'modules/'.$file))
    {
        $path = readlink($pathToModule);
    }
    
    //Si le fichier existe, on inclus le fichier principal du module
    if(file_exists($path.'/'.$file.'.php'))
    {
        require_once($path.'/'.$file.'.php');
        $Modules->addPath($file, $path);
    }
}
closedir($dir);
unset($dir, $dir_arr, $file);

$modulesToLoad = $Modules->listToLoad(modulesLoadTime_Module);
if(is_array($modulesToLoad) && count($modulesToLoad) > 0)
{
    foreach($modulesToLoad as $moduleToLoad)
    {
        $infos = $Modules->getModuleInfos($moduleToLoad);
        $path = $infos['path'];
        
        require_once($path.'/inclus.php');
    }
}
//Inclusions des modules

//Visiteur
$Visiteur = new BFW\Visiteur;
//Visiteur

//Chargement des modules
$modulesToLoad = $Modules->listToLoad(modulesLoadTime_Visiteur);
if(is_array($modulesToLoad) && count($modulesToLoad) > 0)
{
    foreach($modulesToLoad as $moduleToLoad)
    {
        $infos = $Modules->getModuleInfos($moduleToLoad);
        $path = $infos['path'];
        
        require_once($path.'/inclus.php');
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
define('path_web', $rootPath.'web/');

/**
 * @name path : Chemin vers la racine
 */
define('path', $rootPath);
//Chemin

//Chargement des modules
$modulesToLoad = $Modules->listToLoad(modulesLoadTime_EndInit);
if(is_array($modulesToLoad) && count($modulesToLoad) > 0)
{
    foreach($modulesToLoad as $moduleToLoad)
    {
        $infos = $Modules->getModuleInfos($moduleToLoad);
        $path = $infos['path'];
        
        require_once($path.'/inclus.php');
    }
}
//Chargement des modules
?>