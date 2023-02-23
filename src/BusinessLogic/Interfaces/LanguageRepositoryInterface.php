<?php

namespace Logeecom\CleverReachPlugin\BusinessLogic\Interfaces;

use Logeecom\CleverReachPlugin\BusinessLogic\DTO\CustomerDTO;

interface LanguageRepositoryInterface
{
    public function getLanguageId(string $language): string;
}