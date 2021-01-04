<?php

declare(strict_types=1);

namespace App\Tests\Application\Http\Controller;

use App\Application\Security\Authenticator\TokenAuthenticator;
use App\Domain\User\Entity\User;
use App\Tests\AbstractWebTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

class AbstractRestTestCase extends AbstractWebTestCase
{
    /** @var RequestStack|null */
    private $requestStack;

    public function setUp()
    {
        parent::setUp();

        $this->requestStack = self::$container->get(RequestStack::class);
    }

    protected function logIn(User $user): string
    {
        $this->requestStack->push(new Request());

        $this->client->getContainer()->set(
            'test.App\Application\Security\Authenticator\TokenAuthenticator',
            $this->mockAuthenticator($user)
        );

        return 'token';
    }

    protected function sendPost(string $resource, array $data, array $headers = [], string $apiToken = null)
    {
        return $this->sendJSONRequest('POST', $resource, $data, $headers, $apiToken);
    }

    protected function sendPut(string $resource, array $data, array $headers = [], string $apiToken = null)
    {
        return $this->sendJSONRequest('PUT', $resource, $data, $headers, $apiToken);
    }

    protected function sendPatch(string $resource, array $data, array $headers = [], string $apiToken = null)
    {
        return $this->sendJSONRequest('PATCH', $resource, $data, $headers, $apiToken);
    }

    protected function sendGet(string $resource, array $headers = [], string $apiToken = null, array $params = [])
    {
        return $this->sendJSONRequest('GET', $resource, $params, $headers, $apiToken);
    }

    protected function sendDelete(string $resource, array $headers = [], string $apiToken = null)
    {
        return $this->sendJSONRequest('DELETE', $resource, [], $headers, $apiToken);
    }

    protected function sendJSONRequest(
        string $method,
        string $resource,
        array $data = [],
        array $headers = [],
        string $apiToken = null
    ): Response {
        $headers = array_merge([
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => $apiToken ? 'Bearer '.$apiToken : '',
        ], $headers);

        $this->client->request(
            $method,
            $resource,
            $method === Request::METHOD_GET ? $data : [],
            [],
            $headers,
            ($method !== Request::METHOD_GET && $data) ? json_encode($data) : null
        );

        return $this->client->getResponse();
    }

    protected function sendFormDataRequest(
        string $resource,
        array $data = [],
        array $files = [],
        array $headers = [],
        string $apiToken = null
    ): Response {
        $headers = array_merge([
            'CONTENT_TYPE' => 'multipart/form-data',
            'HTTP_AUTHORIZATION' => $apiToken ? 'Bearer '.$apiToken : '',
        ], $headers);

        $this->client->request(
            'POST',
            $resource,
            $data,
            $files,
            $headers
        );

        return $this->client->getResponse();
    }

    private function mockAuthenticator(User $user): MockObject
    {
        $mock = $this->createMock(TokenAuthenticator::class);
        $mock->method('createAuthenticatedToken')->willReturn(
            new PostAuthenticationGuardToken($user, 'token', $user->getRoles())
        );
        $mock->method('supports')->willReturn(true);
        $mock->method('getCredentials')->willReturn('');
        $mock->method('checkCredentials')->willReturn(true);
        $mock->method('getUser')->willReturn($user);
        $mock->method('onAuthenticationFailure')->willReturn(
            new JsonResponse([''], Response::HTTP_UNAUTHORIZED)
        );
        $mock->method('start')->willReturn(
            new JsonResponse(['message' => 'Authentication Required'], Response::HTTP_UNAUTHORIZED)
        );

        return $mock;
    }
}
