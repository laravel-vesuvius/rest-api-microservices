<?php

declare(strict_types=1);

namespace App\Application\Factory\Command;

use App\Application\Http\Request\User\UpdateUserDataRequest;
use App\Domain\User\UseCase\UpdateUserDataCommand;

class UpdateUserDataCommandFactory
{
    public static function createFromUpdateUserDataRequest(UpdateUserDataRequest $request): UpdateUserDataCommand
    {
        return new UpdateUserDataCommand(
            $request->id,
            $request->email,
            $request->firstName,
            $request->lastName
        );
    }
}
