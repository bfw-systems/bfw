<?php

//--- Config pour le kernel BFW ---
$rootPath = realpath(__DIR__.'/').'/';

//Fake $_SERVER pour les scripts créé pour du apache
$_SERVER['HTTP_HOST']      = '';
$_SERVER['SERVER_NAME']    = '';
$_SERVER['REQUEST_URI']    = '/';
$_SERVER['REQUEST_METHOD'] = 'GET';

define('cliMode', true);
//--- Config pour le kernel BFW ---

if(PHP_SAPI != 'cli')
{
    echo 'Fichier utilisable en mode CLI uniquement.';
    exit;
}

require_once('vendor/bulton-fr/bfw/src/bootstrap.php');
