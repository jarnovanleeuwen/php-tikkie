<?php
namespace PHPTikkie\Requests;

use PHPTikkie\Entities\User;

class CreateUserRequest extends AbstractRequest
{
    /**
     * @var User
     */
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getMethod(): string
    {
        return 'POST';
    }

    public function getUri(): string
    {
        return "v1/tikkie/platforms/{$this->user->platformToken}/users";
    }

    public function getPayload(): ?array
    {
        $user = $this->user;
        
        return [
            'name' => $user->name,
            'phoneNumber' => $user->phoneNumber,
            'iban' => $user->iban,
            'bankAccountLabel' => $user->bankAccountLabel
        ];
    }
}
