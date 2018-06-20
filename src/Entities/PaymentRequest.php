<?php
namespace PHPTikkie\Entities;

use DateTimeImmutable;

class PaymentRequest extends AbstractEntity
{
    const STATUS_OPEN = 'OPEN';
    const STATUS_CLOSED = 'CLOSED';
    const STATUS_EXPIRED = 'EXPIRED';
    const STATUS_MAX_YIELD_REACHED = 'MAX_YIELD_REACHED';
    const STATUS_MAX_SUCCESSFUL_PAYMENTS_REACHED = 'MAX_SUCCESSFUL_PAYMENTS_REACHED';
    
    /**
     * @var string
     */
    public $amountInCents;

    /**
     * @var string
     */
    public $bankAccountToken;

    /**
     * @var bool
     */
    public $bankAccountYieldedTooFast;

    /**
     * @var DateTimeImmutable|null
     */
    public $created;

    /**
     * @var string
     */
    public $currency;

    /**
     * @var string
     */
    public $description;

    /**
     * @var DateTimeImmutable|null
     */
    public $expired;

    /**
     * @var Payment[]
     */
    public $payments = [];

    /**
     * @var string
     */
    public $paymentRequestUrl;

    /**
     * @var string
     */
    public $paymentRequestToken;

    /**
     * @var string
     */
    public $externalId;

    /**
     * @var string
     */
    public $platformToken;

    /**
     * @var string
     */
    public $status;

    /**
     * @var string
     */
    public $userToken;

    /**
     * @var array
     */
    protected $fillableAttributes = [
        'amountInCents', 'bankAccountToken', 'bankAccountYieldedTooFast', 'currency', 'description',
        'paymentRequestUrl', 'paymentRequestToken', 'externalId', 'platformToken', 'status', 'userToken'
    ];

    public function setAttributes(array $attributes)
    {
        parent::setAttributes($attributes);

        foreach (['created', 'expired'] as $dateAttribute) {
            if (isset($attributes[$dateAttribute])) {
                $this->{$dateAttribute} = new DateTimeImmutable($attributes[$dateAttribute]);
            }
        }

        $this->payments = [];

        if ($payments = $attributes['payments'] ?? null) {
            foreach ($payments as $paymentData) {
                $payment = new Payment($this->getTikkie());

                $payment->setAttributes($paymentData);

                $this->payments[] = $payment;
            }
        }
    }

    public function save(): self
    {
        $this->getTikkie()->persistPaymentRequest($this);

        return $this;
    }
}
