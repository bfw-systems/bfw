<?php

namespace BFW\Install\test\unit;
use \atoum;

require_once(__DIR__.'/../../../../vendor/autoload.php');

class ReadDirLoadModule extends atoum
{
    /**
     * @var \BFW\Install\ReadDirectory $class : Instance de la class
     */
    protected $class;
    
    /**
     * @var array $list : liste des fichiers trouvés
     */
    protected $list = [];
    
    /**
     * @var int $readdirIndex : Index pour le mock de la fonction readdir
     */
    protected $readdirIndex = -1;

    /**
     * Instanciation de la class avant chaque méthode de test
     */
    public function beforeTestMethod($testMethod)
    {
        if ($testMethod === 'testConstructor') {
            return;
        }
        
        $this->class = new \BFW\Install\ReadDirLoadModule($this->list);
    }
    
    public function testConstructor()
    {
        $this->assert('test constructor')
            ->if($this->class = new \BFW\Install\ReadDirLoadModule($this->list))
            ->array($this->list)
                ->size
                    ->isEqualTo(0);
    }
    
    public function testRun()
    {
        $this->assert('test run (call fileAction and dirAction).')
            ->if($this->function->opendir = 'dirPath')
            ->and($this->function->readdir = function() {
                $this->readdirIndex++;
                
                if($this->readdirIndex === 0) {
                    return '.';
                } elseif ($this->readdirIndex === 1) {
                    return '..';
                } elseif ($this->readdirIndex === 2) {
                    return 'test';
                } elseif ($this->readdirIndex === 3) {
                    return 'bfwModulesInfos.json';
                } elseif ($this->readdirIndex === 4) {
                    return 'test2';
                }
                
                return false;
            })
            ->and($this->function->is_dir = function($path) {
                if($path === 'dirPath/test') {
                    return true;
                }
                
                return false;
            })
            ->and($this->function->closedir = true)
            ->then
            
            ->if($this->class->run(''))
            ->array($this->list)
                ->isEqualTo(['dirPath'])
                ->size
                    ->isEqualTo(1);
    }
}
