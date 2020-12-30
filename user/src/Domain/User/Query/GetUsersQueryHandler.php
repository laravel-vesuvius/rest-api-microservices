<?php

declare(strict_types=1);

namespace App\Domain\User\Query;

use App\Domain\Common\Message\QueryHandlerInterface;
use App\Domain\Common\Repository\PaginatedQueryResult;
use App\Domain\User\Repository\UserRepositoryInterface;

class GetUsersQueryHandler implements QueryHandlerInterface
{
    /**
     * @var UserRepositoryInterface
     */
    private $repository;

    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(GetUsersQuery $query): PaginatedQueryResult
    {
        return $this->repository->findUsers($query);
    }
}
