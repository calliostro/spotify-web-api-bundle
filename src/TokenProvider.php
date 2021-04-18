<?php

namespace Calliostro\SpotifyWebApiBundle;

use SpotifyWebAPI;

final class TokenProvider implements TokenProviderInterface
{
    private $session;

    public function __construct(SpotifyWebAPI\Session $session)
    {
        $this->session = $session;
    }

    public function getAccessToken(): string
    {
        $this->session->requestCredentialsToken();

        return $this->session->getAccessToken();
    }
}
