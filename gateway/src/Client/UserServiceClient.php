<?php

declare(strict_types=1);

namespace App\Client;

use App\Dto\Auth\UserPayloadDto;
use App\Dto\User\UserDetailedDto;
use App\Dto\User\UserListDto;
use App\Dto\User\UserSimpleDto;
use App\Http\Query\UserListQueryParams;
use App\Http\Request\SignUpRequest;
use App\Http\Request\UpdateUserDataRequest;
use App\Storage\TokenStorage;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class UserServiceClient extends AbstractBaseClient
{
    private const SIGN_UP_ROUTE = '/api/auth/sign-up';
    private const LIST_ROUTE = '/api/secure/user';
    private const FIND_ROUTE = '/api/secure/user/%s';
    private const CREATE_ROUTE = '/api/secure/user';
    private const UPDATE_ROUTE = '/api/secure/user/%s';
    private const DELETE_ROUTE = '/api/secure/user/%s';
    private const CHECK_CREDENTIALS_ROUTE = '/api/auth/check-credentials';

    /**
     * @var string
     */
    private $host;

    public function __construct(string $host, HttpClientInterface $httpClient, TokenStorage $tokenStorage)
    {
        parent::__construct($httpClient, $tokenStorage);

        $this->host = ltrim($host, '/');
    }

    public function findAll(UserListQueryParams $params): UserListDto
    {
        $response = $this->httpClient->request(
            Request::METHOD_GET,
            $this->host . self::LIST_ROUTE,
            [
                'query' => $params->toArray(),
                'headers' => ['Authorization' => sprintf('Bearer %s', $this->tokenStorage->getPrivateToken())],
            ]
        );

        $responseData = $this->processResponse($response);

        $users = array_map(static function (array $item) {
            return new UserSimpleDto($item['id'], $item['email'], $item['firstName'], $item['lastName']);
        }, $responseData['data'] ?? []);

        return new UserListDto(
            $users,
            $responseData['limit'],
            $responseData['offset'],
            $responseData['count']
        );
    }

    public function find(string $id): UserDetailedDto
    {
        $response = $this->httpClient->request(
            Request::METHOD_GET,
            $this->host . sprintf(self::FIND_ROUTE, $id),
            [
                'headers' => ['Authorization' => sprintf('Bearer %s', $this->tokenStorage->getPrivateToken())],
            ]
        );

        $responseData = $this->processResponse($response);

        return UserDetailedDto::createFromUserServiceResponse($responseData);
    }

    public function create(SignUpRequest $request): UserDetailedDto
    {
        $response = $this->httpClient->request(
            Request::METHOD_POST,
            $this->host . self::CREATE_ROUTE,
            [
                'body' => (array)$request,
                'headers' => ['Authorization' => sprintf('Bearer %s', $this->tokenStorage->getPrivateToken())],
            ]
        );

        $responseData = $this->processResponse($response);

        return UserDetailedDto::createFromUserServiceResponse($responseData);
    }

    public function update(string $id, UpdateUserDataRequest $request): UserDetailedDto
    {
        $response = $this->httpClient->request(
            Request::METHOD_PUT,
            $this->host . sprintf(self::UPDATE_ROUTE, $id),
            [
                'body' => (array)$request,
                'headers' => ['Authorization' => sprintf('Bearer %s', $this->tokenStorage->getPrivateToken())],
            ]
        );

        $responseData = $this->processResponse($response);

        return UserDetailedDto::createFromUserServiceResponse($responseData);
    }

    public function delete(string $id): void
    {
        $response = $this->httpClient->request(
            Request::METHOD_DELETE,
            $this->host . sprintf(self::DELETE_ROUTE, $id),
            [
                'headers' => ['Authorization' => sprintf('Bearer %s', $this->tokenStorage->getPrivateToken())],
            ]
        );

        $this->processResponse($response);
    }

    public function signUp(SignUpRequest $request): UserDetailedDto
    {
        $response = $this->httpClient->request(
            Request::METHOD_POST,
            $this->host . self::SIGN_UP_ROUTE,
            [
                'body' => (array)$request,
            ]
        );

        $responseData = $this->processResponse($response);

        return UserDetailedDto::createFromUserServiceResponse($responseData);
    }

    public function checkCredentials(string $username, string $password): UserPayloadDto
    {
        $response = $this->httpClient->request(
            Request::METHOD_POST,
            $this->host . self::CHECK_CREDENTIALS_ROUTE,
            [
                'body' => ['username' => $username, 'password' => $password],
            ]
        );

        $responseData = $this->processResponse($response);

        return new UserPayloadDto($responseData['id'], $responseData['username'], $responseData['roles']);
    }
}
