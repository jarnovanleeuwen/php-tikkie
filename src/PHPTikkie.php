<?php
namespace PHPTikkie;

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
            'notificationURL' => $platform->notificationURL,
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
