<?php

declare(strict_types=1);

namespace App\Domain\User\Query;

use App\Domain\Common\Message\QueryHandlerInterface;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\View\UserDetailedView;

class FindUserQueryHandler implements QueryHandlerInterface
{
    /**
     * @var UserRepositoryInterface
     */
    private $repository;

    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(FindUserQuery $query): ?UserDetailedView
    {
        return $this->repository->findDetailedView($query->getId());
    }
}
