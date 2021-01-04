<?php

declare(strict_types=1);

namespace App\Domain\User\ValueObject;

use App\Domain\User\Entity\User;

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
     * @var string[]
     */
    private $roles;

    public function __construct(string $id, string $username, array $roles)
    {
        $this->id = $id;
        $this->username = $username;
        $this->roles = $roles;
    }

    public static function createFromUser(User $user): self
    {
        return new self($user->getId(), $user->getUsername(), $user->getRoles());
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
}
