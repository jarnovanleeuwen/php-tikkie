<?php

namespace PHPTikkie\Tests;

use PHPTikkie\Entities\Platform;
use PHPTikkie\Exceptions\RequestException;

class CreatePlatformTest extends TestCase
{
    public function testCreatePlatformSuccess()
    {
        $this->setMockHttpResponse(['AccessToken.txt', 'CreatePlatformSuccess.txt']);

        $platform = $this->newPlatform()->save();

        $this->assertEquals('platformtoken1', $platform->platformToken);
        $this->assertEquals('x@yz.com', $platform->email);
        $this->assertEquals('NewPlatform', $platform->name);
        $this->assertEquals('0601234567', $platform->phoneNumber);
        $this->assertEquals(Platform::USAGE_TYPE_MYSELF, $platform->platformUsage);
        $this->assertNull($platform->notificationUrl);
        $this->assertTrue($platform->isActive());

        $request = $this->history[1]['request'];

        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('https://api.abnamro.com/v1/tikkie/platforms', $request->getUri());
        $this->assertEquals('{"email":"tikkie@example.com","name":"MyPlatform","notificationUrl":"","phoneNumber":"0612345678","platformUsage":"PAYMENT_REQUEST_FOR_MYSELF"}', $request->getBody());
    }

    public function testCreatePlatformFailed()
    {
        $this->setMockHttpResponse(['AccessToken.txt', 'ValidationError.txt']);

        $this->expectException(RequestException::class);
        $this->expectExceptionMessage("[ERR_1100_004] Field validation error (see https://developer.abnamro.com/api/tikkie/technical-details)");

        $this->newPlatform()->save();
    }

    protected function newPlatform(): Platform
    {
        return $this->tikkie->newPlatform([
            'name' => 'MyPlatform',
            'phoneNumber' => '0612345678',
            'platformUsage' => Platform::USAGE_TYPE_MYSELF,
            'email' => 'tikkie@example.com',
            'notificationUrl' => ''
        ]);
    }
}
