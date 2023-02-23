<?php

namespace Logeecom\CleverReachPlugin\BusinessLogic\Interfaces;

use Logeecom\CleverReachPlugin\BusinessLogic\DTO\CustomerDTO;

interface CustomerAddressRepositoryInterface
{
    public function updateCustomerAddressData(CustomerDTO $customer, string $customerId, string $salutationId): void;

    public function getCountryId(string $countryName): string;

    public function getStateId(string $stateName): string;
}