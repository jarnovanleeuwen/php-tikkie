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
    public $platformUsage = self::USAGE_TYPE_MYSELF;

    public function getRequestAttributes(): array
    {
        return [
            'email' => $this->email,
            'name' => $this->name,
            'notificationURL' => $this->notificationURL,
            'phoneNumber' => $this->phoneNumber,
            'platformUsage' => $this->platformUsage,
        ];
    }

    public function save()
    {
        $this->getTikkie()->persistPlatform($this);
    }
}
