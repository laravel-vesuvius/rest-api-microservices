<?php

declare(strict_types=1);

namespace App\Application\Factory\Command;

use App\Application\Http\Request\User\SignUpRequest;
use App\Domain\User\UseCase\SignUpUserCommand;
use Ramsey\Uuid\Uuid;

class SignUpUserCommandFactory
{
    public static function createFromSignUpRequest(SignUpRequest $request): SignUpUserCommand
    {
        return new SignUpUserCommand(
            Uuid::uuid4()->toString(),
            $request->email,
            $request->password,
            $request->firstName,
            $request->lastName
        );
    }
}
