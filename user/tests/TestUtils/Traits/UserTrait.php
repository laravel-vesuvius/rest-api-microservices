<?php

declare(strict_types=1);

namespace App\Tests\TestUtils\Traits;

use App\Domain\User\Entity\User;
use App\Domain\User\Enum\RoleEnum;
use App\Domain\User\ValueObject\PersonalData;
use Doctrine\ORM\EntityManagerInterface;

trait UserTrait
{
    /**
     * @var EntityManagerInterface|null
     */
    protected $entityManager;

    private function findRandomUser(): User
    {
        $qb = $this->entityManager->createQueryBuilder();

        return $qb
            ->select('u')
            ->from(User::class, 'u')
            ->where(
                $qb->expr()->andX(
                    $qb->expr()->notLike('u.roles', ':adminRole')
                )
            )
            ->setParameters([
                'adminRole' => '%' . RoleEnum::ROLE_ADMIN . '%',
            ])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    private function findUserExcept(User $user): User
    {
        $qb = $this->entityManager->createQueryBuilder();

        return $qb
            ->select('u')
            ->from(User::class, 'u')
            ->where('u.id <> :id')
            ->setParameter('id', $user->getId())
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    private function findAdmin(): User
    {
        $qb = $this->entityManager->createQueryBuilder();

        return $qb
            ->select('u')
            ->from(User::class, 'u')
            ->where($qb->expr()->like('u.roles', ':role'))
            ->setParameter('role', '%' . RoleEnum::ROLE_ADMIN . '%')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    private function getUserPersonalData(User $user): PersonalData
    {
        $reflectionClass = new \ReflectionClass(User::class);
        $property = $reflectionClass->getProperty('personalData');
        $property->setAccessible(true);

        return $property->getValue($user);
    }

    private function getUserField(User $user, string $field)
    {
        $reflectionClass = new \ReflectionClass(User::class);
        if ($reflectionClass->hasProperty($field)) {
            $property = $reflectionClass->getProperty($field);
            $property->setAccessible(true);

            return $property->getValue($user);
        }

        throw new \InvalidArgumentException('Undefined field ' . $field);
    }
}
