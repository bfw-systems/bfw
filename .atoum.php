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

$report = $script->addDefaultReport();

/*
LOGO
*/
// This will add the atoum logo before each run.
$report->addField(new atoum\report\fields\runner\atoum\logo());

// This will add a green or red logo after each run depending on its status.
$report->addField(new atoum\report\fields\runner\result\logo());
/**/

/*
CODE COVERAGE SETUP
*/
// Please replace in next line "Project Name" by your project name and "/path/to/destination/directory" by your destination directory path for html files.
$coverageField = new atoum\report\fields\runner\coverage\html('BFW', '/home/bubu-blog/www/atoum/bfw-v2/report');

// Please replace in next line http://url/of/web/site by the root url of your code coverage web site.
$coverageField->setRootUrl('http://test.bulton.fr/atoum/bfw-v2/');

$report->addField($coverageField);
/**/

/*
TEST GENERATOR SETUP
*/
$testGenerator = new atoum\test\generator();

// Please replace in next line "/path/to/your/tests/units/classes/directory" by your unit test's directory.
$testGenerator->setTestClassesDirectory(__DIR__.'/test/classes');

// Please replace in next line "your\project\namespace\tests\units" by your unit test's namespace.
$testGenerator->setTestClassNamespace('BFW\test\unit');

// Please replace in next line "/path/to/your/classes/directory" by your classes directory.
$testGenerator->setTestedClassesDirectory(__DIR__.'/src/classes');

// Please replace in next line "your\project\namespace" by your project namespace.
$testGenerator->setTestedClassNamespace('BFW');

// Please replace in next line "path/to/your/tests/units/runner.php" by path to your unit test's runner.
//$testGenerator->setRunnerPath('path/to/your/tests/units/runner.php');

$script->getRunner()->setTestGenerator($testGenerator);
/**/


/*
Publish code coverage report on coveralls.io
*/
$sources = './src';
$token = 'PIwBbCLOo492g07VIZ2h6lB2OLe4UkMhH';
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
