<?php
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
 * @param string $options  : Chaque caractère dans cette chaîne sera utilisé en tant que caractères optionnels et 
 *                           devra correspondre aux options passées, commençant par un tiret simple (-). 
 *                           Par exemple, une chaîne optionnelle "x" correspondra à l'option -x. 
 *                           Seuls a-z, A-Z et 0-9 sont autorisés.
 * @param array  $longopts : Un tableau d'options. Chaque élément de ce tableau sera utilisé comme option et devra 
 *                           correspondre aux options passées, commençant par un tiret double (--). 
 *                           Par exemple, un élément longopts "opt" correspondra à l'option --opt.
 *                           Le paramètre options peut contenir les éléments suivants :
 *                              * Caractères individuels (n'accepte pas de valeur)
 *                              * Caractères suivis par un deux-points (le paramètre nécessite une valeur)
 *                              * Caractères suivis par deux deux-points (valeur optionnelle)
 *                           Les valeurs optionnelles sont les premiers arguments après la chaîne. 
 *                           Si une valeur est requise, peu importe que la valeur soit suivi d'un espace ou non.
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
    if($colorTxt == null)
    {
        echo $msg."\n";
        return;
    }

    //Gestion cas avec couleur

    $colorStart = '';
    $colorEnd   = '';

    $styleNum = styleForShell($style);
    if($styleNum === false)
    {
        $styleNum = styleForShell('normal');
    }

    $colorTxtNum = colorForShell('white', 'txt');
    if($colorTxt !== null)
    {
        $colorTxtNumArg = colorForShell($colorTxt, 'txt');
        if($colorTxtNumArg !== false)
        {
            $colorTxtNum = $colorTxtNumArg;
        }
    }

    $colorBgNum = colorForShell('black', 'bg');
    if($colorBg !== null)
    {
        $colorBgNumArg = colorForShell($colorBg, 'bg');
        if($colorBgNumArg !== false)
        {
            $colorBgNum = $colorBgNumArg;
        }
    }

    echo "\033[".$styleNum.";".$colorBgNum.";".$colorTxtNum."m".$msg."\033[0m\n";
}

/**
 * Converti le texte d'une couleur vers son code shell
 * 
 * @param string $color : Le nom de la couleur en anglais
 * @param string $type  : (txt|bg) Si c'est pour le texte (txt) ou pour la couleur de fond (bg)
 * 
 * @return string
 */
function colorForShell($color, $type)
{
    //Possibilité d'améliorer la compléxité du script via des boucles etc...
    if($type == 'txt')
    {
        if($color == 'black')
        {
            return 30;
        }
        elseif($color == 'red')
        {
            return 31;
        }
        elseif($color == 'green')
        {
            return 32;
        }
        elseif($color == 'yellow')
        {
            return 33;
        }
        elseif($color == 'blue')
        {
            return 34;
        }
        elseif($color == 'magenta')
        {
            return 35;
        }
        elseif($color == 'cyan')
        {
            return 36;
        }
        elseif($color == 'white')
        {
            return 37;
        }
    }
    elseif($type == 'bg')
    {
        if($color == 'black')
        {
            return 40;
        }
        elseif($color == 'red')
        {
            return 41;
        }
        elseif($color == 'green')
        {
            return 42;
        }
        elseif($color == 'yellow')
        {
            return 43;
        }
        elseif($color == 'blue')
        {
            return 44;
        }
        elseif($color == 'magenta')
        {
            return 45;
        }
        elseif($color == 'cyan')
        {
            return 46;
        }
        elseif($color == 'white')
        {
            return 47;
        }
    }

    return false;
}

/**
 * Permet de convertir un style définie en anglais pour le shell
 */
function styleForShell($style)
{
    if($style == 'normal')
    {
        return 0;
    }
    elseif($style == 'bold')
    {
        return 1;
    }
    elseif($style == 'not-bold')
    {
        return 21;
    }
    elseif($style == 'underline')
    {
        return 4;
    }
    elseif($style == 'not-underline')
    {
        return 24;
    }
    elseif($style == 'blink')
    {
        return 5;
    }
    elseif($style == 'not-blink')
    {
        return 25;
    }
    elseif($style == 'reverse')
    {
        return 7;
    }
    elseif($style == 'not-reverse')
    {
        return 27;
    }

    return false;
}
