<?php

namespace Logeecom\CleverReachPlugin\DataAccess;

use Logeecom\CleverReachPlugin\BusinessLogic\DTO\CustomerDTO;
use Logeecom\CleverReachPlugin\BusinessLogic\Interfaces\CustomerRepositoryInterface;
use Logeecom\CleverReachPlugin\BusinessLogic\Interfaces\LanguageRepositoryInterface;
use Logeecom\CleverReachPlugin\BusinessLogic\Interfaces\PaymentRepositoryInterface;
use Logeecom\CleverReachPlugin\BusinessLogic\Interfaces\SalesChannelRepositoryInterface;
use Logeecom\CleverReachPlugin\BusinessLogic\Interfaces\SalutationRepositoryInterface;
use Shopware\Core\Checkout\Customer\CustomerCollection;
use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Uuid\Uuid;

class CustomerRepository implements CustomerRepositoryInterface
{
    private EntityRepositoryInterface $customerRepository;
    private EntityRepositoryInterface $customerGroupRepository;
    private LanguageRepositoryInterface $languageRepository;
    private SalutationRepositoryInterface $salutationRepository;
    private SalesChannelRepositoryInterface $salesChannelRepository;
    private CustomerAddressRepository $customerAddressRepository;
    private PaymentRepositoryInterface $paymentRepository;

    public function __construct(
        EntityRepositoryInterface       $customerRepository,
        EntityRepositoryInterface       $customerGroupRepository,
        LanguageRepositoryInterface     $languageRepository,
        SalutationRepositoryInterface   $salutationRepository,
        SalesChannelRepositoryInterface $salesChannelRepository,
        CustomerAddressRepository       $customerAddressRepository,
        PaymentRepositoryInterface      $paymentRepository
    )
    {
        $this->customerRepository = $customerRepository;
        $this->customerGroupRepository = $customerGroupRepository;
        $this->languageRepository = $languageRepository;
        $this->salutationRepository = $salutationRepository;
        $this->salesChannelRepository = $salesChannelRepository;
        $this->customerAddressRepository = $customerAddressRepository;
        $this->paymentRepository = $paymentRepository;
    }

    public function getCustomers(): array
    {
        $criteria = new Criteria();
        $criteria->addAssociations($this->getAssociationsForCustomer());
        /** * @var CustomerCollection $collection */
        $collection = $this->customerRepository->search($criteria, Context::createDefaultContext())->getEntities();
        return $this->convertToCustomerDTOCollection($collection);
    }

    private function getAssociationsForCustomer(): array
    {
        return
            [
                'defaultShippingAddress',
                'defaultShippingAddress.country',
                'defaultShippingAddress.countryState',
                'salesChannel',
                'salesChannel.domains',
                'orderCustomers.order',
                'language',
                'salutation'
            ];
    }

