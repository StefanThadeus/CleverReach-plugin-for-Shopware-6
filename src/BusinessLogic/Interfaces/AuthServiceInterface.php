<?php

namespace Logeecom\CleverReachPlugin\BusinessLogic\Interfaces;

interface AuthServiceInterface
{
    public function getRedirectUrl(): string;

    public function fetchTokens(string $code): void;

    public function fetchNewAccessToken(string $refreshToken): string;

    public function getClientId(): string;
}