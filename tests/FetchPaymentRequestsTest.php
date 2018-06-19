<?php

namespace PHPTikkie\Tests;

use DateTime;
use PHPTikkie\Entities\Payment;
use PHPTikkie\Entities\PaymentRequest;
use PHPTikkie\Exceptions\RequestException;

class FetchPaymentRequestsTest extends TestCase
{
    public function testFetchPaymentRequestsSuccess()
    {
        $this->setMockHttpResponse(['AccessToken.txt', 'FetchPaymentRequestsSuccess.txt']);

        $from = new DateTime('2018-06-01 13:00');
        $to = new DateTime('2018-06-05 01:00');

        $paymentRequests = $this->tikkie->paymentRequests('platformtoken1', 'usertoken1', 0, 10, $from, $to);
        $paymentRequest = $paymentRequests[0];

        $this->assertCount(1, $paymentRequests);
        $this->assertInstanceOf(PaymentRequest::class, $paymentRequest);
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
        $this->assertEquals(Payment::STATUS_NEW, $payment->onlinePaymentStatus);
        $this->assertEquals('paymenttoken1', $payment->paymentToken);
        $this->assertEquals('1465495200', $payment->created->getTimestamp());
        $this->assertFalse($payment->isPaid());

        $request = $this->history[1]['request'];

        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('https://api.abnamro.com/v1/tikkie/platforms/platformtoken1/users/usertoken1/paymentrequests?offset=0&limit=10&fromDate=2018-06-01T11%3A00%3A00Z&toDate=2018-06-04T23%3A00%3A00Z', $request->getUri());
    }

    public function testFetchPaymentRequestsFailed()
    {
        $this->setMockHttpResponse(['AccessToken.txt', 'ValidationError.txt']);

        $this->expectException(RequestException::class);
        $this->expectExceptionMessage("[ERR_1100_004] Field validation error (see https://developer.abnamro.com/api/tikkie/technical-details)");

        $this->tikkie->paymentRequests('platformtoken1', 'usertoken1', 0, 10);
    }
}
