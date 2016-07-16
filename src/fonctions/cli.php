<?php

namespace BFW\Cli;

/**
 * Toutes les fonctions de base utilisé par le système cli
 * @author Vermeulen Maxime <bulton.fr@gmail.com>
 * @package bfw
 */

/**
 * Permet de récupérer les arguments de la console
 * Permet surtout de toujours avoir les arguments obligatoire par le système.
 * 
 * @link http://php.net/manual/fr/function.getopt.php
 * 
 * @param string $options 
 *  Chaque caractère dans cette chaîne sera utilisé en tant que caractères
 *  optionnels et devra correspondre aux options passées, commençant par un
 *  tiret simple (-). 
 *  Par exemple, une chaîne optionnelle "x" correspondra à l'option -x. 
 *  Seuls a-z, A-Z et 0-9 sont autorisés.
 * 
 * @param array  $longopts 
 *  Un tableau d'options. Chaque élément de ce tableau sera utilisé comme
 *  option et devra correspondre aux options passées, commençant par
 *  un tiret double (--). 
 *  Par exemple, un élément longopts "opt" correspondra à l'option --opt.
 *  Le paramètre options peut contenir les éléments suivants :
 *     * Caractères individuels (n'accepte pas de valeur)
 *     * Caractères suivis par : (le paramètre nécessite une valeur)
 *     * Caractères suivis par :: (valeur optionnelle)
 *  Les valeurs optionnelles sont les premiers arguments après la chaîne. 
 *  Si une valeur est requise, peu importe que la valeur soit
 *  suivi d'un espace ou non.
 * 
 * @return array
 */
function getCliParams($options, $longopts = array())
{
    $longopts = array_merge($longopts, array('type_site::'));
    $opt      = getopt('f:'.$options, $longopts);

    unset($opt['f']);

    return $opt;
}

/**
 * Permet de facilement afficher un message dans la console
 * 
 * @param string $msg : Le message à afficher
 */
function displayMsg($msg, $colorTxt = null, $colorBg = null, $style = 'normal')
{
    if ($colorTxt == null) {
        echo $msg."\n";
        return;
    }

    //Gestion cas avec couleur
    $styleNum = styleForShell($style);
    if ($styleNum === false) {
        $styleNum = styleForShell('normal');
    }

    $colorTxtNum = colorForShell('white', 'txt');
    if ($colorTxt !== null) {
        $colorTxtNumArg = colorForShell($colorTxt, 'txt');

        if ($colorTxtNumArg !== false) {
            $colorTxtNum = $colorTxtNumArg;
        }
    }

    $colorBgNum = colorForShell('black', 'bg');
    if ($colorBg !== null) {
        $colorBgNumArg = colorForShell($colorBg, 'bg');

        if ($colorBgNumArg !== false) {
            $colorBgNum = $colorBgNumArg;
        }
    }

    echo "\033[".$styleNum.";".$colorBgNum.";"
        .$colorTxtNum
        ."m".$msg."\033[0m\n";
}

/**
 * Converti le texte d'une couleur vers son code shell
 * 
 * @param string $color : Le nom de la couleur en anglais
 * @param string $type  : (txt|bg) Si c'est pour le texte (txt)
 *                          ou pour la couleur de fond (bg)
 * 
 * @return string
 */
function colorForShell($color, $type)
{
    $colorList = [
        'black'   => 0,
        'red'     => 1,
        'green'   => 2,
        'yellow'  => 3,
        'blue'    => 4,
        'magenta' => 5,
        'cyan'    => 6,
        'white'   => 7
    ];

    if (!in_array($color, $colorList)) {
        throw new Exception('Color '.$color.' is not available in function.');
    }

    if ($type === 'txt') {
        return $colorList[$color] + 30;
    } elseif ($type === 'bg') {
        return $colorList[$color] + 40;
    }

    return false;
}

/**
 * Permet de convertir un style définie en anglais pour le shell
 */
function styleForShell($style)
{
    $styleList = [
        'normal'        => 0,
        'bold'          => 1,
        'not-bold'      => 21,
        'underline'     => 4,
        'not-underline' => 24,
        'blink'         => 5,
        'not-blink'     => 25,
        'reverse'       => 7,
        'not-reverse'   => 27
    ];

    if (!in_array($style, $styleList)) {
        return false;
    }

    return $styleList[$style];
}
