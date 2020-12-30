<?php

declare(strict_types=1);

namespace App\Domain\User\UseCase;

use App\Domain\Common\Message\CommandHandlerInterface;
use App\Domain\User\Repository\UserRepositoryInterface;

class DeleteUserCommandHandler implements CommandHandlerInterface
{
    /**
     * @var UserRepositoryInterface
     */
    private $repository;

    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(DeleteUserCommand $command): void
    {
        if (null !== $user = $this->repository->find($command->getId())) {
            $this->repository->remove($user);
        }
    }
}
