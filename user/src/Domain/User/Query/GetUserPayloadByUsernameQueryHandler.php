<?php

declare(strict_types=1);

namespace App\Domain\User\Query;

use App\Domain\Common\Message\QueryHandlerInterface;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\ValueObject\UserPayload;

class GetUserPayloadByUsernameQueryHandler implements QueryHandlerInterface
{
    /**
     * @var UserRepositoryInterface
     */
    private $repository;

    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(GetUserPayloadByUsernameQuery $query): ?UserPayload
    {
        if (null === $user = $this->repository->findByEmail($query->getUsername())) {
            return null;
        }

        return UserPayload::createFromUser($user);
    }
}
