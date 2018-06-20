<?php
namespace PHPTikkie\Requests;

use PHPTikkie\Entities\PaymentRequest;

class CreatePaymentRequestRequest extends AbstractRequest
{
    /**
     * @var PaymentRequest
     */
    protected $paymentRequest;

    public function __construct(PaymentRequest $paymentRequest)
    {
        $this->paymentRequest = $paymentRequest;
    }

    public function getMethod(): string
    {
        return 'POST';
    }

    public function getUri(): string
    {
        return "v1/tikkie/platforms/{$this->paymentRequest->platformToken}/users/{$this->paymentRequest->userToken}/bankaccounts/{$this->paymentRequest->bankAccountToken}/paymentrequests";
    }

    public function getPayload(): ?array
    {
        $paymentRequest = $this->paymentRequest;
        
        return [
            'amountInCents' => $paymentRequest->amountInCents,
            'currency' => $paymentRequest->currency,
            'description' => $paymentRequest->description,
            'externalId' => $paymentRequest->externalId
        ];
    }
}
