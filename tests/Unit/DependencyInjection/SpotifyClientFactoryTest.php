<?php

declare(strict_types=1);

namespace Calliostro\SpotifyWebApiBundle\Tests\Unit\DependencyInjection;

use Calliostro\SpotifyWebApiBundle\DependencyInjection\SpotifyClientFactory;
use Calliostro\SpotifyWebApiBundle\SpotifyClient;
use Calliostro\SpotifyWebApiBundle\TokenProviderInterface;
use PHPUnit\Framework\TestCase;

final class SpotifyClientFactoryTest extends TestCase
{
    private SpotifyClientFactory $factory;
    private TokenProviderInterface $tokenProvider;

    protected function setUp(): void
    {
        $this->factory = new SpotifyClientFactory();
        $this->tokenProvider = $this->createMock(TokenProviderInterface::class);
        $this->tokenProvider->method('getAccessToken')->willReturn('valid_token');
    }

    public function testCreateClientWithValidCredentials(): void
    {
        $client = $this->factory->createClient(
            'valid_client_id_123',
            'valid_client_secret_123',
            'https://example.com/callback',
            $this->tokenProvider,
            ['return_assoc' => true]
        );

        $this->assertInstanceOf(SpotifyClient::class, $client);
    }

    public function testCreateClientWithMissingClientId(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing Spotify API credentials');

        $this->factory->createClient(
            '',
            'valid_client_secret_123',
            null,
            $this->tokenProvider
        );
    }

    public function testCreateClientWithMissingClientSecret(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing Spotify API credentials');

        $this->factory->createClient(
            'valid_client_id_123',
            '',
            null,
            $this->tokenProvider
        );
    }

    public function testCreateClientWithShortClientId(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Spotify client_id appears invalid');

        $this->factory->createClient(
            'short',
            'valid_client_secret_123',
            null,
            $this->tokenProvider
        );
    }

    public function testCreateClientWithShortClientSecret(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Spotify client_secret appears invalid');

        $this->factory->createClient(
            'valid_client_id_123',
            'short',
            null,
            $this->tokenProvider
        );
    }
}
