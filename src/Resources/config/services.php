<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Calliostro\SpotifyWebApiBundle\DependencyInjection\SpotifyClientFactory;
use Calliostro\SpotifyWebApiBundle\SpotifyClient;
use Calliostro\SpotifyWebApiBundle\TokenProvider;
use Calliostro\SpotifyWebApiBundle\TokenProviderInterface;
use SpotifyWebAPI\Session;
use SpotifyWebAPI\SpotifyWebAPI;

return static function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();

    // Session service - arguments will be set by the extension
    $services->set('calliostro_spotify_web_api.session', Session::class)
        ->public();

    // Token provider - session argument will be set by the extension
    $services->set('calliostro_spotify_web_api.token_provider', TokenProvider::class)
        ->public()
        ->args([
            service('calliostro_spotify_web_api.session'),
        ]);

    // Spotify client factory
    $services->set('calliostro_spotify_web_api.client_factory', SpotifyClientFactory::class);

    // Main Spotify Client service - arguments will be set by the extension
    $services->set('calliostro_spotify_web_api.client', SpotifyClient::class)
        ->public()
        ->factory([service('calliostro_spotify_web_api.client_factory'), 'createClient']);

    // Backward compatibility alias for main service
    $services->alias('calliostro_spotify_web_api', 'calliostro_spotify_web_api.client')
        ->public()
        ->deprecate(
            'calliostro/spotify-web-api-bundle',
            '1.4',
            'The "%alias_id%" service alias is deprecated, use "calliostro_spotify_web_api.client" or type-hint "Calliostro\SpotifyWebApiBundle\SpotifyClient" instead.'
        );

    // Autowiring aliases
    $services->alias(SpotifyClient::class, 'calliostro_spotify_web_api.client');
    $services->alias(SpotifyWebAPI::class, 'calliostro_spotify_web_api.client')
        ->deprecate(
            'calliostro/spotify-web-api-bundle',
            '1.4',
            'The "%alias_id%" autowiring alias is deprecated, type-hint "Calliostro\SpotifyWebApiBundle\SpotifyClient" instead.'
        );
    $services->alias(Session::class, 'calliostro_spotify_web_api.session');
    $services->alias(TokenProviderInterface::class, 'calliostro_spotify_web_api.token_provider');
};
