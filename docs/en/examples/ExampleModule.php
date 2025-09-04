<?php

/**
 * Example Module using the new class-based approach
 * 
 * This demonstrates how a module author would create a module
 * using the new class-based system with full IDE support.
 */

namespace MyVendor\ExampleModule;

use BFW\CommonModule;

/**
 * ExampleModule demonstrates the new class-based module system
 * 
 * Benefits:
 * - Full IDE autocompletion and type hints
 * - No PHP 8.2+ dynamic property warnings  
 * - Better code organization and maintainability
 * - Proper inheritance and composition support
 */
class ExampleModule extends CommonModule
{
    /** @var \Fenom Template engine instance */
    protected $fenom;
    
    /** @var \PDO Database connection */  
    protected $database;
    
    /** @var array Module configuration settings */
    protected array $settings = [];
    
    /** @var string|null API key for external services */
    protected ?string $apiKey = null;

    /**
     * Execute the module's main functionality
     * 
     * This method is called by the BFW framework when the module is loaded.
     * All module initialization and logic should go here.
     */
    public function run(): void
    {
        $this->initializeTemplateEngine();
        $this->initializeDatabase();
        $this->loadModuleSettings();
        
        // Example of how the module would work
        $this->processExampleData();
    }
    
    /**
     * Initialize the template engine
     */
    private function initializeTemplateEngine(): void
    {
        $moduleName = $this->getModuleName();
        $templatesPath = MODULES_ENABLED_DIR . $moduleName . '/templates';
        $cachePath = ROOT_DIR . 'cache/' . $moduleName;
        
        // Full IDE support - no "unknown property" warnings!
        $this->fenom = \Fenom::factory($templatesPath, $cachePath);
    }
    
    /**
     * Initialize database connection
     */
    private function initializeDatabase(): void
    {
        $config = $this->getConfig();
        
        if ($config) {
            $dsn = $config->getValue('database.dsn', 'sqlite:example.db');
            $this->database = new \PDO($dsn);
        }
    }
    
    /**
     * Load module settings from configuration
     */
    private function loadModuleSettings(): void
    {
        $config = $this->getConfig();
        
        if ($config) {
            $this->settings = $config->getValue('settings', []);
            $this->apiKey = $config->getValue('api_key');
        }
    }
    
    /**
     * Example of module functionality with full type safety
     */
    private function processExampleData(): void
    {
        // IDE knows exactly what $this->database is
        if ($this->database) {
            $stmt = $this->database->prepare('SELECT * FROM example_table');
            $stmt->execute();
            $data = $stmt->fetchAll();
            
            // IDE knows exactly what $this->fenom is
            if ($this->fenom && !empty($data)) {
                $this->fenom->display('data_template.tpl', ['data' => $data]);
            }
        }
    }
    
    /**
     * Example of a custom method that modules can add
     * 
     * @param string $message
     * @return string
     */
    public function formatMessage(string $message): string
    {
        return '[' . $this->getModuleName() . '] ' . $message;
    }
}