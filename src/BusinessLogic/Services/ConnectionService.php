<?php

namespace Logeecom\CleverReachPlugin\BusinessLogic\Services;

use Logeecom\CleverReachPlugin\BusinessLogic\Interfaces\ConfigRepositoryInterface;
use Logeecom\CleverReachPlugin\BusinessLogic\Interfaces\ConnectionServiceInterface;
use function Amp\call;

class ConnectionService implements ConnectionServiceInterface
{
    private ConfigRepositoryInterface $configuration;

    public function __construct(ConfigRepositoryInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    public function isIntegrationConnected(): bool
    {
        $tokens = $this->configuration->getTokens();
        return $tokens->getAccessToken() && $tokens->getRefreshToken();
    }

    public function isInitialSyncInProgress(): bool
    {
        $status = $this->configuration->getSyncStatus();
        return !($status === null);
    }
}