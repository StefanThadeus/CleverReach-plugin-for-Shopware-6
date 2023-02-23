<?php

namespace Logeecom\CleverReachPlugin\BusinessLogic\HttpClient;

class HttpRequest
{
    private string $url;
    private string $method;
    private array $headers;
    private ?string $content;

    public function __construct(string $url, array $headers, ?string $content, $method)
    {
        $this->url = $url;
        $this->headers = $headers;
        $this->content = $content;
        $this->method = $method;
    }

    public function getUrl(): string {
        return $this->url;
    }

    public function getHeaders(): array {
        return $this->headers;
    }

    public function getContent(): ?string {
        return $this->content;
    }

    public function getMethod(): string {
        return $this->method;
    }
}