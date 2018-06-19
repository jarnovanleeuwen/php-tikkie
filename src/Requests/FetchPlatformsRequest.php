<?php
namespace PHPTikkie\Requests;

class FetchPlatformsRequest extends AbstractRequest
{
    public function getMethod(): string
    {
        return 'GET';
    }

    public function getUri(): string
    {
        return 'v1/tikkie/platforms';
    }
}
