<?php

declare(strict_types=1);

namespace App\Domain\Exception;

use DomainException;

use function sprintf;

class ResourceNotFoundException extends DomainException
{
    public static function createFromClassAndId(string $class, string $id): self
    {
        return new static(sprintf('Resource of type [%s] with ID [%s] not found', $class, $id));
    }

    public static function createFromClassAndEmail(string $class, string $email): self
    {
        return new static(sprintf('Resource of type [%s] with Email [%s] not found', $class, $email));
    }

    public static function createFromClassAndName(string $class, string $name): self
    {
        return new static(sprintf('Resource of type [%s] with Name [%s] not found', $class, $name));
    }

    public static function createFromClassAndProperty(string $class, string $property, string $value): self
    {
        return new static(sprintf('Resource of type [%s] with [%s] [%s] not found', $class, $property, $value));
    }
}
