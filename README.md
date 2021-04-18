Spotify Web API Bundle
======================

[![Build Status](https://api.travis-ci.com/calliostro/spotify-web-api-bundle.svg)](https://www.travis-ci.com/github/calliostro/spotify-web-api-bundle)
[![Version](https://poser.pugx.org/calliostro/spotify-web-api-bundle/version)](//packagist.org/packages/calliostro/spotify-web-api-bundle)
[![License](https://poser.pugx.org/calliostro/spotify-web-api-bundle/license)](//packagist.org/packages/calliostro/spotify-web-api-bundle)

This bundle provides a simple integration of [jwilsson/spotify-web-api-php](https://github.com/jwilsson/spotify-web-api-php)
into Symfony 5.


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

First, you must register your application at https://developer.spotify.com/dashboard/applications to get the 
`client_id` and `client_secret`. You should also register the `redirect_uri`.

For configuration create a new `config/packages/calliostro_spotify_web_api.yaml` file:

```yaml
# config/packages/calliostro_spotify_web_api.yaml
calliostro_spotify_web_api:

  # Your Client ID
  client_id: ~ # Required

  # Your Client Secret
  client_secret: ~ # Required

  # Address to redirect to after authentication success OR failure
  redirect_uri: ''

  # Service ID of the token provider that provides the user's access token
  token_provider: calliostro_spotify_web_api.token_provider
```


Usage
-----

### Spotify Web API

This bundle provides a single service for communication with Spotify Web API, which you can autowire by using the
`SpotifyWebAPI` type-hint:

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

### Spotify Session

You can also autowire the `Session` service with this type-hint. For example, to refresh the access token:

```php
// src/Controller/SomeOtherController.php

use SpotifyWebAPI\Session;
// ...

class SomeOtherController
{
    public function index(Session $session)
    {
        $session->refreshAccessToken(
            $session->getRefreshToken()
        );

        // ...
    }
}
```

Documentation
-------------

See [jwilsson/spotify-web-api-php](https://github.com/jwilsson/spotify-web-api-php) for documentation of the 
SpotifyWebAPI service.

See [Spotify's Web API](https://developer.spotify.com/documentation/) full API documentation.


Contributing
------------

Implemented a missing feature? You can request it. And creating a pull request is an even better way to get things done.
