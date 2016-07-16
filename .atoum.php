<?php

/*
This file will automatically be included before EACH run.

Use it to configure atoum or anything that needs to be done before EACH run.

More information on documentation:
[en] http://docs.atoum.org/en/chapter3.html#Configuration-files
[fr] http://docs.atoum.org/fr/chapter3.html#Fichier-de-configuration
*/

use \mageekguy\atoum,
    \mageekguy\atoum\reports;

/* Atoum Logo (add slash to start of this line for enable/disable)
$report = $script->addDefaultReport();
$report->addField(new atoum\report\fields\runner\atoum\logo()); //Start
$report->addField(new atoum\report\fields\runner\result\logo()); //End status
/*/
//Nyancat
$stdout = new \mageekguy\atoum\writers\std\out;
$report = new \mageekguy\atoum\reports\realtime\nyancat;
$script->addReport($report->addWriter($stdout));
/**/

/*
CODE COVERAGE SETUP
*/
if(!file_exists('/home/travis'))
{
    $coverageField = new atoum\report\fields\runner\coverage\html('BFW', '/home/bfw/www/reports/bfw-v3');
    $coverageField->setRootUrl('http://bfw.test.bulton.fr/reports/bfw-v3/');
    $report->addField($coverageField);
    
    $treemapField = new atoum\report\fields\runner\coverage\treemap('BFW', '/home/bfw/www/treemap/bfw-v3');
    $treemapField->setHtmlReportBaseUrl('http://bfw.test.bulton.fr/treemap/bfw-v3/');
    $report->addField($treemapField);
}
/**/

/*
TEST GENERATOR SETUP
*/
$script->getRunner()->addTestsFromDirectory(__DIR__.'/test/unit/install/class');
$script->getRunner()->addTestsFromDirectory(__DIR__.'/test/unit/src/class');
$script->getRunner()->addTestsFromDirectory(__DIR__.'/test/unit/src/class/core');
$script->getRunner()->addTestsFromDirectory(__DIR__.'/test/unit/src/class/memcache');
//$script->getRunner()->addTestsFromDirectory(__DIR__.'/test/unit/src/functions');
//$script->getRunner()->addTestsFromDirectory(__DIR__.'/test/unit/src/trait');
/**/


if(file_exists('/home/travis'))
{
    /*
    Publish code coverage report on coveralls.io
    */
    $sources = './src';
    $token = 'ycIQWlEx47Xh3QzvlQ4kxh3jOHHo55m1E';
    $coverallsReport = new reports\asynchronous\coveralls($sources, $token);
    
    /*
    If you are using Travis-CI (or any other CI tool), you should customize the report
    * https://coveralls.io/docs/api
    * http://about.travis-ci.org/docs/user/ci-environment/#Environment-variables
    * https://wiki.jenkins-ci.org/display/JENKINS/Building+a+software+project#Buildingasoftwareproject-JenkinsSetEnvironmentVariables
    */
    $defaultFinder = $coverallsReport->getBranchFinder();
    $coverallsReport
        ->setBranchFinder(function() use ($defaultFinder) {
            if (($branch = getenv('TRAVIS_BRANCH')) === false)
            {
                $branch = $defaultFinder();
            }
    
            return $branch;
        })
        ->setServiceName(getenv('TRAVIS') ? 'travis-ci' : null)
        ->setServiceJobId(getenv('TRAVIS_JOB_ID') ?: null)
        ->addDefaultWriter()
    ;
    
    $runner->addReport($coverallsReport);
    
    //Scrutinizer coverage
	$cloverWriter = new atoum\writers\file('clover.xml');
	$cloverReport = new atoum\reports\asynchronous\clover();
	$cloverReport->addWriter($cloverWriter);

	$runner->addReport($cloverReport);
}
