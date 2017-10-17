<?php
namespace PHPTikkie;

class AccessToken
{
    /**
     * Time in seconds after which this token expires.
     *
     * @var int
     */
    private $lifetime;

    /**
     * @var int
     */
    private $expiryTime;

    /**
     * @var string
     */
    private $token;

    public function __construct(string $token, int $lifetime)
    {
        $this->token = $token;
        $this->lifetime = $lifetime;
        $this->expiryTime = time() + $lifetime;
    }

    public function getLifetime(): int
    {
        return $this->lifetime;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function isValid(): bool
    {
        return time() < $this->expiryTime;
    }

    public function __toString(): string
    {
        return $this->token;
    }
}
