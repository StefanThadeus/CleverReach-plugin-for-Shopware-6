<?php

namespace Logeecom\CleverReachPlugin\BusinessLogic\Proxies;

use Logeecom\CleverReachPlugin\BusinessLogic\DTO\CustomerDTO;
use Logeecom\CleverReachPlugin\BusinessLogic\DTO\TokensDTO;
use Logeecom\CleverReachPlugin\BusinessLogic\HttpClient\HttpClientInterface;
use Logeecom\CleverReachPlugin\BusinessLogic\HttpClient\HttpRequest;
use Logeecom\CleverReachPlugin\BusinessLogic\Interfaces\AuthServiceInterface;
use Logeecom\CleverReachPlugin\BusinessLogic\Interfaces\ReceiverProxyInterface;

class ReceiverProxy implements ReceiverProxyInterface
{
    const GROUP_URL = "https://rest.cleverreach.com/v3/groups.json/";
    const RECEIVER_URL = "https://rest.cleverreach.com/v3/receivers.json/";

    private HttpClientInterface $httpClient;
    private AuthServiceInterface $authService;

    public function __construct(HttpClientInterface $httpClient, AuthServiceInterface $authService)
    {
        $this->httpClient = $httpClient;
        $this->authService = $authService;
    }

    public function syncReceiverData(array $data, int $groupId, TokensDTO $tokens): void
    {
        $customerCollection = [];
        foreach ($data as $customer) {
            array_push($customerCollection, $this->getSingleCustomerData($customer));
        }

        $url = self::GROUP_URL . $groupId . '/receivers/upsert';
        $response = $this->httpClient->sendRequest(new HttpRequest($url,
            ['request_method:POST', 'Content-Type:application/json', 'Accept:application/json', 'Authorization:Bearer ' . $tokens->getAccessToken()],
            json_encode($customerCollection), 'POST'));

        if ($response->getStatus() === 401) {
            $newAccessToken = $this->authService->fetchNewAccessToken($tokens->getRefreshToken());

            $response = $this->httpClient->sendRequest(new HttpRequest($url,
                ['request_method:POST', 'Content-Type:application/json', 'Accept:application/json', 'Authorization:Bearer ' . $newAccessToken],
                json_encode($customerCollection), 'POST'));
        }
    }

    private function getSingleCustomerData(CustomerDTO $customerDTO): array
    {
        $customer["email"] = $customerDTO->getEmail();
        $customer["registered"] = $customerDTO->getRegistered();
        $customer["activated"] = $customerDTO->getActivated();
        $customer["source"] = $customerDTO->getSource();

        $customer['global_attributes']['first_name'] = $customerDTO->getFirstName();
        $customer['global_attributes']['last_name'] = $customerDTO->getLastName();

        $customer['global_attributes']['total_spent'] = $customerDTO->getTotalSpent();
        $customer['global_attributes']['language'] = $customerDTO->getLanguage();
        $customer['global_attributes']['street'] = $customerDTO->getStreet();
        $customer['global_attributes']['street_number'] = $customerDTO->getStreetNumber();
        $customer['global_attributes']['shop'] = $customerDTO->getShop();
        $customer['global_attributes']['salutation'] = $customerDTO->getSalutation();
        $customer['global_attributes']['zip'] = $customerDTO->getZip();
        $customer['global_attributes']['city'] = $customerDTO->getCity();
        $customer['global_attributes']['company'] = $customerDTO->getCompany();
        $customer['global_attributes']['state'] = $customerDTO->getStateProvince();
        $customer['global_attributes']['country'] = $customerDTO->getCountry();
        $customer['global_attributes']['birthday'] = $customerDTO->getBirthday();
        $customer['global_attributes']['phone'] = $customerDTO->getPhone();
        $customer['global_attributes']['customer_number'] = $customerDTO->getCustomerNumber();
        $customer['global_attributes']['order_count'] = $customerDTO->getOrderCount();

        return $customer;
    }

