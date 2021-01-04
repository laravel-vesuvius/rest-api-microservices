<?php

declare(strict_types=1);

namespace App\Tests;

use Doctrine\DBAL\ConnectionException;
use Doctrine\ORM\EntityManagerInterface;

trait TransactionalTrait
{
    /** @var EntityManagerInterface|null */
    protected $entityManager;

    protected function isTransactional(): bool
    {
        return true;
    }

    public function beginTransaction()
    {
        $this->entityManager = self::$container->get('doctrine.orm.entity_manager');

        if ($this->isTransactional()) {
            $this->entityManager->beginTransaction();
        }
    }

    protected function rollbackTransaction()
    {
        if ($this->isTransactional()) {
            try {
                $this->entityManager->rollback();
            } catch(ConnectionException $e) {
                // Happens with autocommit on, can't disable in current version of Doctrine
            }
        }
    }
}
