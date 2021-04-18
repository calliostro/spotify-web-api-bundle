<?php

namespace Calliostro\SpotifyWebApiBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('calliostro_spotify_web_api');
        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
                ->scalarNode('client_id')
                    ->info('Your Client ID')
                    ->isRequired()
                ->end()
                ->scalarNode('client_secret')
                    ->info('Your Client Secret')
                    ->isRequired()
                ->end()
                ->scalarNode('redirect_uri')
                    ->info('Address to redirect to after authentication success OR failure')
                    ->defaultValue('')
                ->end()
                ->scalarNode('token_provider')
                    ->info('Service ID of the token provider that provides the user\'s access token')
                    ->defaultValue('calliostro_spotify_web_api.token_provider')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
