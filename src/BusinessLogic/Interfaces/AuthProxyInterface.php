<?php

namespace Logeecom\CleverReachPlugin\BusinessLogic\Interfaces;

use Logeecom\CleverReachPlugin\BusinessLogic\DTO\TokensDTO;

interface AuthProxyInterface
{
    public function getAuthUri(): string;

    public function fetchTokens(string $code, string $clientId, string $clientSecret, string $redirectUri): TokensDTO;

    public function fetchNewAccessToken(string $refreshToken, string $clientId, string $clientSecret): string;
}