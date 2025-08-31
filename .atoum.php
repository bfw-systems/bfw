<?php

/*
This file will automatically be included before EACH run.

Use it to configure atoum or anything that needs to be done before EACH run.

More information on documentation:
[en] http://docs.atoum.org/en/chapter3.html#Configuration-files
[fr] http://docs.atoum.org/fr/chapter3.html#Fichier-de-configuration
*/

use \mageekguy\atoum;
//use \mageekguy\atoum\reports;

// CODE COVERAGE SETUP
if(!getenv('GITHUB_ACTIONS'))
{
    $report = $script->addDefaultReport();
    
    $coverageField = new atoum\report\fields\runner\coverage\html('BFW', __DIR__.'/test/unit/report/coverage');
    //$coverageField->setRootUrl('http://bfw.bulton.fr/reports/bfw-v3/test-unit/index.html');
    $report->addField($coverageField);
    
    $treemapField = new atoum\report\fields\runner\coverage\treemap('BFW', __DIR__.'/test/unit/report/treemap');
    //$treemapField->setHtmlReportBaseUrl('http://bfw.bulton.fr/reports/bfw-v3/treemap/index.html');
    $report->addField($treemapField);
}
/**/

/*
TEST GENERATOR SETUP
*//*
$script->getRunner()->addTestsFromDirectory(__DIR__.'/test/unit/install/class');
$script->getRunner()->addTestsFromDirectory(__DIR__.'/test/unit/src/class');
$script->getRunner()->addTestsFromDirectory(__DIR__.'/test/unit/src/class/core');
$script->getRunner()->addTestsFromDirectory(__DIR__.'/test/unit/src/class/memcache');
//$script->getRunner()->addTestsFromDirectory(__DIR__.'/test/unit/src/functions');
//$script->getRunner()->addTestsFromDirectory(__DIR__.'/test/unit/src/trait');
/**/

if(getenv('GITHUB_ACTIONS'))
{
    $script->addDefaultReport(); //For GitHub Actions debug!
    
    // Generate clover coverage report for Codecov
    $cloverWriter = new atoum\writers\file('clover.xml');
    $cloverReport = new atoum\reports\asynchronous\clover();
    $cloverReport->addWriter($cloverWriter);

    $runner->addReport($cloverReport);
}
