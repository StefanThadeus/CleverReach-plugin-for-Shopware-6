<?php

namespace Logeecom\CleverReachPlugin\BusinessLogic\Interfaces;

interface PaymentRepositoryInterface
{
    public function getDefaultPaymentMethodId(string $salesChannelId = ''): string;

}