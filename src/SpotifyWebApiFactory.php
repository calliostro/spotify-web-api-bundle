<?php

namespace Calliostro\SpotifyWebApiBundle;

use SpotifyWebAPI\SpotifyWebAPI;

final class SpotifyWebApiFactory
{
    public static function factory(TokenProviderInterface $tokenProvider): SpotifyWebAPI
    {
        $api = new SpotifyWebAPI();
        $api->setAccessToken($tokenProvider->getAccessToken());

        return $api;
    }
}
