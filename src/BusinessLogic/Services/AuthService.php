<?php

namespace Logeecom\CleverReachPlugin\BusinessLogic\Services;

use Logeecom\CleverReachPlugin\BusinessLogic\DTO\TokensDTO;
use Logeecom\CleverReachPlugin\BusinessLogic\Interfaces\AuthProxyInterface;
use Logeecom\CleverReachPlugin\BusinessLogic\Interfaces\AuthServiceInterface;
use Logeecom\CleverReachPlugin\BusinessLogic\Interfaces\ConfigRepositoryInterface;

class AuthService implements AuthServiceInterface
{
    const CLIENT_ID = "xxxxxxxx";
    const CLIENT_SECRET = "xxxxxxxx";
    const REDIRECT_URI = "http://6-4-dev.shopware6.localhost/api/cleverreach/callbackUrl";

    private AuthProxyInterface $authProxy;
    private ConfigRepositoryInterface $configuration;

    public function __construct(AuthProxyInterface $authProxy, ConfigRepositoryInterface $configuration)
    {
        $this->authProxy = $authProxy;
        $this->configuration = $configuration;
    }

    public function getRedirectUrl(): string
    {
        $redir = urlencode(self::REDIRECT_URI);
        return $this->authProxy->getAuthUri() . "?client_id=" . self::CLIENT_ID . "&grant=basic&response_type=code&redirect_uri=" . $redir;
    }

    public function fetchTokens(string $code): void
    {
        $this->configuration->setSyncStatus('in_progress');
        $tokens = $this->authProxy->fetchTokens($code, self::CLIENT_ID, self::CLIENT_SECRET, self::REDIRECT_URI);
        $this->configuration->saveTokens($tokens);
    }

    public function fetchNewAccessToken(string $refreshToken): string
    {
        $accessToken = $this->authProxy->fetchNewAccessToken($refreshToken, self::CLIENT_ID, self::CLIENT_SECRET);
        $this->configuration->saveTokens(new TokensDTO($accessToken, $refreshToken));
        return $accessToken;
    }

    public function getClientId(): string
    {
        return self::CLIENT_ID;
    }
}