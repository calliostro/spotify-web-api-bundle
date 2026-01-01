<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Calliostro\SpotifyWebApiBundle\SpotifyWebApiFactory;
use Calliostro\SpotifyWebApiBundle\TokenProvider;
use SpotifyWebAPI\Session;
use SpotifyWebAPI\SpotifyWebAPI;

return static function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();

    $services->set('calliostro_spotify_web_api.session', Session::class)
        ->public()
        ->args([
            null, // client_id - set by extension
            null, // client_secret - set by extension
            null, // redirect_uri - set by extension
        ]);

    $services->set('calliostro_spotify_web_api.token_provider', TokenProvider::class)
        ->args([
            null, // session - set by extension
        ]);

    $services->set('calliostro_spotify_web_api', SpotifyWebAPI::class)
        ->public()
        ->factory([SpotifyWebApiFactory::class, 'factory'])
        ->args([
            null, // token_provider - set by extension
            null, // options - set by extension
        ]);

    // Aliases for autowiring
    $services->alias(Session::class, 'calliostro_spotify_web_api.session');
    $services->alias(SpotifyWebAPI::class, 'calliostro_spotify_web_api');
};
