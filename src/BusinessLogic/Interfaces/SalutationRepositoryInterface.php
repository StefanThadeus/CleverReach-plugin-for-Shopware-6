<?php

namespace Logeecom\CleverReachPlugin\BusinessLogic\Interfaces;

use Logeecom\CleverReachPlugin\BusinessLogic\DTO\CustomerDTO;

interface SalutationRepositoryInterface
{
    public function getSalutationId(string $salutation): string;
}