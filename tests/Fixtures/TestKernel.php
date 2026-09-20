<?php

declare(strict_types=1);

namespace Calliostro\SpotifyWebApiBundle\Tests\Fixtures;

use Calliostro\SpotifyWebApiBundle\CalliostroSpotifyWebApiBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

class TestKernel extends Kernel
{
    /**
     * @param array<string, mixed> $calliostroSpotifyWebApiConfig
     * @param array<int, mixed>    $extraBundles
     */
    public function __construct(
        private readonly array $calliostroSpotifyWebApiConfig = [],
        string $environment = 'test',
        private readonly array $extraBundles = [],
    ) {
        parent::__construct($environment, true);
    }

    /**
     * @return array<int, mixed>
     */
    public function registerBundles(): array
    {
        $bundles = [
            new CalliostroSpotifyWebApiBundle(),
        ];

        return array_merge($bundles, $this->extraBundles);
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(function (ContainerBuilder $container) {
            if (!empty($this->calliostroSpotifyWebApiConfig)) {
                $container->loadFromExtension('calliostro_spotify_web_api', $this->calliostroSpotifyWebApiConfig);
            }

            $container->setParameter('kernel.secret', 'test_secret');
        });
    }

    public function getCacheDir(): string
    {
        return $this->getProjectDir() . '/var/cache/' .
            $this->environment . '/' .
            md5(serialize($this->calliostroSpotifyWebApiConfig)) . '/' .
            spl_object_hash($this);
    }

    public function getLogDir(): string
    {
        return $this->getProjectDir() . '/var/log/' . $this->environment;
    }
}
