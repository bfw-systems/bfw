# CI/CD and Code Quality

This document explains the continuous integration and code quality setup for the BFW framework.

## GitHub Actions Workflows

The project uses GitHub Actions for CI/CD with the following workflows:

### Main CI Workflow (`.github/workflows/ci.yml`)
- Runs tests on PHP 7.4, 8.0, 8.1, 8.2, and 8.3
- Validates composer.json
- Runs unit tests with atoum
- Tests framework installation and module manager
- Generates code coverage reports

### Code Quality Workflow (`.github/workflows/code-quality.yml`)
- Runs PHP_CodeSniffer for PSR-12 compliance
- Runs PHPStan for static analysis
- Runs Psalm for additional static analysis

### Security Workflow (`.github/workflows/security.yml`)
- Runs security audits on dependencies
- Performs dependency vulnerability checks

## Code Quality Tools

### PHP_CodeSniffer (PHPCS)
Configuration: `phpcs.xml.dist`
- Uses PSR-12 coding standard
- Allows 120 character line length
- Excludes vendor, test, and skeleton directories

Run manually:
```bash
composer cs-check      # Check coding standards
composer cs-fix        # Fix coding standards automatically
```

### PHPStan
Configuration: `phpstan.neon.dist`
- Level 6 analysis
- Analyzes src/ directory
- Excludes PrivateBinaries for legacy compatibility

Run manually:
```bash
composer phpstan
```

### Psalm
Configuration: `psalm.xml.dist`
- Error level 6
- Static analysis for type safety
- Configured for legacy compatibility

Run manually:
```bash
composer psalm
```

## Running All Quality Checks

Run all quality checks at once:
```bash
composer quality
```

Run complete CI suite locally:
```bash
composer ci
```

## Migration from Legacy CI

This setup replaces the previous CI configuration:
- **Travis CI** → GitHub Actions
- **Coveralls.io** → Codecov
- **Scrutinizer CI** → GitHub Actions with PHPStan/Psalm

The badges in README.md and documentation have been updated to reflect the new CI system.

## Required Secrets/Configuration

For full functionality, you may need to configure:
- **Codecov**: For code coverage reporting (optional, works without token for public repos)

No additional API keys are required for the basic CI functionality.