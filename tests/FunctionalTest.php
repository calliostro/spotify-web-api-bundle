<?php

namespace Calliostro\SpotifyWebApiBundle\Tests;

use Calliostro\SpotifyWebApiBundle\CalliostroSpotifyWebApiBundle;
use Calliostro\SpotifyWebApiBundle\TokenProviderInterface;
use PHPUnit\Framework\TestCase;
use SpotifyWebAPI\SpotifyWebAPI;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

final class FunctionalTest extends TestCase
{
    public function testServiceWiring(): void
    {
        $kernel = new CalliostroSpotifyWebApiTestingKernel([
            'client_id' => 'some client ID',
            'client_secret' => 'some client secret',
            'token_provider' => 'fake_token_provider',
        ]);
        $kernel->boot();
        $container = $kernel->getContainer();

        $SpotifyWebApi = $container->get('calliostro_spotify_web_api');
        $this->assertInstanceOf(SpotifyWebAPI::class, $SpotifyWebApi);
    }
}

class CalliostroSpotifyWebApiTestingKernel extends Kernel
{
    private $calliostroSpotifyWebApiConfig;

    public function __construct(array $calliostroSpotifyWebApiConfig = [])
    {
        $this->calliostroSpotifyWebApiConfig = $calliostroSpotifyWebApiConfig;

        parent::__construct('test', true);
    }

    public function registerBundles(): array
    {
        return [
            new CalliostroSpotifyWebApiBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(function (ContainerBuilder $container) {
            $container->register('fake_token_provider', FakeTokenProvider::class);

            $container->loadFromExtension('calliostro_spotify_web_api', $this->calliostroSpotifyWebApiConfig);
        });
    }

    public function getCacheDir(): string
    {
        return $this->getProjectDir() . '/var/cache/'.$this->environment.'/'.spl_object_hash($this);
    }
}

class FakeTokenProvider implements TokenProviderInterface
{
    public function getAccessToken(): string
    {
        return 'some access token';
    }
}
