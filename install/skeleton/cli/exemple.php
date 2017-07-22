<?php
/**
 * Cli exemple file
 */

use \BFW\Helpers\Cli;

Cli::displayMsg('CLI Exemple file', 'green');

//Get parameters passed to cli
$opt = Cli::getopt('vhp', array('version::', 'help::', 'parameters::'));

//version parameter
if (isset($opt['v']) || isset($opt['version'])) {
    Cli::displayMsg('v0.2.1');
}

//Display all detected parameters passed to cli
if (isset($opt['p']) || isset($opt['parameters'])) {
    Cli::displayMsg(print_r($opt, true));
}

//Help message
if (isset($opt['h']) || isset($opt['help'])) {
    Cli::displayMsg('');
    Cli::displayMsg('Helping Informations : Parameters script');
    Cli::displayMsg('* -v --version : Version of test script');
    Cli::displayMsg('* -p --parameters : Display args array');
    Cli::displayMsg('* -h --help : View this message');
}