    public function removeReceiverByEmail(int $groupId, TokensDTO $tokens, string $email): void
    {
        $url = self::GROUP_URL . $groupId . '/receivers/delete';

        $response = $this->httpClient->sendRequest(new HttpRequest($url,
            ['request_method:POST', 'Content-Type:application/json', 'Accept:application/json', 'Authorization:Bearer ' . $tokens->getAccessToken()],
            json_encode([$email]), 'POST'));

        if ($response->getStatus() === 401) {
            $newAccessToken = $this->authService->fetchNewAccessToken($tokens->getRefreshToken());

            $response = $this->httpClient->sendRequest(new HttpRequest($url,
                ['request_method:POST', 'Content-Type:application/json', 'Accept:application/json', 'Authorization:Bearer ' . $newAccessToken],
                json_encode([$email]), 'POST'));
        }
    }

    public function syncReceiverEmailChange(string $oldEmail, string $newEmail, TokensDTO $tokens): void
    {
        $url = self::RECEIVER_URL . $oldEmail . '/email';

        $response = $this->httpClient->sendRequest(new HttpRequest($url,
            ['request_method:PUT', 'Content-Type:application/json; charset=utf-8', 'Accept:application/json', 'Authorization:Bearer ' . $tokens->getAccessToken()],
            json_encode(['email' => $newEmail]), 'PUT'));

        if ($response->getStatus() === 401) {
            $newAccessToken = $this->authService->fetchNewAccessToken($tokens->getRefreshToken());

            $response = $this->httpClient->sendRequest(new HttpRequest($url,
                ['request_method:PUT', 'Content-Type:application/json; charset=utf-8', 'Accept:application/json', 'Authorization:Bearer ' . $newAccessToken],
                json_encode(['email' => $newEmail]), 'PUT'));
        }
    }

    public function fetchReceiverDataById(string $receiverId, int $groupId, TokensDTO $tokens): CustomerDTO
    {
        $url = self::RECEIVER_URL . $receiverId;

        $response = $this->httpClient->sendRequest(new HttpRequest($url,
            ['request_method:GET', 'Content-Type:application/json; charset=utf-8', 'Accept:application/json', 'Authorization:Bearer ' . $tokens->getAccessToken()],
            json_encode(['group_id' => $groupId]), 'GET'));

        if ($response->getStatus() === 401) {
            $newAccessToken = $this->authService->fetchNewAccessToken($tokens->getRefreshToken());

            $response = $this->httpClient->sendRequest(new HttpRequest($url,
                ['request_method:GET', 'Content-Type:application/json; charset=utf-8', 'Accept:application/json', 'Authorization:Bearer ' . $newAccessToken],
                json_encode(['group_id' => $groupId]), 'GET'));
        }

        return $this->convertPayloadToCustomerDTO($response->getBody());
    }

    private function convertPayloadToCustomerDTO(array $customerData): CustomerDTO
    {
        $customer = new CustomerDTO();

        $customer->setEmail($customerData['email']);
        $customer->setActivated(intval($customerData['activated']));
        $customer->setRegistered(intval($customerData['registered']));
        $customer->setSource($customerData['source']);

        $customer->setShop($customerData['global_attributes']['shop']);
        $customer->setOrderCount($customerData['global_attributes']['order_count']);
        $customer->setTotalSpent(floatval($customerData['global_attributes']['total_spent']));
        $customer->setCustomerNumber($customerData['global_attributes']['customer_number']);
        $customer->setPhone($customerData['global_attributes']['phone']);
        $customer->setCompany($customerData['global_attributes']['company']);
        $customer->setStreet($customerData['global_attributes']['street']);
        $customer->setStreetNumber($customerData['global_attributes']['street_number']);
        $customer->setCity($customerData['global_attributes']['city']);
        $customer->setZip($customerData['global_attributes']['zip']);
        $customer->setStateProvince($customerData['global_attributes']['state']);
        $customer->setCountry($customerData['global_attributes']['country']);
        $customer->setFirstName($customerData['global_attributes']['first_name']);
        $customer->setLastName($customerData['global_attributes']['last_name']);
        $customer->setSalutation($customerData['global_attributes']['salutation']);
        $customer->setBirthday(strtotime($customerData['global_attributes']['birthday']));
        $customer->setLanguage($customerData['global_attributes']['language']);

        return $customer;
    }
}