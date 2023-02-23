<?php

namespace Logeecom\CleverReachPlugin\BusinessLogic\Interfaces;

use Symfony\Component\HttpFoundation\Response;

interface WebhookServiceInterface
{
    const CLASS_NAME = __CLASS__;

    public function getReceiverCreatedEventName(): string;

    public function getReceiverUpdatedEventName(): string;

    public function getEventCallToken(string $eventName): string;

    public function registerWebhooks(): void;

    public function confirmWebhookRegistration(string $eventName, string $secret): Response;
}