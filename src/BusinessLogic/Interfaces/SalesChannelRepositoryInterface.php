<?php

namespace Logeecom\CleverReachPlugin\BusinessLogic\Interfaces;

use Logeecom\CleverReachPlugin\BusinessLogic\DTO\CustomerDTO;

interface SalesChannelRepositoryInterface
{
    public function getSalesChannelId(string $ShopName): string;
}