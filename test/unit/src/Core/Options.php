<?php

namespace BFW\Core\test\unit;

use \atoum;

require_once(__DIR__.'/../../../../vendor/autoload.php');

/**
 * @engine isolate
 */
class Options extends atoum
{
    //use \BFW\Test\Helpers\Application;
    
    protected $defaultOptions;
    protected $mock;
    
    public function beforeTestMethod($testMethod)
    {
        $this->mockGenerator
            ->makeVisible('searchVendorDir')
            ->makeVisible('searchRootDir')
            ->generate('BFW\Core\Options')
        ;
        
        $this->defaultOptions = [
            'rootDir'    => null,
            'vendorDir'  => null,
            'runSession' => true
        ];
        
        $this->mock = new \mock\BFW\Core\Options(
            $this->defaultOptions,
            [
                'rootDir'    => '/',
                'vendorDir'  => '/vendor/',
            ]
        );
    }
    
    protected function prepareTestSearchPaths() {
        $this
            ->and($this->calling($this->mock)->searchRootDir = '/')
            ->and($this->calling($this->mock)->searchVendorDir = '/vendor/')
            ->then
        ;
            
        return $this;
    }
    
    public function testSearchPathsWithBothPath()
    {
        $this->assert('test Core\Options::searchPaths with both path into args')
            ->given($this->mock = new \mock\BFW\Core\Options(
                $this->defaultOptions,
                [
                    'rootDir'    => '/',
                    'vendorDir'  => '/vendor/',
                ]
            ))
            ->and($this->prepareTestSearchPaths())
            ->then
            ->object($this->mock->searchPaths())
                ->isIdenticalTo($this->mock)
            ->mock($this->mock)
                ->call('searchRootDir')
                    ->never()
            ->mock($this->mock)
                ->call('searchVendorDir')
                    ->never()
        ;
    }
    
    public function testSearchPathsWithOnlyRootPath()
    {
        $this->assert('test Core\Options::searchPaths with only root path into args')
            ->given($this->mock = new \mock\BFW\Core\Options(
                $this->defaultOptions,
                [
                    'rootDir'    => '/',
                    'vendorDir'  => null,
                ]
            ))
            ->and($this->prepareTestSearchPaths())
            ->then
            ->object($this->mock->searchPaths())
                ->isIdenticalTo($this->mock)
            ->mock($this->mock)
                ->call('searchRootDir')
                    ->never()
            ->mock($this->mock)
                ->call('searchVendorDir')
                    ->once()
        ;
    }
    
    public function testSearchPathsWithOnlyVendorPath()
    {
        $this->assert('test Core\Options::searchPaths with only vendor path into args')
            ->given($this->mock = new \mock\BFW\Core\Options(
                $this->defaultOptions,
                [
                    'rootDir'    => null,
                    'vendorDir'  => '/vendor/',
                ]
            ))
            ->and($this->prepareTestSearchPaths())
            ->then
            ->object($this->mock->searchPaths())
                ->isIdenticalTo($this->mock)
            ->mock($this->mock)
                ->call('searchRootDir')
                    ->once()
            ->mock($this->mock)
                ->call('searchVendorDir')
                    ->never() //Not 1 because mocked method
        ;
    }
    
    public function testSearchPathsWithoutPath()
    {
        $this->assert('test Core\Options::searchPaths without path into args')
            ->given($this->mock = new \mock\BFW\Core\Options(
                $this->defaultOptions,
                [
                    'rootDir'    => null,
                    'vendorDir'  => null,
                ]
            ))
            ->and($this->prepareTestSearchPaths())
            ->then
            ->object($this->mock->searchPaths())
                ->isIdenticalTo($this->mock)
            ->mock($this->mock)
                ->call('searchRootDir')
                    ->once()
            ->mock($this->mock)
                ->call('searchVendorDir')
                    ->once() //Not 2 because mocked method
        ;
    }
    
