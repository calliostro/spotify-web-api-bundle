<?php

namespace Calliostro\SpotifyWebApiBundle;

interface TokenProviderInterface
{
    public function getAccessToken(): string;
}
