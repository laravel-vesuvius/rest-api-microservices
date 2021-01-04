<?php

declare(strict_types=1);

namespace App\Domain\User\View;

class UserDetailedView extends UserSimpleView
{
    /**
     * @var string[]
     */
    private $roles;

    public function __construct(string $id, string $email, string $firstName, string $lastName, array $roles)
    {
        parent::__construct($id, $email, $firstName, $lastName);

        $this->roles = $roles;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }
}
