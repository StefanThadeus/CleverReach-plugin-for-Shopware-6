<?php

namespace Logeecom\CleverReachPlugin\BusinessLogic\DTO;

use Shopware\Core\Checkout\Customer\CustomerEntity;

class CustomerDTO
{
    private ?string $id;
    private ?float $totalSpent;
    private ?string $language;
    private ?string $street;
    private ?string $streetNumber;
    private ?string $shop;
    private ?string $email;
    private ?int $activated;
    private ?int $registered;
    private ?string $source;
    private ?string $salutation;
    private ?string $firstName;
    private ?string $lastName;
    private ?string $zip;
    private ?string $city;
    private ?string $company;
    private ?string $stateProvince;
    private ?string $country;
    private ?int $birthday;
    private ?string $phone;
    private ?string $customerNumber;
    private ?string $orderCount;

    public function __construct(CustomerEntity $customerEntity = null)
    {
        if (!$customerEntity) return;

        $this->id = $customerEntity->getId();

        $this->totalSpent = $customerEntity->getOrderTotalAmount();

        $languageData = $customerEntity->getLanguage();
        $this->language = $languageData ? $languageData->getName() : '';

        $defaultShippingAddress = $customerEntity->getDefaultShippingAddress();
        if ($defaultShippingAddress) {
            $this->street = implode(' ',
                [
                    $defaultShippingAddress->getStreet(),
                    $defaultShippingAddress->getAdditionalAddressLine1(),
                    $defaultShippingAddress->getAdditionalAddressLine2()
                ]);

            $this->streetNumber = $defaultShippingAddress->getStreet();
            $this->city = $defaultShippingAddress->getCity();
            $this->zip = $defaultShippingAddress->getZipcode();
            $this->phone = $defaultShippingAddress->getPhoneNumber();
            $this->country = $defaultShippingAddress->getCountry() ? $defaultShippingAddress->getCountry()->getName() : '';
            $this->stateProvince = $defaultShippingAddress->getCountryState() ? $defaultShippingAddress->getCountryState()->getName() : '';
        } else {
            $this->street = $this->streetNumber = $this->city = $this->zip = $this->phone = $this->country = $this->stateProvince = '';
        }

        $salesChannel = $customerEntity->getSalesChannel();
        if ($salesChannel) {
            $this->shop = "Shopware6 " . $salesChannel->getName();

            $domain = $salesChannel->getDomains();
            if ($domain) {
                $this->source = $domain->first() ? $domain->first()->getUrl() : '';
            }
        } else {
            $this->shop = $this->source = '';
        }

        $salutation = $customerEntity->getSalutation();
        $this->salutation = $salutation ? $salutation->getSalutationKey() : '';

        $birthday = $customerEntity->getBirthday();
        $this->birthday = $birthday ? $birthday->getTimestamp() : 0;

        $this->email = $customerEntity->getEmail();
        $this->activated = $customerEntity->getCreatedAt() ? $customerEntity->getCreatedAt()->getTimestamp() : 0;
        $this->registered = $customerEntity->getCreatedAt() ? $customerEntity->getCreatedAt()->getTimestamp() : 0;
        $this->firstName = $customerEntity->getFirstName();
        $this->lastName = $customerEntity->getLastName();
        $this->company = $customerEntity->getCompany();
        $this->customerNumber = $customerEntity->getCustomerNumber();
        $this->orderCount = $customerEntity->getOrderCount();
    }

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return float|null
     */
    public function getTotalSpent(): ?float
    {
        return $this->totalSpent;
    }

    /**
     * @return string|null
     */
    public function getLanguage(): ?string
    {
        return $this->language;
    }

    /**
     * @return string|null
     */
    public function getStreet(): ?string
    {
        return $this->street;
    }

    /**
     * @return string|null
     */
    public function getStreetNumber(): ?string
    {
        return $this->streetNumber;
    }

    /**
     * @return string|null
     */
    public function getShop(): ?string
    {
        return $this->shop;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @return int|null
     */
    public function getActivated(): ?int
    {
        return $this->activated;
    }

    /**
     * @return int|null
     */
    public function getRegistered(): ?int
    {
        return $this->registered;
    }

    /**
     * @return string|null
     */
    public function getSource(): ?string
    {
        return $this->source;
    }

    /**
     * @return string|null
     */
    public function getSalutation(): ?string
    {
        return $this->salutation;
    }

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @return string|null
     */
    public function getZip(): ?string
    {
        return $this->zip;
    }

    /**
     * @return string|null
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @return string|null
     */
    public function getCompany(): ?string
    {
        return $this->company;
    }

    /**
     * @return string|null
     */
    public function getStateProvince(): ?string
    {
        return $this->stateProvince;
    }

    /**
     * @return string|null
     */
    public function getCountry(): ?string
    {
        return $this->country;
    }

    /**
     * @return int|null
     */
    public function getBirthday(): ?int
    {
        return $this->birthday;
    }

    /**
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @return string|null
     */
    public function getCustomerNumber(): ?string
    {
        return $this->customerNumber;
    }

    /**
     * @return string|null
     */
    public function getOrderCount(): ?string
    {
        return $this->orderCount;
    }

    /**
     * @param string|null $id
     */
    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    /**
     * @param float|null $totalSpent
     */
    public function setTotalSpent(?float $totalSpent): void
    {
        $this->totalSpent = $totalSpent;
    }

    /**
     * @param string|null $language
     */
    public function setLanguage(?string $language): void
    {
        $this->language = $language;
    }

    /**
     * @param string|null $street
     */
    public function setStreet(?string $street): void
    {
        $this->street = $street;
    }

    /**
     * @param string|null $streetNumber
     */
    public function setStreetNumber(?string $streetNumber): void
    {
        $this->streetNumber = $streetNumber;
    }

    /**
     * @param string|null $shop
     */
    public function setShop(?string $shop): void
    {
        $this->shop = $shop;
    }

    /**
     * @param string|null $email
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    /**
     * @param int|null $activated
     */
    public function setActivated(?int $activated): void
    {
        $this->activated = $activated;
    }

    /**
     * @param int|null $registered
     */
    public function setRegistered(?int $registered): void
    {
        $this->registered = $registered;
    }

    /**
     * @param string|null $source
     */
    public function setSource(?string $source): void
    {
        $this->source = $source;
    }

    /**
     * @param string|null $salutation
     */
    public function setSalutation(?string $salutation): void
    {
        $this->salutation = $salutation;
    }

    /**
     * @param string|null $firstName
     */
    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @param string|null $lastName
     */
    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @param string|null $zip
     */
    public function setZip(?string $zip): void
    {
        $this->zip = $zip;
    }

    /**
     * @param string|null $city
     */
    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    /**
     * @param string|null $company
     */
    public function setCompany(?string $company): void
    {
        $this->company = $company;
    }

    /**
     * @param string|null $stateProvince
     */
    public function setStateProvince(?string $stateProvince): void
    {
        $this->stateProvince = $stateProvince;
    }

    /**
     * @param string|null $country
     */
    public function setCountry(?string $country): void
    {
        $this->country = $country;
    }

    /**
     * @param int|null $birthday
     */
    public function setBirthday(?int $birthday): void
    {
        $this->birthday = $birthday;
    }

    /**
     * @param string|null $phone
     */
    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @param string|null $customerNumber
     */
    public function setCustomerNumber(?string $customerNumber): void
    {
        $this->customerNumber = $customerNumber;
    }

    /**
     * @param string|null $orderCount
     */
    public function setOrderCount(?string $orderCount): void
    {
        $this->orderCount = $orderCount;
    }
}