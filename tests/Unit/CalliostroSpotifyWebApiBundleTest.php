<?php

declare(strict_types=1);

namespace Calliostro\SpotifyWebApiBundle\Tests\Unit;

use Calliostro\SpotifyWebApiBundle\CalliostroSpotifyWebApiBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class CalliostroSpotifyWebApiBundleTest extends TestCase
{
    public function testBundleInstantiation(): void
    {
        $bundle = new CalliostroSpotifyWebApiBundle();

        $this->assertInstanceOf(Bundle::class, $bundle);
    }
}
