<?php

declare(strict_types=1);

namespace App\Tests\Application\Http\Controller;

use App\Tests\TestUtils\Traits\UserTrait;

class SignUpControllerTest extends AbstractRestTestCase
{
    use UserTrait;

    /** @test */
    public function shouldSignUp(): void
    {
        $data = [
            'email' => self::$faker->unique()->safeEmail,
            'password' => self::$faker->password(),
            'firstName' => self::$faker->firstName,
            'lastName' => self::$faker->lastName,
        ];

        $response = $this->sendPost('/api/auth/sign-up', $data);

        $responseData = json_decode($response->getContent(), true);

        self::assertEquals(200, $response->getStatusCode());

        self::assertEquals($data['email'], $responseData['email']);
        self::assertEquals($data['firstName'], $responseData['firstName']);
        self::assertEquals($data['lastName'], $responseData['lastName']);
    }

    /**
     * @test
     *
     * @dataProvider invalidUserDataProvider
     *
     * @param array $data
     * @param array $errors
     */
    public function shouldReturnValidationErrors(array $data, array $errors): void
    {
        $response = $this->sendPost('/api/auth/sign-up', $data);

        $responseData = json_decode($response->getContent(), true);

        self::assertEquals(422, $response->getStatusCode());
        self::assertArrayHasKey('errors', $responseData);

        foreach ($errors as $field => $error) {
            self::assertArrayHasKey($field, $responseData['errors']);
            self::assertContains($error, $responseData['errors'][$field]);
        }
    }

    /** @test */
    public function shouldReturnEmailExistsValidationError(): void
    {
        $user = $this->findRandomUser();

        $data = [
            'email' => $user->getUsername(),
            'password' => self::$faker->password(),
            'firstName' => self::$faker->firstName,
            'lastName' => self::$faker->lastName,
        ];

        $response = $this->sendPost('/api/auth/sign-up', $data);

        $responseData = json_decode($response->getContent(), true);

        self::assertEquals(422, $response->getStatusCode());
        self::assertArrayHasKey('errors', $responseData);
        self::assertArrayHasKey('email', $responseData['errors']);
        self::assertEquals(['NOT_UNIQUE_ERROR'], $responseData['errors']['email']);
    }

    public function invalidUserDataProvider(): array
    {
        return [
            [
                'data' => [
                    'email' => null,
                    'password' => null,
                    'firstName' => null,
                    'lastName' => null,
                ],
                'errors' => [
                    'email' => 'IS_BLANK_ERROR',
                    'password' => 'IS_BLANK_ERROR',
                    'firstName' => 'IS_BLANK_ERROR',
                    'lastName' => 'IS_BLANK_ERROR',
                ],
            ],
            [
                'data' => [
                    'email' => self::$faker->text(600),
                    'password' => self::$faker->text(600),
                    'firstName' => self::$faker->text(600),
                    'lastName' => self::$faker->text(600),
                ],
                'errors' => [
                    'email' => 'TOO_LONG_ERROR',
                    'password' => 'TOO_LONG_ERROR',
                    'firstName' => 'TOO_LONG_ERROR',
                    'lastName' => 'TOO_LONG_ERROR',
                ],
            ],
            [
                'data' => [
                    'email' => 'test',
                    'password' => 'aa',
                    'firstName' => [12],
                    'lastName' => 55,
                ],
                'errors' => [
                    'email' => 'STRICT_CHECK_FAILED_ERROR',
                    'password' => 'TOO_SHORT_ERROR',
                    'firstName' => 'INVALID_TYPE_ERROR',
                    'lastName' => 'INVALID_TYPE_ERROR',
                ],
            ],
        ];
    }
}
