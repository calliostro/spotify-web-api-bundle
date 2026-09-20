<?php

declare(strict_types=1);

namespace Calliostro\SpotifyWebApiBundle\Tests\Integration;

use Calliostro\SpotifyWebApiBundle\SpotifyClient;
use Calliostro\SpotifyWebApiBundle\Tests\Fixtures\TestKernel;
use Calliostro\SpotifyWebApiBundle\TokenProviderInterface;
use PHPUnit\Framework\TestCase;
use SpotifyWebAPI\Session;
use SpotifyWebAPI\SpotifyWebAPI;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class FunctionalTest extends TestCase
{
    public function testServiceWiring(): void
    {
        $kernel = new FunctionalTestKernel([
            'client_id' => 'valid_client_id_123',
            'client_secret' => 'valid_client_secret_123',
            'token_provider' => 'fake_token_provider',
        ]);
        $kernel->boot();
        $container = $kernel->getContainer();

        $clientByOldId = $container->get('calliostro_spotify_web_api');
        $this->assertInstanceOf(SpotifyClient::class, $clientByOldId);
        $this->assertInstanceOf(SpotifyWebAPI::class, $clientByOldId);

        $clientByNewId = $container->get('calliostro_spotify_web_api.client');
        $this->assertSame($clientByOldId, $clientByNewId);

        $session = $container->get('calliostro_spotify_web_api.session');
        $this->assertInstanceOf(Session::class, $session);
    }
}

class FunctionalTestKernel extends TestKernel
{
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        parent::registerContainerConfiguration($loader);

        $loader->load(function (ContainerBuilder $container) {
            $container->register('fake_token_provider', FakeTokenProvider::class);
        });
    }
}

class FakeTokenProvider implements TokenProviderInterface
{
    public function getAccessToken(bool $forceRefresh = false): string
    {
        return 'some access token';
    }
}
