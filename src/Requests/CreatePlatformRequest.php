<?php
namespace PHPTikkie\Requests;

use PHPTikkie\Entities\Platform;

class CreatePlatformRequest extends AbstractRequest
{
    /**
     * @var Platform
     */
    protected $platform;

    public function __construct(Platform $platform)
    {
        $this->platform = $platform;
    }

    public function getMethod(): string
    {
        return 'POST';
    }

    public function getUri(): string
    {
        return 'v1/tikkie/platforms';
    }

    public function getPayload(): ?array
    {
        $platform = $this->platform;
        
        return [
            'email' => $platform->email,
            'name' => $platform->name,
            'notificationUrl' => $platform->notificationUrl,
            'phoneNumber' => $platform->phoneNumber,
            'platformUsage' => $platform->platformUsage
        ];
    }
}
