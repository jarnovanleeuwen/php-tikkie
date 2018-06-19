<?php

namespace PHPTikkie\Tests;

use PHPTikkie\Entities\BankAccount;
use PHPTikkie\Entities\User;
use PHPTikkie\Exceptions\RequestException;

class FetchUsersTest extends TestCase
{
    public function testFetchUsersSuccess()
    {
        $this->setMockHttpResponse(['AccessToken.txt', 'FetchUsersSuccess.txt']);

        $users = $this->tikkie->users('platformtoken1');
        $user = $users[0];

        $this->assertCount(1, $users);
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('NewUser', $user->name);
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

        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('https://api.abnamro.com/v1/tikkie/platforms/platformtoken1/users', $request->getUri());
        $this->assertEmpty($request->getUri()->getQuery());
    }

    public function testFetchUsersFailed()
    {
        $this->setMockHttpResponse(['AccessToken.txt', 'ValidationError.txt']);

        $this->expectException(RequestException::class);
        $this->expectExceptionMessage("[ERR_1100_004] Field validation error (see https://developer.abnamro.com/api/tikkie/technical-details)");

        $this->tikkie->users('platformtoken1');
    }
}
