<?php

declare(strict_types=1);

namespace App\Tests\Application\Http\Controller;

use App\Tests\TestUtils\Traits\UserTrait;

class UserControllerTest extends AbstractRestTestCase
{
    use UserTrait;

    /** @test */
    public function shouldReturnUserList(): void
    {
        $user = $this->findRandomUser();

        $response = $this->sendGet(
            '/api/secure/user',
            [],
            $this->logIn($user)
        );

        $responseData = json_decode($response->getContent(), true);

        self::assertEquals(200, $response->getStatusCode());
        self::assertArrayHasKey('count', $responseData);
        self::assertArrayHasKey('limit', $responseData);
        self::assertArrayHasKey('offset', $responseData);

        foreach ($responseData['data'] as $item) {
            self::assertArrayHasKey('id', $item);
            self::assertArrayHasKey('firstName', $item);
            self::assertArrayHasKey('lastName', $item);
            self::assertArrayHasKey('email', $item);
        }
    }

    /** @test */
    public function shouldReturnUserData(): void
    {
        $user = $this->findRandomUser();

        $response = $this->sendGet(
            sprintf('/api/secure/user/%s', $user->getId()),
            [],
            $this->logIn($user)
        );

        $responseData = json_decode($response->getContent(), true);

        self::assertEquals(200, $response->getStatusCode());

        self::assertArrayHasKey('id', $responseData);
        self::assertArrayHasKey('firstName', $responseData);
        self::assertArrayHasKey('lastName', $responseData);
        self::assertArrayHasKey('email', $responseData);
        self::assertArrayHasKey('roles', $responseData);
    }

    /** @test */
    public function shouldCreateUser(): void
    {
        $user = $this->findAdmin();

        $data = [
            'email' => self::$faker->unique()->safeEmail,
            'password' => self::$faker->password(),
            'firstName' => self::$faker->firstName,
            'lastName' => self::$faker->lastName,
        ];

        $response = $this->sendPost(
            '/api/secure/user',
            $data,
            [],
            $this->logIn($user)
        );

        $responseData = json_decode($response->getContent(), true);

        self::assertEquals(200, $response->getStatusCode());

        self::assertEquals($data['email'], $responseData['email']);
        self::assertEquals($data['firstName'], $responseData['firstName']);
        self::assertEquals($data['lastName'], $responseData['lastName']);
    }

    /** @test */
    public function shouldDenyAccessOnUserCreation(): void
    {
        $user = $this->findRandomUser();

        $data = [
            'email' => self::$faker->unique()->safeEmail,
            'password' => self::$faker->password(),
            'firstName' => self::$faker->firstName,
            'lastName' => self::$faker->lastName,
        ];

        $response = $this->sendPost(
            '/api/secure/user',
            $data,
            [],
            $this->logIn($user)
        );

        self::assertEquals(403, $response->getStatusCode());
    }

    /** @test */
    public function shouldUpdatePersonalData(): void
    {
        $user = $this->findRandomUser();

        $data = [
            'email' => $user->getUsername(),
            'firstName' => self::$faker->firstName,
            'lastName' => self::$faker->lastName,
        ];

        $response = $this->sendPut(
            sprintf('/api/secure/user/%s', $user->getId()),
            $data,
            [],
            $this->logIn($user)
        );

        $responseData = json_decode($response->getContent(), true);

        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals($data['email'], $responseData['email']);
        self::assertEquals($data['firstName'], $responseData['firstName']);
        self::assertEquals($data['lastName'], $responseData['lastName']);
    }

    /**
     * @test
     *
     * @dataProvider invalidPersonalDataProvider
     *
     * @param array $data
     * @param array $errors
     */
    public function shouldReturnValidationErrors(array $data, array $errors): void
    {
        $user = $this->findRandomUser();

        $response = $this->sendPut(
            sprintf('/api/secure/user/%s', $user->getId()),
            $data,
            [],
            $this->logIn($user)
        );

        $responseData = json_decode($response->getContent(), true);

        self::assertEquals(422, $response->getStatusCode());
        self::assertArrayHasKey('errors', $responseData);

        foreach ($errors as $field => $error) {
            self::assertArrayHasKey($field, $responseData['errors']);
            self::assertContains($error, $responseData['errors'][$field]);
        }
    }

    /** @test */
    public function shouldDeleteUser(): void
    {
        $admin = $this->findAdmin();
        $user = $this->findRandomUser();

        $response = $this->sendDelete(
            sprintf('/api/secure/user/%s', $user->getId()),
            [],
            $this->logIn($admin)
        );

        self::assertEquals(204, $response->getStatusCode());
    }

    /** @test */
    public function shouldDenyAccessOnUserDeleting(): void
    {
        $user = $this->findRandomUser();
        $anotherUser = $this->findUserExcept($user);

        $response = $this->sendDelete(
            sprintf('/api/secure/user/%s', $anotherUser->getId()),
            [],
            $this->logIn($user)
        );

        self::assertEquals(403, $response->getStatusCode());
    }

    public function invalidPersonalDataProvider(): array
    {
        return [
            [
                'data' => [
                    'email' => null,
                    'firstName' => null,
                    'lastName' => null,
                ],
                'errors' => [
                    'email' => 'IS_BLANK_ERROR',
                    'firstName' => 'IS_BLANK_ERROR',
                    'lastName' => 'IS_BLANK_ERROR',
                ],
            ],
            [
                'data' => [
                    'email' => self::$faker->text(600),
                    'firstName' => self::$faker->text(600),
                    'lastName' => self::$faker->text(600),
                ],
                'errors' => [
                    'email' => 'TOO_LONG_ERROR',
                    'firstName' => 'TOO_LONG_ERROR',
                    'lastName' => 'TOO_LONG_ERROR',
                ],
            ],
            [
                'data' => [
                    'email' => 'test',
                    'firstName' => [12],
                    'lastName' => 55,
                ],
                'errors' => [
                    'email' => 'STRICT_CHECK_FAILED_ERROR',
                    'firstName' => 'INVALID_TYPE_ERROR',
                    'lastName' => 'INVALID_TYPE_ERROR',
                ],
            ],
        ];
    }
}
