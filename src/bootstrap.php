<?php
/**
 * Fichier bootstrap. Détermination du controller.
 * @author Vermeulen Maxime
 * @package BFW
 */

$rootPath = $_SERVER['DOCUMENT_ROOT'].'/';
require_once($rootPath.'config.php');

if(substr($base_url, -1) == '/')
{
    $base_url = substr($base_url, 0, -1);
}

/** Gestion à faire à cause de l'url rewriting qui prend en compte tous les fichiers, css/js/images y compris. **/
$requestAll = $_SERVER['REQUEST_URI'];

$requestExplode = explode('?', $requestAll);
$request = $requestExplode[0];
$error = null;

if(!($request == '/index.php' || $request == '/'))
{
    $ext = null;
    if(strpos($request, '.') !== false)
    {
        $ext = substr($request, (strpos($request, '.')+1));
    }
    
    $file = $request;
    
    if(substr($exp, 0, 3) != 'php' && !is_null($ext))
    {
        $pathFile = '';
        
        if(file_exists($rootPath.'web/'.$file)) //Un fichier mit dans /web
        {
            $pathFile = $rootPath.'web';
        }
        elseif(strpos($request, '/modules/') !== false) //Un fichier non php mit dans un modules.
        {
            $modulePos = strpos($request, '/modules/')+9;
            $moduleName = substr($request, $modulePos, (strpos($request, '/', $modulePos)-$modulePos));
            
            if(!file_exists('modules/'.$moduleName.'/externe.php'))
            {
                $error = 403;
            }
        }
        else
        {
            $error = 404;
        }
        
        if(is_null($error))
        {
            if($exp == 'css')
            {
                header('Content-type: text/css');
            }
            elseif($exp == 'js')
            {
                header('Content-type: text/javascript');
            }
            else
            {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                header('Content-type: '.finfo_file($finfo, $pathFile.$file));
            }
            
            echo file_get_contents($pathFile.$file);
        }
    }
    elseif(substr($exp, 0, 3) == 'php' && !is_null($ext))
    {
        $modulePos = strpos($request, '/modules/')+9;
        $moduleName = substr($request, $modulePos, (strpos($request, '/', $modulePos)-$modulePos));
        
        if(!file_exists($rootPath.'modules/'.$moduleName.'/externe.php'))
        {
            $afterModuleName = $modulePos+strlen($moduleName)+1;
            require_once('modules/'.$moduleName.'/externe.php');
            
            if(in_array($afterModuleName, $moduleExterneAuthorized))
            {
                require_once($rootPath.$request);
            }
            else {$error = 403;}
        }
        else {$error = 404;}
    }
    //Pas de else car aucune extension peut dire l'index d'un controller.
}

require_once(__DIR__.'BFW_init.php');

if(!is_null($error))
{
    Errorview($error);
}
else
{
    if(file_exists($rootPath.'modules/'.$ctr_module.'/kernel_init.php'))
    {
        require_once($rootPath.'modules/'.$ctr_module.'/kernel_init.php');
    }
}
?>