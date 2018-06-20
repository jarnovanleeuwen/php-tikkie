<?php

namespace PHPTikkie\Tests;

use PHPTikkie\Exceptions\AccessTokenException;

class EnvironmentTest extends TestCase
{
    public function testPrivateKeyIsRequired()
    {
        $this->expectException(AccessTokenException::class);

        $this->tikkie->getEnvironment()->loadPrivateKeyFromString('');

        $this->tikkie->newPlatform()->save();
    }

    public function testInvalidApiKeyHandling()
    {
        $this->setMockHttpResponse('InvalidApiKey.txt');

        $this->expectException(AccessTokenException::class);
        $this->expectExceptionMessage("[ERR_2005_001] The API Key is invalid (see https://developer.abnamro.com/get-started#obtaining-an-api-key)");

        $this->tikkie->newPlatform()->save();
    }

    public function testHeadersOnApiRequest()
    {
        $this->setMockHttpResponse(['AccessToken.txt', 'CreatePlatformSuccess.txt']);

        $this->tikkie->newPlatform()->save();

        // AccessTokenRequest
        $request = $this->history[0]['request'];
        $this->assertEquals(static::API_KEY, $request->getHeader('API-Key')[0]);

        // CreatePlatformRequest
        $request = $this->history[1]['request'];
        $this->assertEquals(static::API_KEY, $request->getHeader('API-Key')[0]);
        $this->assertTrue($request->hasHeader('Authorization'));
    }
}
