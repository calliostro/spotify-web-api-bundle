# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.4.0](https://github.com/calliostro/spotify-web-api-bundle/releases/tag/v1.4.0) – 2026-09-20

### Added

- Compatibility testing and CI matrix coverage for PHP 8.1–8.6 and Symfony 6.4 LTS, 7.x, 8.0, 8.1, and 8.2.
- Created root `phpstan.neon.dist` (Level 8) and added PHPStan static analysis.
- Created `.php-cs-fixer.dist.php` for PSR-12 and PSR-12:risky code styling.
- Added comprehensive unit test suite in `tests/Unit/` reaching 100% code coverage across all classes, methods, and lines.
- Added `DEVELOPMENT.md` guide and Keep a Changelog `CHANGELOG.md`.
- Added `.gitattributes` for clean release archives and updated `.gitignore`.

### Changed

- Updated GitHub Actions runners to `ubuntu-24.04` and modernized action versions to Node 24 compatible runners (`actions/checkout@v7`, `actions/cache@v6`, `codecov/codecov-action@v7`).
- Added strict type declarations (`declare(strict_types=1);`) and typed properties across the entire codebase.
- Overhauled `README.md` layout, badges, structure, and sister bundle links to align with sister bundles (`discogs-bundle`, `lastfm-bundle`).
- Updated `phpunit.xml.dist` with separated `Unit Tests`, `Integration Tests`, and `All Tests` test suites.

## [1.3.0](https://github.com/calliostro/spotify-web-api-bundle/releases/tag/v1.3.0) – 2026-01-01

### Added

- Added support for `jwilsson/spotify-web-api-php` v7.
- Migrated dependency injection configuration to PHP config (`services.php`).

## [1.2.1](https://github.com/calliostro/spotify-web-api-bundle/releases/tag/v1.2.1) – 2025-08-23

### Changed

- Migrated CI from Travis CI to GitHub Actions.

## [1.2.0](https://github.com/calliostro/spotify-web-api-bundle/releases/tag/v1.2.0) – 2025-08-22

### Changed

- Refactored PHPUnit configuration and enhanced deprecation handling.

## [1.1.1](https://github.com/calliostro/spotify-web-api-bundle/releases/tag/v1.1.1) – 2024-01-03

### Changed

- Updated dependencies and minor maintenance updates.

## [1.1.0](https://github.com/calliostro/spotify-web-api-bundle/releases/tag/v1.1.0) – 2022-07-16

### Added

- Added Symfony 6 support.

## [1.0.0](https://github.com/calliostro/spotify-web-api-bundle/releases/tag/v1.0.0) – 2021-07-01

### Changed

- Upgraded to `jwilsson/spotify-web-api-php` v5.

## [0.1.2](https://github.com/calliostro/spotify-web-api-bundle/releases/tag/v0.1.2) – 2021-04-20

### Added

- Added client options support.

## [0.1.1](https://github.com/calliostro/spotify-web-api-bundle/releases/tag/v0.1.1) – 2021-04-19

### Changed

- Updated usage documentation and examples.

## [0.1.0](https://github.com/calliostro/spotify-web-api-bundle/releases/tag/v0.1.0) – 2021-04-18

### Added

- Initial release of Spotify Web API Bundle for Symfony.
