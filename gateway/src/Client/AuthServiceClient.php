<?php

declare(strict_types=1);

namespace App\Client;

use App\Dto\Auth\UserPayload;
use App\Storage\TokenStorage;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AuthServiceClient extends AbstractBaseClient
{
    private const GENERATE_TOKEN_ROUTE = '/api/generate-token';

    /**
     * @var string
     */
    private $host;

    public function __construct(string $host, HttpClientInterface $httpClient, TokenStorage $tokenStorage)
    {
        parent::__construct($httpClient, $tokenStorage);

        $this->host = ltrim($host, '/');
    }

    public function generateToken(UserPayload $userPayload): string
    {
        $response = $this->httpClient->request(
            Request::METHOD_POST,
            $this->host . self::GENERATE_TOKEN_ROUTE,
            [
                'body' => $userPayload->toArray(),
            ]
        );

        return $this->processResponse($response)['token'];
    }
}
