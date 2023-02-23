<?php

namespace Logeecom\CleverReachPlugin\DataAccess;

use Logeecom\CleverReachPlugin\BusinessLogic\DTO\CustomerDTO;
use Logeecom\CleverReachPlugin\BusinessLogic\Interfaces\CustomerAddressRepositoryInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;

class CustomerAddressRepository implements CustomerAddressRepositoryInterface
{
    private EntityRepositoryInterface $customerAddressRepository;
    private EntityRepositoryInterface $countryTranslationRepository;
    private EntityRepositoryInterface $countryStateTranslationRepository;

    public function __construct(
        EntityRepositoryInterface $customerAddressRepository,
        EntityRepositoryInterface $countryTranslationRepository,
        EntityRepositoryInterface $countryStateTranslationRepository
    )
    {
        $this->customerAddressRepository = $customerAddressRepository;
        $this->countryTranslationRepository = $countryTranslationRepository;
        $this->countryStateTranslationRepository = $countryStateTranslationRepository;
    }

    public function updateCustomerAddressData(CustomerDTO $customer, string $customerId, string $salutationId): void
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('customerId', $customerId));
        $addressId = $this->customerAddressRepository->searchIds($criteria, Context::createDefaultContext())->firstId();

        $countryId = $this->getCountryId($customer->getCountry());
        $countryStateId = $this->getStateId($customer->getStateProvince());

        $this->customerAddressRepository->update([
            [
                'id' => $addressId,
                'countryId' => $countryId,
                'countryStateId' => $countryStateId,
                'customerId' => $customerId,
                'salutationId' => $salutationId,
                'firstName' => $customer->getFirstName(),
                'lastName' => $customer->getLastName(),
                'street' => $customer->getStreet(),
                'zipcode' => $customer->getZip(),
                'city' => $customer->getCity(),
                'phoneNumber' => $customer->getPhone(),
                'company' => $customer->getCompany()
            ]
        ], Context::createDefaultContext());
    }

    public function getCountryId(string $countryName): string
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('name', $countryName ?: ''));
        $results = $this->countryTranslationRepository->searchIds($criteria, Context::createDefaultContext())->getIds();

        $countryId = count($results) ? $results[0]['countryId'] : '';

        if (!$countryId) {
            $results = $this->countryTranslationRepository->searchIds(new Criteria(), Context::createDefaultContext())->getIds();
            $countryId = count($results) ? $results[0]['countryId'] : '';
        }

        return $countryId;
    }

    public function getStateId(string $stateName): string
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('name', $stateName ?: ''));
        $results = $this->countryStateTranslationRepository->searchIds($criteria, Context::createDefaultContext())->getIds();

        $stateId = count($results) ? $results[0]['countryStateId'] : '';

        if (!$stateId) {
            $results = $this->countryStateTranslationRepository->searchIds(new Criteria(), Context::createDefaultContext())->getIds();
            $stateId = count($results) ? $results[0]['countryStateId'] : '';
        }

        return $stateId;
    }
}