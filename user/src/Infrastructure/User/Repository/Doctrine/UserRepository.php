<?php

namespace App\Infrastructure\User\Repository\Doctrine;

use App\Domain\Common\Repository\PaginatedQueryResult;
use App\Domain\User\Entity\User;
use App\Domain\User\Query\GetUsersQuery;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\View\UserDetailedView;
use App\Domain\User\View\UserSimpleView;
use App\Infrastructure\Common\Repository\AbstractDoctrineRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends AbstractDoctrineRepository implements UserRepositoryInterface, UserLoaderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @param string $username
     * @return User|null
     */
    public function loadUserByUsername($username): ?User
    {
        return $this->findOneBy([
            'email' => $username,
        ]);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->findOneBy(['email' => $email]);
    }

    public function findDetailedView(string $id): ?UserDetailedView
    {
        return $this->_em->createQueryBuilder()
            ->select(
                sprintf('NEW %s (u.id, u.email, u.personalData.firstName, u.personalData.lastName)', UserDetailedView::class)
            )
            ->from(User::class, 'u')
            ->where('u.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @param GetUsersQuery $query
     * @return PaginatedQueryResult
     */
    public function findUsers(GetUsersQuery $query): PaginatedQueryResult
    {
        $qb = $this->_em->createQueryBuilder()
            ->select(
                sprintf('NEW %s (u.id, u.email, u.personalData.firstName, u.personalData.lastName)', UserSimpleView::class)
            )
            ->from(User::class, 'u')
        ;

        return $this->paginate($qb, $query);
    }
}
