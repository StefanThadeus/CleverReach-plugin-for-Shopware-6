<?php

namespace Logeecom\CleverReachPlugin\BusinessLogic\Services;

use Logeecom\CleverReachPlugin\BusinessLogic\Interfaces\AttributeServiceInterface;
use Logeecom\CleverReachPlugin\BusinessLogic\Interfaces\ConfigRepositoryInterface;
use Logeecom\CleverReachPlugin\BusinessLogic\Interfaces\GroupServiceInterface;
use Logeecom\CleverReachPlugin\BusinessLogic\Interfaces\ReceiverServiceInterface;
use Logeecom\CleverReachPlugin\BusinessLogic\Interfaces\SynchronizationServiceInterface;
use Logeecom\CleverReachPlugin\BusinessLogic\Interfaces\WebhookServiceInterface;

class SynchronizationService implements SynchronizationServiceInterface
{
    private ConfigRepositoryInterface $configuration;
    private GroupServiceInterface $groupService;
    private AttributeServiceInterface $attributeService;
    private ReceiverServiceInterface $receiverService;
    private WebhookServiceInterface $webhookService;

    public function __construct(
        ConfigRepositoryInterface $configuration,
        GroupServiceInterface     $groupService,
        AttributeServiceInterface $attributeService,
        ReceiverServiceInterface  $receiverService,
        WebhookServiceInterface   $webhookService
    )
    {
        $this->configuration = $configuration;
        $this->groupService = $groupService;
        $this->attributeService = $attributeService;
        $this->receiverService = $receiverService;
        $this->webhookService = $webhookService;
    }

    public function getSyncStatus(): ?string
    {
        return $this->configuration->getSyncStatus();
    }

    public function startInitialSync(): void
    {
        if ($this->configuration->getReceiverGroupId() === null) {
            $this->configuration->setSyncStatus('in_progress');
            $this->groupService->makeNewReceiverGroup('CleverReachGroup');
            $this->attributeService->addAttributes();
            $this->receiverService->syncReceiverData();
            $this->webhookService->registerWebhooks();
            $this->configuration->setSyncStatus('done');
        }
    }

    public function startManualSync(): void
    {
        $this->configuration->setSyncStatus('in_progress');
        $this->receiverService->syncReceiverData();
        $this->configuration->setSyncStatus('done');
    }
}