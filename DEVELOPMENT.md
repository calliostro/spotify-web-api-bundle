# Development Guide

This guide is for contributors and developers working on `spotify-web-api-bundle` itself.

## 🧪 Testing

### Quick Commands

```bash
# Unit tests (fast, CI-compatible, 100% coverage target)
composer test

# Integration tests (functional kernel boot & service wiring)
composer test-integration

# All tests together (unit + integration)
composer test-all

# Code coverage (HTML + Clover XML reports)
composer test-coverage
```

### Static Analysis & Code Quality

```bash
# Static analysis (PHPStan Level 8)
composer analyse

# Code style check (PSR-12 standards)
composer cs

# Auto-fix code style
composer cs-fix
```

## 🏗️ Test Strategy

- **Unit Tests (`tests/Unit/`)**: Fast, reliable, no external dependencies → **CI default**
  - Container configuration validation
  - Dependency injection extension loading & service wiring
  - Factory client instantiation with options and credentials
  - Token provider resolution
- **Integration Tests (`tests/Integration/`)**: Full Symfony kernel boot & service container wiring
- **Code Coverage Target**: 100% coverage across all classes, methods, and lines

## 🛠️ Development Workflow

1. Fork the repository
2. Create a feature or release branch (`git checkout -b feature/my-feature` or `git checkout -b release/v1.4.0`)
3. Make changes with tests
4. Run test suite (`composer test-all`)
5. Check code quality (`composer analyse && composer cs`)
6. Commit changes (`git commit -m 'Add feature'`)
7. Push to branch (`git push origin feature/my-feature`)
8. Open Pull Request

## 📋 Code Standards

- **PHP Version**: `^8.1` (tested on PHP 8.1–8.6)
- **Code Style**: PSR-12 / PSR-12:risky (`friendsofphp/php-cs-fixer`)
- **Static Analysis**: PHPStan Level 8
- **Strict Types**: `declare(strict_types=1);` in all PHP files
- **Symfony Compatibility**: 6.4 LTS, 7.x, 8.x

## 🏛️ Bundle Architecture

The Symfony bundle provides:

1. **Service Integration**: Seamless `SpotifyWebAPI` autowiring and factory creation
2. **Session Management**: Configurable `SpotifyWebAPI\Session` service
3. **Token Provider Pattern**: Flexible `TokenProviderInterface` enabling custom access token retrieval (Client Credentials, Authorization Code, etc.)
4. **Configuration Options**: Built-in support for `auto_refresh`, `auto_retry`, and `return_assoc` options
