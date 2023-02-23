<?php

namespace Logeecom\CleverReachPlugin\BusinessLogic\Proxies;

use Logeecom\CleverReachPlugin\BusinessLogic\DTO\TokensDTO;
use Logeecom\CleverReachPlugin\BusinessLogic\HttpClient\HttpClientInterface;
use Logeecom\CleverReachPlugin\BusinessLogic\HttpClient\HttpRequest;
use Logeecom\CleverReachPlugin\BusinessLogic\Interfaces\AuthProxyInterface;

class AuthProxy implements AuthProxyInterface
{
    const AUTH_URL = "https://rest.cleverreach.com/oauth/authorize.php";
    const TOKEN_URL = "https://rest.cleverreach.com/oauth/token.php";

    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function getAuthUri(): string
    {
        return self::AUTH_URL;
    }

    public function fetchTokens(string $code, string $clientId, string $clientSecret, string $redirectUri): TokensDTO
    {
        $content["client_id"] = $clientId;
        $content["client_secret"] = $clientSecret;
        $content["redirect_uri"] = $redirectUri;
        $content["grant_type"] = "authorization_code";
        $content["code"] = $code;

        $response = $this->httpClient->sendRequest(new HttpRequest(self::TOKEN_URL,
            ['request_method:POST', 'Content-Type:application/json', 'Accept:application/json'], json_encode($content), 'POST'));

        return new TokensDTO($response->getBody()['access_token'], $response->getBody()['refresh_token']);
    }

    public function fetchNewAccessToken(string $refreshToken, string $clientId, string $clientSecret): string
    {
        $content["client_id"] = $clientId;
        $content["client_secret"] = $clientSecret;
        $content["grant_type"] = "refresh_token";
        $content["refresh_token"] = $refreshToken;
        $response = $this->httpClient->sendRequest(new HttpRequest(self::TOKEN_URL,
            ['request_method:POST', 'Content-Type:application/json', 'Accept:application/json'], json_encode($content), 'POST'));

        return $response['access_token'];
    }
}