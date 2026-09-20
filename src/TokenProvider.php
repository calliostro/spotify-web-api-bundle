<?php

declare(strict_types=1);

namespace Calliostro\SpotifyWebApiBundle;

use SpotifyWebAPI\Session;

final class TokenProvider implements TokenProviderInterface
{
    private ?string $cachedToken = null;
    private ?int $tokenExpiresAt = null;

    public function __construct(
        private readonly Session $session,
        private readonly int $ttl = 3300,
    ) {
    }

    public function getAccessToken(bool $forceRefresh = false): string
    {
        if ($forceRefresh || $this->cachedToken === null || $this->isExpired()) {
            $this->session->requestCredentialsToken();
            $this->cachedToken = $this->session->getAccessToken();
            $this->tokenExpiresAt = time() + $this->ttl;
        }

        return $this->cachedToken;
    }

    public function getTtl(): int
    {
        return $this->ttl;
    }

    public function isExpired(): bool
    {
        if ($this->tokenExpiresAt === null) {
            return true;
        }

        return time() >= $this->tokenExpiresAt;
    }

    public function reset(): void
    {
        $this->cachedToken = null;
        $this->tokenExpiresAt = null;
    }
}
