<?php

namespace Logeecom\CleverReachPlugin\BusinessLogic\Interfaces;

use Logeecom\CleverReachPlugin\BusinessLogic\DTO\TokensDTO;

interface GroupProxyInterface
{
    public function createReceiverGroup(string $groupName, TokensDTO $tokens): int;

}