<?php

namespace Logeecom\CleverReachPlugin\BusinessLogic\Services;

use Logeecom\CleverReachPlugin\BusinessLogic\Interfaces\AttributeProxyInterface;
use Logeecom\CleverReachPlugin\BusinessLogic\Interfaces\AttributeServiceInterface;
use Logeecom\CleverReachPlugin\BusinessLogic\Interfaces\ConfigRepositoryInterface;

class AttributeService implements AttributeServiceInterface
{
    private ConfigRepositoryInterface $configuration;
    private AttributeProxyInterface $attributeProxy;

    public function __construct(ConfigRepositoryInterface $configuration, AttributeProxyInterface $attributeProxy)
    {
        $this->configuration = $configuration;
        $this->attributeProxy = $attributeProxy;
    }

    public function addAttributes(): void
    {
        $tokens = $this->configuration->getTokens();
        $this->attributeProxy->addAttributes($tokens);
    }
}