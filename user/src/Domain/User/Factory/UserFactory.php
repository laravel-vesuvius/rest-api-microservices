<?php

declare(strict_types=1);

namespace App\Domain\User\Factory;

use App\Domain\User\Entity\User;
use App\Domain\User\Service\PasswordEncoderInterface;
use App\Domain\User\ValueObject\PersonalData;

class UserFactory
{
    /**
     * @var PasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(PasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function createUser(string $id, string $email, string $password, string $firstName, string $lastName): User
    {
        $user = User::createUser($id, $email, new PersonalData($firstName, $lastName));
        $user->setPassword(
            $this->passwordEncoder->encodePassword($user, $password)
        );

        return $user;
    }

    public function createAdmin(string $id, string $email, string $password, string $firstName, string $lastName): User
    {
        $user = User::createAdmin($id, $email, new PersonalData($firstName, $lastName));
        $user->setPassword(
            $this->passwordEncoder->encodePassword($user, $password)
        );

        return $user;
    }
}
