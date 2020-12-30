<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class UserDataDto
{
    /**
     * @var string
     *
     * @Assert\Type("string")
     * @Assert\Uuid
     * @Assert\NotBlank
     */
    public $id;

    /**
     * @var string
     *
     * @Assert\Type("string")
     * @Assert\Email
     * @Assert\NotBlank
     */
    public $username;

    /**
     * @var string[]
     *
     * @Assert\Type("array")
     * @Assert\NotBlank
     */
    public $roles;

    public static function create(string $id, string $username, array $roles): self
    {
        $self = new self();
        $self->id = $id;
        $self->username = $username;
        $self->roles = $roles;

        return $self;
    }
}
