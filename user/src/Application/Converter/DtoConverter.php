<?php

declare(strict_types=1);

namespace App\Application\Converter;

use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class DtoConverter
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function convertToDto(string $dtoClass, array $data, string $format = 'json'): object
    {
        return $this->serializer->denormalize(
            $data,
            $dtoClass,
            $format,
            [
                AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true,
            ]
        );
    }
}
