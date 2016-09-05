<?php
/**
 * Cli exemple file
 */

use function BFW\Cli\displayMsg;
use function BFW\Cli\getCliParams;

displayMsg('CLI Exemple file', 'green');

//Get parameters passed to cli
$opt = getCliParams('vhp', array('version::', 'help::', 'parameters::'));

//version parameter
if (isset($opt['v']) || isset($opt['version'])) {
    displayMsg('v0.2');
}

//Display all detected parameters passed to cli
if (isset($opt['p']) || isset($opt['paramters'])) {
    displayMsg(print_r($opt, true));
}

//Help message
if (isset($opt['h']) || isset($opt['help'])) {
    displayMsg('');
    displayMsg('Helping Informations : Parameters script');
    displayMsg('* -v --version : Version of test script');
    displayMsg('* -p --parameters : Display option array');
    displayMsg('* -h --help : View this message');
}
