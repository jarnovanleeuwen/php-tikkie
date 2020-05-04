

# <img src="https://user-images.githubusercontent.com/1358997/33891467-4e1a15ea-df56-11e7-98a0-ecc3e1ec07f0.png" width="64">

[![Build Status](https://travis-ci.com/jarnovanleeuwen/php-tikkie.svg?branch=master)](https://travis-ci.com/jarnovanleeuwen/php-tikkie)

⚠️ Tikkie has announced that on **01-01-2021** it will stop supporting the Tikkie Payment Request API (the API that this library implements) in favor of the new Tikkie API. See https://medium.com/abn-amro-developer/abn-amro-introduces-the-new-tikkie-api-87c8bb54720a for more information, including a roadmap.

Currently, I am not planning to update this library. This might change in the feature, but meanwhile PRs are welcome.

---

#### Easily create payment requests through [Tikkie](https://tikkie.me/).

Unofficial PHP implementation of the [Tikkie Payment Request API](https://developer.abnamro.com/content/tikkie).

# Installation

*PHPTikkie requires PHP 7.1+*

Add this package to your project using [Composer](https://getcomposer.org/):

`composer require jarnovanleeuwen/php-tikkie`

# Usage

### Initialise PHPTikkie
```php
use PHPTikkie\Environment;
use PHPTikkie\PHPTikkie;

$apiKey = "abc123";
$testMode = true;

$environment = new Environment($apiKey, $testMode);
$environment->loadPrivateKey('private_rsa.pem');

$tikkie = new PHPTikkie($environment);
```

### Create platform
```php
use PHPTikkie\Entities\Platform;

$platform = $tikkie->newPlatform([
    // Mandatory attributes
    'name' => 'YourPlatform',
    'phoneNumber' => '06123456789',
    'platformUsage' => Platform::USAGE_TYPE_MYSELF,

    // Optional attributes
    'email' => 'tikkie@yourcompany.com',
    'notificationUrl' => ''
])->save();

$platformToken = $platform->platformToken;
```

### Create user
```php
$user = $tikkie->newUser($platformToken, [
    'name' => 'ExamplePlatform',
    'phoneNumber' => '06123456789',
    'iban' => 'NL00BANK123456789',
    'bankAccountLabel' => 'YourLabel'
])->save();

$userToken = $user->userToken;
$bankAccountToken = $user->bankAccounts[0]->bankAccountToken;
```

### Create payment request
```php
$paymentRequest = $tikkie->newPaymentRequest($platformToken, $userToken, $bankAccountToken, [
    // Mandatory attributes
    'amountInCents' => '1250',
    'currency' => 'EUR',
    'description' => 'Thank you',
    'externalId' => 'Order 1234'
])->save();

$tikkieUrl = $paymentRequest->paymentRequestUrl;
$paymentRequestToken = $paymentRequest->paymentRequestToken;
```

### Get payment request
```php
function paymentRequest(string $platformToken, string $userToken, string $paymentRequestToken): PaymentRequest
```

### List platforms
```php
function platforms(): Platform[]
```

### List users
```php
function users(string $platformToken): User[]
```

### List payment requests
```php
function paymentRequests(string $platformToken, string $userToken, int $offset, int $limit, DateTimeInterface $fromDate = null, DateTimeInterface $toDate = null): PaymentRequest[]
```

### Process payments
```php
$paymentRequest = $tikkie->paymentRequest($platformToken, $userToken, $paymentRequestToken);

foreach ($paymentRequest->payments as $payment) {
    if ($payment->isPaid()) {
        // Payment successful
    }
}
```

### Exception handling
All methods may return a `PHPTikkieException` containing an error code and description.

```php
use PHPTikkie\Exceptions\PHPTikkieException;

try {
    var_dump($tikkie->platforms());
} catch (PHPTikkieException $exception) {
    print $exception->getMessage(); // [ERR_2005_002] The API Key is invalid for the requested resource | traceId: 6fda2ce8-225d-4ca2-920a-b687c7aeb2f3 | (see https://developer.abnamro.com/get-started#obtaining-an-api-key)
}
