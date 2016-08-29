<?php

namespace BFW;

use Constants;

class Application extends Subjects
{
    protected static $instance = null;

    protected $rootDir = '';

    protected $config;

    protected $options;

    protected $composerLoader;

    protected $runPhases = [];

    protected $memcached;

    protected $request;

    protected $modules;
    
    protected $errors;

    protected function __construct($options)
    {
        ob_start();

        $this->initOptions($options);
        $this->initConstants();
        $this->initComposerLoader();
        $this->initConfig();
        $this->initRequest();
        $this->initSession();
        $this->initErrors();
        $this->initModules();

        $this->declareRunPhases();

        //Defaut http header. Define here add possiblity to override him
        header('Content-Type: text/html; charset=utf-8');
    }

    public static function getInstance($options = [])
    {
        if (self::$instance === null) {
            $calledClass = get_called_class(); //Autorize extends this class
            
            self::$instance = new $calledClass($options);
        }

        return self::$instance;
    }

    public static function init($options = [])
    {
        return self::getInstance($options);
    }

    public function getComposerLoader()
    {
        return $this->composerLoader;
    }

    public function getConfig($configKey)
    {
        return $this->config->getConfig($configKey);
    }

    public function getOption($optionKey)
    {
        return $this->options->getOption($optionKey);
    }
    
    public function getRequest()
    {
        return $this->request;
    }

    protected function initOptions($options)
    {
        $defaultOptions = [
            'rootDir'    => null,
            'vendorDir'  => null,
            'runSession' => true
        ];

        $this->options = new \BFW\Core\Options($defaultOptions, $options);
    }

    protected function initConstants()
    {
        Constants::create('ROOT_DIR', $this->options->getOption('rootDir'));

        Constants::create('APP_DIR', ROOT_DIR.'app/');
        Constants::create('SRC_DIR', ROOT_DIR.'src/');
        Constants::create('WEB_DIR', ROOT_DIR.'web/');

        Constants::create('CONFIG_DIR', APP_DIR.'config/');
        Constants::create('MODULES_DIR', APP_DIR.'modules/');

        Constants::create('CLI_DIR', SRC_DIR.'cli/');
        Constants::create('CTRL_DIR', SRC_DIR.'controllers/');
        Constants::create('MODELES_DIR', SRC_DIR.'modeles/');
        Constants::create('VIEW_DIR', SRC_DIR.'view/');
    }

    protected function initComposerLoader()
    {
        $this->composerLoader = require($this->options->getOption('vendorDir').'autoload.php');
        $this->addComposerNamespaces();
    }

    protected function initConfig()
    {
        $this->config = new \BFW\Config('bfw');
        $this->config->loadFiles();
    }

    protected function initRequest()
    {
        $this->request = \BFW\Request::getInstance();
    }

    protected function initSession()
    {
        if ($this->options->getOption('runSession') === false) {
            return;
        }

        //Destroy session cookie if browser quit
        session_set_cookie_params(0);

        //Run session
        session_start();
    }

    protected function initErrors()
    {
        $this->errors = new \BFW\Core\Errors($this);
    }

    protected function initModules()
    {
        $this->modules = new \BFW\Modules;
    }

    protected function addComposerNamespaces()
    {
        $this->composerLoader->addPsr4('Controller\\', CTRL_DIR);
        $this->composerLoader->addPsr4('Modules\\', MODULES_DIR);
        $this->composerLoader->addPsr4('Modeles\\', MODELES_DIR);
    }

    protected function declareRunPhases()
    {
        $this->runPhases = [
            [$this, 'loadMemcached'],
            [$this, 'readAllModules'],
            [$this, 'loadAllCoreModules'],
            [$this, 'loadAllAppModules'],
            [$this, 'runCliFile']
        ];
    }

    public function run()
    {
        foreach ($this->runPhases as $action) {
            $action();

            $notifyAction = $action;
            if (is_array($action)) {
                $notifyAction = $action[1];
            }

            $this->notifyAction('apprun_'.$notifyAction);
        }

        $this->notifyAction('bfw_run_finish');
    }

    protected function loadMemcached()
    {
        $memcacheConfig = $this->getConfig('memcached');

        if ($memcacheConfig['enabled'] === false) {
            return;
        }

        $class = $memcacheConfig['class'];
        if (empty($class)) {
            throw new Exception('Memcached is active but no class is define');
        }

        if (class_exists($class) === false) {
            throw new Exception('Memcache class '.$class.' not found.');
        }

        $this->memcached = new $class;
    }

    protected function readAllModules()
    {
        $listModules = array_diff(scandir(MODULES_DIR), ['.', '..']);

        foreach ($listModules as $moduleName) {
            $moduleName = realpath($moduleName); //Symlink

            if (!is_dir($moduleName)) {
                continue;
            }

            $this->modules->addModule($moduleName);
        }

        $this->modules->generateTree();
    }

    protected function loadAllCoreModules()
    {
        foreach ($this->getConfig('modules') as $moduleInfos) {
            $moduleName    = $moduleInfos['name'];
            $moduleEnabled = $moduleInfos['enabled'];

            if (empty($moduleName) || $moduleEnabled === false) {
                continue;
            }

            $this->loadModule($moduleName);
        }
    }

    protected function loadAllAppModules()
    {
        $tree = $this->modules->getLoadTree();

        foreach ($tree as $firstLine) {
            foreach ($firstLine as $secondLine) {
                foreach ($secondLine as $moduleName) {
                    $this->loadModule($moduleName);
                }
            }
        }
    }

    protected function loadModule($moduleName)
    {
        $this->notifyAction('load_module_'.$moduleName);
        $this->modules->getModule($moduleName)->runModule();
    }

    protected function runCliFile()
    {
        if (PHP_SAPI !== 'cli') {
            return;
        }

        $opt = getopt('f:');
        if (!isset($opt['f'])) {
            throw new Exception('Error: No file specified.');
        }

        $file = $opt['f'];
        if (!file_exists(CLI_DIR.$file.'.php')) {
            throw new Exception('File to execute not found.');
        }

        $fctExecuteFile = function() use ($file) {
            require_once(CLI_DIR.$file.'.php');
        };

        $this->notifyAction('run_cli_file');
        $fctExecuteFile();
    }
}
