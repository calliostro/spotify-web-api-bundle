<?php

declare(strict_types=1);

namespace Calliostro\SpotifyWebApiBundle\DependencyInjection;

use Calliostro\SpotifyWebApiBundle\SpotifyClient;
use Calliostro\SpotifyWebApiBundle\TokenProviderInterface;
use SpotifyWebAPI\Session;

final class SpotifyClientFactory
{
    /**
     * @param array<string, mixed> $options
     */
    public function createClient(
        ?string $clientId,
        ?string $clientSecret,
        ?string $redirectUri,
        TokenProviderInterface $tokenProvider,
        array $options = [],
        ?Session $session = null,
    ): SpotifyClient {
        $clientId = $clientId !== null ? trim($clientId) : '';
        $clientSecret = $clientSecret !== null ? trim($clientSecret) : '';

        $this->validateCredentials($clientId, $clientSecret);

        return new SpotifyClient($tokenProvider, $options, $session);
    }

    private function validateCredentials(string $clientId, string $clientSecret): void
    {
        if ($clientId === '' || $clientSecret === '') {
            throw new \InvalidArgumentException(
                'Missing Spotify API credentials. Both client_id and client_secret are required to initialize the Spotify client.' . $this->getSetupInstructions()
            );
        }

        if (\strlen($clientId) < 10) {
            throw new \InvalidArgumentException(\sprintf(
                'Spotify client_id appears invalid (expected at least 10 characters, got %d). %s',
                \strlen($clientId),
                $this->getSetupInstructions()
            ));
        }

        if (\strlen($clientSecret) < 10) {
            throw new \InvalidArgumentException(\sprintf(
                'Spotify client_secret appears invalid (expected at least 10 characters, got %d). %s',
                \strlen($clientSecret),
                $this->getSetupInstructions()
            ));
        }
    }

    private function getSetupInstructions(): string
    {
        return "\n\nTo configure Spotify API credentials:\n" .
            "1. Register an application at: https://developer.spotify.com/dashboard/applications\n" .
            "2. Set environment variables in your .env or .env.local file:\n" .
            "   SPOTIFY_CLIENT_ID=your_client_id_here\n" .
            "   SPOTIFY_CLIENT_SECRET=your_client_secret_here\n" .
            "3. Ensure config/packages/calliostro_spotify_web_api.yaml is configured:\n" .
            "   calliostro_spotify_web_api:\n" .
            "       client_id: '%env(SPOTIFY_CLIENT_ID)%'\n" .
            "       client_secret: '%env(SPOTIFY_CLIENT_SECRET)%'\n";
    }
}
