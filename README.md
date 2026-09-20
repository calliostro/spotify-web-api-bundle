# Spotify Web API Bundle for Symfony

[![Package Version](https://img.shields.io/packagist/v/calliostro/spotify-web-api-bundle.svg)](https://packagist.org/packages/calliostro/spotify-web-api-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/calliostro/spotify-web-api-bundle.svg)](https://packagist.org/packages/calliostro/spotify-web-api-bundle)
[![License](https://poser.pugx.org/calliostro/spotify-web-api-bundle/license)](https://packagist.org/packages/calliostro/spotify-web-api-bundle)
[![PHP Version](https://img.shields.io/badge/php-%5E8.1-blue.svg)](https://php.net)
[![CI](https://github.com/calliostro/spotify-web-api-bundle/actions/workflows/ci.yml/badge.svg)](https://github.com/calliostro/spotify-web-api-bundle/actions/workflows/ci.yml)
[![Code Coverage](https://codecov.io/gh/calliostro/spotify-web-api-bundle/graph/badge.svg)](https://codecov.io/gh/calliostro/spotify-web-api-bundle)
[![PHPStan Level](https://img.shields.io/badge/PHPStan-level%208-brightgreen.svg)](https://phpstan.org/)
[![Code Style](https://img.shields.io/badge/code%20style-PSR12-brightgreen.svg)](https://github.com/FriendsOfPHP/PHP-CS-Fixer)

A resilient Symfony bundle integrating [`jwilsson/spotify-web-api-php`](https://github.com/jwilsson/spotify-web-api-php) into your Symfony application. Features automated token management for long-running CLI commands and Symfony Messenger background workers, dependency injection, autowiring, and support for Client Credentials & Authorization Code flows on PHP 8.1+ and Symfony 6.4, 7.x, and 8.x.

## 📦 Installation

Install via Composer:

```bash
composer require calliostro/spotify-web-api-bundle
```

---

## ⚙️ Configuration

Register your application on the [Spotify Developer Dashboard](https://developer.spotify.com/dashboard/applications) to obtain your `client_id` and `client_secret`.

Configure the bundle in `config/packages/calliostro_spotify_web_api.yaml`:

```yaml
calliostro_spotify_web_api:
    # Your Client ID from the Spotify Developer Dashboard
    client_id: '%env(SPOTIFY_CLIENT_ID)%'

    # Your Client Secret
    client_secret: '%env(SPOTIFY_CLIENT_SECRET)%'

    # Address to redirect to after authentication success OR failure (required for Authorization Code flow)
    redirect_uri: '%env(SPOTIFY_REDIRECT_URI)%'

    # Optional: Client options for jwilsson/spotify-web-api-php
    options:
        auto_refresh: false
        auto_retry: false
        return_assoc: false

    # Optional: Custom token provider service (defaults to built-in Client Credentials TokenProvider)
    # token_provider: calliostro_spotify_web_api.token_provider
```

> [!NOTE]
> If you are using the Authorization Code flow, make sure to allowlist your `redirect_uri` (e.g. `https://127.0.0.1:8000/callback/`) in your Spotify Developer App settings.

---

## 🚀 Quick Start

### 1. Client Credentials Flow (Machine-to-Machine)

For public data endpoints (searching tracks, getting artist info, browsing playlists), inject `SpotifyClient` directly into your controllers, services, or console commands:

```php
<?php

namespace App\Controller;

use Calliostro\SpotifyWebApiBundle\SpotifyClient;
use Symfony\Component\HttpFoundation\JsonResponse;

final class MusicController
{
    public function search(SpotifyClient $spotify): JsonResponse
    {
        $results = $spotify->search('Billie Eilish', 'artist');

        return new JsonResponse($results);
    }
}
```

> [!TIP]
> Type-hinting `Calliostro\SpotifyWebApiBundle\SpotifyClient` is recommended. It extends `SpotifyWebAPI\SpotifyWebAPI`, ensuring full backward compatibility while providing automated token freshness checks and retry handling for long-running processes.

### 2. Authorization Code Flow (User Data)

To access private user data (playlists, saved tracks, top artists), inject both `SpotifyClient` and `Session`:

```php
<?php

namespace App\Controller;

use Calliostro\SpotifyWebApiBundle\SpotifyClient;
use SpotifyWebAPI\Session;
use SpotifyWebAPI\SpotifyWebAPIAuthException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class SpotifyController extends AbstractController
{
    public function __construct(
        private readonly SpotifyClient $spotify,
        private readonly Session $session,
    ) {
    }

    #[Route('/authorize', name: 'spotify_authorize')]
    public function authorize(): RedirectResponse
    {
        $options = [
            'scope' => [
                'user-read-email',
                'user-read-private',
                'playlist-read-private',
            ],
        ];

        return $this->redirect($this->session->getAuthorizeUrl($options));
    }

    #[Route('/callback', name: 'spotify_callback')]
    public function callback(Request $request): Response
    {
        $code = $request->query->getString('code');

        try {
            $this->session->requestAccessToken($code);
        } catch (SpotifyWebAPIAuthException) {
            return $this->redirectToRoute('spotify_authorize');
        }

        $this->spotify->setAccessToken($this->session->getAccessToken());
        $user = $this->spotify->me();

        return new Response(sprintf('<h1>Hello, %s!</h1>', htmlspecialchars($user->display_name ?? 'Spotify User')));
    }
}
```

---

## ⚡ Long-Running Processes (CLI & Messenger Workers)

Spotify OAuth access tokens expire strictly after 3,600 seconds (1 hour). In standard setups, long-running CLI commands or Symfony Messenger background workers (`bin/console messenger:consume`) crash with a `401 Expired Token` error after 60 minutes.

This bundle solves this problem automatically out of the box:
1. **In-Memory Caching:** `TokenProvider` caches the access token in memory with an automatic freshness threshold (55 minutes).
2. **Pre-emptive Refresh:** Before any API call is sent, `SpotifyClient` ensures the token is still valid and refreshes it transparently if needed.
3. **Self-Healing Retries:** If Spotify returns an expired token exception, `SpotifyClient` catches it, forces a token refresh, and retries the request once before failing.

Your workers and daemon commands can run for days without interruption or manual token management.

---

## ✨ Key Features

- **Resilient `SpotifyClient`** – Extends `SpotifyWebAPI` with transparent token refresh and self-healing retries.
- **Daemon & CLI Ready** – Runs indefinitely in Symfony Messenger workers and console commands without 60-minute token expiration crashes.
- **Runtime Credential Validation** – Clear, actionable error messages pointing to your Spotify dashboard when credentials are missing.
- **Seamless Autowiring** – Type-hint `SpotifyClient` (recommended) or `SpotifyWebAPI` (deprecated alias) and `Session`.
- **Dual Flow Support** – Out-of-the-box support for both Client Credentials and Authorization Code flows.
- **Client Options** – Easily toggle `auto_refresh`, `auto_retry`, and `return_assoc` via YAML configuration.
- **Type Safety & IDE Support** – PHP 8.1+ types, strict types, and PHPStan Level 8 static analysis.
- **Symfony Native** – Full compatibility with Symfony 6.4 LTS, 7.x, and 8.x.

---

## 📋 Requirements

- **PHP** `^8.1` (tested on PHP 8.1–8.6)
- **Symfony** `^6.4 || ^7.0 || ^8.0`
- **jwilsson/spotify-web-api-php** `^6.0 || ^7.0`

---

## 🧪 Development & Testing Guide

See [DEVELOPMENT.md](DEVELOPMENT.md) for detailed setup instructions, test suite commands, static analysis, and contribution guidelines.

---

## 🤝 Contributing

Contributions are welcome! Please ensure that all tests pass and coding standards are maintained:

```bash
composer cs-fix
composer analyse
composer test-all
```

---

## 📄 License

This project is licensed under the MIT License — see the [LICENSE](LICENSE) file for details.

---

## ⚖️ Disclaimer

Spotify is a registered trademark of Spotify AB. This project is an independent, unofficial open-source bundle and is not affiliated with, endorsed by, or sponsored by Spotify AB.

---

## 🙏 Acknowledgments

- [jwilsson/spotify-web-api-php](https://github.com/jwilsson/spotify-web-api-php) for the underlying Spotify Web API client.
- [Symfony](https://symfony.com) for the web framework and dependency injection container.
- Sister Symfony bundles:
  - [`calliostro/discogs-bundle`](https://github.com/calliostro/discogs-bundle) – Symfony bundle for the Discogs API.
  - [`calliostro/lastfm-bundle`](https://github.com/calliostro/last-fm-client-bundle) – Symfony bundle for the Last.fm API.
  - [`calliostro/spotify-bundle`](https://github.com/calliostro/spotify-bundle) – Lightweight Symfony bundle for [`calliostro/spotify-client`](https://github.com/calliostro/spotify-client).
  - [`calliostro/musicbrainz-bundle`](https://github.com/calliostro/musicbrainz-bundle) – Symfony bundle for the MusicBrainz API.
