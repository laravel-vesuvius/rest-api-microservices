<?php

declare(strict_types=1);

namespace App\Facade;

use App\Client\AuthServiceClient;
use App\Client\UserServiceClient;
use App\Dto\Auth\UserPayloadDto;
use App\Service\TokenManager;

class AuthFacade
{
    /**
     * @var AuthServiceClient
     */
    private $authServiceClient;

    /**
     * @var UserServiceClient
     */
    private $userServiceClient;

    /**
     * @var TokenManager
     */
    private $tokenManager;

    public function __construct(AuthServiceClient $authServiceClient, UserServiceClient $userServiceClient, TokenManager $tokenManager)
    {
        $this->authServiceClient = $authServiceClient;
        $this->userServiceClient = $userServiceClient;
        $this->tokenManager = $tokenManager;
    }

    public function signIn(string $username, string $password): string
    {
        return $this->generateToken(
            $this->userServiceClient->checkCredentials($username, $password)
        );
    }

    public function generateToken(UserPayloadDto $userPayload): string
    {
        $token = $this->tokenManager->create(
            $this->authServiceClient->generateToken($userPayload)
        );

        return $token->getPublicToken();
    }
}
