<?php

namespace BFW\Core\AppSystems;

class Cli extends AbstractSystem
{
    /**
     * @var \BFW\Core\Cli|null $cli
     */
    protected $cli;
    
    /**
     * {@inheritdoc}
     * 
     * @return \BFW\Core\Cli|null
     */
    public function __invoke()
    {
        return $this->cli;
    }

    /**
     * Getter accessor to cli property
     * 
     * @return \BFW\Core\Cli|null
     */
    public function getCli()
    {
        return $this->cli;
    }
    
    /**
     * {@inheritdoc}
     * Define the cli property
     */
    public function init()
    {
        $this->cli        = new \BFW\Core\Cli;
        $this->initStatus = true;
    }
    
    /**
     * {@inheritdoc}
     */
    public function toRun(): bool
    {
        return true;
    }
    
    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->runCliFile();
        $this->runStatus = true;
    }
    
    /**
     * Run the cli file if we're in cli mode
     * 
     * @return void
     * 
     * @throws \Exception If no file is specified or if the file not exist.
     */
    protected function runCliFile()
    {
        if (PHP_SAPI !== 'cli') {
            return;
        }

        \BFW\Application::getInstance()
            ->getSubjectList()
            ->getSubjectByName('ApplicationTasks')
            ->sendNotify('run_cli_file');
        
        $fileToExec = $this->cli->obtainFileFromArg();
        $this->cli->run($fileToExec);
    }
}
