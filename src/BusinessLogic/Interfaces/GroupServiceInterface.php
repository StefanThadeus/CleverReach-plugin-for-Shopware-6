<?php

namespace Logeecom\CleverReachPlugin\BusinessLogic\Interfaces;

interface GroupServiceInterface
{
    public function getReceiverGroupId(): string;

    public function makeNewReceiverGroup(string $groupName): void;
}