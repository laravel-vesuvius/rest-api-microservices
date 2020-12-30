<?php

declare(strict_types=1);

namespace App\Application\Utils;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\Persistence\Proxy;
use InvalidArgumentException;
use ReflectionClass;

class ObjectUtils
{
    /**
     * @param object $object
     * @return bool
     */
    public static function isEntity($object): bool
    {
        if (!is_object($object)) {
            return false;
        }

        if ($object instanceof Proxy) {
            return true;
        }

        $annotationReader = new AnnotationReader();

        return $annotationReader->getClassAnnotation(new ReflectionClass($object), Entity::class) !== null;
    }

    /**
     * @param object $object
     * @return mixed
     */
    public static function getIdFromEntity($object)
    {
        if (self::isEntity($object)) {
            return $object->getId();
        }

        throw new InvalidArgumentException('The object is not an entity');
    }
}
