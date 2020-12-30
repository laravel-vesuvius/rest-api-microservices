<?php

declare(strict_types=1);

namespace App\Domain\User\Exception;

use App\Domain\Common\Exception\DomainException;

class UserWithEmailAlreadyExistsException extends DomainException
{
    public function __construct()
    {
        parent::__construct('USER_WITH_EMAIL_ALREADY_EXISTS');
    }
}
