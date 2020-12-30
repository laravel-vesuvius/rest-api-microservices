<?php

declare(strict_types=1);

namespace App\Infrastructure\Common\Repository;

use App\Domain\Common\Exception\EntityNotFoundException;
use App\Domain\Common\Message\AbstractListQuery;
use App\Domain\Common\Repository\BaseEntityRepositoryInterface;
use App\Domain\Common\Repository\PaginatedQueryResult;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

abstract class AbstractDoctrineRepository extends ServiceEntityRepository implements BaseEntityRepositoryInterface
{
    public function paginate(QueryBuilder $qb, AbstractListQuery $params): PaginatedQueryResult
    {
        $paginator = new Paginator($qb->getQuery());
        $paginator->setUseOutputWalkers(false);
        $paginator->getQuery()
            ->setFirstResult($params->getOffset())
            ->setMaxResults($params->getLimit())
        ;

        return new PaginatedQueryResult(
            iterator_to_array($paginator->getIterator()),
            $params->getLimit(),
            $params->getOffset(),
            $paginator->count()
        );
    }

    public function findOrFail(string $id): object
    {
        $entity = $this->find($id);
        if (!$entity) {
            throw new EntityNotFoundException($this->getClassName());
        }

        return $entity;
    }

    public function save(object $entity): object
    {
        $this->_em->persist($entity);
        $this->_em->flush();

        return $entity;
    }

    /**
     * @param object[] $entities
     * @return object[]
     */
    public function saveAll(array $entities): array
    {
        foreach ($entities as $entity) {
            $this->_em->persist($entity);
        }

        $this->_em->flush();

        return $entities;
    }

    public function remove(object $entity): void
    {
        $this->_em->remove($entity);
        $this->_em->flush();
    }
}
