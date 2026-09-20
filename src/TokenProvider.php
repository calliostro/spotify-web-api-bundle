<?php

declare(strict_types=1);

namespace Calliostro\SpotifyWebApiBundle;

use SpotifyWebAPI\Session;

final class TokenProvider implements TokenProviderInterface
{
    private Session $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function getAccessToken(): string
    {
        $this->session->requestCredentialsToken();

        return $this->session->getAccessToken();
    }
}
