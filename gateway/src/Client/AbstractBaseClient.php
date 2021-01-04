<?php

declare(strict_types=1);

namespace App\Client;

use App\Exception\ServiceErrorException;
use App\Storage\TokenStorage;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

abstract class AbstractBaseClient
{
    /**
     * @var HttpClientInterface
     */
    protected $httpClient;

    /**
     * @var TokenStorage
     */
    protected $tokenStorage;

    public function __construct(HttpClientInterface $httpClient, TokenStorage $tokenStorage)
    {
        $this->httpClient = $httpClient;
        $this->tokenStorage = $tokenStorage;
    }

    protected function processResponse(ResponseInterface $response): ?array
    {
        $responseData = json_decode($response->getContent(false), true);
        if (400 <= $response->getStatusCode()) {
            throw new ServiceErrorException($response->getStatusCode(), $responseData);
        }

        return $responseData;
    }
}
