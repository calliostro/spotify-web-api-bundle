<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Calliostro\SpotifyWebApiBundle\SpotifyWebApiFactory;
use Calliostro\SpotifyWebApiBundle\TokenProvider;
use SpotifyWebAPI\Session;
use SpotifyWebAPI\SpotifyWebAPI;

return static function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();

    // Session service - arguments will be set by the extension
    $services->set('calliostro_spotify_web_api.session', Session::class)
        ->public();

    // Token provider - arguments will be set by the extension
    $services->set('calliostro_spotify_web_api.token_provider', TokenProvider::class);

    // Main Spotify Web API service - arguments will be set by the extension
    $services->set('calliostro_spotify_web_api', SpotifyWebAPI::class)
        ->public()
        ->factory([SpotifyWebApiFactory::class, 'factory']);

    // Aliases for autowiring
    $services->alias(Session::class, 'calliostro_spotify_web_api.session');
    $services->alias(SpotifyWebAPI::class, 'calliostro_spotify_web_api');
};
