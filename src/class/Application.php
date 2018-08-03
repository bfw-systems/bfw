<?php

namespace BFW;

use \Exception;
use \BFW\Core\AppSystems\SystemInterface;

/**
 * Application class
 * Manage all BFW application
 * Load and init components, modules, ...
 */
class Application
{
    /**
     * @const ERR_CALL_UNKNOWN_METHOD Exception code if __call is called with
     * an unmanaged method
     */
    const ERR_CALL_UNKNOWN_METHOD = 1101001;
    
    /**
     * @const ERR_CALL_UNKNOWN_PROPERTY Exception code if __call is called with
     * an unmanaged property
     */
    const ERR_CALL_UNKNOWN_PROPERTY = 1101002;
    
    /**
     * @var \BFW\Application|null $instance Application instance (Singleton)
     */
    protected static $instance = null;
    
    /**
     * @var \BFW\Core\AppSystems\SystemInterface[] $coreSystemList A list of
     * all core system to init and run
     */
    protected $coreSystemList = [];
    
    /**
     * @var array $declaredOptions All options passed to initSystems method
     */
    protected $declaredOptions = [];
    
    /**
     * @var \BFW\RunTasks|null All method tu exec during run
     */
    protected $runTasks;

    /**
     * Constructor
     * Init output buffering
     * Declare core systems
     * Set UTF-8 header
     * 
     * protected for Singleton pattern
     */
    protected function __construct()
    {
        //Start the output buffering
        ob_start();
        
        $this->defineCoreSystemList();

        //Defaut http header. Define here add possiblity to override him
        header('Content-Type: text/html; charset=utf-8');
        
        //Default charset to UTF-8. Define here add possiblity to override him
        ini_set('default_charset', 'UTF-8');
    }

    /**
     * Get the Application instance (Singleton pattern)
     * 
     * @return \BFW\Application The current instance of this class
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            $calledClass = get_called_class(); //Autorize extends this class
            self::$instance = new $calledClass;
        }

        return self::$instance;
    }
    
    /**
     * Getter accessor to property coreSystemList
     * 
     * @return \BFW\Core\AppSystems\SystemInterface[]
     */
    public function getCoreSystemList()
    {
        return $this->coreSystemList;
    }
    
    /**
     * Getter accessor to property declaredOptions
     * 
     * @return array
     */
    public function getDeclaredOptions()
    {
        return $this->declaredOptions;
    }
    
    /**
     * Getter accessor to property runTasks
     * 
     * @return \BFW\RunTasks|null
     */
    public function getRunTasks()
    {
        return $this->runTasks;
    }
    
    /**
     * PHP Magic method, called when we call an unexisting method
     * Only method getXXX are allowed.
     * The property should be a key (ucfirst for camelcase) of the array
     * coreSystemList.
     * Ex: getConfig() or getModuleList()
     * The value returned will be the returned value of the __invoke method
     * into the core system class called.
     * 
     * @param string $name The method name
     * @param array $arguments The argument passed to the method
     * 
     * @return mixed
     * 
     * @throws \Exception If the method is not allowed or if the property
     * not exist.
     */
    public function __call($name, $arguments)
    {
        $prefix = substr($name, 0, 3);
        
        if ($prefix !== 'get') {
            throw new Exception(
                'Unknown method '.$name,
                self::ERR_CALL_UNKNOWN_METHOD
            );
        }
        
        $property = lcfirst(substr($name, 3));
        if (!array_key_exists($property, $this->coreSystemList)) {
            throw new Exception(
                'Unknown property '.$property,
                self::ERR_CALL_UNKNOWN_PROPERTY
            );
        }
        
        return $this->coreSystemList[$property](...$arguments);
    }
    
    /**
     * Define the list of coreSystem to init and/or run.
     * 
     * @return void
     */
    protected function defineCoreSystemList()
    {
        $this->coreSystemList = [
            'options'        => new Core\AppSystems\Options,
            'constants'      => new Core\AppSystems\Constants,
            'composerLoader' => new Core\AppSystems\ComposerLoader,
            'subjectList'    => new Core\AppSystems\SubjectList,
            'config'         => new Core\AppSystems\Config,
            'monolog'        => new Core\AppSystems\Monolog,
            'request'        => new Core\AppSystems\Request,
            'session'        => new Core\AppSystems\Session,
            'errors'         => new Core\AppSystems\Errors,
            'memcached'      => new Core\AppSystems\Memcached,
            'moduleList'     => new Core\AppSystems\ModuleList,
            'cli'            => new Core\AppSystems\Cli,
            'ctrlRouterLink' => new Core\AppSystems\CtrlRouterLink
        ];
    }
    
    /**
     * Initialize all components
     * 
     * @param array $options Options passed to application
     * 
     * @return void
     */
    public function initSystems($options)
    {
        $this->declaredOptions = $options;
        $this->runTasks        = new \BFW\RunTasks([], 'BfwApp');
        
        foreach ($this->coreSystemList as $name => $coreSystem) {
            $this->initCoreSystem($name, $coreSystem);
            
            if ($name === 'subjectList') {
                $this->getSubjectList()->addSubject(
                    $this->runTasks,
                    'ApplicationTasks'
                );
            }
        }
        
        $this->getMonolog()
            ->getLogger()
            ->debug('Framework initializing done.')
        ;
        
        return $this;
    }
    
    /**
     * Init all core system declared, only if they have not been already init.
     * If the system should be run, we add him to the runTasks object.
     * 
     * @param string $name The core system name
     * @param \BFW\Core\AppSystems\SystemInterface $coreSystem The core system
     * instance.
     * 
     * @return void
     */
    protected function initCoreSystem($name, SystemInterface $coreSystem)
    {
        if ($coreSystem->isInit() === true) {
            return;
        }

        $coreSystem->init();
        
        if ($coreSystem->toRun() === true) {
            $this->runTasks->addToRunSteps(
                $name,
                (object) [
                    'callback' => [$coreSystem, 'run']
                ]
            );
        }
    }

    /**
     * Run the application
     * 
     * @return void
     */
    public function run()
    {
        $this->getMonolog()->getLogger()->debug('running framework');
        
        $this->runTasks->run();
        $this->runTasks->sendNotify('bfw_run_done');
    }
}
