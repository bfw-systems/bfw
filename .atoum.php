<?php

require __DIR__ . '/vendor/autoload.php';

$script->addDefaultReport();

// Only enable code coverage in CI with proper Xdebug mode
if (getenv('CI') === 'true' && getenv('ENABLE_COVERAGE') === 'true' && 
    extension_loaded('xdebug') && ini_get('xdebug.mode') !== false) {
    
    $script->noCodeCoverageForClasses('\\atoum\\atoum\\*');
    $script->excludeDirectoriesFromCoverage([__DIR__ . '/vendor']);
    
    // Add XML coverage report for Codecov using full class paths (PHP 7.x compatible)
    $coverageReportPath = __DIR__ . '/coverage.xml';
    $cloverReport = new \atoum\atoum\reports\asynchronous\clover();
    $fileWriter = new \atoum\atoum\writers\file($coverageReportPath);
    $cloverReport->addWriter($fileWriter);
    $script->addReport($cloverReport);
} else {
    // Disable code coverage by default to avoid errors
    $script->disableCodeCoverage();
}

$runner->addTestsFromDirectory(__DIR__.'/test/unit/src');
