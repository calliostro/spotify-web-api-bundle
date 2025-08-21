
# 🎵 Spotify Web API Bundle

[![Build Status](https://api.travis-ci.com/calliostro/spotify-web-api-bundle.svg)](https://app.travis-ci.com/github/calliostro/spotify-web-api-bundle)
[![Version](https://poser.pugx.org/calliostro/spotify-web-api-bundle/version)](https://packagist.org/packages/calliostro/spotify-web-api-bundle)
[![License](https://poser.pugx.org/calliostro/spotify-web-api-bundle/license)](https://packagist.org/packages/calliostro/spotify-web-api-bundle)

> 🚀 **Easy integration of [jwilsson/spotify-web-api-php](https://github.com/jwilsson/spotify-web-api-php) into Symfony 6.4 & 7!**

## ✨ Features

- Simple integration with Symfony 6.4 & 7
- Supports Client Credentials & Authorization Code flows
- Autowire Spotify API services
- Customizable token provider
- Easy configuration

## 📦 Installation

Make sure Composer is installed globally, as explained in the [installation chapter](https://getcomposer.org/doc/00-intro.md) of the Composer documentation.

### ⚡ Applications that use Symfony Flex

Open a command console, enter your project directory and execute:

```console
composer require calliostro/spotify-web-api-bundle
```

### 🛠️ Applications that don't use Symfony Flex

#### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the following command to download the latest stable version of this bundle:

```console
composer require calliostro/spotify-web-api-bundle
```

#### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles in the `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    Calliostro\SpotifyWebApiBundle\CalliostroSpotifyWebApiBundle::class => ['all' => true],
];
```

## ⚙️ Configuration

First, you must register your application at https://developer.spotify.com/dashboard/applications to obtain the `client_id` and `client_secret`.

If you want to access user-related endpoints, the user must grant access to your application. Spotify provides OAuth 2.0 for this purpose. You need to register the `redirect_uri` in the Spotify dashboard. For the following example, you would add `https://127.0.0.1:8000/callback/` to the allowlist addresses.

For configuration, create a new `config/packages/calliostro_spotify_web_api.yaml` file. Here is an example:

```yaml
# config/packages/calliostro_spotify_web_api.yaml
calliostro_spotify_web_api:

    # Your Client ID
    client_id:            '' # Required

    # Your Client Secret
    client_secret:        '' # Required

    # Options for SpotifyWebAPI client
    # https://github.com/jwilsson/spotify-web-api-php/blob/main/docs/examples/setting-options.md
    options:
        auto_refresh:     false
        auto_retry:       false
        return_assoc:     false

    # Address to redirect to after authentication success OR failure
    redirect_uri:         '' # Example: 'https://127.0.0.1:8000/callback/'

    # Service ID of the token provider that provides the user's access token
    token_provider:       calliostro_spotify_web_api.token_provider
```

## 🎬 Usage

This bundle provides a single service for communication with Spotify Web API, which you can autowire by using the `SpotifyWebAPI` and `Session` type-hint:

### 🔑 Client Credentials

This is the simpler option if no user-related endpoints are required.

```php
// src/Controller/SomeController.php

use SpotifyWebAPI\SpotifyWebAPI;
// ...

class SomeController
{
    public function index(SpotifyWebAPI $api)
    {
        $search = $api->search('Thriller', 'album');

        var_dump($search);

        // ...
    }
}
```

### 🧑‍💻 Authorization Code

If you want to access a Spotify user's profile or data, you must first redirect the user to Spotify's approval page. Then you can start the session.

```php
// src/Controller/SpotifyController.php

namespace App\Controller;

use SpotifyWebAPI\Session;
use SpotifyWebAPI\SpotifyWebAPI;
use SpotifyWebAPI\SpotifyWebAPIAuthException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SpotifyController extends AbstractController
{
    public function __construct(
        private SpotifyWebAPI $api,
        private Session $session
    ) {}

    #[Route('/', name: 'home')]
    public function index(): Response
    {
        return new Response('
            <h1>Spotify Web API Demo</h1>
            <p>Welcome to the Spotify Web API Bundle demonstration!</p>
            <p><a href="/authorize">Click here to authorize with Spotify</a></p>
        ', 200, ['Content-Type' => 'text/html']);
    }

    #[Route('/callback')]
    public function callback(Request $request): Response
    {
        try {
            $this->session->requestAccessToken($request->query->getString('code'));
        } catch (SpotifyWebAPIAuthException) {
            return $this->redirectToRoute('authorize');
        }

        $this->api->setAccessToken($this->session->getAccessToken());
        $me = $this->api->me();

        return new Response('
            <h1>Spotify Authorization Successful!</h1>
            <p>Welcome, ' . htmlspecialchars($me->display_name ?? 'Spotify User') . '!</p>
            <pre>' . htmlspecialchars(var_export($me, true)) . '</pre>
            <p><a href="/">Back to Home</a></p>
        ', 200, ['Content-Type' => 'text/html']);
    }

    #[Route('/authorize', name: 'authorize')]
    public function authorize(): Response
    {
        $options = [
            'scope' => [
                'user-read-email',
            ],
        ];

        return $this->redirect($this->session->getAuthorizeUrl($options));
    }
}
```

> ⚠️ **Remember to set `redirect_uri` in the configuration file and allowlist it on Spotify!**

## 📚 Documentation

See [jwilsson/spotify-web-api-php](https://github.com/jwilsson/spotify-web-api-php) for documentation of the SpotifyWebAPI service.

See [Spotify's Web API](https://developer.spotify.com/documentation/) full API documentation.

## 🤝 Contributing

Implemented a missing feature? You can request it. And creating a pull request is an even better way to get things done.

---

## 🏁 Quick Start
1. Install the bundle with Composer
2. Configure your Spotify credentials
3. Autowire the service and start using the API!

## 💬 Support
For questions or help, feel free to open an issue or reach out! 😊
