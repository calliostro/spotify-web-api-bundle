# Spotify Web API Bundle for Symfony

[![Package Version](https://img.shields.io/packagist/v/calliostro/spotify-web-api-bundle.svg)](https://packagist.org/packages/calliostro/spotify-web-api-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/calliostro/spotify-web-api-bundle.svg)](https://packagist.org/packages/calliostro/spotify-web-api-bundle)
[![License](https://poser.pugx.org/calliostro/spotify-web-api-bundle/license)](https://packagist.org/packages/calliostro/spotify-web-api-bundle)
[![PHP Version](https://img.shields.io/badge/php-%5E8.1-blue.svg)](https://php.net)
[![CI](https://github.com/calliostro/spotify-web-api-bundle/actions/workflows/ci.yml/badge.svg)](https://github.com/calliostro/spotify-web-api-bundle/actions/workflows/ci.yml)
[![Code Coverage](https://codecov.io/gh/calliostro/spotify-web-api-bundle/graph/badge.svg)](https://codecov.io/gh/calliostro/spotify-web-api-bundle)
[![PHPStan Level](https://img.shields.io/badge/PHPStan-level%208-brightgreen.svg)](https://phpstan.org/)
[![Code Style](https://img.shields.io/badge/code%20style-PSR12-brightgreen.svg)](https://github.com/FriendsOfPHP/PHP-CS-Fixer)

A Symfony bundle integrating [`jwilsson/spotify-web-api-php`](https://github.com/jwilsson/spotify-web-api-php) into your Symfony application. Provides dependency injection, autowiring, customizable token management, and support for Client Credentials & Authorization Code flows for PHP 8.1+ and Symfony 6.4, 7.x, and 8.x.

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

For public data endpoints (searching tracks, getting artist info, browsing playlists), inject `SpotifyWebAPI` directly into your controllers or services. The built-in token provider automatically requests a Client Credentials token:

```php
<?php

namespace App\Controller;

use SpotifyWebAPI\SpotifyWebAPI;
use Symfony\Component\HttpFoundation\JsonResponse;

final class MusicController
{
    public function search(SpotifyWebAPI $api): JsonResponse
    {
        $results = $api->search('Billie Eilish', 'artist');

        return new JsonResponse($results);
    }
}
```

### 2. Authorization Code Flow (User Data)

To access private user data (playlists, saved tracks, top artists), inject both `SpotifyWebAPI` and `Session`:

```php
<?php

namespace App\Controller;

use SpotifyWebAPI\Session;
use SpotifyWebAPI\SpotifyWebAPI;
use SpotifyWebAPI\SpotifyWebAPIAuthException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class SpotifyController extends AbstractController
{
    public function __construct(
        private readonly SpotifyWebAPI $api,
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

        $this->api->setAccessToken($this->session->getAccessToken());
        $user = $this->api->me();

        return new Response(sprintf('<h1>Hello, %s!</h1>', htmlspecialchars($user->display_name ?? 'Spotify User')));
    }
}
```

---

## ✨ Key Features

- **Seamless Autowiring** – Type-hint `SpotifyWebAPI` and `Session` directly in your services and controllers.
- **Dual Flow Support** – Ready out of the box for both Client Credentials and Authorization Code flows.
- **Custom Token Providers** – Implement `TokenProviderInterface` to plug in your own token storage (Redis, database, session).
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
  - [`calliostro/musicbrainz-bundle`](https://github.com/calliostro/musicbrainz-bundle) – Symfony bundle for the MusicBrainz API.
  - [`calliostro/spotify-bundle`](https://github.com/calliostro/spotify-bundle) – Lightweight Symfony bundle for [`calliostro/spotify-client`](https://github.com/calliostro/spotify-client).
