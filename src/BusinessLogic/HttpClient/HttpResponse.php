<?php

namespace Logeecom\CleverReachPlugin\BusinessLogic\HttpClient;

class HttpResponse
{
    private int $status;
    private array $headers;
    private array $body;

    public function __construct(int $status, array $headers, array $body)
    {
        $this->status = $status;
        $this->headers = $headers;
        $this->body = $body;
    }

    public function getStatus(): int {
        return $this->status;
    }

    public function getHeaders(): array {
        return $this->headers;
    }

    public function getBody(): array {
        return $this->body;
    }
}