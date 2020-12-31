<?php

declare(strict_types=1);

namespace App\Application\Client\Auth;

use App\Application\Service\UserPayload;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AuthServiceClient
{
    private CONST AUTHENTICATE_ROUTE = '/api/authenticate';

    /**
     * @var string
     */
    private $authServiceHost;

    /**
     * @var HttpClientInterface
     */
    private $client;

    public function __construct(string $authServiceHost, HttpClientInterface $client)
    {
        $this->authServiceHost = ltrim($authServiceHost, '/');
        $this->client = $client;
    }

    public function authenticate(string $token): ?UserPayload
    {
        $response = $this->client->request(
            Request::METHOD_POST,
            $this->authServiceHost . self::AUTHENTICATE_ROUTE,
            [
                'body' => ['token' => $token]
            ]
        );

        if (200 !== $response->getStatusCode()) {
            return null;
        }

        $responseData = json_decode($response->getContent(false), true);

        return new UserPayload($responseData['id'], $responseData['username'], $responseData['roles']);
    }
}
