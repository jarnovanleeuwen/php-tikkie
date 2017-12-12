# <img src="https://user-images.githubusercontent.com/1358997/33891467-4e1a15ea-df56-11e7-98a0-ecc3e1ec07f0.png" width="64">

#### Easily create payment requests through [Tikkie](https://tikkie.me/).

Unofficial PHP implementation of the [Tikkie API](https://developer.abnamro.com/content/tikkie).

# Installation

*PHPTikkie requires PHP 7+*

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

    // Optional attributes
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
    print $exception->getMessage(); // [ERR_2005_002] The API Key is invalid for the requested resource (see https://developer.abnamro.com/get-started#obtaining-an-api-key)
}
