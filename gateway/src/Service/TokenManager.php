<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Token;
use App\Repository\TokenRepository;
use App\Storage\TokenStorage;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class TokenManager
{
    /**
     * @var TokenRepository
     */
    private $repository;

    /**
     * @var TokenStorage
     */
    private $storage;

    public function __construct(TokenRepository $repository, TokenStorage $storage)
    {
        $this->repository = $repository;
        $this->storage = $storage;
    }

    public function setTokenToStorage(string $publicToken): void
    {
        if (null === $token = $this->repository->findByPublicToken($publicToken)) {
            throw new AccessDeniedException('Invalid token');
        }

        $this->storage->setToken($token);
    }

    public function create(string $privateToken): Token
    {
        return $this->repository->save(
            new Token($privateToken, bin2hex(random_bytes(40)))
        );
    }
}
