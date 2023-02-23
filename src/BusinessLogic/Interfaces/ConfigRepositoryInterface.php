<?php

namespace Logeecom\CleverReachPlugin\BusinessLogic\Interfaces;

use Logeecom\CleverReachPlugin\BusinessLogic\DTO\TokensDTO;

interface ConfigRepositoryInterface
{
    public function getTokens(): TokensDTO;

    public function saveTokens(TokensDTO $tokens): void;

    public function getSyncStatus(): ?string;

    public function setSyncStatus(string $status): void;

    public function getReceiverGroupId(): ?string;

    public function setReceiverGroupId(string $groupId): void;

    public function saveWebhookData(string $eventName, string $callToken, string $secret): void;

    public function getEventCallToken(string $eventName): string;
}