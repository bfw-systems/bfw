# Class-based Module Runners

With BFW v3.0, you can now create modules using class-based runners instead of simple PHP files. This approach provides full IDE support, eliminates PHP 8.2+ dynamic property warnings, and offers better code organization.

## Basic Usage

### 1. Create Your Module Class

Create a new PHP class that extends `CommonModule`:

```php
<?php

namespace MyModule;

use BFW\CommonModule;

class MyModule extends CommonModule
{
    protected $fenom;
    protected $database;
    protected $config;
    
    public function run(): void
    {
        // Initialize your module components
        $this->fenom = \Fenom::factory('/path/to/templates', '/path/to/cache');
        $this->database = new \PDO('sqlite:mymodule.db');
        
        // Your module logic here
        echo "MyModule is running with full IDE support!\n";
    }
}
```

### 2. Configure Your Module

In your `module.json` file, specify the class instead of a runner file:

```json
{
    "class": "MyModule\\MyModule",
    "priority": 10,
    "require": []
}
```

## Benefits

### ✅ Full IDE Support
- Autocompletion for all properties and methods
- Type hints and documentation
- Refactoring support
- Error detection

### ✅ No PHP 8.2+ Warnings
- Properly declared properties eliminate deprecation warnings
- Type-safe code reduces runtime errors

### ✅ Better Code Organization
- Clear separation of concerns
- Inheritance and composition support
- Namespace organization

### ✅ Backward Compatibility
- Existing file-based modules continue to work
- Gradual migration path available

## Advanced Features

### Accessing Framework Services

The `CommonModule` base class provides access to module name and configuration:

```php
class MyModule extends CommonModule
{
    public function run(): void
    {
        // Access module name (set by framework)
        $name = $this->getModuleName();
        
        // Access module configuration (set by framework)
        $config = $this->getConfig();
        
        if ($config) {
            $setting = $config->getValue('my_setting');
        }
    }
}
```

### Custom Properties with Type Hints

Declare your module properties with proper types:

```php
class MyModule extends CommonModule
{
    protected \Fenom $templateEngine;
    protected \PDO $database;
    protected array $settings = [];
    protected ?string $apiKey = null;
    
    public function run(): void
    {
        // Full type safety and IDE support
        $this->templateEngine = \Fenom::factory(/* ... */);
        $this->database = new \PDO(/* ... */);
    }
}
```

## Migration from File-based Modules

### Before (file-based):
```php
// runner.php
$this->fenom = \Fenom::factory('/path/to/templates', '/path/to/cache');
$this->database = new \PDO('sqlite:mymodule.db');

// module.json
{
    "runner": "runner.php",
    "priority": 10
}
```

### After (class-based):
```php
// MyModule.php
namespace MyModule;

class MyModule extends \BFW\CommonModule
{
    protected $fenom;
    protected $database;
    
    public function run(): void
    {
        $this->fenom = \Fenom::factory('/path/to/templates', '/path/to/cache');
        $this->database = new \PDO('sqlite:mymodule.db');
    }
}

// module.json
{
    "class": "MyModule\\MyModule",
    "priority": 10
}
```

## Best Practices

1. **Use Type Hints**: Declare property types for better IDE support
2. **Namespace Organization**: Use meaningful namespaces for your modules
3. **Constructor Logic**: Keep constructors simple, put initialization in `run()`
4. **Property Visibility**: Use `protected` for properties that subclasses might need
5. **Documentation**: Add PHPDoc comments for better IDE integration

## Example Module Structure

```
mymodule/
├── module.json
├── src/
│   └── MyModule.php
├── config/
│   └── mymodule.json
└── README.md
```

```php
// src/MyModule.php
<?php

namespace MyVendor\MyModule;

use BFW\CommonModule;

/**
 * MyModule provides awesome functionality with full IDE support
 */
class MyModule extends CommonModule
{
    /** @var \Fenom Template engine instance */
    protected \Fenom $templates;
    
    /** @var \PDO Database connection */
    protected \PDO $db;
    
    /** @var array Module settings */
    protected array $settings = [];
    
    public function run(): void
    {
        $this->initializeTemplates();
        $this->initializeDatabase();
        $this->loadSettings();
        
        // Your module logic here
    }
    
    private function initializeTemplates(): void
    {
        $this->templates = \Fenom::factory(
            MODULES_ENABLED_DIR . $this->getModuleName() . '/templates',
            ROOT_DIR . 'cache/mymodule'
        );
    }
    
    private function initializeDatabase(): void
    {
        $config = $this->getConfig();
        $dsn = $config->getValue('database.dsn', 'sqlite:mymodule.db');
        $this->db = new \PDO($dsn);
    }
    
    private function loadSettings(): void
    {
        $config = $this->getConfig();
        $this->settings = $config->getValue('settings', []);
    }
}
```

This approach provides a clean, maintainable, and IDE-friendly way to develop BFW modules while maintaining full backward compatibility with existing file-based modules.