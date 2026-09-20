<?php

declare(strict_types=1);

namespace Calliostro\SpotifyWebApiBundle;

interface TokenProviderInterface
{
    public function getAccessToken(): string;
}
