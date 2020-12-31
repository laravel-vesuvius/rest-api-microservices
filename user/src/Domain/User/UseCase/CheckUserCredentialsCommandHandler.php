<?php

declare(strict_types=1);

namespace App\Domain\User\UseCase;

use App\Domain\Common\Message\CommandHandlerInterface;
use App\Domain\User\Exception\InvalidCredentialsException;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\Service\PasswordEncoderInterface;

class CheckUserCredentialsCommandHandler implements CommandHandlerInterface
{
    /**
     * @var UserRepositoryInterface
     */
    private $repository;

    /**
     * @var PasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserRepositoryInterface $repository, PasswordEncoderInterface $passwordEncoder)
    {
        $this->repository = $repository;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function __invoke(CheckUserCredentialsCommand $command)
    {
        if (null === $user = $this->repository->loadUserByUsername($command->getUsername())) {
            throw new InvalidCredentialsException();
        }

        if (!$this->passwordEncoder->isPasswordValid($user, $command->getPassword())) {
            throw new InvalidCredentialsException();
        }
    }
}
