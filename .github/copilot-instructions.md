# BFW PHP Framework Development Instructions

**ALWAYS follow these instructions first and fallback to search or bash commands only when the information here is incomplete or found to be in error.**

BFW (Bulton Framework) is a modular PHP 7.x framework designed for lightness, flexibility, and speed. It uses a module-based architecture with support for MVC patterns, logging via Monolog, caching via Memcached, and a robust configuration system.

## Critical Setup Requirements

### Initial Bootstrap (REQUIRED for fresh clones)
Run these commands in sequence after cloning the repository:

```bash
# 1. Install PHP dependencies - NEVER CANCEL: Takes 2-5 minutes  
composer install --ignore-platform-reqs --no-interaction
# Note: --ignore-platform-reqs needed due to PHP 8.3 vs framework's PHP 7.x target

# 2. Install framework structure - Takes <1 second
./bin/bfwInstall

# 3. Regenerate autoloader to include framework source
composer dump-autoload
```

**CRITICAL WARNING**: NEVER run `./bin/bfwInstall -f` (force mode) in the framework development environment as it will delete all source files in `src/`. Use `git checkout -- src/` to restore if accidentally deleted.

## Working Effectively

### Build and Run Commands
- **Test framework functionality**: `php web/index.php` (should complete silently)
- **Start development server**: `php -S localhost:8000 -t web web/index.php`
- **Test server**: `curl -I http://localhost:8000/` (expect 404 response - normal with no routes)

### Testing
- **Test runner**: `./vendor/bin/atoum -c .atoum.php -d test/unit/src +verbose`  
- **COMPATIBILITY WARNING**: Test runner (atoum) has compatibility issues with PHP 8.3 but framework itself works correctly
- **Test timing**: Tests would take ~5-15 minutes if compatible - NEVER CANCEL when working
- **Alternative validation**: Run `php web/index.php` and development server for manual testing

### Module Management
- **Add modules**: `./bin/bfwAddMod [--all] [-- moduleName]`
- **Remove modules**: `./bin/bfwDelMod [--all] [-- moduleName]`  
- **Enable modules**: `./bin/bfwEnMod [-- moduleName]`
- **Disable modules**: `./bin/bfwDisMod [-- moduleName]`
- **List options**: Use `--help` with any module command for detailed usage

### Development Workflow Validation
ALWAYS validate changes by running these steps:
1. `composer dump-autoload` - Regenerate autoloader after code changes
2. `php web/index.php` - Verify framework loads without errors
3. `php -S localhost:8000 -t web web/index.php` - Test development server
4. `curl -I http://localhost:8000/` - Verify server responds

## Architecture Overview

### Key Directories
- `src/` - Framework source code (BFW namespace)
- `app/` - Application structure (created by bfwInstall)
  - `app/config/bfw/` - Framework configuration files
  - `app/modules/` - Available and enabled modules
- `web/` - Web entry point and assets
- `docs/en/` - Comprehensive documentation
- `test/` - Test suites (unit tests in test/unit/src/)
- `skel/` - Skeleton files for new installations

### Configuration Files in app/config/bfw/
- `global.php` - Global framework settings
- `modules.php` - Core module configuration
- `errors.php` - Error handling configuration  
- `monolog.php` - Logging configuration
- `memcached.php` - Caching configuration

### Core Framework Classes
- `\BFW\Application` - Main application class
- `\BFW\Config` - Configuration management
- `\BFW\Monolog` - Logging system
- `\BFW\Module` - Module base class
- `\BFW\Request` - HTTP request handling

## Common Development Tasks

### Creating New Modules
1. Follow documentation in `docs/en/how-it-works/create-module.md`
2. Use existing modules as examples (see `docs/en/how-it-works/existing-modules.md`)
3. Install via `./bin/bfwAddMod -- moduleName`

### Debugging and Logging
- Framework uses Monolog for internal logging
- Check `app/config/bfw/monolog.php` for handler configuration
- Default uses TestHandler (keeps messages in memory, doesn't output)

### Web Application Development
- Entry point: `web/index.php`
- Example development: See `docs/en/get-started/example-scripts.md`
- Requires router and controller modules for full MVC functionality

## Platform Compatibility Notes

### PHP Version Compatibility
- **Framework target**: PHP 7.0-7.3
- **Current environment**: PHP 8.3.6
- **Status**: Framework code runs correctly, testing tools have compatibility issues
- **Required flag**: Always use `--ignore-platform-reqs` with composer

### Dependencies Issue Resolution
- If composer install fails due to GitHub authentication, run with `--no-interaction`
- Private repositories may require GitHub token setup (not needed for framework development)

## Timing Expectations - NEVER CANCEL
- **Composer install**: 2-5 minutes depending on network - Set timeout to 10+ minutes
- **Framework install**: <1 second
- **Test runs**: 5-15 minutes when working - Set timeout to 30+ minutes  
- **Autoloader regeneration**: <1 second

## Documentation References
For detailed information, always check:
- `docs/en/intro.md` - Framework philosophy and principles
- `docs/en/get-started/install.md` - Installation guide
- `docs/en/get-started/config.md` - Configuration guide
- `docs/en/get-started/example-scripts.md` - Usage examples
- `docs/en/how-it-works/` - Architecture documentation

## Validation Scenarios
After making changes, ALWAYS test these complete scenarios:

1. **Fresh Clone Workflow**:
   ```bash
   composer install --ignore-platform-reqs --no-interaction
   ./bin/bfwInstall  
   composer dump-autoload
   php web/index.php
   ```

2. **Development Server Workflow**:
   ```bash
   php -S localhost:8000 -t web web/index.php &
   curl -I http://localhost:8000/
   # Kill server with Ctrl+C
   ```

3. **Module Management Workflow**:
   ```bash
   ./bin/bfwAddMod --help
   ls -la app/modules/
   ```

4. **Source Code Validation**:
   ```bash
   composer dump-autoload
   php -r "require_once('vendor/autoload.php'); var_dump(class_exists('\BFW\Application'));"
   # Should output: bool(true)
   ```

Always ensure ALL validation scenarios pass before considering changes complete.