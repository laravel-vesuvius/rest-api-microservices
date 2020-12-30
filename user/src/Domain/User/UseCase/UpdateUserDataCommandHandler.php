<?php

declare(strict_types=1);

namespace App\Domain\User\UseCase;

use App\Domain\Common\Message\CommandHandlerInterface;
use App\Domain\User\Repository\UserRepositoryInterface;

class UpdateUserDataCommandHandler implements CommandHandlerInterface
{
    /**
     * @var UserRepositoryInterface
     */
    private $repository;

    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(UpdateUserDataCommand $command): void
    {
        $user = $this->repository->findOrFail($command->getId());
        $user->updateData($command->getEmail(), $command->getFirstName(), $command->getLastName());

        $this->repository->save($user);
    }
}
