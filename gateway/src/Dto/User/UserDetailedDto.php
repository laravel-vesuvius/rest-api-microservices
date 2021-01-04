<?php

declare(strict_types=1);

namespace App\Dto\User;

class UserDetailedDto extends UserSimpleDto
{
    /**
     * @var string[]
     */
    private $roles;

    /**
     * @var string|null
     */
    private $token;

    public function __construct(string $id, string $email, string $firstName, string $lastName, array $roles)
    {
        parent::__construct($id, $email, $firstName, $lastName);

        $this->roles = $roles;
    }

    public static function createFromUserServiceResponse(array $data): self
    {
        return new self(
            $data['id'],
            $data['email'],
            $data['firstName'],
            $data['lastName'],
            $data['roles']
        );
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }
}
