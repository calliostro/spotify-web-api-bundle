<?php

namespace Calliostro\SpotifyWebApiBundle;

use SpotifyWebAPI\SpotifyWebAPI;

final class SpotifyWebApiFactory
{
    public static function factory(TokenProviderInterface $tokenProvider, array $options = []): SpotifyWebAPI
    {
        $api = new SpotifyWebAPI($options);
        $api->setAccessToken($tokenProvider->getAccessToken());

        return $api;
    }
}
