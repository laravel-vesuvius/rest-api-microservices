<?php

namespace App\Application\Doctrine\DataFixtures;

use App\Domain\User\Entity\User;
use App\Domain\User\ValueObject\PersonalData;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Ramsey\Uuid\Uuid;

class AppFixtures extends Fixture
{
    /**
     * @var Generator
     */
    protected $faker;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->faker = Factory::create();
        $this->entityManager = $entityManager;
    }

    public function load(ObjectManager $manager): void
    {
        $this->createUsers($manager, 20);
        $this->createAdmins($manager, 5);

        $manager->flush();
    }

    private function createUsers(ObjectManager $manager, int $count = 10): void
    {
        for ($i = 0; $i < $count; $i++) {
            $user = User::createUser(
                Uuid::uuid4()->toString(),
                $this->faker->unique()->safeEmail,
                new PersonalData($this->faker->firstName, $this->faker->lastName)
            );

            $manager->persist($user);
        }
    }

    private function createAdmins(ObjectManager $manager, int $count = 10): void
    {
        for ($i = 0; $i < $count; $i++) {
            $user = User::createAdmin(
                Uuid::uuid4()->toString(),
                $this->faker->unique()->safeEmail,
                new PersonalData($this->faker->firstName, $this->faker->lastName)
            );

            $manager->persist($user);
        }
    }
}
