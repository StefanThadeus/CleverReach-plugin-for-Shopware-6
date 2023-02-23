<?php

namespace Logeecom\CleverReachPlugin\BusinessLogic\Interfaces;

interface SynchronizationServiceInterface
{
    const CLASS_NAME = __CLASS__;

    public function getSyncStatus(): ?string;

    public function startInitialSync(): void;

    public function startManualSync(): void;
}