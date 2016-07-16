<?php

namespace BFW;

use \Exception;

/**
 * Toutes les fonctions de base utilisé un peu partout dans les scripts
 * @author Vermeulen Maxime <bulton.fr@gmail.com>
 * @package bfw
 */

/**
 * Permet d'hasher une chaine de texte, par exemple un mot de passe
 * 
 * @param string $val : la chaine à haser
 * 
 * @return string la chaine hashé
 */
function hashage($val)
{
    return substr(hash('sha256', md5($val)), 0, 32);
}

function securiseKnownTypes($data, $type)
{
    //--- Gestion de type de data ---
    $filterType = 'text';

    if ($type === 'int' || $type === 'integer') {
        $filterType = FILTER_VALIDATE_INT;
    } elseif ($type === 'float' || $type === 'double') {
        $filterType = FILTER_VALIDATE_FLOAT;
    } elseif ($type === 'bool' || $type === 'boolean') {
        $filterType = FILTER_VALIDATE_BOOLEAN;
    } elseif ($type === 'email') {
        $filterType = FILTER_VALIDATE_EMAIL;
    }
    //--- FIN Gestion de type de data ---

    if ($filterType === 'text') {
        throw new Exception('Unknown type');
    }

    return filter_var($data, $filterType);
}

function securise($data, $type, $htmlentities)
{
    if (is_array($data)) {
        foreach ($data as $key => $val) {
            unset($data[$key]);

            $key = securise($key, true);
            $val = securise($val, $htmlentities);

            $data[$key] = $val;
        }

        return $data;
    }

    try {
        return securiseKnownTypes($data, $type);
    } catch (Exception $ex) {
        if ($ex->getMessage() !== 'Unknown type') {
            throw new Exception($ex->getCode(), $ex->getMessage());
        }
        //Else : Use securise text type
    }

    $sqlSecureMethod = getSqlSecureMethod();
    if ($sqlSecureMethod !== false) {
        $data = $sqlSecureMethod($data);
    } else {
        $data = addslashes($data);
    }

    if ($htmlentities === false) {
        $data = htmlentities($data, ENT_COMPAT | ENT_HTML401, 'UTF-8');
    }

    return $data;
}

function getSqlSecureMethod()
{
    $app = \BFW\Application::getInstance();
    $fct = $app->getConfig('sqlSecureMethod');

    $callableName = '';
    if (!is_callable($fct, true, $callableName)) {
        return false;
    }

    return $callableName;
}

/**
 * Fonction de création de cookie
 * 
 * @param string $name   : le nom du cookie
 * @param string $value  : la valeur du cookie
 * @param int    $expire : (default: 1209600) durée du cookie en seconde.
 *                          Par défault sur 2 semaines
 * 
 * @return void
 */
function createCookie($name, $value, $expire = 1209600)
{
    $expireTime = time() + $expire; //Durée d'existance du cookie
    setcookie($name, $value, $expireTime);
}

/**
 * Fonction nl2br refait.
 * Celle de php AJOUTE <br/> APRES les \n, il ne les remplace pas.
 * 
 * @param string $str : le texte à convertir
 * 
 * @return string : le texte converti
 */
function nl2brReplace($str)
{
    return str_replace("\n", '<br>', $str);
}

/**
 * Permet de rediriger une page
 * 
 * @param string $page      : la page vers laquelle rediriger
 * @param bool   $permanent : If it's a permanent redirection or not
 */
function redirection($page, $permanent = false)
{
    $httpStatus = 302;
    if ($permanent === true) {
        $httpStatus = 301;
    }

    http_response_code($httpStatus);
    header('Location: '.$page);
    exit;
}

function getSecurisedKeyInArray(&$array, $key, $type, $htmlentities = false)
{
    if (!isset($array[$key])) {
        throw new Exception('The key '.$key.' not exist');
    }

    return securise(trim($array[$key]), $type, $htmlentities);
}

function getSecurisedPostKey($key, $type, $htmlentities = false)
{
    return getSecurisedKeyInArray($_POST, $key, $type, $htmlentities);
}

function getSecurisedGetKey($key, $type, $htmlentities = false)
{
    return getSecurisedKeyInArray($_GET, $key, $type, $htmlentities);
}

/**
 * Permet de savoir si le mail passé en paramètre est un e-mail valide ou non
 * 
 * @param string $mail : L'adresse e-mail à vérifier
 * 
 * @return integer : 
 */
function validMail($mail)
{
    return securise($mail, 'email');
}

/**
 * Vérifie le type d'un ensemble de variable
 * 
 * @param array $vars : Les variables à vérifier 
 *  array(array('type' => 'monType', 'data' => 'mesData), array(...)...)
 * 
 * @return bool
 */
function verifTypeData($vars)
{
    if (!is_array($vars)) {
        return false;
    }

    foreach ($vars as $var) {
        if (!is_array($var)) {
            return false;
        }

        if (!(!empty($var['type']) && isset($var['data']))) {
            return false;
        }

        if (!is_string($var['type'])) {
            return false;
        }

        if ($var['type'] === 'int') {
            $var['type'] = 'integer';
        }

        if ($var['type'] === 'float') {
            $var['type'] = 'double';
        }

        if (gettype($var['data']) !== $var['type']) {
            return false;
        }
    }

    return true;
}

/**
 * Retourne l'instance courrante du kernel. La créé si elle n'est pas trouvé.
 * 
 * @return \BFW\Kernel
 */
function getApplication()
{
    return \BFW\Application::getInstance();
}

/**
 * Détermine si la session est démarré
 * 
 * @link http://fr2.php.net/manual/fr/function.session-status.php#113468
 * 
 * @return bool
 */
function sessionIsStarted()
{
    if (PHP_SAPI === 'cli') {
        return false;
    }

    if (session_status() === PHP_SESSION_ACTIVE) {
        return true;
    }

    return false;
}
