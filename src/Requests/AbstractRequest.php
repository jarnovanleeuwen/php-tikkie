<?php
namespace PHPTikkie\Requests;

abstract class AbstractRequest
{
    /**
     * @var array|null
     */
    protected $parameters;

    /**
     * @var array|null
     */
    protected $payload;

    /**
     * Extra request options that are used by Guzzle when sending the request.
     *
     * @var array|null
     */
    protected $requestOptions;

    public function getParameters(): ?array
    {
        return $this->parameters;
    }

    public function getPayload(): ?array
    {
        return $this->payload;
    }

    public function getRequestOptions(): ?array
    {
        return [];
    }

    abstract public function getMethod(): string;
    abstract public function getUri(): string;
}
