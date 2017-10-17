<?php
namespace PHPTikkie\Entities;

class BankAccount extends AbstractEntity
{
    /**
     * @var string
     */
    public $bankAccountLabel;

    /**
     * @var string
     */
    public $bankAccountToken;

    /**
     * @var string
     */
    public $iban;

    /**
     * @var array
     */
    protected $fillableAttributes = [
        'bankAccountLabel', 'bankAccountToken', 'iban'
    ];
}
