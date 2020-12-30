<?php

namespace App\Manager;

use App\Dto\UserDataDto;

interface TokenManagerInterface
{
    public function encode(UserDataDto $dto): string;

    public function decode(string $token): UserDataDto;
}
