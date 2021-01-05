<?php

declare(strict_types=1);

namespace App\Facade;

use App\Client\AuthServiceClient;
use App\Client\UserServiceClient;
use App\Dto\Auth\UserPayloadDto;
use App\Dto\User\UserDetailedDto;
use App\Dto\User\UserListDto;
use App\Http\Query\UserListQueryParams;
use App\Http\Request\SignUpRequest;
use App\Http\Request\UpdateUserDataRequest;

class UserFacade
{
    /**
     * @var UserServiceClient
     */
    private $userServiceClient;

    /**
     * @var AuthFacade
     */
    private $authFacade;

    public function __construct(UserServiceClient $userServiceClient, AuthFacade $authFacade)
    {
        $this->userServiceClient = $userServiceClient;
        $this->authFacade = $authFacade;
    }

    public function findAll(UserListQueryParams $params): UserListDto
    {
        return $this->userServiceClient->findAll($params);
    }

    public function find(string $id): UserDetailedDto
    {
        return $this->userServiceClient->find($id);
    }

    public function create(SignUpRequest $request): UserDetailedDto
    {
        return $this->userServiceClient->create($request);
    }

    public function update(string $id, UpdateUserDataRequest $request): UserDetailedDto
    {
        $user = $this->userServiceClient->update($id, $request);

        $token = $this->authFacade->generateToken(
            new UserPayloadDto($user->getId(), $user->getEmail(), $user->getRoles())
        );
        $user->setToken($token);

        return $user;
    }

    public function delete(string $id): void
    {
        $this->userServiceClient->delete($id);
    }

    public function signUp(SignUpRequest $request): UserDetailedDto
    {
        $user = $this->userServiceClient->signUp($request);

        $token = $this->authFacade->generateToken(
            new UserPayloadDto($user->getId(), $user->getEmail(), $user->getRoles())
        );
        $user->setToken($token);

        return $user;
    }
}
