<?php

declare(strict_types=1);

namespace Calliostro\SpotifyWebApiBundle;

use SpotifyWebAPI\SpotifyWebAPI;

final class SpotifyWebApiFactory
{
    /**
     * @param array<string, mixed> $options
     */
    public static function factory(TokenProviderInterface $tokenProvider, array $options = []): SpotifyWebAPI
    {
        $api = new SpotifyWebAPI($options);
        $api->setAccessToken($tokenProvider->getAccessToken());

        return $api;
    }
}
