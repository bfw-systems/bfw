<?php
/**
 * Cli exemple file
 */

use \BFW\Helpers\Cli;

Cli::displayMsgNL('CLI Exemple file', 'green');

//Get parameters passed to cli
$opt = getopt('f:vhp', array('version::', 'help::', 'parameters::'));

//version parameter
if (array_key_exists('v', $opt) || array_key_exists('version', $opt)) {
    Cli::displayMsgNL('v0.2.2');
}

//Display all detected parameters passed to cli
if (array_key_exists('p', $opt) || array_key_exists('parameters', $opt)) {
    Cli::displayMsgNL(print_r($opt, true));
}

//Help message
if (array_key_exists('h', $opt) || array_key_exists('help', $opt)) {
    Cli::displayMsgNL('');
    Cli::displayMsgNL('Helping Informations : Parameters script');
    Cli::displayMsgNL('* -v --version : Version of test script');
    Cli::displayMsgNL('* -p --parameters : Display args array');
    Cli::displayMsgNL('* -h --help : View this message');
}
