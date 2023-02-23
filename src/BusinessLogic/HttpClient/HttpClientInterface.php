<?php

namespace Logeecom\CleverReachPlugin\BusinessLogic\HttpClient;

interface HttpClientInterface
{
    public function sendRequest(HttpRequest $request): HttpResponse;
}