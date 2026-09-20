<?php

declare(strict_types=1);

namespace Calliostro\SpotifyWebApiBundle\Tests\Unit;

use Calliostro\SpotifyWebApiBundle\SpotifyClient;
use Calliostro\SpotifyWebApiBundle\SpotifyWebApiFactory;
use Calliostro\SpotifyWebApiBundle\TokenProviderInterface;
use PHPUnit\Framework\TestCase;
use SpotifyWebAPI\SpotifyWebAPI;

final class SpotifyWebApiFactoryTest extends TestCase
{
    public function testFactoryCreatesClientWithTokenAndOptions(): void
    {
        $tokenProvider = $this->createMock(TokenProviderInterface::class);
        $tokenProvider->expects($this->once())
            ->method('getAccessToken')
            ->willReturn('factory_access_token');

        $options = [
            'auto_retry' => true,
            'return_assoc' => true,
        ];

        $client = SpotifyWebApiFactory::factory($tokenProvider, $options);

        $this->assertInstanceOf(SpotifyWebAPI::class, $client);
        $this->assertInstanceOf(SpotifyClient::class, $client);

        $reflection = new \ReflectionClass($client);
        $accessTokenProp = $reflection->getProperty('accessToken');
        $accessTokenProp->setAccessible(true);
        $this->assertSame('factory_access_token', $accessTokenProp->getValue($client));

        $optionsProp = $reflection->getProperty('options');
        $optionsProp->setAccessible(true);
        /** @var array<string, mixed> $actualOptions */
        $actualOptions = $optionsProp->getValue($client);
        $this->assertTrue($actualOptions['auto_retry']);
        $this->assertTrue($actualOptions['return_assoc']);
    }
}
