<?php

namespace PHPTikkie\Tests;

use PHPTikkie\Entities\Platform;
use PHPTikkie\Exceptions\RequestException;

class FetchPlatformsTest extends TestCase
{
    public function testFetchPlatformsSuccess()
    {
        $this->setMockHttpResponse(['AccessToken.txt', 'FetchPlatformsSuccess.txt']);

        $platforms = $this->tikkie->platforms();
        $platform = $platforms[0];

        $this->assertCount(1, $platforms);
        $this->assertInstanceOf(Platform::class, $platform);
        $this->assertEquals('platformtoken1', $platform->platformToken);
        $this->assertEquals('x@yz.com', $platform->email);
        $this->assertEquals('NewPlatform', $platform->name);
        $this->assertEquals('0601234567', $platform->phoneNumber);
        $this->assertEquals(Platform::USAGE_TYPE_MYSELF, $platform->platformUsage);
        $this->assertNull($platform->notificationUrl);
        $this->assertTrue($platform->isActive());

        $request = $this->history[1]['request'];

        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('https://api.abnamro.com/v1/tikkie/platforms', $request->getUri());
        $this->assertEmpty($request->getUri()->getQuery());
    }

    public function testFetchUsersFailed()
    {
        $this->setMockHttpResponse(['AccessToken.txt', 'ValidationError.txt']);

        $this->expectException(RequestException::class);
        $this->expectExceptionMessage("[ERR_1100_004] Field validation error (see https://developer.abnamro.com/api/tikkie/technical-details)");

        $this->tikkie->platforms();
    }
}
