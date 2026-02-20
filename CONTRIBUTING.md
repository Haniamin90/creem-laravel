# Contributing to creem/laravel

Thank you for considering contributing to the CREEM Laravel package! This document provides guidelines and instructions for contributing.

## Development Setup

1. **Fork and clone** the repository:
   ```bash
   git clone https://github.com/Haniamin90/creem-laravel.git
   cd creem-laravel
   ```

2. **Install dependencies:**
   ```bash
   composer install
   ```

3. **Run tests** to verify everything works:
   ```bash
   composer test
   ```

## Development Workflow

### Running Tests

```bash
# Run all tests
composer test

# Run with coverage report (requires pcov or xdebug)
composer test:coverage

# Run a specific test file
vendor/bin/phpunit tests/Unit/CreemClientTest.php

# Run a specific test method
vendor/bin/phpunit --filter=test_it_creates_checkout
```

### Code Style

This package uses [Laravel Pint](https://laravel.com/docs/pint) for code formatting with the `laravel` preset.

```bash
# Check code style
composer lint:check

# Fix code style automatically
composer lint
```

### Project Structure

```
src/
├── Api/                  # API resource classes (one per CREEM resource)
├── Commands/             # Artisan commands
├── Events/               # Webhook event classes (15 events)
├── Exceptions/           # Typed exception classes
├── Facades/              # Creem facade with @method annotations
├── Http/
│   ├── Controllers/      # Webhook controller
│   └── Middleware/        # Webhook signature verification
├── Traits/               # Billable trait for Eloquent models
├── Creem.php             # Main service class
├── CreemClient.php       # HTTP client with error handling
├── CreemServiceProvider.php
└── WebhookEventType.php  # Event type constants
tests/
├── Unit/                 # Unit tests with mocked HTTP
└── Feature/              # Feature tests with service container
```

## Pull Request Guidelines

1. **Create a feature branch** from `main`:
   ```bash
   git checkout -b feature/my-feature
   ```

2. **Write tests** for any new functionality. We aim for >70% code coverage.

3. **Follow PSR-12** coding standards. Run `composer lint` before committing.

4. **Update documentation** if you're adding or changing public API methods.

5. **Keep PRs focused** — one feature or fix per PR.

6. **Write descriptive commit messages** explaining *why*, not just *what*.

### PR Checklist

- [ ] Tests pass (`composer test`)
- [ ] Code style passes (`composer lint:check`)
- [ ] DocBlocks added for new public methods
- [ ] README updated (if adding public API)
- [ ] CHANGELOG updated

## Reporting Issues

- Use [GitHub Issues](https://github.com/Haniamin90/creem-laravel/issues) for bug reports and feature requests.
- Include your PHP version, Laravel version, and package version.
- For bugs, include steps to reproduce and the expected vs actual behavior.

## Security Vulnerabilities

Please review our [Security Policy](SECURITY.md) for reporting vulnerabilities.
