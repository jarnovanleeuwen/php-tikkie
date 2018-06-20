<?php

namespace PHPTikkie\Tests;

use PHPTikkie\Entities\PaymentRequest;
use PHPTikkie\Exceptions\RequestException;

class CreatePaymentRequestTest extends TestCase
{
    public function testCreatePaymentRequestSuccess()
    {
        $this->setMockHttpResponse(['AccessToken.txt', 'CreatePaymentRequestSuccess.txt']);

        $paymentRequest = $this->newPaymentRequest()->save();

        $this->assertEquals('https://pay.here.com/123', $paymentRequest->paymentRequestUrl);
        $this->assertEquals('paymentrequesttoken1', $paymentRequest->paymentRequestToken);
        $this->assertEquals('Invoice: 4567', $paymentRequest->externalId);

        $request = $this->history[1]['request'];

        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('https://api.abnamro.com/v1/tikkie/platforms/platformtoken1/users/usertoken1/bankaccounts/bankaccounttoken1/paymentrequests', $request->getUri());
        $this->assertEquals('{"amountInCents":"1250","currency":"EUR","description":"Thank you","externalId":"Order 1234"}', $request->getBody());
    }

    public function testCreatePaymentRequestFailed()
    {
        $this->setMockHttpResponse(['AccessToken.txt', 'ValidationError.txt']);

        $this->expectException(RequestException::class);
        $this->expectExceptionMessage("[ERR_1100_004] Field validation error (see https://developer.abnamro.com/api/tikkie/technical-details)");

        $this->newPaymentRequest()->save();
    }

    protected function newPaymentRequest(): PaymentRequest
    {
        return $this->tikkie->newPaymentRequest('platformtoken1', 'usertoken1', 'bankaccounttoken1', [
            'amountInCents' => '1250',
            'currency' => 'EUR',
            'description' => 'Thank you',
            'externalId' => 'Order 1234'
        ]);
    }
}
