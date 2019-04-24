<?php

namespace PHPTikkie\Tests;

use DateTimeImmutable;
use PHPTikkie\Entities\Payment;
use PHPTikkie\Entities\PaymentRequest;
use PHPTikkie\Exceptions\RequestException;

class FetchPaymentRequestTest extends TestCase
{
    public function testFetchPaymentRequestSuccess()
    {
        $this->setMockHttpResponse(['AccessToken.txt', 'FetchPaymentRequestSuccess.txt']);

        $paymentRequest = $this->tikkie->paymentRequest('platformtoken1', 'usertoken1', 'paymentrequesttoken1');

        $this->assertEquals('123', $paymentRequest->amountInCents);
        $this->assertFalse($paymentRequest->bankAccountYieldedTooFast);
        $this->assertEquals('1465495200', $paymentRequest->created->getTimestamp());
        $this->assertEquals('EUR', $paymentRequest->currency);
        $this->assertEquals('Last night\'s dinner', $paymentRequest->description);
        $this->assertNull($paymentRequest->expired);
        $this->assertEquals('paymentrequesttoken1', $paymentRequest->paymentRequestToken);
        $this->assertEquals('Invoice: 4567', $paymentRequest->externalId);
        $this->assertEquals(PaymentRequest::STATUS_OPEN, $paymentRequest->status);
        $this->assertCount(1, $paymentRequest->payments);

        $payment = $paymentRequest->payments[0];

        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertEquals('EUR', $payment->amountCurrency);
        $this->assertEquals('123', $payment->amountInCents);
        $this->assertEquals('E. Xample', $payment->counterPartyName);
        $this->assertEquals('Payment for tikkie', $payment->description);
        $this->assertEquals(Payment::STATUS_PAID, $payment->onlinePaymentStatus);
        $this->assertEquals('paymenttoken1', $payment->paymentToken);
        $this->assertEquals('1465495200', $payment->created->getTimestamp());
        $this->assertTrue($payment->isPaid());

        $request = $this->history[1]['request'];

        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('https://api.abnamro.com/v1/tikkie/platforms/platformtoken1/users/usertoken1/paymentrequests/paymentrequesttoken1', $request->getUri());
    }

    public function testFetchPaymentRequestFailed()
    {
        $this->setMockHttpResponse(['AccessToken.txt', 'ValidationError.txt']);

        $this->expectException(RequestException::class);
        $this->expectExceptionMessage("[ERR_1100_004] Field validation error | traceId: 3cbf4bc9-108c-4e02-ad6e-937c79d875e3 | (see https://developer.abnamro.com/api/tikkie/technical-details)");

        $this->tikkie->paymentRequest('platformtoken1', 'usertoken1', 'paymentrequesttoken1');
    }

    // Due to a Tikkie bug, the API may return epoch timestamps with milliseconds instead of a ISO-8601 formatted string.
    // I reported this on 24-04-2019.
    public function testCreatedDateCanBeTimestampWithMilliseconds()
    {
        $payment = new Payment($this->tikkie);
        $payment->setAttributes(['created' => '1554957274674']);

        $this->assertInstanceOf(DateTimeImmutable::class, $payment->created);
        $this->assertEquals('1554957274', $payment->created->getTimestamp());
    }
}
