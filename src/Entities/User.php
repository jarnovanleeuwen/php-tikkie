<?php
namespace PHPTikkie\Entities;

class User extends AbstractEntity
{
    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_INACTIVE = 'INACTIVE';

    /**
     * @var string
     */
    public $bankAccountLabel;

    /**
     * @var BankAccount[]
     */
    public $bankAccounts = [];

    /**
     * @var string
     */
    public $iban;

    /**
     * @var string
     */
    public $name;

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
    public $status;

    /**
     * @var string
     */
    public $userToken;

    /**
     * @var array
     */
    protected $fillableAttributes = [
        'bankAccountLabel', 'iban', 'name', 'phoneNumber', 'platformToken', 'status', 'userToken'
    ];

    public function isActive(): bool
    {
        return $this->status === static::STATUS_ACTIVE;
    }

    public function setAttributes(array $attributes)
    {
        parent::setAttributes($attributes);

        $this->bankAccounts = [];

        if ($bankAccounts = $attributes['bankAccounts'] ?? null) {
            foreach ($bankAccounts as $bankAccountData) {
                $bankAccount = new BankAccount($this->getTikkie());

                $bankAccount->setAttributes($bankAccountData);

                $this->bankAccounts[] = $bankAccount;
            }
        }
    }

    public function save(): self
    {
        $this->getTikkie()->persistUser($this);

        return $this;
    }
}
