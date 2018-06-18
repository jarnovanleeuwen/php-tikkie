<?php
namespace PHPTikkie;

use DateTime;
use DateTimeInterface;
use PHPTikkie\Entities\PaymentRequest;
use PHPTikkie\Entities\Platform;
use PHPTikkie\Entities\User;

class PHPTikkie
{
    /**
     * @var Environment
     */
    protected $environment;

    public function __construct(Environment $environment)
    {
        $this->environment = $environment;
    }

    public function newPaymentRequest(string $platformToken, string $userToken, string $bankAccountToken, array $attributes = []): PaymentRequest
    {
        $paymentRequest = new PaymentRequest($this);

        $paymentRequest->platformToken = $platformToken;
        $paymentRequest->userToken = $userToken;
        $paymentRequest->bankAccountToken = $bankAccountToken;

        $paymentRequest->setAttributes($attributes);

        return $paymentRequest;
    }

    public function persistPaymentRequest(PaymentRequest $paymentRequest)
    {
        $response = $this->environment->postRequest("/v1/tikkie/platforms/{$paymentRequest->platformToken}/users/{$paymentRequest->userToken}/bankaccounts/{$paymentRequest->bankAccountToken}/paymentrequests", [
            'amountInCents' => $paymentRequest->amountInCents,
            'currency' => $paymentRequest->currency,
            'description' => $paymentRequest->description,
            'externalId' => $paymentRequest->externalId
        ]);

        $paymentRequest->setAttributes($response->getData());
    }

    public function paymentRequest(string $platformToken, string $userToken, string $paymentRequestToken): PaymentRequest
    {
        $response = $this->environment->getRequest("/v1/tikkie/platforms/{$platformToken}/users/{$userToken}/paymentrequests/{$paymentRequestToken}");

        $paymentRequest = new PaymentRequest($this);

        $paymentRequest->setAttributes($response->getData());

        return $paymentRequest;
    }

    /**
     * @return PaymentRequest[]
     */
    public function paymentRequests(string $platformToken, string $userToken, int $offset, int $limit, DateTimeInterface $fromDate = null, DateTimeInterface $toDate = null): array
    {
        $paymentRequests = [];

        $params = compact('offset', 'limit');

        if ($fromDate) {
            $params['fromDate'] = (new DateTime())->setTimestamp($fromDate->getTimestamp())
                ->setTimezone('UTC')
                ->format('Y-m-d\TH:i:s\Z');
        }

        if ($toDate) {
            $params['toDate'] = (new DateTime())->setTimestamp($toDate->getTimestamp())
                ->setTimezone('UTC')
                ->format('Y-m-d\TH:i:s\Z');
        }

        $response = $this->environment->getRequest("/v1/tikkie/platforms/{$platformToken}/users/{$userToken}/paymentrequests", $params);

        foreach ($response->getData()['paymentRequests'] as $paymentRequestData) {
            $paymentRequest = new PaymentRequest($this);

            $paymentRequest->setAttributes($paymentRequestData);

            $paymentRequests[] = $paymentRequest;
        }

        return $paymentRequests;
    }

    public function newPlatform(array $attributes = []): Platform
    {
        $platform = new Platform($this);

        $platform->setAttributes($attributes);

        return $platform;
    }

    public function persistPlatform(Platform $platform)
    {
        $response = $this->environment->postRequest("/v1/tikkie/platforms", [
            'email' => $platform->email,
            'name' => $platform->name,
            'notificationUrl' => $platform->notificationUrl,
            'phoneNumber' => $platform->phoneNumber,
            'platformUsage' => $platform->platformUsage,
        ]);

        $platform->setAttributes($response->getData());
    }

    /**
     * @return Platform[]
     */
    public function platforms(): array
    {
        $platforms = [];

        $response = $this->environment->getRequest("/v1/tikkie/platforms");

        foreach ($response->getData() as $platformData) {
            $platform = new Platform($this);

            $platform->setAttributes($platformData);

            $platforms[] = $platform;
        }

        return $platforms;
    }

    public function newUser(string $platformToken, array $attributes = []): User
    {
        $user = new User($this);

        $user->platformToken = $platformToken;

        $user->setAttributes($attributes);

        return $user;
    }

    public function persistUser(User $user)
    {
        $response = $this->environment->postRequest("/v1/tikkie/platforms/{$user->platformToken}/users", [
            'name' => $user->name,
            'phoneNumber' => $user->phoneNumber,
            'iban' => $user->iban,
            'bankAccountLabel' => $user->bankAccountLabel
        ]);

        $user->setAttributes($response->getData());
    }

    /**
     * @return User[]
     */
    public function users(string $platformToken): array
    {
        $users = [];

        $response = $this->environment->getRequest("/v1/tikkie/platforms/{$platformToken}/users");

        foreach ($response->getData() as $userData) {
            $user = new User($this);

            $user->setAttributes($userData);

            $users[] = $user;
        }

        return $users;
    }
}
