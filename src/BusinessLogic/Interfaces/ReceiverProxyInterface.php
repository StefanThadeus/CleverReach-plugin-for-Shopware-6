<?php

namespace Logeecom\CleverReachPlugin\BusinessLogic\Interfaces;


use Logeecom\CleverReachPlugin\BusinessLogic\DTO\CustomerDTO;
use Logeecom\CleverReachPlugin\BusinessLogic\DTO\TokensDTO;

interface ReceiverProxyInterface
{
    public function syncReceiverData(array $data, int $groupId, TokensDTO $tokens): void;

    public function fetchReceiverDataById(string $receiverId, int $groupId, TokensDTO $tokens): CustomerDTO;

    public function removeReceiverByEmail(int $groupId, TokensDTO $tokens, string $email): void;

    public function syncReceiverEmailChange(string $oldEmail, string $newEmail, TokensDTO $tokens): void;
}