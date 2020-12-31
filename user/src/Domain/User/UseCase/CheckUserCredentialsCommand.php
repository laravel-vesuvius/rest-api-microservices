<?php

declare(strict_types=1);

namespace App\Domain\User\UseCase;

use App\Domain\Common\Message\SyncMessageInterface;

class CheckUserCredentialsCommand implements SyncMessageInterface
{
    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    public function __construct(string $username, string $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
