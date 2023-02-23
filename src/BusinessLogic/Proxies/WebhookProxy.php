<?php

namespace Logeecom\CleverReachPlugin\BusinessLogic\Proxies;

use Logeecom\CleverReachPlugin\BusinessLogic\DTO\TokensDTO;
use Logeecom\CleverReachPlugin\BusinessLogic\HttpClient\HttpClientInterface;
use Logeecom\CleverReachPlugin\BusinessLogic\HttpClient\HttpRequest;
use Logeecom\CleverReachPlugin\BusinessLogic\HttpClient\HttpResponse;
use Logeecom\CleverReachPlugin\BusinessLogic\Interfaces\WebhookProxyInterface;
use Symfony\Component\HttpFoundation\Response;

class WebhookProxy implements WebhookProxyInterface
{
    const WEBHOOK_REGISTER_URL = "https://rest.cleverreach.com/hooks/eventhook";
//    For NGROK
    const BASE_DOMAIN = "http://f486-178-222-249-248.ngrok.io";

    const EVENT_NAME_RECEIVER_CREATED = 'receiver.created';
    const EVENT_NAME_RECEIVER_UPDATED = 'receiver.updated';

    const VERIFY_STRING_RECEIVER_CREATED = 'VerifyReceiverCreated';
    const VERIFY_STRING_RECEIVER_UPDATED = 'VerifyReceiverUpdated';

    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function getReceiverCreatedEventName(): string
    {
        return self::EVENT_NAME_RECEIVER_CREATED;
    }

    public function getReceiverUpdatedEventName(): string
    {
        return self::EVENT_NAME_RECEIVER_UPDATED;
    }

    public function registerWebhook(string $eventName, TokensDTO $tokens): HttpResponse
    {
        switch ($eventName) {
            case self::EVENT_NAME_RECEIVER_CREATED:
            {
                $content["url"] = self::BASE_DOMAIN . '/api/cleverreach/webhook/createReceiver';
                $content["event"] = self::EVENT_NAME_RECEIVER_CREATED;
                $content["verify"] = self::VERIFY_STRING_RECEIVER_CREATED;
                break;
            }
            case self::EVENT_NAME_RECEIVER_UPDATED:
            {
                $content["url"] = self::BASE_DOMAIN . '/api/cleverreach/webhook/updateReceiver';
                $content["event"] = self::EVENT_NAME_RECEIVER_UPDATED;
                $content["verify"] = self::VERIFY_STRING_RECEIVER_UPDATED;
            }
        }

        sleep(2);
        $response = $this->httpClient->sendRequest(new HttpRequest(self::WEBHOOK_REGISTER_URL,
            ['request_method:POST', 'Content-Type:application/json; charset=utf-8', 'Accept:application/json', 'Authorization:Bearer ' . $tokens->getAccessToken()],
            json_encode($content), 'POST'));

        while ($response->getStatus() !== 200) {
            $response = $this->httpClient->sendRequest(new HttpRequest(self::WEBHOOK_REGISTER_URL,
                ['request_method:POST', 'Content-Type:application/json; charset=utf-8', 'Accept:application/json', 'Authorization:Bearer ' . $tokens->getAccessToken()],
                json_encode($content), 'POST'));
        }

        return $response;
    }

    public function confirmWebhookRegistration(string $eventName, string $secret, TokensDTO $tokens): Response
    {
        $verifyString = '';
        switch ($eventName) {
            case self::EVENT_NAME_RECEIVER_CREATED:
            {
                $verifyString = self::VERIFY_STRING_RECEIVER_CREATED;
                break;
            }
            case self::EVENT_NAME_RECEIVER_UPDATED:
            {
                $verifyString = self::VERIFY_STRING_RECEIVER_UPDATED;
            }
        }

        $content = $verifyString . ' ' . $secret;
        return new Response($content, 200, ['Authorization:Bearer ' . $tokens->getAccessToken()]);
    }
}