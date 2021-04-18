<?php

namespace Calliostro\SpotifyWebApiBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

final class CalliostroSpotifyWebApiExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $container->getDefinition('calliostro_spotify_web_api.session')
            ->replaceArgument(0, $config['client_id'])
            ->replaceArgument(1, $config['client_secret'])
            ->replaceArgument(2, $config['redirect_uri']);

        $container->getDefinition('calliostro_spotify_web_api.token_provider')
            ->replaceArgument(0, new Reference('calliostro_spotify_web_api.session'));

        $container->getDefinition('calliostro_spotify_web_api')
            ->replaceArgument(0, new Reference($config['token_provider']));
    }
}
