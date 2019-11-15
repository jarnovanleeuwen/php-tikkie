<?php
namespace PHPTikkie\Entities;

class Platform extends AbstractEntity
{
    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_INACTIVE = 'INACTIVE';

    const USAGE_TYPE_MYSELF = 'PAYMENT_REQUEST_FOR_MYSELF';
    
    /** @deprecated No longer mentioned in the official documentation. */
    const USAGE_TYPE_OTHERS = 'PAYMENT_REQUEST_FOR_OTHERS';

    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string|null
     */
    public $notificationUrl;

    /**
     * @var string
     */
    public $phoneNumber;

    /**
     * @var string
     */
    public $platformToken;

    /**
     * @var string
     */
    public $platformUsage = self::USAGE_TYPE_MYSELF;

    /**
     * @var string
     */
    public $status;

    /**
     * @var array
     */
    protected $fillableAttributes = [
        'email', 'name', 'notificationUrl', 'phoneNumber', 'platformUsage', 'platformToken', 'status'
    ];

    public function isActive(): bool
    {
        return $this->status === static::STATUS_ACTIVE;
    }

    public function save(): self
    {
        $this->getTikkie()->persistPlatform($this);

        return $this;
    }
}
