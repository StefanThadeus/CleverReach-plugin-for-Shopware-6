<?php

namespace Logeecom\CleverReachPlugin\BusinessLogic\Proxies;

use Logeecom\CleverReachPlugin\BusinessLogic\DTO\TokensDTO;
use Logeecom\CleverReachPlugin\BusinessLogic\HttpClient\HttpClientInterface;
use Logeecom\CleverReachPlugin\BusinessLogic\HttpClient\HttpRequest;
use Logeecom\CleverReachPlugin\BusinessLogic\Interfaces\AuthServiceInterface;
use Logeecom\CleverReachPlugin\BusinessLogic\Interfaces\GroupProxyInterface;

class GroupProxy implements GroupProxyInterface
{
    const GROUP_URL = "https://rest.cleverreach.com/v3/groups.json";

    private HttpClientInterface $httpClient;
    private AuthServiceInterface $authService;

    public function __construct(HttpClientInterface $httpClient, AuthServiceInterface $authService)
    {
        $this->httpClient = $httpClient;
        $this->authService = $authService;
    }

    public function createReceiverGroup(string $groupName, TokensDTO $tokens): int
    {
        $content["name"] = $groupName;

        $response = $this->httpClient->sendRequest(new HttpRequest(self::GROUP_URL,
            ['request_method:POST', 'Content-Type:application/json', 'Accept:application/json', 'Authorization:Bearer ' . $tokens->getAccessToken()], json_encode($content), 'POST'));

        if ($response->getStatus() === 401) {
            $newAccessToken = $this->authService->fetchNewAccessToken($tokens->getRefreshToken());

            $response = $this->httpClient->sendRequest(new HttpRequest(self::GROUP_URL,
                ['request_method:POST', 'Content-Type:application/json', 'Accept:application/json', 'Authorization:Bearer ' . $newAccessToken], json_encode($content), 'POST'));
        }

        return $response->getBody()['id'];
    }
}