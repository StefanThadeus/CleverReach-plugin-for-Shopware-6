<?php

namespace Logeecom\CleverReachPlugin\BusinessLogic\Services;

use Logeecom\CleverReachPlugin\BusinessLogic\DTO\CustomerDTO;
use Logeecom\CleverReachPlugin\BusinessLogic\Interfaces\ConfigRepositoryInterface;
use Logeecom\CleverReachPlugin\BusinessLogic\Interfaces\CustomerRepositoryInterface;
use Logeecom\CleverReachPlugin\BusinessLogic\Interfaces\ReceiverProxyInterface;
use Logeecom\CleverReachPlugin\BusinessLogic\Interfaces\ReceiverServiceInterface;
use Shopware\Core\Checkout\Customer\CustomerCollection;

class ReceiverService implements ReceiverServiceInterface
{

    private CustomerRepositoryInterface $customerRepository;
    private ReceiverProxyInterface $receiverProxy;
    private ConfigRepositoryInterface $configuration;


    public function __construct(CustomerRepositoryInterface $customerRepository, ReceiverProxyInterface $receiverProxy, ConfigRepositoryInterface $configuration)
    {
        $this->customerRepository = $customerRepository;
        $this->receiverProxy = $receiverProxy;
        $this->configuration = $configuration;
    }

    public function syncReceiver(string $customerId): void
    {
        $customer = $this->customerRepository->getCustomerById($customerId);
        $tokens = $this->configuration->getTokens();
        $groupId = $this->configuration->getReceiverGroupId();
        $this->receiverProxy->syncReceiverData([$customer], $groupId, $tokens);
    }

    public function syncCustomer(string $receiverId): void
    {
        $groupId = $this->configuration->getReceiverGroupId();
        $receiver = $this->receiverProxy->fetchReceiverDataById($receiverId, $groupId, $this->configuration->getTokens());
        $this->customerRepository->upsertCustomer($receiver);
    }

    public function syncReceiverEmailChange(string $oldEmail, string $newEmail): void
    {
        $this->receiverProxy->syncReceiverEmailChange($oldEmail, $newEmail, $this->configuration->getTokens());
    }

    public function syncReceiverData(): void
    {
        $customerCollection = $this->customerRepository->getCustomers();
        $tokens = $this->configuration->getTokens();
        $groupId = $this->configuration->getReceiverGroupId();
        $this->receiverProxy->syncReceiverData($customerCollection, $groupId, $tokens);
    }

    public function removeReceiverByEmail(string $email): void
    {
        $tokens = $this->configuration->getTokens();
        $groupId = $this->configuration->getReceiverGroupId();
        $this->receiverProxy->removeReceiverByEmail($groupId, $tokens, $email);
    }

    public function getReceiverEmail(string $receiverId): string
    {
        return $this->customerRepository->getCustomerEmailById($receiverId);
    }
}