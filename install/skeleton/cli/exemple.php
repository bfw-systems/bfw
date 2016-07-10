<?php

use function BFW\Cli\displayMsg;
use function BFW\Cli\getCliParams;

displayMsg('CLI Exemple file', 'green');

$opt = getCliParams('vhp', array('version::', 'help::', 'parameters::'));

if(isset($opt['v']) || isset($opt['version']))
{
    displayMsg('v0.2');
}

if(isset($opt['p']) || isset($opt['paramters']))
{
    displayMsg(print_r($opt, true));
}

if(isset($opt['h']) || isset($opt['help']))
{
    displayMsg('');
    displayMsg('Helping Informations : Parameters script');
    displayMsg('* -v --version : Version of test script');
    displayMsg('* -p --parameters : Display option array');
    displayMsg('* -h --help : View this message');
}
