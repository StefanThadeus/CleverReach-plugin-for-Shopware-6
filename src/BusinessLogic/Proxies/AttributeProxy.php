<?php

namespace Logeecom\CleverReachPlugin\BusinessLogic\Proxies;

use Logeecom\CleverReachPlugin\BusinessLogic\DTO\TokensDTO;
use Logeecom\CleverReachPlugin\BusinessLogic\HttpClient\HttpClientInterface;
use Logeecom\CleverReachPlugin\BusinessLogic\HttpClient\HttpRequest;
use Logeecom\CleverReachPlugin\BusinessLogic\Interfaces\AttributeProxyInterface;
use Logeecom\CleverReachPlugin\BusinessLogic\Interfaces\AuthServiceInterface;

class AttributeProxy implements AttributeProxyInterface
{
    const ATTRIBUTE_URL = "https://rest.cleverreach.com/v3/attributes.json";

    const GLOBAL_ATTRIBUTES = [
        [
            'name' => 'zip',
            'description' => 'zip',
            'preview_value' => 'Zip',
            'type' => 'text'
        ],
        [
            'name' => 'order_count',
            'description' => 'order_count',
            'preview_value' => 'Order count',
            'type' => 'number'
        ],
        [
            'name' => 'customer_number',
            'description' => 'customer_number',
            'preview_value' => 'Customer number',
            'type' => 'number'
        ],
        [
            'name' => 'phone',
            'description' => 'phone',
            'preview_value' => 'Phone',
            'type' => 'text'
        ],
        [
            'name' => 'birthday',
            'description' => 'birthday',
            'preview_value' => 'Birthday',
            'type' => 'date'
        ],
        [
            'name' => 'country',
            'description' => 'country',
            'preview_value' => 'Country',
            'type' => 'text'
        ],
        [
            'name' => 'state',
            'description' => 'state',
            'preview_value' => 'State',
            'type' => 'text'
        ],
        [
            'name' => 'company',
            'description' => 'company',
            'preview_value' => 'Company',
            'type' => 'text'
        ],
        [
            'name' => 'city',
            'description' => 'city',
            'preview_value' => 'City',
            'type' => 'text'
        ],
        [
            'name' => 'first_name',
            'description' => 'first_name',
            'preview_value' => 'First name',
            'type' => 'text'
        ],
        [
            'name' => 'last_name',
            'description' => 'last_name',
            'preview_value' => 'Last name',
            'type' => 'text'
        ],
        [
            'name' => 'salutation',
            'description' => 'salutation',
            'preview_value' => 'Salutation',
            'type' => 'text'
        ],
        [
            'name' => 'shop',
            'description' => 'shop',
            'preview_value' => 'Shop',
            'type' => 'text'
        ],
        [
            'name' => 'street',
            'description' => 'street',
            'preview_value' => 'Street',
            'type' => 'text'
        ],
        [
            'name' => 'street_number',
            'description' => 'street_number',
            'preview_value' => 'Street number',
            'type' => 'text'
        ],
        [
            'name' => 'language',
            'description' => 'language',
            'preview_value' => 'Language',
            'type' => 'text'
        ],
        [
            'name' => 'total_spent',
            'description' => 'total_spent',
            'preview_value' => 'Total spent',
            'type' => 'number'
        ]
    ];

    private HttpClientInterface $httpClient;
    private AuthServiceInterface $authService;

    public function __construct(HttpClientInterface $httpClient, AuthServiceInterface $authService)
    {
        $this->httpClient = $httpClient;
        $this->authService = $authService;
    }

    public function addAttributes(TokensDTO $tokens): void
    {
        $containedAttributes = $this->getAttributes($tokens);

        foreach (self::GLOBAL_ATTRIBUTES as $attribute) {

            $attributeExists = false;
            for ($i = 0; $i < count($containedAttributes); $i++){
                if ($containedAttributes[$i]['name'] === $attribute['name']){
                    $attributeExists = true;
                    break;
                }
            }

            if(!$attributeExists) {
                $this->addAttribute(
                    $attribute['name'],
                    $attribute['type'],
                    $attribute['description'],
                    $attribute['preview_value'],
                    $tokens
                );
            }
        }
    }

    private function getAttributes(TokensDTO $tokens): array
    {
        $response = $this->httpClient->sendRequest(new HttpRequest(self::ATTRIBUTE_URL,
            ['request_method:GET', 'Content-Type:application/json', 'Accept:application/json', 'Authorization:Bearer ' . $tokens->getAccessToken()], '', 'GET'));

        if ($response->getStatus() === 401) {
            $newAccessToken = $this->authService->fetchNewAccessToken($tokens->getRefreshToken());

            $response = $this->httpClient->sendRequest(new HttpRequest(self::ATTRIBUTE_URL,
                ['request_method:GET', 'Content-Type:application/json', 'Accept:application/json', 'Authorization:Bearer ' . $newAccessToken], '', 'GET'));
        }

        return $response->getBody();
    }

    private function addAttribute(string $name, string $type, string $description, string $previewValue, TokensDTO $tokens): void
    {
        $content["name"] = $name;
        $content["type"] = $type;
        $content["description"] = $description;
        $content["preview_value"] = $previewValue;

        $response = $this->httpClient->sendRequest(new HttpRequest(self::ATTRIBUTE_URL,
            ['request_method:POST', 'Content-Type:application/json', 'Accept:application/json', 'Authorization:Bearer ' . $tokens->getAccessToken()], json_encode($content), 'POST'));

        if ($response->getStatus() === 401) {
            $newAccessToken = $this->authService->fetchNewAccessToken($tokens->getRefreshToken());

            $response = $this->httpClient->sendRequest(new HttpRequest(self::ATTRIBUTE_URL,
                ['request_method:POST', 'Content-Type:application/json', 'Accept:application/json', 'Authorization:Bearer ' . $newAccessToken], json_encode($content), 'POST'));
        }
    }
}