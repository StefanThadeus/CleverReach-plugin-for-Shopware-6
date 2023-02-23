<?php

namespace Logeecom\CleverReachPlugin\BusinessLogic\Interfaces;

interface ReceiverServiceInterface
{
    public function syncReceiverData(): void;

    public function removeReceiverByEmail(string $email): void;

    public function getReceiverEmail(string $receiverId): string;

    public function syncReceiver(string $customerId): void;

    public function syncCustomer(string $receiverId): void;

    public function syncReceiverEmailChange(string $oldEmail, string $newEmail): void;
}