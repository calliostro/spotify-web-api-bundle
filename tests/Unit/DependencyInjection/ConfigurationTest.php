<?php

declare(strict_types=1);

namespace Calliostro\SpotifyWebApiBundle\Tests\Unit\DependencyInjection;

use Calliostro\SpotifyWebApiBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;

final class ConfigurationTest extends TestCase
{
    private Processor $processor;
    private Configuration $configuration;

    protected function setUp(): void
    {
        $this->processor = new Processor();
        $this->configuration = new Configuration();
    }

    public function testDefaultConfig(): void
    {
        $config = $this->processor->processConfiguration($this->configuration, []);

        $expected = [
            'client_id' => '',
            'client_secret' => '',
            'options' => [
                'auto_refresh' => false,
                'auto_retry' => false,
                'return_assoc' => false,
            ],
            'redirect_uri' => '',
            'token_provider' => 'calliostro_spotify_web_api.token_provider',
        ];

        $this->assertSame($expected, $config);
    }

    public function testCustomConfig(): void
    {
        $customConfig = [
            'client_id' => 'my_client_id',
            'client_secret' => 'my_client_secret',
            'options' => [
                'auto_refresh' => true,
                'auto_retry' => true,
                'return_assoc' => true,
            ],
            'redirect_uri' => 'https://127.0.0.1:8000/callback/',
            'token_provider' => 'app.custom_token_provider',
        ];

        $config = $this->processor->processConfiguration($this->configuration, [$customConfig]);

        $this->assertSame($customConfig, $config);
    }
}
