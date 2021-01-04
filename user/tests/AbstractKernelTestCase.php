<?php

declare(strict_types=1);

namespace App\Tests;

use App\Domain\User\Entity\User;
use Faker\Factory;
use Faker\Generator;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\JWTUserToken;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class AbstractKernelTestCase extends KernelTestCase
{
    use TransactionalTrait;

    /**
     * @var Generator
     */
    protected $faker;

    protected function setUp()
    {
        parent::setUp();

        static::$kernel = static::bootKernel();

        $this->beginTransaction();

        $this->faker = Factory::create();
    }

    protected function tearDown(): void
    {
        $this->rollbackTransaction();

        $this->faker = null;

        parent::tearDown();
    }

    protected function authenticate(User $user)
    {
        self::$container->get('security.token_storage')->setToken(new JWTUserToken([], $user));
    }
}
