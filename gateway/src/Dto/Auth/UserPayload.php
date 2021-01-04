<?php

declare(strict_types=1);

namespace App\Dto\Auth;

class UserPayload
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $username;

    /**
     * @var array
     */
    private $roles;

    public function __construct(string $id, string $username, array $roles)
    {
        $this->id = $id;
        $this->username = $username;
        $this->roles = $roles;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function toArray(): array
    {
        $result = [];
        foreach ($this as $property => $value) {
            $result[$property] = $value;
        }

        return $result;
    }
}
