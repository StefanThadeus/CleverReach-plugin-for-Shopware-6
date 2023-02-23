<?php

namespace Logeecom\CleverReachPlugin\BusinessLogic\Interfaces;

interface ConnectionServiceInterface
{
    const CLASS_NAME = __CLASS__;

    public function isIntegrationConnected(): bool;

    public function isInitialSyncInProgress(): bool;
}