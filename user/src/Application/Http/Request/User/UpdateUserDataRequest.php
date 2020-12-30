<?php

declare(strict_types=1);

namespace App\Application\Http\Request\User;

use App\Application\Validator\Constraints as AppAssert;
use App\Domain\User\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @AppAssert\UniqueDto(
 *     fields={"email": "email"},
 *     mapToEntityClass=User::class,
 *     errorPath="email"
 * )
 */
class UpdateUserDataRequest
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     *
     * @Assert\Type("string")
     * @Assert\Email
     * @Assert\NotBlank
     * @Assert\Length(max="255")
     */
    public $email;

    /**
     * @var string
     *
     * @Assert\Type("string")
     * @Assert\NotBlank
     * @Assert\Length(max="255")
     */
    public $firstName;

    /**
     * @var string
     *
     * @Assert\Type("string")
     * @Assert\NotBlank
     * @Assert\Length(max="255")
     */
    public $lastName;
}
