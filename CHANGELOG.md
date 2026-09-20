# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.4.0](https://github.com/calliostro/spotify-web-api-bundle/releases/tag/v1.4.0) – 2026-09-20

### Added

- Added `Calliostro\SpotifyWebApiBundle\SpotifyClient` subclassing `SpotifyWebAPI\SpotifyWebAPI` with automatic token freshness checking and retry handling on expired token errors (`401`).
- Added in-memory token caching with timestamp tracking (55-minute TTL) and force-refresh capabilities to `TokenProvider`.
- Solved token expiration after 60 minutes for long-running processes (CLI commands and Symfony Messenger background workers).
- Added `SpotifyClientFactory` with runtime credential validation (`client_id`, `client_secret`) and actionable configuration error instructions.
- Added `tests/Fixtures/TestKernel.php` for clean, isolated testing.
- Created `UPGRADE.md` guide.
- Added comprehensive unit test suite in `tests/Unit/` reaching 100% code coverage across all classes, methods, and lines.
- Compatibility testing and CI matrix coverage for PHP 8.1–8.6 and Symfony 6.4 LTS, 7.x, 8.0, 8.1, and 8.2.
- Created root `phpstan.neon.dist` (Level 8) and added PHPStan static analysis.
- Created `.php-cs-fixer.dist.php` for PSR-12 and PSR-12:risky code styling.
- Added `DEVELOPMENT.md` guide.
- Added `.gitattributes` for clean release archives and updated `.gitignore`.

### Deprecated

- Deprecated `SpotifyWebApiFactory` in favor of `SpotifyClientFactory`.
- Deprecated `calliostro_spotify_web_api` service alias in favor of `calliostro_spotify_web_api.client`.
- Deprecated `SpotifyWebAPI\SpotifyWebAPI` autowiring alias in favor of `Calliostro\SpotifyWebApiBundle\SpotifyClient`.

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
