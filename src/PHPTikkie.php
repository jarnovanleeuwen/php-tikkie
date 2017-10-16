<?php
namespace PHPTikkie;

use PHPTikkie\Entities\Platform;

class PHPTikkie
{
    /**
     * @var Environment
     */
    protected $environment;

    public function __construct(Environment $environment)
    {
        $this->environment = $environment;
    }

    public function newPlatform(array $attributes = []): Platform
    {
        $platform = new Platform($this);

        $platform->setAttributes($attributes);

        return $platform;
    }

    public function persistPlatform(Platform $platform)
    {
        $this->environment->postRequest('/v1/tikkie/platforms', $platform->getRequestAttributes());
    }
}
