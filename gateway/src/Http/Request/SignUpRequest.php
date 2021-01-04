<?php

declare(strict_types=1);

namespace App\Http\Request;

use Symfony\Component\Validator\Constraints as Assert;

class SignUpRequest
{
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
     * @Assert\Length(min=6, max=20)
     */
    public $password;

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
