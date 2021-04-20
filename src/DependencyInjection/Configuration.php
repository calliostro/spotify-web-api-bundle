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
                    ->defaultValue('')
                ->end()
                ->scalarNode('client_secret')
                    ->info('Your Client Secret')
                    ->defaultValue('')
                ->end()
                ->arrayNode('options')
                    ->info("Options for SpotifyWebAPI client\nhttps://github.com/jwilsson/spotify-web-api-php/blob/main/docs/examples/setting-options.md")
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('auto_refresh')->defaultFalse()->end()
                        ->booleanNode('auto_retry')->defaultFalse()->end()
                        ->booleanNode('return_assoc')->defaultFalse()->end()
                    ->end()
                ->end()
                ->scalarNode('redirect_uri')
                    ->info('Address to redirect to after authentication success OR failure')
                    ->example('https://127.0.0.1:8000/callback/')
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
