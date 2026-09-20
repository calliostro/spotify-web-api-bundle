<?php

declare(strict_types=1);

namespace Calliostro\SpotifyWebApiBundle;

use SpotifyWebAPI\Request;
use SpotifyWebAPI\Session;
use SpotifyWebAPI\SpotifyWebAPI;
use SpotifyWebAPI\SpotifyWebAPIException;

class SpotifyClient extends SpotifyWebAPI
{
    /**
     * @param array<string, mixed>|object $options
     */
    public function __construct(
        private readonly TokenProviderInterface $tokenProvider,
        array|object $options = [],
        ?Session $session = null,
        ?Request $request = null,
    ) {
        parent::__construct($options, $session, $request);

        $this->setAccessToken($this->tokenProvider->getAccessToken());
    }

    public function getTokenProvider(): TokenProviderInterface
    {
        return $this->tokenProvider;
    }

    /**
     * {@inheritdoc}
     *
     * @param string|array<string, mixed> $parameters
     * @param array<string, mixed>        $headers
     *
     * @return array<string, mixed>
     */
    protected function sendRequest(
        string $method,
        string $uri,
        string|array $parameters = [],
        array $headers = [],
    ): array {
        // Pre-emptively ensure token is fresh before sending request
        $this->setAccessToken($this->tokenProvider->getAccessToken());

        try {
            return parent::sendRequest($method, $uri, $parameters, $headers);
        } catch (SpotifyWebAPIException $e) {
            if ($e->hasExpiredToken()) {
                // Force token refresh and retry request once
                $this->setAccessToken($this->tokenProvider->getAccessToken(true));

                return parent::sendRequest($method, $uri, $parameters, $headers);
            }

            throw $e;
        }
    }
}
