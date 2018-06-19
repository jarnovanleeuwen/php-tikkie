<?php
namespace PHPTikkie;

use DateTimeInterface;
use PHPTikkie\Entities\PaymentRequest;
use PHPTikkie\Entities\Platform;
use PHPTikkie\Entities\User;
use PHPTikkie\Requests\CreatePaymentRequestRequest;
use PHPTikkie\Requests\CreatePlatformRequest;
use PHPTikkie\Requests\CreateUserRequest;
use PHPTikkie\Requests\FetchPaymentRequestRequest;
use PHPTikkie\Requests\FetchPaymentRequestsRequest;
use PHPTikkie\Requests\FetchPlatformsRequest;
use PHPTikkie\Requests\FetchUsersRequest;

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

    public function getEnvironment(): Environment
    {
        return $this->environment;
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
        $response = $this->environment->send(new CreatePaymentRequestRequest($paymentRequest));

        $paymentRequest->setAttributes($response->getData());
    }

    public function paymentRequest(string $platformToken, string $userToken, string $paymentRequestToken): PaymentRequest
    {
        $response = $this->environment->send(new FetchPaymentRequestRequest($platformToken, $userToken, $paymentRequestToken));

        $paymentRequest = new PaymentRequest($this);

        $paymentRequest->setAttributes($response->getData());

        return $paymentRequest;
    }

    /**
     * @return PaymentRequest[]
     */
    public function paymentRequests(string $platformToken, string $userToken, int $offset, int $limit, DateTimeInterface $fromDate = null, DateTimeInterface $toDate = null): array
    {
        $response = $this->environment->send(new FetchPaymentRequestsRequest($platformToken, $userToken, $offset, $limit, $fromDate, $toDate));

        $paymentRequests = [];
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
        $response = $this->environment->send(new CreatePlatformRequest($platform));

        $platform->setAttributes($response->getData());
    }

    /**
     * @return Platform[]
     */
    public function platforms(): array
    {
        $response = $this->environment->send(new FetchPlatformsRequest);

        $platforms = [];
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
        $response = $this->environment->send(new CreateUserRequest($user));

        $user->setAttributes($response->getData());
    }

    /**
     * @return User[]
     */
    public function users(string $platformToken): array
    {
        $response = $this->environment->send(new FetchUsersRequest($platformToken));

        $users = [];
        foreach ($response->getData() as $userData) {
            $user = new User($this);

            $user->setAttributes($userData);

            $users[] = $user;
        }

        return $users;
    }
}
