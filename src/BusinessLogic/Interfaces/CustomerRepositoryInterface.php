<?php

namespace Logeecom\CleverReachPlugin\BusinessLogic\Interfaces;

use Logeecom\CleverReachPlugin\BusinessLogic\DTO\CustomerDTO;

interface CustomerRepositoryInterface
{
    public function getCustomers(): array;

    public function getCustomerEmailById(string $customerId): string;

    public function getCustomerById(string $customerId): CustomerDTO;

    public function upsertCustomer(CustomerDTO $receiver): void;

    public function getCustomerByEmail(string $customerEmail): ?CustomerDTO;
}