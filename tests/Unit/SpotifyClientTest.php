<?php

declare(strict_types=1);

namespace Calliostro\SpotifyWebApiBundle\Tests\Unit;

use Calliostro\SpotifyWebApiBundle\SpotifyClient;
use Calliostro\SpotifyWebApiBundle\TokenProviderInterface;
use PHPUnit\Framework\TestCase;
use SpotifyWebAPI\Request;
use SpotifyWebAPI\SpotifyWebAPI;
use SpotifyWebAPI\SpotifyWebAPIException;

final class SpotifyClientTest extends TestCase
{
    public function testInstantiationAndTokenProvider(): void
    {
        $tokenProvider = $this->createMock(TokenProviderInterface::class);
        $tokenProvider->expects($this->once())
            ->method('getAccessToken')
            ->willReturn('initial_token');

        $client = new SpotifyClient($tokenProvider);

        $this->assertInstanceOf(SpotifyWebAPI::class, $client);
        $this->assertSame($tokenProvider, $client->getTokenProvider());

        $reflection = new \ReflectionClass($client);
        $tokenProp = $reflection->getProperty('accessToken');
        $tokenProp->setAccessible(true);
        $this->assertSame('initial_token', $tokenProp->getValue($client));
    }

    public function testSendRequestPreemptivelyRefreshesToken(): void
    {
        $tokenProvider = $this->createMock(TokenProviderInterface::class);
        $tokenProvider->expects($this->exactly(2))
            ->method('getAccessToken')
            ->with(false)
            ->willReturnOnConsecutiveCalls('token_v1', 'token_v2');

        $request = $this->createMock(Request::class);
        $request->expects($this->once())
            ->method('api')
            ->with(
                'GET',
                '/v1/me',
                [],
                $this->callback(function (array $headers) {
                    return isset($headers['Authorization']) && $headers['Authorization'] === 'Bearer token_v2';
                })
            )
            ->willReturn([
                'body' => (object) ['id' => 'user123'],
                'headers' => [],
                'status' => 200,
                'url' => 'https://api.spotify.com/v1/me',
            ]);

        $client = new SpotifyClient($tokenProvider, [], null, $request);
        $result = $client->me();

        $this->assertSame('user123', $result->id);
    }

    public function testSendRequestRetriesOnExpiredTokenException(): void
    {
        $tokenProvider = $this->createMock(TokenProviderInterface::class);
        $tokenProvider->expects($this->exactly(3))
            ->method('getAccessToken')
            ->willReturnCallback(function (bool $forceRefresh = false) {
                static $calls = 0;
                $calls++;

                if ($calls === 1) {
                    return 'expired_token'; // on constructor
                }
                if ($calls === 2) {
                    return 'expired_token'; // pre-emptive check
                }

                // $calls === 3: forceRefresh is true
                return 'fresh_token';
            });

        $request = $this->createMock(Request::class);
        $request->expects($this->exactly(2))
            ->method('api')
            ->willReturnOnConsecutiveCalls(
                $this->throwException(new SpotifyWebAPIException(SpotifyWebAPIException::TOKEN_EXPIRED)),
                [
                    'body' => (object) ['id' => 'recovered_user'],
                    'headers' => [],
                    'status' => 200,
                    'url' => 'https://api.spotify.com/v1/me',
                ]
            );

        $client = new SpotifyClient($tokenProvider, [], null, $request);
        $result = $client->me();

        $this->assertSame('recovered_user', $result->id);
    }

    public function testSendRequestRethrowsNonExpiredTokenException(): void
    {
        $tokenProvider = $this->createMock(TokenProviderInterface::class);
        $tokenProvider->expects($this->exactly(2))
            ->method('getAccessToken')
            ->willReturn('valid_token');

        $request = $this->createMock(Request::class);
        $request->expects($this->once())
            ->method('api')
            ->willThrowException(new SpotifyWebAPIException('Some random API error', 500));

        $client = new SpotifyClient($tokenProvider, [], null, $request);

        $this->expectException(SpotifyWebAPIException::class);
        $this->expectExceptionMessage('Some random API error');

        $client->me();
    }
}
