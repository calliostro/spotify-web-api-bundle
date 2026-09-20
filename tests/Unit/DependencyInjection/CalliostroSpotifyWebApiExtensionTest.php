<?php

declare(strict_types=1);

namespace Calliostro\SpotifyWebApiBundle\Tests\Unit\DependencyInjection;

use Calliostro\SpotifyWebApiBundle\DependencyInjection\CalliostroSpotifyWebApiExtension;
use Calliostro\SpotifyWebApiBundle\SpotifyClient;
use Calliostro\SpotifyWebApiBundle\TokenProviderInterface;
use PHPUnit\Framework\TestCase;
use SpotifyWebAPI\Session;
use SpotifyWebAPI\SpotifyWebAPI;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class CalliostroSpotifyWebApiExtensionTest extends TestCase
{
    public function testLoadWithDefaults(): void
    {
        $container = new ContainerBuilder();
        $extension = new CalliostroSpotifyWebApiExtension();

        $extension->load([], $container);

        $this->assertTrue($container->hasDefinition('calliostro_spotify_web_api.session'));
        $this->assertTrue($container->hasDefinition('calliostro_spotify_web_api.token_provider'));
        $this->assertTrue($container->hasDefinition('calliostro_spotify_web_api.client_factory'));
        $this->assertTrue($container->hasDefinition('calliostro_spotify_web_api.client'));

        $this->assertTrue($container->hasAlias(Session::class));
        $this->assertTrue($container->hasAlias(SpotifyClient::class));
        $this->assertTrue($container->hasAlias(SpotifyWebAPI::class));
        $this->assertTrue($container->hasAlias(TokenProviderInterface::class));
        $this->assertTrue($container->hasAlias('calliostro_spotify_web_api'));

        $sessionDef = $container->getDefinition('calliostro_spotify_web_api.session');
        $this->assertSame(['', '', ''], $sessionDef->getArguments());

        $tokenProviderDef = $container->getDefinition('calliostro_spotify_web_api.token_provider');
        $this->assertEquals([new Reference('calliostro_spotify_web_api.session')], $tokenProviderDef->getArguments());

        $clientDef = $container->getDefinition('calliostro_spotify_web_api.client');
        $this->assertEquals([
            '',
            '',
            '',
            new Reference('calliostro_spotify_web_api.token_provider'),
            [
                'auto_refresh' => false,
                'auto_retry' => false,
                'return_assoc' => false,
            ],
            new Reference('calliostro_spotify_web_api.session'),
        ], $clientDef->getArguments());
    }

    public function testLoadWithCustomConfig(): void
    {
        $container = new ContainerBuilder();
        $extension = new CalliostroSpotifyWebApiExtension();

        $configs = [
            [
                'client_id' => 'custom_id_12345',
                'client_secret' => 'custom_secret_12345',
                'redirect_uri' => 'https://example.com/callback',
                'token_provider' => 'my_custom_provider',
                'options' => [
                    'auto_refresh' => true,
                    'auto_retry' => true,
                    'return_assoc' => true,
                ],
            ],
        ];

        $extension->load($configs, $container);

        $sessionDef = $container->getDefinition('calliostro_spotify_web_api.session');
        $this->assertSame(['custom_id_12345', 'custom_secret_12345', 'https://example.com/callback'], $sessionDef->getArguments());

        $clientDef = $container->getDefinition('calliostro_spotify_web_api.client');
        $this->assertEquals([
            'custom_id_12345',
            'custom_secret_12345',
            'https://example.com/callback',
            new Reference('my_custom_provider'),
            [
                'auto_refresh' => true,
                'auto_retry' => true,
                'return_assoc' => true,
            ],
            new Reference('calliostro_spotify_web_api.session'),
        ], $clientDef->getArguments());
    }
}
