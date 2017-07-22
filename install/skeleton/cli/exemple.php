<?php
/**
 * Cli exemple file
 */

use \BFW\Helpers\Cli;

Cli::displayMsgNL('CLI Exemple file', 'green');

//Get parameters passed to cli
$opt = Cli::getopt('vhp', array('version::', 'help::', 'parameters::'));

//version parameter
if (isset($opt['v']) || isset($opt['version'])) {
    Cli::displayMsgNL('v0.2.2');
}

//Display all detected parameters passed to cli
if (isset($opt['p']) || isset($opt['parameters'])) {
    Cli::displayMsgNL(print_r($opt, true));
}

//Help message
if (isset($opt['h']) || isset($opt['help'])) {
    Cli::displayMsgNL('');
    Cli::displayMsgNL('Helping Informations : Parameters script');
    Cli::displayMsgNL('* -v --version : Version of test script');
    Cli::displayMsgNL('* -p --parameters : Display args array');
    Cli::displayMsgNL('* -h --help : View this message');
}
