<?php
namespace PHPTikkie\Entities;

use DateTimeImmutable;

class Payment extends AbstractEntity
{
    const STATUS_NEW = 'NEW';
    const STATUS_NOT_PAID = 'NOT_PAID';
    const STATUS_PAID = 'PAID';
    const STATUS_PENDING = 'PENDING';

    /**
     * @var string
     */
    public $amountCurrency;

    /**
     * @var string
     */
    public $amountInCents;

    /**
     * @var string
     */
    public $counterPartyName;

    /**
     * @var DateTimeImmutable
     */
    public $created;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $onlinePaymentStatus;

    /**
     * @var string
     */
    public $paymentToken;

    /**
     * @var array
     */
    protected $fillableAttributes = [
        'amountCurrency', 'amountInCents', 'counterPartyName', 'description', 'onlinePaymentStatus', 'paymentToken'
    ];

    public function isPaid(): bool
    {
        return $this->onlinePaymentStatus === static::STATUS_PAID;
    }

    public function setAttributes(array $attributes)
    {
        parent::setAttributes($attributes);

        if (isset($attributes['created'])) {
            $this->created = new DateTimeImmutable($attributes['created']);
        }
    }
}
