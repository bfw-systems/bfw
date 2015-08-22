<?php
/**
 * Fichier bootstrap. Détermination du controller.
 * @author Vermeulen Maxime <bulton.fr@gmail.com>
 * @package bfw
 */

if(!isset($rootPath))
{
    if(!isset($myVendorName))
    {
        $myVendorName = 'vendor';
    }
    
    $rootPath = substr(__DIR__, 0, strpos(__DIR__, $myVendorName));
}

require_once($rootPath.'configs/bfw_config.php');

//Récupération de la page appelé avec le host (sans les paramètre)
$requestAll = $_SERVER['REQUEST_URI'];
$requestExplode = explode('?', $requestAll);
$request = $requestExplode[0];

//Si multi-domain, recherche du domaine courant
$base_url_config = $base_url;
if(is_array($base_url))
{
    foreach($base_url as $url)
    {
        if(strpos($url, $_SERVER['SERVER_NAME']) !== false)
        {
            $base_url = $url;
            break;
        }
    }
    
    //Si pas trouvé, on prend le premier domaine
    if(is_array($base_url)) {$base_url = $base_url[0];}
}

if(substr($base_url, -1) == '/')
{
    $base_url = substr($base_url, 0, -1);
}

/** Gestion à faire à cause de l'url rewriting qui prend en compte tous les fichiers, css/js/images y compris. **/
$error = null;
$exBaseUrl = explode('/', $base_url);

if(count($exBaseUrl) > 3)
{
    unset($exBaseUrl[0], $exBaseUrl[1], $exBaseUrl[2]);
    $imBaseUrl = '/'.implode('/', $exBaseUrl);
    $lenBaseUrl = strlen($imBaseUrl);
    
    $request = substr($request, $lenBaseUrl);
}

if(!($request == '/index.php' || $request == '/'))
{
    $ext = null;
    if(strpos($request, '.') !== false)
    {
        $ext = substr($request, (strrpos($request, '.')+1));
    }
    
    $file = $request;
    
    if(substr($ext, 0, 3) != 'php' && !is_null($ext))
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
            if($ext == 'css')
            {
                header('Content-type: text/css');
            }
            elseif($ext == 'js')
            {
                header('Content-type: text/javascript');
            }
            else
            {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                header('Content-type: '.finfo_file($finfo, $pathFile.$file));
            }
            
            echo file_get_contents($pathFile.$file);
            exit;
        }
    }
    elseif(substr($ext, 0, 3) == 'php' && !is_null($ext))
    {
        $modulePos = strpos($request, '/modules/')+9;
        $moduleName = substr($request, $modulePos, (strpos($request, '/', $modulePos)-$modulePos));
        
        if(file_exists($rootPath.'modules/'.$moduleName.'/externe.php'))
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

require_once(__DIR__.'/BFW_init.php');

if(!is_null($error))
{
    Errorview($error, false);
}
else
{
    if(kernelModuleLoad_ctr_test == true)
    {
        require_once(kernelModuleLoad_ctr_path);
    }
}
