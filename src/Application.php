<?php

namespace BFW;

use \Exception;
use \BFW\Core\AppSystems\SystemInterface;

/**
 * Application class
 * Manage all BFW application
 * Load and init components, modules, ...
 * 
 * @method \Composer\Autoload\ClassLoader getComposerLoader()
 * @method \BFW\Config getConfig()
 * @method null getConstants()
 * @method object getCtrlRouterLink()
 * @method \BFW\Core\Errors getErrors()
 * @method \BFW\Memcached getMemcached()
 * @method \BFW\Core\ModuleList getModuleList()
 * @method \BFW\Monolog getMonolog()
 * @method \BFW\Core\Options getOptions()
 * @method \BFW\Request getRequest()
 * @method null getSession()
 * @method \BFW\Core\SubjectList getSubjectList()
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
     * @const ERR_APP_SYSTEM_CLASS_NOT_EXIST Exception code if an appSystem
     * class not exist.
     */
    const ERR_APP_SYSTEM_CLASS_NOT_EXIST = 1101003;
    
    /**
     * @const ERR_APP_SYSTEM_NOT_IMPLEMENT_INTERFACE Exception code if an
     * AppSystem not implement \BFW\Core\AppSystems\SystemInterface.
     */
    const ERR_APP_SYSTEM_NOT_IMPLEMENT_INTERFACE = 1101004;
    
    
    /**
     * @var \BFW\Application|null $instance Application instance (Singleton)
     */
    protected static $instance = null;
    
    /**
     * @var \BFW\Core\AppSystems\SystemInterface[] $appSystemList A list of
     * all appSystem
     */
    protected $appSystemList = [];
    
    /**
     * @var string[] $appSystemsFromMods AppSystems added from modules
     */
    protected $appSystemsFromMods = [];
    
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
    public static function getInstance(): Application
    {
        if (self::$instance === null) {
            $calledClass = get_called_class(); //Autorize extends this class
            self::$instance = new $calledClass;
        }

        return self::$instance;
    }
    
    /**
     * Getter accessor to property appSystemList
     * 
     * @return \BFW\Core\AppSystems\SystemInterface[]
     */
    public function getAppSystemList(): array
    {
        return $this->appSystemList;
    }
    
    /**
     * Getter accessor to property appSystemsFromMods
     * 
     * @return string[]
     */
    public function getAppSystemsFromMods(): array
    {
        return $this->appSystemsFromMods;
    }
    
    /**
     * Getter accessor to property appSystemsFromMods
     * 
     * @param string $name The appSystem name
     * @param string $className The appSystem class (with namespace)
     * 
     * @return \BFW\Application
     */
    public function addAppSystemsFromMods($name, $className): Application
    {
        $this->appSystemsFromMods[$name] = $className;
        
        return $this;
    }
        
    /**
     * Getter accessor to property declaredOptions
     * 
     * @return array
     */
    public function getDeclaredOptions(): array
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
    public function __call(string $name, array $arguments)
    {
        $prefix = substr($name, 0, 3);
        
        if ($prefix !== 'get') {
            throw new Exception(
                'Unknown method '.$name,
                self::ERR_CALL_UNKNOWN_METHOD
            );
        }
        
        $property = lcfirst(substr($name, 3));
        if (!array_key_exists($property, $this->appSystemList)) {
            throw new Exception(
                'Unknown property '.$property,
                self::ERR_CALL_UNKNOWN_PROPERTY
            );
        }
        
        return $this->appSystemList[$property](...$arguments);
    }
    
    /**
     * Define the list of coreSystem to init and/or run.
     * 
     * @return string[]
     */
    protected function obtainAppSystemList(): array
    {
        $appSystemNS = '\BFW\Core\AppSystems\\';
        
        return [
            'options'        => $appSystemNS.'Options',
            'constants'      => $appSystemNS.'Constants',
            'composerLoader' => $appSystemNS.'ComposerLoader',
            'subjectList'    => $appSystemNS.'SubjectList',
            'config'         => $appSystemNS.'Config',
            'monolog'        => $appSystemNS.'Monolog',
            'request'        => $appSystemNS.'Request',
            'session'        => $appSystemNS.'Session',
            'errors'         => $appSystemNS.'Errors',
            'memcached'      => $appSystemNS.'Memcached',
            'moduleList'     => $appSystemNS.'ModuleList',
            'ctrlRouterLink' => $appSystemNS.'CtrlRouterLink'
        ];
    }
    
    /**
     * Initialize all components
     * 
     * @param array $options Options passed to application
     * 
     * @return $this
     */
    public function initSystems(array $options): self
    {
        $appSystemList         = $this->obtainAppSystemList();
        $this->declaredOptions = $options;
        $this->runTasks        = new \BFW\RunTasks([], 'BfwApp');
        
        foreach ($appSystemList as $name => $className) {
            if ($name === 'ctrlRouterLink') {
                continue;
            }
            
            $this->initAppSystem($name, $className);
            
            if ($name === 'subjectList') {
                $this->getSubjectList()->addSubject(
                    $this->runTasks,
                    'ApplicationTasks'
                );
            }
        }
        
        foreach ($this->appSystemsFromMods as $name => $className) {
            $this->initAppSystem($name, $className);
        }
        
        //Because the appSystemList can be overrided
        if (array_key_exists('ctrlRouterLink', $appSystemList)) {
            $this->initAppSystem(
                'ctrlRouterLink',
                $appSystemList['ctrlRouterLink']
            );
        }
        
        $this->getMonolog()
            ->getLogger()
            ->debug('Framework initializing done.')
        ;
        
        return $this;
    }
    
    /**
     * Instantiate the appSystem declared, only if they implement the interface.
     * If the system should be run, we add him to the runTasks object.
     * 
     * @param string $name The core system name
     * @param string $className The core system class name
     * instance.
     * 
     * @return void
     */
    protected function initAppSystem(string $name, string $className)
    {
        if (!class_exists($className)) {
            throw new Exception(
                'The appSystem class '.$className.' not exist.',
                self::ERR_APP_SYSTEM_CLASS_NOT_EXIST
            );
        }
        
        $appSystem = new $className;
        if ($appSystem instanceof SystemInterface === false) {
            throw new Exception(
                'The appSystem '.$className.' not implement the interface.',
                self::ERR_APP_SYSTEM_NOT_IMPLEMENT_INTERFACE
            );
        }
        
        $this->appSystemList[$name] = $appSystem;
        
        if ($appSystem->toRun() === true) {
            $this->runTasks->addToRunSteps(
                $name,
                \BFW\RunTasks::generateStepItem(null, [$appSystem, 'run'])
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
