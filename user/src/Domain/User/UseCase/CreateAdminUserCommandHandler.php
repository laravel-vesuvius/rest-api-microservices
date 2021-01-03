<?php

declare(strict_types=1);

namespace App\Domain\User\UseCase;

use App\Domain\Common\Message\CommandHandlerInterface;
use App\Domain\User\Exception\UserWithEmailAlreadyExistsException;
use App\Domain\User\Factory\UserFactory;
use App\Domain\User\Repository\UserRepositoryInterface;

class CreateAdminUserCommandHandler implements CommandHandlerInterface
{
    /**
     * @var UserRepositoryInterface
     */
    private $repository;

    /**
     * @var UserFactory
     */
    private $factory;

    public function __construct(UserRepositoryInterface $repository, UserFactory $factory)
    {
        $this->repository = $repository;
        $this->factory = $factory;
    }

    public function __invoke(CreateAdminUserCommand $command): void
    {
        if (null !== $this->repository->findByEmail($command->getEmail())) {
            throw new UserWithEmailAlreadyExistsException();
        }

        $this->repository->save(
            $this->factory->createAdmin(
                $command->getId(),
                $command->getEmail(),
                $command->getPassword(),
                $command->getFirstName(),
                $command->getLastName()
            )
        );
    }
}
