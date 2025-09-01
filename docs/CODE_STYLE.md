# PSR-12 Code Style Compliance

This project now follows the [PSR-12](https://www.php-fig.org/psr/psr-12/) coding standard.

## Code Quality Checks

To check code compliance with PSR-12:

```bash
composer phpcs
```

To automatically fix code style violations:

```bash
composer phpcbf
```

## Configuration

The PHPCS configuration is defined in `phpcs.xml` and includes:
- PSR-12 standard compliance
- Coverage of `src/` and `test/` directories  
- Colored output and progress indicators
- Exclusion of vendor directory

## Development Workflow

It's recommended to run code style checks before committing:

```bash
# Check for violations
composer phpcs

# Fix automatically fixable issues
composer phpcbf

# Re-check to verify fixes
composer phpcs
```