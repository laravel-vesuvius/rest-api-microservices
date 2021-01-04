<?php

declare(strict_types=1);

namespace App\Domain\User\Query;

use App\Domain\Common\Message\SyncMessageInterface;

class GetUserPayloadByUsernameQuery implements SyncMessageInterface
{
    /**
     * @var string
     */
    private $username;

    public function __construct(string $username)
    {
        $this->username = $username;
    }

    public function getUsername(): string
    {
        return $this->username;
    }
}
