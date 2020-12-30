<?php

namespace App\Domain\User\Service;

use App\Domain\User\Entity\UserInterface;

interface PasswordEncoderInterface
{
    /**
     * Encodes the plain password.
     *
     * @param UserInterface $user
     * @param string $plainPassword
     * @return string The encoded password
     */
    public function encodePassword(UserInterface $user, string $plainPassword): string;

    /**
     * @param UserInterface $user
     * @param string $raw
     * @return bool true if the password is valid, false otherwise
     */
    public function isPasswordValid(UserInterface $user, string $raw): bool;
}
