<?php

namespace Logeecom\CleverReachPlugin\BusinessLogic\Services;

use Logeecom\CleverReachPlugin\BusinessLogic\Interfaces\ConfigRepositoryInterface;
use Logeecom\CleverReachPlugin\BusinessLogic\Interfaces\WebhookProxyInterface;
use Logeecom\CleverReachPlugin\BusinessLogic\Interfaces\WebhookServiceInterface;
use Symfony\Component\HttpFoundation\Response;

class WebhookService implements WebhookServiceInterface
{
    private WebhookProxyInterface $webhookProxy;
    private ConfigRepositoryInterface $configuration;

    public function __construct(WebhookProxyInterface $webhookProxy, ConfigRepositoryInterface $configuration)
    {
        $this->webhookProxy = $webhookProxy;
        $this->configuration = $configuration;
    }

    public function registerWebhooks(): void
    {
        $this->registerWebhook($this->webhookProxy->getReceiverCreatedEventName());
        $this->registerWebhook($this->webhookProxy->getReceiverUpdatedEventName());
    }

    private function registerWebhook(string $eventName): void
    {
        $tokens = $this->configuration->getTokens();
        $response = $this->webhookProxy->registerWebhook($eventName, $tokens);
        $callToken = $response->getBody()['call_token'];
        $secret = $response->getBody()['secret'];
        $this->configuration->saveWebhookData($eventName, $callToken, $secret);
    }

    public function confirmWebhookRegistration(string $eventName, string $secret): Response
    {
        $tokens = $this->configuration->getTokens();
        return $this->webhookProxy->confirmWebhookRegistration($eventName, $secret, $tokens);
    }

    public function getReceiverCreatedEventName(): string
    {
       return $this->webhookProxy->getReceiverCreatedEventName();
    }

    public function getReceiverUpdatedEventName(): string
    {
        return $this->webhookProxy->getReceiverUpdatedEventName();
    }

    public function getEventCallToken(string $eventName): string
    {
        return $this->configuration->getEventCallToken($eventName);
    }
}