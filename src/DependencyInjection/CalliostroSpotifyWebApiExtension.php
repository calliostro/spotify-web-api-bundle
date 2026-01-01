<?php

namespace Calliostro\SpotifyWebApiBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Reference;

final class CalliostroSpotifyWebApiExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.php');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        // Set arguments instead of replacing them since services.php doesn't define them
        $container->getDefinition('calliostro_spotify_web_api.session')
            ->setArguments([
                $config['client_id'],
                $config['client_secret'],
                $config['redirect_uri'],
            ]);

        $container->getDefinition('calliostro_spotify_web_api.token_provider')
            ->setArguments([
                new Reference('calliostro_spotify_web_api.session'),
            ]);

        $container->getDefinition('calliostro_spotify_web_api')
            ->setArguments([
                new Reference($config['token_provider']),
                $config['options'],
            ]);
    }
}
