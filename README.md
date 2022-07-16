Spotify Web API Bundle
======================

[![Build Status](https://app.travis-ci.com/calliostro/spotify-web-api-bundle.svg?branch=main)](https://www.travis-ci.com/github/calliostro/spotify-web-api-bundle)
[![Version](https://poser.pugx.org/calliostro/spotify-web-api-bundle/version)](//packagist.org/packages/calliostro/spotify-web-api-bundle)
[![License](https://poser.pugx.org/calliostro/spotify-web-api-bundle/license)](//packagist.org/packages/calliostro/spotify-web-api-bundle)

This bundle provides a simple integration of [jwilsson/spotify-web-api-php](https://github.com/jwilsson/spotify-web-api-php)
into Symfony 5 or Symfony 6.


Installation
------------

Make sure Composer is installed globally, as explained in the 
[installation chapter](https://getcomposer.org/doc/00-intro.md) of the Composer documentation.

### Applications that use Symfony Flex

Open a command console, enter your project directory and execute:

```console
$ composer require calliostro/spotify-web-api-bundle
```

### Applications that don't use Symfony Flex

#### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require calliostro/spotify-web-api-bundle
```

#### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    Calliostro\SpotifyWebApiBundle\CalliostroSpotifyWebApiBundle::class => ['all' => true],
];
```


Configuration
-------------

First, you must register your application at https://developer.spotify.com/dashboard/applications to obtain the 
`client_id` and `client_secret`.

If you want to access user-related endpoints, the user must grant access to your application. Spotify provides OAuth 2.0
for this purpose. You need to register the `redirect_uri` in the Spotify dashboard. For the following example, you would
add `https://127.0.0.1:8000/callback/` to the whitelist addresses.

For configuration, create a new `config/packages/calliostro_spotify_web_api.yaml`  file. Here is an example:

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
    auto_refresh:         false
    auto_retry:           false
    return_assoc:         false

  # Address to redirect to after authentication success OR failure
  redirect_uri:         '' # Example: 'https://127.0.0.1:8000/callback/'

  # Service ID of the token provider that provides the user's access token
  token_provider:       calliostro_spotify_web_api.token_provider
```


Usage
-----

This bundle provides a single service for communication with Spotify Web API, which you can autowire by using the
`SpotifyWebAPI` and `Session` type-hint:


### Client Credentials

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

### Authorization Code

If you want to access a Spotify user's profile or data, you must first redirect the user to Spotify's approval page.
Then you can start the session.

```php
// src/Controller/SomeController.php

namespace App\Controller;

use SpotifyWebAPI\Session;
use SpotifyWebAPI\SpotifyWebAPI;
use SpotifyWebAPI\SpotifyWebAPIAuthException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SomeController extends AbstractController
{
    private $api;
    private $session;

    public function __construct(SpotifyWebAPI $api, Session $session)
    {
        $this->api = $api;
        $this->session = $session;
    }

    /**
     * @Route("/callback")
     */
    public function callbackFromSpotify(Request $request): Response
    {
        try {
            $this->session->requestAccessToken($request->query->get('code'));
        } catch (SpotifyWebAPIAuthException $e) {
            return $this->redirectToRoute('some_redirect');
        }

        $this->api->setAccessToken($this->session->getAccessToken());
        $me = $this->api->me();

        return new Response(var_export($me, false), 200, ['Content-Type' => 'text/plain']);
    }

    /**
     * @Route("/redirect", name="some_redirect")
     */
    public function redirectToSpotify(): Response
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

Don't forget to set `redirect_uri` in the configuration file and whitelist it on Spotify.



Documentation
-------------

See [jwilsson/spotify-web-api-php](https://github.com/jwilsson/spotify-web-api-php) for documentation of the 
SpotifyWebAPI service.

See [Spotify's Web API](https://developer.spotify.com/documentation/) full API documentation.


Contributing
------------

Implemented a missing feature? You can request it. And creating a pull request is an even better way to get things done.
