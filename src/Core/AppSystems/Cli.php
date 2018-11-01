<?php

namespace BFW\Core\AppSystems;

class Cli extends AbstractSystem
{
    /**
     * @var \BFW\Core\Cli $cli
     */
    protected $cli;
    
    /**
     * Define the cli property
     */
    public function __construct()
    {
        $this->cli = new \BFW\Core\Cli;
    }
    
    /**
     * {@inheritdoc}
     * 
     * @return \BFW\Core\Cli
     */
    public function __invoke()
    {
        return $this->cli;
    }

    /**
     * Getter accessor to cli property
     * 
     * @return \BFW\Core\Cli
     */
    public function getCli()
    {
        return $this->cli;
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