    public function getCustomerEmailById(string $customerId): string
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('id', $customerId));

        /** * @var CustomerEntity $customer */
        $customerCollection = $this->customerRepository->search($criteria, Context::createDefaultContext())->getEntities();

        $customerDTO = new CustomerDTO($customerCollection->first());
        return $customerDTO->getEmail();
    }

    public function getCustomerById(string $customerId): CustomerDTO
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('id', $customerId));
        $criteria->addAssociations($this->getAssociationsForCustomer());

        /** * @var CustomerCollection $collection */
        $customerCollection = $this->customerRepository->search($criteria, Context::createDefaultContext())->getEntities();

        return new CustomerDTO($customerCollection->first());
    }

    private function convertToCustomerDTOCollection(CustomerCollection $customerCollection): array
    {
        $DTOCollection = [];
        foreach ($customerCollection as $customer) {
            array_push($DTOCollection, new CustomerDTO($customer));
        }

        return $DTOCollection;
    }

    public function upsertCustomer(CustomerDTO $receiver): void
    {
        $oldCustomerData = $this->getCustomerByEmail($receiver->getEmail());

        if ($oldCustomerData) {
            $this->updateCustomer($receiver);
        } else {
            $this->createCustomer($receiver);
        }
    }

    private function updateCustomer(CustomerDTO $receiver): void
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('email', $receiver->getEmail()));
        $customerId = $this->customerRepository->searchIds($criteria, Context::createDefaultContext())->firstId();
        $languageId = $this->languageRepository->getLanguageId($receiver->getLanguage());
        $salutationId = $this->salutationRepository->getSalutationId($receiver->getSalutation());
        $salesChannelId = $this->salesChannelRepository->getSalesChannelId($receiver->getShop());

        $this->customerAddressRepository->updateCustomerAddressData($receiver, $customerId, $salutationId);

        $this->customerRepository->update([
            [
                'id' => $customerId,
                'languageId' => $languageId,
                'salutationId' => $salutationId,
                'salesChannelId' => $salesChannelId,
                'customerNumber' => $receiver->getCustomerNumber(),
                'firstName' => $receiver->getFirstName(),
                'lastName' => $receiver->getLastName(),
                'company' => $receiver->getCompany(),
                'birthday' => date("Y-m-d H:i:s", $receiver->getBirthday()),
                'orderCount' => intval($receiver->getOrderCount()),
                'orderTotalAmount' => intval($receiver->getTotalSpent())
            ]
        ], Context::createDefaultContext());
    }

    private function createCustomer(CustomerDTO $receiver): void
    {
        $addressId = Uuid::randomHex();
        $countryId = $this->customerAddressRepository->getCountryId($receiver->getCountry());
        $salesChannelId = $this->salesChannelRepository->getSalesChannelId($receiver->getShop());
        $defaultPaymentMethodId = $this->paymentRepository->getDefaultPaymentMethodId($salesChannelId);
        $languageId = $this->languageRepository->getLanguageId($receiver->getLanguage());
        $salutationId = $this->salutationRepository->getSalutationId($receiver->getSalutation());
        $groupId = $this->getDefaultGroupId();

        $this->customerRepository->create([
            [
                'languageId' => $languageId,
                'salutationId' => $salutationId,
                'salesChannelId' => $salesChannelId,
                'groupId' => $groupId,
                'defaultShippingAddress' => [
                    'id' => $addressId,
                    'firstName' => $receiver->getFirstName(),
                    'lastName' => $receiver->getLastName(),
                    'street' => $receiver->getStreet(),
                    'city' => $receiver->getCity(),
                    'zipcode' => $receiver->getZip(),
                    'salutationId' => $salutationId,
                    'countryId' => $countryId,
                ],
                'defaultBillingAddressId' => $addressId,
                'defaultPaymentMethodId' => $defaultPaymentMethodId,
                'email' => $receiver->getEmail(),
                'customerNumber' => $receiver->getCustomerNumber(),
                'firstName' => $receiver->getFirstName(),
                'lastName' => $receiver->getLastName(),
                'company' => $receiver->getCompany(),
                'birthday' => date("Y-m-d H:i:s", $receiver->getBirthday()),
                'orderCount' => intval($receiver->getOrderCount()),
                'orderTotalAmount' => intval($receiver->getTotalSpent())
            ]
        ], Context::createDefaultContext());
    }

    private function getDefaultGroupId(): string
    {
        $ids = $this->customerGroupRepository->searchIds(new Criteria(), Context::createDefaultContext())->getIds();

        if (!count($ids)) {
            return '';
        }

        return $ids[0]['customerGroupId'];
    }

    public function getCustomerByEmail(string $customerEmail): ?CustomerDTO
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('email', $customerEmail));
        $criteria->addAssociations($this->getAssociationsForCustomer());

        /** * @var CustomerEntity $customer */
        $customerCollection = $this->customerRepository->search($criteria, Context::createDefaultContext())->getEntities();

        $customer = $customerCollection->first();
        return $customer ? new CustomerDTO($customer) : null;
    }
}