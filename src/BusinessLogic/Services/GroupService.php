<?php

namespace Logeecom\CleverReachPlugin\BusinessLogic\Services;

use Logeecom\CleverReachPlugin\BusinessLogic\Interfaces\ConfigRepositoryInterface;
use Logeecom\CleverReachPlugin\BusinessLogic\Interfaces\GroupProxyInterface;
use Logeecom\CleverReachPlugin\BusinessLogic\Interfaces\GroupServiceInterface;

class GroupService implements GroupServiceInterface
{

    private ConfigRepositoryInterface $configuration;
    private GroupProxyInterface $groupProxy;

    public function __construct(ConfigRepositoryInterface $configuration, GroupProxyInterface $groupProxy)
    {
        $this->configuration = $configuration;
        $this->groupProxy = $groupProxy;
    }

    public function getReceiverGroupId(): string
    {
        return $this->configuration->getReceiverGroupId();
    }

    public function makeNewReceiverGroup(string $groupName): void
    {
        $tokens = $this->configuration->getTokens();
        $groupId = $this->groupProxy->createReceiverGroup($groupName, $tokens);
        $this->configuration->setReceiverGroupId($groupId);
    }
}