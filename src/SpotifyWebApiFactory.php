<?php

declare(strict_types=1);

namespace Calliostro\SpotifyWebApiBundle;

use SpotifyWebAPI\SpotifyWebAPI;

/**
 * @deprecated since calliostro/spotify-web-api-bundle 1.4, use Calliostro\SpotifyWebApiBundle\DependencyInjection\SpotifyClientFactory instead.
 */
final class SpotifyWebApiFactory
{
    /**
     * @param array<string, mixed> $options
     *
     * @deprecated since calliostro/spotify-web-api-bundle 1.4, use Calliostro\SpotifyWebApiBundle\SpotifyClient instead.
     */
    public static function factory(TokenProviderInterface $tokenProvider, array $options = []): SpotifyWebAPI
    {
        trigger_deprecation('calliostro/spotify-web-api-bundle', '1.4', 'The "%s" class is deprecated, use "%s" instead.', self::class, SpotifyClient::class);

        return new SpotifyClient($tokenProvider, $options);
    }
}