    public function testCheckPaths()
    {
        $this->assert('test Core\Options::checkPaths with ending slashes')
            ->object($this->mock->checkPaths())
                ->isIdenticalTo($this->mock)
            ->string($this->mock->getValue('rootDir'))
                ->isEqualTo('/')
            ->string($this->mock->getValue('vendorDir'))
                ->isEqualTo('/vendor/')
        ;
        
        $this->assert('test Core\Options::checkPaths without ending slashes and empty rootDir')
            ->given($this->mock = new \mock\BFW\Core\Options(
                $this->defaultOptions,
                [
                    'rootDir'    => '',
                    'vendorDir'  => '',
                ]
            ))
            ->object($this->mock->checkPaths())
                ->isIdenticalTo($this->mock)
            ->string($this->mock->getValue('rootDir'))
                ->isEqualTo('/')
            ->string($this->mock->getValue('vendorDir'))
                ->isEqualTo('/')
        ;
        
        $this->assert('test Core\Options::checkPaths without ending slashes')
            ->given($this->mock = new \mock\BFW\Core\Options(
                $this->defaultOptions,
                [
                    'rootDir'    => '/rootDir',
                    'vendorDir'  => '/rootDir/vendor',
                ]
            ))
            ->object($this->mock->checkPaths())
                ->isIdenticalTo($this->mock)
            ->string($this->mock->getValue('rootDir'))
                ->isEqualTo('/rootDir/')
            ->string($this->mock->getValue('vendorDir'))
                ->isEqualTo('/rootDir/vendor/')
        ;
    }
    
    /**
     * @php >= 7.0
     */
    public function testSearchVendorDirPHP7()
    {
        $this->assert('test Core\Options::searchVendorDir - prepare');
        
        $composerLoader = require(__DIR__.'/../../../../vendor/autoload.php');
        $classPath      = realpath($composerLoader->findFile('BFW\Core\Options'));
        $classDirPath   = str_replace('/Options.php', '', $classPath);
        
        $explodeClassDirPath = explode('/', $classDirPath);
        $countExplodeClassDirPath = count($explodeClassDirPath);
        
        unset(
            $explodeClassDirPath[$countExplodeClassDirPath],
            $explodeClassDirPath[$countExplodeClassDirPath-1],
            $explodeClassDirPath[$countExplodeClassDirPath-2],
            $explodeClassDirPath[$countExplodeClassDirPath-3]
        );
        $expectedVendorDir = implode('/', $explodeClassDirPath).'/';
        
        $this->assert('test Core\Options::searchVendorDir')
            ->string($this->invoke($this->mock)->searchVendorDir())
                ->isEqualTo($expectedVendorDir)
        ;
    }
    
    /**
     * @php < 7.0
     */
    public function testSearchVendorDirPHP5()
    {
        $this->assert('test Core\Options::searchVendorDir - prepare');
        
        $composerLoader = require(__DIR__.'/../../../../vendor/autoload.php');
        $classPath      = realpath($composerLoader->findFile('BFW\Core\Options'));
        $classDirPath   = str_replace('/Options.php', '', $classPath);
        
        $explodeClassDirPath = explode('/', $classDirPath);
        $countExplodeClassDirPath = count($explodeClassDirPath);
        
        unset(
            $explodeClassDirPath[$countExplodeClassDirPath],
            $explodeClassDirPath[$countExplodeClassDirPath-1],
            $explodeClassDirPath[$countExplodeClassDirPath-2],
            $explodeClassDirPath[$countExplodeClassDirPath-3]
        );
        $expectedVendorDir = implode('/', $explodeClassDirPath).'/';
        
        $this->assert('test Core\Options::searchVendorDir')
            ->string($this->invoke($this->mock)->searchVendorDir())
                ->isEqualTo($expectedVendorDir)
        ;
    }
    
    public function testSearchRootDir()
    {
        $this->assert('test Core\Options::searchRootDir')
            ->given($this->calling($this->mock)->searchVendorDir = function() {
                return '/var/www/myProject/vendor/';
            })
            ->string($this->invoke($this->mock)->searchRootDir())
                ->isEqualTo('/var/www/myProject/')
        ;
    }
}
