<?php

declare(strict_types=1);

namespace App\Tests\Application\Mapper;

use App\Application\Http\Request\User\SignUpRequest;
use App\Application\Mapper\DtoToEntityMapper;
use App\Domain\User\Entity\User;
use App\Tests\AbstractKernelTestCase;
use App\Tests\TestUtils\Traits\UserTrait;
use ReflectionClass;

class DtoToEntityMapperTest extends AbstractKernelTestCase
{
    use UserTrait;

    /**
     * @dataProvider dataProvider
     * @param array $fields
     * @param SignUpRequest $dto
     */
    public function testMap(array $fields, SignUpRequest $dto): void
    {
        $mapper = new DtoToEntityMapper($dto);

        $result = $mapper->map($fields, User::class);

        self::assertInstanceOf(User::class, $result);
        self::assertEquals($dto->email, $this->getUserField($result, 'email'));
    }

    /**
     * @dataProvider dataProvider
     * @param array $fields
     * @param SignUpRequest $dto
     */
    public function testGetDtoReflection(array $fields, SignUpRequest $dto): void
    {
        $mapper = new DtoToEntityMapper($dto);

        $result = $mapper->getDtoReflection();

        self::assertInstanceOf(ReflectionClass::class, $result);
    }

    public function dataProvider(): array
    {
        $data = [
            [
                'fields' => ['email'],
            ],
            [
                'fields' => ['email' => 'email'],
            ],
        ];

        return array_map(function (array $item) {
            $dto = new SignUpRequest();
            $dto->email = self::$faker->safeEmail;
            $item['dto'] = $dto;

            return $item;
        }, $data);
    }
}
