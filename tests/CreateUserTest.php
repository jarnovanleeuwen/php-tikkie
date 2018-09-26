<?php

namespace PHPTikkie\Tests;

use PHPTikkie\Entities\BankAccount;
use PHPTikkie\Entities\User;
use PHPTikkie\Exceptions\RequestException;

class CreateUserTest extends TestCase
{
    public function testCreateUserSuccess()
    {
        $this->setMockHttpResponse(['AccessToken.txt', 'CreateUserSuccess.txt']);

        $user = $this->newUser()->save();

        $this->assertEquals('YourLabel', $user->bankAccountLabel);
        $this->assertEquals('NL00BANK123456789', $user->iban);
        $this->assertEquals('NewUser', $user->name);
        $this->assertEquals('06123456789', $user->phoneNumber);
        $this->assertEquals('platformtoken1', $user->platformToken);
        $this->assertEquals(User::STATUS_ACTIVE, $user->status);
        $this->assertEquals('usertoken1', $user->userToken);
        $this->assertTrue($user->isActive());
        $this->assertCount(1, $user->bankAccounts);

        $bankAccount = $user->bankAccounts[0];

        $this->assertInstanceOf(BankAccount::class, $bankAccount);
        $this->assertEquals('Personal account', $bankAccount->bankAccountLabel);
        $this->assertEquals('bankaccounttoken1', $bankAccount->bankAccountToken);
        $this->assertEquals('NL02ABNA0123456789', $bankAccount->iban);

        $request = $this->history[1]['request'];

        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('https://api.abnamro.com/v1/tikkie/platforms/platformtoken1/users', $request->getUri());
        $this->assertEquals('{"name":"ExamplePlatform","phoneNumber":"06123456789","iban":"NL00BANK123456789","bankAccountLabel":"YourLabel"}', $request->getBody());
    }

    public function testCreateUserFailed()
    {
        $this->setMockHttpResponse(['AccessToken.txt', 'ValidationError.txt']);

        $this->expectException(RequestException::class);
        $this->expectExceptionMessage("[ERR_1100_004] Field validation error | traceId: 3cbf4bc9-108c-4e02-ad6e-937c79d875e3 | (see https://developer.abnamro.com/api/tikkie/technical-details)");

        $this->newUser()->save();
    }

    protected function newUser(): User
    {
        return $this->tikkie->newUser('platformtoken1', [
            'name' => 'ExamplePlatform',
            'phoneNumber' => '06123456789',
            'iban' => 'NL00BANK123456789',
            'bankAccountLabel' => 'YourLabel'
        ]);
    }
}
