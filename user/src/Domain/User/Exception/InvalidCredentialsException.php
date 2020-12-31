<?php

declare(strict_types=1);

namespace App\Domain\User\Exception;

use App\Domain\Common\Exception\DomainException;

class InvalidCredentialsException extends DomainException
{
    public function __construct()
    {
        parent::__construct('INVALID_CREDENTIALS');
    }
}
