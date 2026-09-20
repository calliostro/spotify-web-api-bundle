<?php

declare(strict_types=1);

namespace Calliostro\SpotifyWebApiBundle\Tests\Unit;

use Calliostro\SpotifyWebApiBundle\TokenProvider;
use Calliostro\SpotifyWebApiBundle\TokenProviderInterface;
use PHPUnit\Framework\TestCase;
use SpotifyWebAPI\Session;

final class TokenProviderTest extends TestCase
{
    public function testGetAccessTokenCachesInMemory(): void
    {
        $session = $this->createMock(Session::class);
        $session->expects($this->once())
            ->method('requestCredentialsToken');
        $session->expects($this->once())
            ->method('getAccessToken')
            ->willReturn('mock_access_token');

        $tokenProvider = new TokenProvider($session, 3300);

        $this->assertInstanceOf(TokenProviderInterface::class, $tokenProvider);
        $this->assertSame(3300, $tokenProvider->getTtl());

        // First call should request token
        $this->assertSame('mock_access_token', $tokenProvider->getAccessToken());
        $this->assertFalse($tokenProvider->isExpired());

        // Second call should return cached token without calling session again
        $this->assertSame('mock_access_token', $tokenProvider->getAccessToken());
    }

    public function testForceRefreshBypassesCache(): void
    {
        $session = $this->createMock(Session::class);
        $session->expects($this->exactly(2))
            ->method('requestCredentialsToken');
        $session->expects($this->exactly(2))
            ->method('getAccessToken')
            ->willReturnOnConsecutiveCalls('token_1', 'token_2');

        $tokenProvider = new TokenProvider($session, 3300);

        $this->assertSame('token_1', $tokenProvider->getAccessToken(false));
        $this->assertSame('token_2', $tokenProvider->getAccessToken(true));
    }

    public function testTokenRefreshesWhenExpired(): void
    {
        $session = $this->createMock(Session::class);
        $session->expects($this->exactly(2))
            ->method('requestCredentialsToken');
        $session->expects($this->exactly(2))
            ->method('getAccessToken')
            ->willReturnOnConsecutiveCalls('token_initial', 'token_after_expiry');

        // Set TTL to 0 so it immediately expires
        $tokenProvider = new TokenProvider($session, -1);

        $this->assertSame('token_initial', $tokenProvider->getAccessToken());
        $this->assertTrue($tokenProvider->isExpired());

        $this->assertSame('token_after_expiry', $tokenProvider->getAccessToken());
    }

    public function testResetClearsCache(): void
    {
        $session = $this->createMock(Session::class);
        $session->expects($this->exactly(2))
            ->method('requestCredentialsToken');
        $session->expects($this->exactly(2))
            ->method('getAccessToken')
            ->willReturnOnConsecutiveCalls('token_1', 'token_2');

        $tokenProvider = new TokenProvider($session);

        $this->assertTrue($tokenProvider->isExpired());
        $this->assertSame('token_1', $tokenProvider->getAccessToken());
        $this->assertFalse($tokenProvider->isExpired());

        $tokenProvider->reset();
        $this->assertTrue($tokenProvider->isExpired());

        $this->assertSame('token_2', $tokenProvider->getAccessToken());
    }
}
