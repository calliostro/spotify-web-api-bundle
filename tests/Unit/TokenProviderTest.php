<?php

declare(strict_types=1);

namespace Calliostro\SpotifyWebApiBundle\Tests\Unit;

use Calliostro\SpotifyWebApiBundle\TokenProvider;
use Calliostro\SpotifyWebApiBundle\TokenProviderInterface;
use PHPUnit\Framework\TestCase;
use SpotifyWebAPI\Session;

final class TokenProviderTest extends TestCase
{
    public function testGetAccessToken(): void
    {
        $session = $this->createMock(Session::class);
        $session->expects($this->once())
            ->method('requestCredentialsToken');
        $session->expects($this->once())
            ->method('getAccessToken')
            ->willReturn('mock_access_token');

        $tokenProvider = new TokenProvider($session);

        $this->assertInstanceOf(TokenProviderInterface::class, $tokenProvider);
        $this->assertSame('mock_access_token', $tokenProvider->getAccessToken());
    }
}
