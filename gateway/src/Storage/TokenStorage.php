<?php

declare(strict_types=1);

namespace App\Storage;

use App\Entity\Token;

class TokenStorage
{
    /**
     * @var Token|null
     */
    private $token;

    public function getToken(): ?Token
    {
        return $this->token;
    }

    public function getPrivateToken(): ?string
    {
        return $this->token ? $this->token->getPrivateToken() : null;
    }

    public function setToken(Token $token): void
    {
        $this->token = $token;
    }
}
