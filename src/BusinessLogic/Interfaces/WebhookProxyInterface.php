<?php

namespace Logeecom\CleverReachPlugin\BusinessLogic\Interfaces;

use Logeecom\CleverReachPlugin\BusinessLogic\DTO\TokensDTO;
use Symfony\Component\HttpFoundation\Response;
use Logeecom\CleverReachPlugin\BusinessLogic\HttpClient\HttpResponse;

interface WebhookProxyInterface
{
    public function getReceiverCreatedEventName(): string;

    public function getReceiverUpdatedEventName(): string;

    public function registerWebhook(string $eventName, TokensDTO $tokens): HttpResponse;

    public function confirmWebhookRegistration(string $eventName, string $secret, TokensDTO $tokens): Response;
}