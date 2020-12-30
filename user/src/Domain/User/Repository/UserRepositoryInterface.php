<?php

namespace App\Domain\User\Repository;

use App\Domain\Common\Repository\BaseEntityRepositoryInterface;
use App\Domain\Common\Repository\PaginatedQueryResult;
use App\Domain\User\Entity\User;
use App\Domain\User\Query\GetUsersQuery;
use App\Domain\User\View\UserDetailedView;

interface UserRepositoryInterface extends BaseEntityRepositoryInterface
{
    /**
     * @param $id
     * @return User|null
     */
    public function find($id);

    /**
     * @param string $id
     * @return User
     */
    public function findOrFail(string $id): object;

    public function findByEmail(string $email): ?User;

    public function findDetailedView(string $id): ?UserDetailedView;

    public function findUsers(GetUsersQuery $query): PaginatedQueryResult;
}
