<?php
namespace PHPTikkie\Entities;

class Platform extends AbstractEntity
{
    /**
     * @var string
     */
    const USAGE_TYPE_MYSELF = 'PAYMENT_REQUEST_FOR_MYSELF';

    /**
     * @var string
     */
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
     * @var string
     */
    public $notificationURL;

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
        'email', 'name', 'notificationURL', 'phoneNumber', 'platformUsage', 'platformToken', 'status'
    ];

    public function save(): self
    {
        $this->getTikkie()->persistPlatform($this);

        return $this;
    }
}
