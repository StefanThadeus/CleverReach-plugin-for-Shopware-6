<?php

namespace Logeecom\CleverReachPlugin\BusinessLogic\Interfaces;

use Logeecom\CleverReachPlugin\BusinessLogic\DTO\TokensDTO;

interface AttributeProxyInterface
{
    public function addAttributes(TokensDTO $tokens): void;

}